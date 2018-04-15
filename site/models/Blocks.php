<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "blocks".
 *
 * @property int $id
 * @property int $height
 * @property int $difficulty
 * @property int $tx_count
 * @property string $reward
 * @property string $timestamp
 */
class Blocks extends \yii\db\ActiveRecord
{
    public $circulation;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blocks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['height', 'difficulty', 'reward', 'timestamp'], 'required'],
            [['height', 'difficulty', 'tx_count'], 'integer'],
            [['reward'], 'number'],
            [['timestamp'], 'safe'],
            [['height'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'height' => 'Height',
            'difficulty' => 'Difficulty',
            'tx_count' => 'Tx Count',
            'reward' => 'Reward',
            'timestamp' => 'Timestamp',
        ];
    }
}
