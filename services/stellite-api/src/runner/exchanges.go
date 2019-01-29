package runner

import (
	"fmt"
	"strings"
	"sync/atomic"
	"time"

	"github.com/donovansolms/stellite-api/src/connectors"
	"github.com/donovansolms/stellite-api/src/models"
	"github.com/jinzhu/gorm"
	"github.com/sirupsen/logrus"
)

// Exchanges retrieves trading information from exchanges and stores it
// in the database
type Exchanges struct {
	// ConnectionString is the database connection string
	ConnectionString string
	// SleepSeconds determines the sleep interval between fetching data
	SleepSeconds int
	// Logger for the runner
	Logger *logrus.Entry

	// isRunning keeps the routine running
	isRunning uint32
}

// Run retrieves the exchange information at specified intevals
func (ex *Exchanges) Run() error {
	ex.Logger.Debug("Fetching exchanges from database")

	db, err := gorm.Open("mysql", ex.ConnectionString)
	if err != nil {
		return err
	}
	defer func() {
		err = db.Close()
		if err != nil {
			ex.Logger.Error(err)
		}
	}()

	var exchangeModels []models.Exchange
	query := db.Find(&exchangeModels)
	if query.Error != nil {
		ex.Logger.Error(query.Error)
		return query.Error
	}
	var exchanges []connectors.Exchange
	for _, exchangeModel := range exchangeModels {
		if strings.ToLower(exchangeModel.Name) == "crex24" {
			exchanges = append(exchanges, &connectors.Crex24{
				Endpoint: exchangeModel.Endpoint,
				Base:     "BTC",
				Alt:      "XTL",
			})
		}
		if strings.ToLower(exchangeModel.Name) == "tradeogre" {
			exchanges = append(exchanges, &connectors.TradeOgre{
				Endpoint: exchangeModel.Endpoint,
				Base:     "BTC",
				Alt:      "XTL",
			})
		}
		if strings.ToLower(exchangeModel.Name) == "stex" {
			exchanges = append(exchanges, &connectors.Stex{
				Endpoint: exchangeModel.Endpoint,
				Base:     "BTC",
				Alt:      "XTL",
			})
		}
	}

	atomic.StoreUint32(&ex.isRunning, 1)
	for atomic.LoadUint32(&ex.isRunning) == 1 {
		var maxPrice float64
		var totalVolume float64
		for _, exchange := range exchanges {
			ex.Logger.WithFields(logrus.Fields{
				"exchange": exchange.GetName(),
			}).Info("Fetching trade information")

			ticker, err := exchange.GetTicker()
			if err != nil {
				ex.Logger.WithFields(logrus.Fields{
					"exchange": exchange.GetName(),
					"err":      err,
				}).Warning("Unable to fetch trade information")
				time.Sleep(time.Second * time.Duration(ex.SleepSeconds))
				continue
			}

			price := models.Price{
				Exchange:     exchange.GetName(),
				High:         ticker.High,
				Low:          ticker.Low,
				Last:         ticker.Last,
				Volume:       ticker.VolumeBTC,
				DateCaptured: time.Now().UTC(),
			}
			query = db.Save(&price)
			if query.Error != nil {
				ex.Logger.WithFields(logrus.Fields{
					"exchange": exchange.GetName(),
					"err":      query.Error,
				}).Warning("Unable to save trade information")
				time.Sleep(time.Second * time.Duration(ex.SleepSeconds))
				continue
			}

			if price.High > maxPrice {
				maxPrice = price.High
			}
			totalVolume += price.Volume
		}

		// Now check if a record has been broken
		// Highest price first
		var tradingRecord models.TradingRecord
		query = db.Where("name = ?", "price_high").Find(&tradingRecord)
		if query.Error != nil {
			ex.Logger.WithFields(logrus.Fields{
				"err": query.Error,
			}).Warning("Unable to fetch trading record")
			continue
		}
		if maxPrice > tradingRecord.Value {
			tradingRecord.Value = maxPrice
			tradingRecord.Date = time.Now()
			query = db.Save(&tradingRecord)
			if query.Error != nil {
				ex.Logger.WithFields(logrus.Fields{
					"err": query.Error,
				}).Warning("Unable to update trading record (price)")
				continue
			}
		}

		// Now trading volume
		tradingRecord = models.TradingRecord{}
		query = db.Where("name = ?", "volume").Find(&tradingRecord)
		if query.Error != nil {
			ex.Logger.WithFields(logrus.Fields{
				"err": query.Error,
			}).Warning("Unable to fetch trading record (volume)")
			continue
		}
		if totalVolume > tradingRecord.Value {
			tradingRecord.Value = totalVolume
			tradingRecord.Date = time.Now()
			query = db.Save(&tradingRecord)
			if query.Error != nil {
				ex.Logger.WithFields(logrus.Fields{
					"err": query.Error,
				}).Warning("Unable to update trading record (volume)")
				continue
			}
		}

		// Update the BTC price
		btcExchange := connectors.Crex24{
			Endpoint: "https://api.crex24.com/CryptoExchangeService/BotPublic/ReturnTicker?request=[NamePairs=%s_%s]",
			Base:     "USD",
			Alt:      "BTC",
		}
		ticker, err := btcExchange.GetTicker()
		if err == nil {
			btcPrice := models.BtcUsd{
				Usd:         ticker.Last,
				DateUpdated: time.Now(),
			}
			query = db.Save(&btcPrice)
			if query.Error != nil {
				if query.Error != nil {
					ex.Logger.WithFields(logrus.Fields{
						"err": query.Error,
					}).Warning("Unable to update BTC price")
				}
			}
		}

		ex.Logger.WithFields(logrus.Fields{
			"seconds": ex.SleepSeconds,
		}).Debug("Sleeping")
		time.Sleep(time.Second * time.Duration(ex.SleepSeconds))
	}
	return nil
}

// Stop fetching data
func (ex *Exchanges) Stop() {
	if ex.Logger == nil {
		fmt.Println("LOGGER IS NIL FOR NO REASON - WE LOGGED WHILE RUNNING!!")
	}
	atomic.StoreUint32(&ex.isRunning, 0)
	ex.Logger.Info("Stopping exchange runner")
}
