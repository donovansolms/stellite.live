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
      '//fonts.googleapis.com/css?family=Poppins:400,600',
    ];
    public $js = [];
    public $depends = [];
}
