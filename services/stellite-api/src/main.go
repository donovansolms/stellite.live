// Package main implements a quickly hacked together information
// gatherer to collect information about the Stellite blockchain
// for display on stellite.live
package main

import (
	"fmt"
	"os"
	"os/signal"
	"strings"
	"sync"
	"sync/atomic"
	"syscall"
	"time"

	"bitbucket.org/iliveit/other-projects/stellite-api/src/connectors"
	"bitbucket.org/iliveit/other-projects/stellite-api/src/models"
	"github.com/jinzhu/gorm"
	"github.com/kelseyhightower/envconfig"
	log "github.com/sirupsen/logrus"

	// This is how SQL drivers are imported, golint complains if
	// you don't have an explanation comment here
	_ "github.com/go-sql-driver/mysql"
)

var isRunning uint32
var logger *log.Entry
var waitGroup sync.WaitGroup

// Config represents the configuration for the application
type Config struct {
	LogFormat                 string `split_words:"true"`
	LogLevel                  string `split_words:"true"`
	DatabaseEndpoint          string `split_words:"true"`
	DatabaseName              string `split_words:"true"`
	DatabaseUsername          string `split_words:"true"`
	DatabasePassword          string `split_words:"true"`
	XtlDaemonEndpoint         string `split_words:"true"`
	ExchangeTradeogreEndpoint string `split_words:"true"`
	ExchangeCrex24Endpoint    string `split_words:"true"`
	ExchangeSleepSeconds      int    `split_words:"true"`
	BlockchainSleepSeconds    int    `split_words:"true"`
}

func main() {

	var config Config
	err := envconfig.Process("", &config)
	if err != nil {
		log.Fatal(err.Error())
	}
	log.SetOutput(os.Stdout)
	log.SetFormatter(&log.JSONFormatter{
		TimestampFormat: "Jan 02 15:04:05",
	})
	if strings.ToLower(config.LogFormat) == "text" {
		log.SetFormatter(&log.TextFormatter{
			FullTimestamp:   true,
			TimestampFormat: "Jan 02 15:04:05",
		})
	}
	logLevel, err := log.ParseLevel(config.LogLevel)
	if err != nil {
		log.Fatal(err)
	}
	log.SetLevel(logLevel)
	logger = log.WithFields(log.Fields{
		"service": "stellite-api",
	})

	// Setup signal handlers
	signalChannel := make(chan os.Signal, 2)
	signal.Notify(signalChannel, syscall.SIGHUP, syscall.SIGINT, syscall.SIGTERM)
	// Remember, on Linux, syscall.SIGKILL can't be caught
	//runWaitGroup.Add(1)
	go func() {
		sig := <-signalChannel
		switch sig {
		case syscall.SIGHUP:
			log.Warn("SIGHUP received from OS")
			//handle Reload
		case syscall.SIGINT:
			log.Warn("SIGINT received from OS")
			stop()
		case syscall.SIGTERM:
			log.Warn("SIGTERM received from OS")
			stop()
		}
	}()

	waitGroup.Add(1)
	go run(config)
	waitGroup.Add(1)
	go syncExchanges(config)

	waitGroup.Wait()
	logger.Info("Shutdown")
}

func stop() {
	atomic.StoreUint32(&isRunning, 0)
}

