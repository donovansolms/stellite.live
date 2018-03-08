package models

import "time"

// Price ...
type Price struct {
	Exchange     string
	Volume       float64
	High         float64
	Low          float64
	Last         float64
	DateCaptured time.Time
}
