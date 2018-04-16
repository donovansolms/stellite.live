<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "btc_usd".
 *
 * @property int $id
 * @property string $usd
 * @property string $date_updated
 */
class BtcUsd extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'btc_usd';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usd', 'date_updated'], 'required'],
            [['usd'], 'number'],
            [['date_updated'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usd' => 'Usd',
            'date_updated' => 'Date Updated',
        ];
    }
}