func syncExchanges(config Config) {
	defer waitGroup.Done()
	var exchanges []connectors.Exchange
	exchanges = append(exchanges, &connectors.Crex24{
		Endpoint: config.ExchangeCrex24Endpoint,
	})
	exchanges = append(exchanges, &connectors.TradeOgre{
		Endpoint: config.ExchangeTradeogreEndpoint,
	})
	connectionString := fmt.Sprintf("%s:%s@tcp(%s)/%s?%s",
		config.DatabaseUsername,
		config.DatabasePassword,
		config.DatabaseEndpoint,
		config.DatabaseName,
		"charset=utf8&parseTime=True")

	logger.WithFields(log.Fields{
		"daemon_endpoint": config.XtlDaemonEndpoint,
		"routine":         "exchange_sync",
	}).Info("Run")
	atomic.StoreUint32(&isRunning, 1)
	for atomic.LoadUint32(&isRunning) == 1 {

		db, err := gorm.Open("mysql", connectionString)
		if err != nil {
			logger.Fatalf("Unable to connect to database: %s", err)
		}
		defer db.Close()

		for _, exchange := range exchanges {
			logger.WithFields(log.Fields{
				"exchange": exchange.GetName(),
				"routine":  "exchange_sync",
			}).Info("Fetching trade information")

			ticker, err := exchange.GetTicker()
			if err != nil {
				logger.WithFields(log.Fields{
					"exchange": exchange.GetName(),
					"err":      err,
					"routine":  "exchange_sync",
				}).Warning("Unable to fetch trade information")
				goto skip
			}

			price := models.Price{
				Exchange:     exchange.GetName(),
				High:         ticker.High,
				Low:          ticker.Low,
				Last:         ticker.Last,
				Volume:       ticker.VolumeBTC,
				DateCaptured: time.Now().UTC(),
			}
			query := db.Save(&price)
			if err != nil {
				logger.WithFields(log.Fields{
					"exchange": exchange.GetName(),
					"err":      query.Error,
					"routine":  "exchange_sync",
				}).Warning("Unable to save trade information")
				goto skip
			}
		}

	skip:
		db.Close()
		logger.WithFields(log.Fields{
			"routine": "exchange_sync",
			"seconds": config.ExchangeSleepSeconds,
		}).Info("Sleeping")
		time.Sleep(time.Second * time.Duration(config.ExchangeSleepSeconds))
	}

}

func run(config Config) {
	defer waitGroup.Done()
	connectionString := fmt.Sprintf("%s:%s@tcp(%s)/%s?%s",
		config.DatabaseUsername,
		config.DatabasePassword,
		config.DatabaseEndpoint,
		config.DatabaseName,
		"charset=utf8&parseTime=True")

	logger.WithFields(log.Fields{
		"daemon_endpoint": config.XtlDaemonEndpoint,
		"routine":         "chain",
	}).Info("Run")
	atomic.StoreUint32(&isRunning, 1)

	daemon := connectors.Daemon{
		Endpoint: config.XtlDaemonEndpoint,
	}
	for atomic.LoadUint32(&isRunning) == 1 {
		// Get last block height from database
		db, err := gorm.Open("mysql", connectionString)
		if err != nil {
			logger.Fatalf("Unable to connect to database: %s", err)
		}
		defer db.Close()

		// Since I', hacing this quickly and using gotos, declare the vars here
		var blockInfo connectors.GetBlockResponse

		var lastBlock models.Block
		query := db.Last(&lastBlock)
		if query.Error != nil {
			if query.Error == gorm.ErrRecordNotFound {
				// If none, start at 0
				logger.WithFields(log.Fields{
					"err": query.Error,
				}).Warning("No block information found - starting from height 0")
			} else {
				logger.WithFields(log.Fields{
					"err":     query.Error,
					"routine": "chain",
				}).Error("Unable to query database")
				goto skip
			}
		} else {
			lastBlock.Height++
		}

		logger.WithFields(log.Fields{
			"height":  lastBlock.Height,
			"routine": "chain",
		}).Info("Reading blockchain")

		// Fetch information for the given height
		blockInfo, err = daemon.GetBlockInfo(lastBlock.Height)
		if err != nil {
			logger.WithFields(log.Fields{
				"height":  lastBlock.Height,
				"err":     err,
				"routine": "chain",
			}).Warning("Unable to get block info from daemon")
			goto skip
		}

		lastBlock = models.Block{
			Height:     blockInfo.Result.BlockHeader.Height,
			Difficulty: blockInfo.Result.BlockHeader.Difficulty,
			Reward:     float64(blockInfo.Result.BlockHeader.Reward) / float64(100.00),
			Timestamp:  time.Unix(blockInfo.Result.BlockHeader.Timestamp, 0).UTC(),
			TxCount:    blockInfo.Result.BlockHeader.NumTxes,
		}
		query = db.Save(&lastBlock)
		if query.Error != nil {
			logger.WithFields(log.Fields{
				"err":     query.Error,
				"routine": "chain",
			}).Error("Unable to save block to database")
			goto skip
		}

	skip:
		db.Close()
		logger.WithFields(log.Fields{
			"routine": "chain",
			"seconds": config.BlockchainSleepSeconds,
		}).Info("Sleeping")
		time.Sleep(time.Second * time.Duration(config.BlockchainSleepSeconds))
	}
}
