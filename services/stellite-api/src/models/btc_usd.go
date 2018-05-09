package models

import "time"

// BtcUsd price
type BtcUsd struct {
	ID          uint16
	Usd         float64
	DateUpdated time.Time
}

// TableName returns the name of the table because it is different from
// the auto generated name
func (model *BtcUsd) TableName() string {
	return "btc_usd"
}
