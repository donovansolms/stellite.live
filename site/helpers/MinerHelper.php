<?php

namespace app\helpers;

use app\models\BtcUsd;
use app\models\Blocks;
use app\models\Prices;
use app\models\TradingRecords;


/**
 * MinerHelper contains
 */
class MinerHelper
{
  /**
   * GetStats returns the network stats as logged
   */
  function GetStats($hashrate = false) {
    $block_sum = Blocks::find()
      ->select('SUM(reward) as circulation')
      ->one();
    $stats['circulation'] = $block_sum->circulation;

    $block = Blocks::find()
      ->orderBy('id DESC')
      ->limit(1)
      ->one();
    $stats['last_block'] = $block;
    $stats['difficulty'] = number_format($block->difficulty, 0, '.', ' ');
    $stats['height'] = number_format($block->height, 0, '.', ' ');

    $stats['hashrate'] = $this->HumanizeHashrate($block->difficulty/60);

    $tradeogre = Prices::find()
      ->where(['exchange' => 'TradeOgre'])
      ->orderBy('id DESC')
      ->limit(1)
      ->one();
    $crex = Prices::find()
      ->where(['exchange' => 'Crex24'])
      ->orderBy('id DESC')
      ->limit(1)
      ->one();
    $stex = Prices::find()
      ->where(['exchange' => 'Stex'])
      ->orderBy('id DESC')
      ->limit(1)
      ->one();

    $stats['volume_crex'] = number_format($crex->volume, 8, '.', ' ');
    $stats['volume_tradeogre'] = number_format($tradeogre->volume, 8, '.', ' ');
    $stats['volume_stex'] = number_format($stex->volume, 8, '.', ' ');
    $stats['volume'] = $tradeogre->volume + $crex->volume + $stex->volume;
    $stats['volume'] = number_format($stats['volume'], 8, '.', ' ');
    $price = $tradeogre->last;
    if ($crex->last > $price)
    {
      $price = $crex->last;
    }
    if ($stex->last > $price)
    {
      $price = $stex->last;
    }
    $stats['price'] = $price;

    $btc_price = BtcUsd::find()
      ->orderBy('id DESC')
      ->limit(1)
      ->one();
    $stats['market_cap'] = ($stats['circulation'] * $stats['price']) * $btc_price->usd;
    if ($stats['market_cap'] > 1000000) {
      $stats['market_cap'] = 'USD '. number_format(($stats['market_cap'] / 1000000.00), 2, '.', ' ') . ' million';
    } else {
      $stats['market_cap'] = 'USD '. number_format(['market_cap'], 2, '.', ' ');
    }
    if ($stats['circulation'] > 1000000000) {
      $stats['circulation'] = number_format(($stats['circulation'] / 1000000000.00), 2, '.', ' ') . ' billion';
    }

    $record_volume = TradingRecords::find()
      ->where(['name' => 'volume'])
      ->limit(1)
      ->one();
    $record_price = TradingRecords::find()
      ->where(['name' => 'price_high'])
      ->limit(1)
      ->one();
    $stats['records'] = [
      'price' => $record_price->value,
      'volume' => $record_volume->value,
    ];

    if ($hashrate !== false) {
      // calculate the hashrate to XTL / day
      //var xtlPerDay = (hashrate * 86400 / 1205230299) * 17878.36;
      //$('#payout').html(xtlPerDay.toFixed(2) + ' XTL');
      $xtlPerDay = ($hashrate * 86400 / $block->difficulty) * $block->reward;
      $stats['xtl_per_day'] = number_format($xtlPerDay, 2, '.', ' ');
    }
    return $stats;
  }

  /**
   * HumanizePoolStats updates some pool stats to be readable by humans
   * @param models\Pools $pool The pool database model to humanize
   */
  function HumanizePoolStats($pool) {
    $pool->hashrate = $this->HumanizeHashrate($pool->hashrate);
    $pool->miners = number_format($pool->miners, 0, '.', ' ');
    $now = strtotime(date('Y-m-d H:i:s'));
    $block_time = strtotime($pool->last_block);
    $pool->last_block = round(abs($now - $block_time) / 60,0);
    if ($pool->last_block != 1) {
      if ($pool->last_block > 60) {
        $pool->last_block = round($pool->last_block / 60,0);
        if ($pool->last_block != 1) {
           $pool->last_block .= ' hours';
        } else $pool->last_block .= ' hour';
      }
      else $pool->last_block .= ' minutes';
    } else $pool->last_block .= ' minute';
    $pool->last_block .= ' ago';
    return $pool;
  }

  /**
   * HumanizeHashrate takes an integer hashrate and returns the KH or MH
   * equivalent
   * @param int $hashrate The hashrate to convert
   */
  function HumanizeHashrate($hashrate) {

    if ($hashrate > 1000000) {
      return number_format($hashrate/1000000, 2, '.', ' ') . ' MH/s';
    } else if ($hashrate > 1000) {
      return number_format($hashrate/1000, 2, '.', ' ') . ' KH/s';
    }
    return number_format($hashrate, 0, '.', ' ') . ' H/s';
  }
}
