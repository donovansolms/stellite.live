<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Pools;
use app\models\BtcUsd;
use app\models\Blocks;
use app\models\Prices;
use app\models\TradingRecords;

class SiteController extends Controller
{
  /**
  * {@inheritdoc}
  */
  public function actions()
  {
    return [
      'error' => [
        'class' => 'yii\web\ErrorAction',
      ],
    ];
  }

  /**
  * Displays homepage.
  *
  * @return string
  */
  public function actionIndex()
  {
    if (\Yii::$app->request->isAjax) {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return $this->getStats();
    }

    $pools = Pools::find()
      ->where('rank > 0')
      ->orderBy('rank ASC')
      ->all();

    foreach ($pools as $pool) {
      $pool->hashrate = $this->toHumanHashrate($pool->hashrate);
      $pool->miners = number_format($pool->miners, 0, '.', ' ');
    }
    return $this->render('index', [
      'stats' => $this->getStats(),
      'pools' => $pools,
    ]);
  }


  function toHumanHashrate($hashrate) {

    if ($hashrate > 1000000) {
      return number_format($hashrate/1000000, 2, '.', ' ') . ' MH/s';
    } else if ($hashrate > 1000) {
      return number_format($hashrate/1000, 2, '.', ' ') . ' KH/s';
    }
    return number_format($hashrate, 0, '.', ' ') . ' H/s';
  }

  function getStats() {
    $block_sum = Blocks::find()
      ->select('SUM(reward) as circulation')
      ->one();
    $circulation = $block_sum->circulation;
    $stats['circulation'] = $circulation;

    $block = Blocks::find()
      ->orderBy('id DESC')
      ->one();
    $stats['last_block'] = $block;


    $tradeogre = Prices::find()
      ->where(['exchange' => 'TradeOgre'])
      ->orderBy('id DESC')
      ->one();
    $crex = Prices::find()
      ->where(['exchange' => 'Crex24'])
      ->orderBy('id DESC')
      ->one();

    $stats['volume_crex'] = number_format($crex->volume, 8, '.', ' ');
    $stats['volume_tradeogre'] = number_format($tradeogre->volume, 8, '.', ' ');
    $stats['volume'] = $tradeogre->volume + $crex->volume;
    $stats['volume'] = number_format($stats['volume'], 8, '.', ' ');
    $price = $tradeogre->last;
    if ($crex->last > $price)
    {
      $price = $crex->last;
    }
    $stats['price'] = $price;

    $btc_price = BtcUsd::find()
      ->orderBy('id DESC')
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
      ->one();
    $record_price = TradingRecords::find()
      ->where(['name' => 'price_high'])
      ->one();
    $stats['records'] = [
      'price' => $record_price->value,
      'volume' => $record_volume->value,
    ];

    // calculate the hashrate to XTL / day
    //var xtlPerDay = (hashrate * 86400 / 1205230299) * 17878.36;
    //$('#payout').html(xtlPerDay.toFixed(2) + ' XTL');
    $xtlPerDay = ($hashrate * 86400 / $block->difficulty) * $block->reward;
    $stats['hashrate'] = $this->toHumanHashrate($block->difficulty/60);
    $block->difficulty = number_format($block->difficulty, 0, '.', ' ');
    $block->height = number_format($block->height, 0, '.', ' ');
    $stats['difficulty'] = $block->difficulty;
    $stats['height'] = $block->height;
    return $stats;
  }
}
