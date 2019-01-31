package connectors

import (
	"encoding/json"
	"net/http"
	"strconv"
	"strings"
)

// StexTicker implements the Stex ticker response format
type StexTicker struct {
	Ask            interface{} `json:"ask"`
	Bid            interface{} `json:"bid"`
	Vol            interface{} `json:"vol"`
	VolMarket      interface{} `json:"vol_market"`
	Last           interface{} `json:"last"`
	LastDayAgo     interface{} `json:"lastDayAgo"`
	Spread         interface{} `json:"spread"`
	MinOrderAmount interface{} `json:"min_order_amount"`
	BuyFeePercent  interface{} `json:"buy_fee_percent"`
	SellFeePercent interface{} `json:"sell_fee_percent"`
	MarketName     interface{} `json:"market_name"`
	Currency1Name  interface{} `json:"currency1_name"`
	Currency2Name  interface{} `json:"currency2_name"`
	MarketID       int         `json:"market_id"`
	UpdatedTime    int         `json:"updated_time"`
	ServerTime     int         `json:"server_time"`
	Active         string      `json:"active"`
}

// Stex retrieves trade information from https://stex.com/
type Stex struct {
	Endpoint string
	Base     string
	Alt      string
}

// GetName returns the name of the exchange
func (exchange *Stex) GetName() string {
	return "Stex"
}

// GetTicker fetches the latest trade information for the exchange and
// trading pair
func (exchange *Stex) GetTicker() (Ticker, error) {
	var ticker Ticker

	response, err := http.Get(exchange.Endpoint)
	if err != nil {
		return ticker, err
	}

	var stexTickers []StexTicker
	err = json.NewDecoder(response.Body).Decode(&stexTickers)
	if err != nil {
		return ticker, err
	}

	for _, stexTicker := range stexTickers {
		if strings.ToLower(stexTicker.MarketName.(string)) == "xtl_btc" {

			switch v := stexTicker.Last.(type) {
			case float64:
				ticker.Last = v
			case string:
				ticker.Last, err = strconv.ParseFloat(stexTicker.Last.(string), 64)
				if err != nil {
					return ticker, nil
				}
			}

			switch v := stexTicker.Ask.(type) {
			case float64:
				ticker.High = v
			case string:
				ticker.High, err = strconv.ParseFloat(stexTicker.Ask.(string), 64)
				if err != nil {
					return ticker, nil
				}
			}

			switch v := stexTicker.Bid.(type) {
			case float64:
				ticker.Low = v
			case string:
				ticker.Low, err = strconv.ParseFloat(stexTicker.Bid.(string), 64)
				if err != nil {
					return ticker, nil
				}
			}

			switch v := stexTicker.VolMarket.(type) {
			case float64:
				ticker.VolumeBTC = v
			case string:
				ticker.VolumeBTC, err = strconv.ParseFloat(stexTicker.VolMarket.(string), 64)
				if err != nil {
					return ticker, nil
				}
			}

			break
		}
	}

	return ticker, nil
}
