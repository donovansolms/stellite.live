<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pools".
 *
 * @property int $id
 * @property int $rank
 * @property string $api_type
 * @property string $name
 * @property string $url
 * @property string $endpoint
 * @property int $hashrate
 * @property int $miners
 * @property string $last_block
 * @property int $is_enabled
 * @property int $display_in_miner
 * @property string $last_update
 */
class Pools extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pools';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rank', 'api_type', 'name', 'url', 'endpoint', 'hashrate', 'miners', 'last_block', 'last_update'], 'required'],
            [['rank', 'hashrate', 'miners'], 'integer'],
            [['last_block', 'last_update'], 'safe'],
            [['api_type', 'name'], 'string', 'max' => 64],
            [['url', 'endpoint'], 'string', 'max' => 128],
            [['is_enabled', 'display_in_miner'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rank' => 'Rank',
            'api_type' => 'Api Type',
            'name' => 'Name',
            'url' => 'Url',
            'endpoint' => 'Endpoint',
            'hashrate' => 'Hashrate',
            'miners' => 'Miners',
            'last_block' => 'Last Block',
            'is_enabled' => 'Is Enabled',
            'display_in_miner' => 'Display In Miner',
            'last_update' => 'Last Update',
        ];
    }
}
