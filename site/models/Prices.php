<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prices".
 *
 * @property int $id
 * @property string $exchange
 * @property string $volume
 * @property string $high
 * @property string $low
 * @property string $last
 * @property string $date_captured
 *
 * @property Exchanges $exchange0
 */
class Prices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'prices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exchange', 'volume', 'high', 'low', 'last', 'date_captured'], 'required'],
            [['volume', 'high', 'low', 'last'], 'number'],
            [['date_captured'], 'safe'],
            [['exchange'], 'string', 'max' => 32],
            [['exchange'], 'exist', 'skipOnError' => true, 'targetClass' => Exchanges::className(), 'targetAttribute' => ['exchange' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'exchange' => 'Exchange',
            'volume' => 'Volume',
            'high' => 'High',
            'low' => 'Low',
            'last' => 'Last',
            'date_captured' => 'Date Captured',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExchange0()
    {
        return $this->hasOne(Exchanges::className(), ['name' => 'exchange']);
    }
}
