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
      <script async src="https://www.googletagmanager.com/gtag/js?id=UA-26768009-5"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-26768009-5');
      </script>

      <meta charset="<?= Yii::$app->charset ?>">
      <meta http-equiv="X-UA-Compatible" content="IE=edge"></meta>
      <meta name="mobile-web-app-capable" content="yes">
      <link rel="manifest" href="/manifest.json">
      <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
      <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
      <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
      <link rel="icon" type="image/png" href="/android-icon-144x144.png" sizes="128x128">
      <link rel="icon" type="image/png" href="/android-icon-144x144.png" sizes="144x144">
      <link rel="icon" type="image/png" href="/android-icon-192x192.png" sizes="192x192">
      <link rel="icon" type="image/png" href="/android-icon-192x192.png" sizes="196x196">
      <link rel="apple-touch-icon" href="/apple-icon-120x120.png">
      <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
      <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
      <link rel="apple-touch-icon" sizes="167x167" href="/apple-icon-152x152.png">
      <meta name="theme-color" content="#06071D"></meta>
      <meta name="viewport" content="width=device-width, initial-scale=1"></meta>
      <meta property="og:title" content="Stellite LIVE"></meta>
      <meta property="og:type" content="website"></meta>
      <meta property="og:site_name" content="Stellite LIVE"></meta>
      <meta property="og:url" content="https://www.stellite.live"></meta>
      <meta property="og:image" content="https://www.stellite.live/i/logo-large.png"></meta>
      <meta property="og:image:type" content="image/png"></meta>
      <meta property="og:image:alt" content="Stellite LIVE logo"></meta>
      <meta property="og:image:width" content="332"></meta>
      <meta property="og:image:height" content="331"></meta>
      <meta property="og:description" content="Real-time community driven information about the Stellite cryptocurrency"></meta>
      <meta name="twitter:card" content="summary"></meta>
      <meta name="twitter:site" content="@donovansolms"></meta>
      <meta name="twitter:creator" content="@donovansolms"></meta>
      <meta name="twitter:title" content="Stellite LIVE"></meta>
      <meta name="twitter:description" content="Real-time community driven information about the Stellite cryptocurrency"></meta>
      <meta name="twitter:image" content="https://www.stellite.live/i/logo-large.png"></meta>
      <?= Html::csrfMetaTags() ?>
      <title>Stellite LIVE - Real-time community driven information about the Stellite cryptocurrency</title>
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
