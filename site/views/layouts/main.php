<?php

  /* @var $this \yii\web\View */
  /* @var $content string */

  use yii\helpers\Html;
  use app\assets\AppAsset;
  use app\assets\FontAsset;

  AppAsset::register($this);
  FontAsset::register($this);
?>
<?php $this->beginPage() ?>
  <!DOCTYPE html>
  <html lang="<?= Yii::$app->language ?>">
    <head>
      <meta charset="<?= Yii::$app->charset ?>">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <?= Html::csrfMetaTags() ?>
      <title>Stellite LIVE - Community driven real-time information about the Stellite cryptocurrency</title>
      <?php $this->head() ?>
    </head>
    <body>
      <?php $this->beginBody() ?>
      <div class="content">
        <?= $content ?>
      </div>
      <?php $this->endBody() ?>
    </body>
  </html>
<?php $this->endPage() ?>
