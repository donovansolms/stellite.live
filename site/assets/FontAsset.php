<?php
/**
 * Font and icon assets
 */

namespace app\assets;

use yii\web\AssetBundle;

class FontAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
      '/css/font-awesome.min.css',
      '//fonts.googleapis.com/css?family=Roboto:400,500',
    ];
    public $js = [];
    public $depends = [];
}
