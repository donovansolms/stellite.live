package connectors

import (
	"encoding/json"
	"fmt"
	"net/http"
	"strconv"
)

type TradeOgreTicker struct {
	InitialPrice string `json:"initialprice"`
	Price        string `json:"price"`
	High         string `json:"high"`
	Low          string `json:"low"`
	Volume       string `json:"volume"`
}

type TradeOgre struct {
	Endpoint string
}

func (exchange *TradeOgre) GetName() string {
	return "TradeOgre"
}

// GetTicker ...
func (exchange *TradeOgre) GetTicker() (Ticker, error) {
	var ticker Ticker

	response, err := http.Get(fmt.Sprintf(exchange.Endpoint, "BTC", "XTL"))
	if err != nil {
		return ticker, err
	}

	var tradeOgreTicker TradeOgreTicker
	err = json.NewDecoder(response.Body).Decode(&tradeOgreTicker)
	if err != nil {
		return ticker, err
	}

	ticker.Last, err = strconv.ParseFloat(tradeOgreTicker.Price, 64)
	if err != nil {
		return ticker, nil
	}

	ticker.High, err = strconv.ParseFloat(tradeOgreTicker.High, 64)
	if err != nil {
		return ticker, nil
	}

	ticker.Low, err = strconv.ParseFloat(tradeOgreTicker.Low, 64)
	if err != nil {
		return ticker, nil
	}

	ticker.VolumeBTC, err = strconv.ParseFloat(tradeOgreTicker.Volume, 64)
	if err != nil {
		return ticker, nil
	}

	return ticker, nil
}
