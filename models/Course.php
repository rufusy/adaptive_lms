<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "courses".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 *
 * @property Content[] $contents
 */
class Course extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'courses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['code', 'name'], 'required'],
            [['code'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 256],
            [['code'], 'unique'],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[Contents]].
     *
     * @return ActiveQuery
     */
    public function getContents(): ActiveQuery
    {
        return $this->hasMany(Content::class, ['courseId' => 'id']);
    }
}
