<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trading_records".
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property string $date
 */
class TradingRecords extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trading_records';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value', 'date'], 'required'],
            [['value'], 'number'],
            [['date'], 'safe'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value' => 'Value',
            'date' => 'Date',
        ];
    }
}
