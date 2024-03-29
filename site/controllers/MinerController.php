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
use app\models\Announcements;

class MinerController extends Controller
{
    public $enableCsrfValidation = false;
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

    public function behaviors() {
      return array_merge(parent::behaviors(), [
        // For cross-domain AJAX request
        'corsFilter'  => [
          'class' => \yii\filters\Cors::className(),
          'cors'  => [
            // restrict access to domains:
            'Origin'                           => ["*"],
            'Access-Control-Request-Method'    => ['OPTIONS', 'GET', 'POST'],
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Max-Age'           => 3600,
          ],
        ],
      ]);
    }


    /**
     * PoolList returns the top 3 pools as ranked in the database.
     * If ?all=true is provided, all pools are returned
     * @return string JSON array of pools
     */
    public function actionPoolList() {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

      $showall = \Yii::$app->request->get('all');
      $allowed = \Yii::$app->request->get('allowed');

      if ($showall == 'true') {
        $pools = Pools::find()
          ->where('display_in_miner = 1 AND rank > 0')
          ->orderBy('rank ASC')
          ->all();
      }
      else if ($allowed == 'true') {
        $pools = Pools::find()
          ->where('display_in_miner = 1 AND rank > 0 AND hashrate < 1000000')
          ->orderBy('rank ASC')
          ->all();
      }
      else
      {
        $pools = Pools::find()
          ->where('display_in_miner = 1 AND rank > 0')
          ->orderBy('rank ASC')
          ->limit(3)
          ->all();
      }

      $helper = new MinerHelper();
      foreach ($pools as $pool) {
        $pool = $helper->HumanizePoolStats($pool);
      }
      return $pools;
    }

    /**
     * Pool returns a specific pool's information and basic config
     * @param  int $id The pool's ID
     * @return string JSON of the pool
     */
    public function actionPool($id) {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $pool = Pools::find()
        ->where(['id' => $id])
        ->one();
      $helper = new MinerHelper();
      $pool = $helper->HumanizePoolStats($pool);
      return $pool;
    }

    /**
     * Stats returns the current network stats together with daily
     * earnings if hashrate is provided
     * @return string JSON stats
     */
    public function actionStats() {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

      $mid = \Yii::$app->request->get('mid');
      $hashrate = \Yii::$app->request->get('hr');
      $pool = \Yii::$app->request->get('pool');

      if (isset($mid))
      {
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
      }
      $helper = new MinerHelper();
      $stats = $helper->GetStats($hashrate);

      if (isset($pool))
      {
        // Get pool stats
        $pool = Pools::find()
          ->where(['id' => $pool])
          ->one();
        $stats['pool'] = $helper->HumanizePoolStats($pool);
      }

      return $stats;
    }

    /**
     * Announcement returns an object containing a timestamp and link
     * to the announcement
     * @return string JSON representing the announement
     */
    public function actionAnnouncement() {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $ann = Announcements::find()
      ->where('active = 1')
      ->orderBy('id DESC')
      ->one();

      if (isset($ann)) {
        return [
          'id' => $ann->id,
          'text' => $ann->text,
          'link' => $ann->link,
          'date' => date('Y-m-d H:i:s', strtotime($ann->date_created)),
          'ann' => true,
        ];
      }
      return [
        'ann' => false,
      ];
    }

}
