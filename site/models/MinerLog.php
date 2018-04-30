<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "miner_log".
 *
 * @property int $id
 * @property string $mid
 * @property int $pool_id
 * @property int $hashrate
 * @property string $date_updated
 */
class MinerLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'miner_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mid', 'pool_id', 'hashrate', 'date_updated'], 'required'],
            [['pool_id'], 'integer'],
            [['hashrate'], 'number'],
            [['date_updated'], 'safe'],
            [['mid'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mid' => 'Mid',
            'pool_id' => 'Pool ID',
            'hashrate' => 'Hashrate',
            'ip' => 'Ip',
            'date_updated' => 'Date Updated',
        ];
    }
}
