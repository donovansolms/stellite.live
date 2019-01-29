package runner

import (
	"sync/atomic"
	"time"

	"github.com/donovansolms/stellite-api/src/connectors"
	"github.com/donovansolms/stellite-api/src/models"
	"github.com/jinzhu/gorm"
	"github.com/sirupsen/logrus"
)

// Blockchain runner retrieves information from the blockchain
type Blockchain struct {
	// DaemonEndpoint is the blockchain daemon's endpoint used to retrieve
	// the information from
	DaemonEndpoint string
	// ConnectionString is the database connection string
	ConnectionString string
	// SleepSeconds determines the sleep interval between fetching data
	SleepSeconds int
	// Logger for the runner
	Logger *logrus.Entry

	// isRunning keeps the routine running
	isRunning uint32
}

// Run the blockchain information retriever
func (chain *Blockchain) Run() error {

	chain.Logger.Debug("Fetching blocks from blockchain")

	db, err := gorm.Open("mysql", chain.ConnectionString)
	if err != nil {
		return err
	}
	defer func() {
		err = db.Close()
		if err != nil {
			chain.Logger.Error(err)
		}
	}()

	daemon := connectors.Daemon{
		Endpoint: chain.DaemonEndpoint,
	}

	var blockInfo connectors.GetBlockResponse

	var lastBlock models.Block
	query := db.Last(&lastBlock)
	if query.Error != nil {
		if query.Error == gorm.ErrRecordNotFound {
			// If none, start at 0
			chain.Logger.WithFields(logrus.Fields{
				"err": query.Error,
			}).Warning("No block information found - starting from height 0")
		} else {
			chain.Logger.WithFields(logrus.Fields{
				"err": query.Error,
			}).Fatal("Unable to query database")
		}
	} else {
		// Start from the next block
		lastBlock.Height++
	}

	atomic.StoreUint32(&chain.isRunning, 1)
	for atomic.LoadUint32(&chain.isRunning) == 1 {

		chain.Logger.WithFields(logrus.Fields{
			"height": lastBlock.Height,
		}).Info("Reading blockchain")

		// Fetch information for the given height
		blockInfo, err = daemon.GetBlockInfo(lastBlock.Height)
		if err != nil {
			chain.Logger.WithFields(logrus.Fields{
				"height": lastBlock.Height,
				"err":    err,
			}).Warning("Unable to get block info from daemon, sleep")
			time.Sleep(time.Minute)
			continue
		}
		if blockInfo.Error.Message != "" {
			chain.Logger.WithFields(logrus.Fields{
				"height":  lastBlock.Height,
				"err":     blockInfo.Error.Message,
				"routine": "chain",
			}).Warning("End of chain, sleep")
			time.Sleep(time.Second * time.Duration(chain.SleepSeconds))
			continue
		}
		// Only save non orphans
		if blockInfo.Result.BlockHeader.OrphanStatus == false {
			lastBlock = models.Block{
				Height:     blockInfo.Result.BlockHeader.Height,
				Difficulty: blockInfo.Result.BlockHeader.Difficulty,
				Reward:     float64(blockInfo.Result.BlockHeader.Reward) / float64(100.00),
				Timestamp:  time.Unix(blockInfo.Result.BlockHeader.Timestamp, 0).UTC(),
				TxCount:    blockInfo.Result.BlockHeader.NumTxes,
			}
			query = db.Save(&lastBlock)
			if query.Error != nil {
				chain.Logger.WithFields(logrus.Fields{
					"err":     query.Error,
					"routine": "chain",
				}).Error("Unable to save block to database, sleep")
				time.Sleep(time.Second * time.Duration(chain.SleepSeconds))
				continue
			}
		}
		lastBlock.Height++
	}
	return nil
}

// Stop syncing
func (chain *Blockchain) Stop() {
	chain.Logger.Info("Stopping blockchain runner")
	atomic.StoreUint32(&chain.isRunning, 0)
}
