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
      <title>XTL Sync check</title>
      <?php $this->head() ?>
    </head>
    <body>
      <?php $this->beginBody() ?>
      <!--nav class="side-menu" id="menu">
        <ul>
          <li>
            <h2>Menu</h2>
          </li>
          <li>
            <a href="#">Home</a>
          </li>
        </ul>
      </nav>
      <main id="panel">
        <header>
          <a href="#" class="menu-toggle">
            <i class="fa fa-bars"></i>
            <img src="/i/logos/goderma-white-red-small.png"/>
          </a>
        </header-->
        <div class="content">
          <?= $content ?>
        </div>
      <!--/main-->
      <?php $this->endBody() ?>
    </body>
  </html>
<?php $this->endPage() ?>
