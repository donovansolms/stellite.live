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
use app\helpers\MinerHelper;
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

      $showall = \Yii::$app->request->get('all');
      $limit = 3;
      if ($showall == 'true') {
        $limit = -1;
      }

      $pools = Pools::find()
        ->where('display_in_miner = 1 AND rank > 0')
        ->orderBy('rank ASC')
        ->limit($limit)
        ->all();

      $helper = new MinerHelper();
      foreach ($pools as $pool) {
        $pool = $helper->HumanizePoolStats($pool);
      }
      return $pools;
    }

    public function actionPool($id) {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $pool = Pools::find()
        ->where(['id' => $id])
        ->one();
      $helper = new MinerHelper();
      $pool = $helper->HumanizePoolStats($pool);
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

      $helper = new MinerHelper();
      $stats = $helper->GetStats($hashrate);

      // Get pool stats
      $pool = Pools::find()
        ->where(['id' => $pool])
        ->one();
      $stats['pool'] = $helper->HumanizePoolStats($pool);

      return $stats;
    }

}
