<?php

namespace app\controllers;

use Yii;
use yii\db\Expression;
use yii\web\Controller;
use app\models\Pools;
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
      return $pools;
    }

    public function actionPool($id) {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $pool = Pools::find()
        ->where(['id' => $id])
        ->one();
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

      $stats['volume'] = $tradeogre->volume + $crex->volume;
      $price = $tradeogre->last;
      if ($crex->last > $price)
      {
        $price = $crex->last;
      }
      $stats['price'] = $price;

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

      return $stats;
    }

}
