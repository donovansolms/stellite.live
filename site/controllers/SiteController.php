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
    if (\Yii::$app->request->isAjax) {
      \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return $this->getStats();
    }

    $pools = Pools::find()
      ->where('rank > 0 AND is_enabled = 1')
      ->orderBy('rank ASC')
      ->all();

    $helper = new MinerHelper();

    foreach ($pools as $pool) {
      $pool = $helper->HumanizePoolStats($pool);
    }
    return $this->render('index', [
      'stats' => $helper->GetStats(),
      'pools' => $pools,
    ]);
  }
}
