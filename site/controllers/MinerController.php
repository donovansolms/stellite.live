<?php

namespace app\controllers;

use Yii;
use yii\db\Expression;
use yii\web\Controller;
use app\models\Pools;
use app\models\BtcUsd;
use app\models\Blocks;
use app\models\Prices;
use app\models\MinerLog;
use app\models\TradingRecords;

class MinerController extends Controller
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

    public function actionPoolList() {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $pools = Pools::find()
        ->where('display_in_miner = 1 AND rank > 0')
        ->orderBy('rank ASC')
        ->limit(3)
        ->all();

      foreach ($pools as $pool) {
        $pool->hashrate = strval($pool->hashrate);
      }
      return $pools;
    }

    public function actionPool($id) {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $pool = Pools::find()
        ->where(['id' => $id])
        ->one();
      $pool->hashrate = $this->toHumanHashrate($pool->hashrate);
      return $pool;
    }

    public function actionStats() {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


      $mid = \Yii::$app->request->get('mid');
      $hashrate = \Yii::$app->request->get('hr');
      $pool = \Yii::$app->request->get('pool');

      // Save miner log
      $miner = MinerLog::find()
        ->where(['mid' => $mid])
        ->one();
      if (isset($miner) == false)
      {
        $miner = new MinerLog();
      }
      $miner->mid = $mid;
      $miner->pool_id = $pool;
      $miner->hashrate = $hashrate;
      $miner->ip = \Yii::$app->request->userIP;
      $miner->date_updated = new Expression('NOW()');
      // Not checking errors since we don't worry if we can't save the stats
      $miner->save();


      $stats = [];
      // Get pool stats
      $pool = Pools::find()
        ->where(['id' => $pool])
        ->one();
      $stats['pool'] = $pool;
      $stats['pool']->hashrate = strval($pool->hashrate);

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
      $stats['xtl_per_day'] = number_format($xtlPerDay, 2, '.', ' ');

      $stats['hashrate'] = $this->toHumanHashrate($block->difficulty/60);
      $block->difficulty = number_format($block->difficulty, 0, '.', ' ');
      $block->height = number_format($block->height, 0, '.', ' ');

      return $stats;
    }

    function toHumanHashrate($hashrate) {

      if ($hashrate > 1000000) {
        return number_format($hashrate/1000000, 2, '.', ' ') . ' MH/s';
      } else if ($hashrate > 1000) {
        return number_format($hashrate/1000, 2, '.', ' ') . ' KH/s';
      }
      return number_format($hashrate, 0, '.', ' ') . ' H/s';
    }

}
