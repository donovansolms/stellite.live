package models

import "time"

// TradingRecord model
type TradingRecord struct {
	ID    int16
	Name  string
	Value float64
	Date  time.Time
}
