<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "announcements".
 *
 * @property int $id
 * @property string $text
 * @property string $link
 * @property string $date_created
 * @property int $active
 */
class Announcements extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'announcements';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text', 'link', 'date_created'], 'required'],
            [['date_created'], 'safe'],
            [['text'], 'string', 'max' => 128],
            [['link'], 'string', 'max' => 255],
            [['active'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Text',
            'link' => 'Link',
            'date_created' => 'Date Created',
            'active' => 'Active',
        ];
    }
}
