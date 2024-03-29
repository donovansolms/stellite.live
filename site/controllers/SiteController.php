<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Pools;
use app\helpers\MinerHelper;

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
    $helper = new MinerHelper();
    if (\Yii::$app->request->isAjax) {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return $helper->GetStats();;
    }

    $pools = Pools::find()
      ->where('rank > 0 AND is_enabled = 1')
      ->orderBy('rank ASC')
      ->all();

    $poolHashrate = 0.00;
    foreach ($pools as $pool) {
      if ($pool->id != 31)
      {
        // Skip the official pool's hashrate since it is the same as cryptopool.space's
        $poolHashrate += $pool->hashrate;
      }
      $pool = $helper->HumanizePoolStats($pool);
    }
    return $this->render('index', [
      'stats' => $helper->GetStats(),
      'pools' => $pools,
      'poolHashrate' => $helper->HumanizeHashrate($poolHashrate),
    ]);
  }
}
