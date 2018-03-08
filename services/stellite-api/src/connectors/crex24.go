package connectors

import (
	"encoding/json"
	"errors"
	"fmt"
	"net/http"
)

type Crex24Ticker struct {
	Error   interface{} `json:"Error"`
	Tickers []struct {
		PairID        int     `json:"PairId"`
		PairName      string  `json:"PairName"`
		Last          float64 `json:"Last"`
		LowPrice      float64 `json:"LowPrice"`
		HighPrice     float64 `json:"HighPrice"`
		PercentChange float64 `json:"PercentChange"`
		BaseVolume    float64 `json:"BaseVolume"`
		QuoteVolume   float64 `json:"QuoteVolume"`
	} `json:"Tickers"`
}

type Crex24 struct {
	Endpoint string
}

func (exchange *Crex24) GetName() string {
	return "Crex24"
}

// GetTicker ...
func (exchange *Crex24) GetTicker() (Ticker, error) {
	var ticker Ticker

	response, err := http.Get(fmt.Sprintf(exchange.Endpoint, "BTC", "XTL"))
	if err != nil {
		return ticker, err
	}

	var crexTicker Crex24Ticker
	err = json.NewDecoder(response.Body).Decode(&crexTicker)
	if err != nil {
		return ticker, err
	}

	if len(crexTicker.Tickers) == 0 {
		return ticker, errors.New("No ticker information returned")
	}
	data := crexTicker.Tickers[0]

	ticker.Last = data.Last
	ticker.High = data.HighPrice
	ticker.Low = data.LowPrice
	ticker.VolumeBTC = data.BaseVolume

	return ticker, nil
}
