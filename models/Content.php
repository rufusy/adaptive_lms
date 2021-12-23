<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "content".
 *
 * @property int $id
 * @property int $courseId
 * @property string $url
 * @property int|null $description
 * @property int|null $type
 * @property int|null $createdBy
 * @property int|null $updateBy
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property Course $course
 * @property User $Creator
 * @property Characteristic $characteristic
 */
class Content extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['courseId', 'url'], 'required'],
            [['courseId', 'type', 'createdBy', 'updateBy'], 'integer'],
            [['url', 'description'], 'string'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['courseId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class,
                'targetAttribute' => ['courseId' => 'id']],
            [['createdBy'], 'exist', 'skipOnError' => true, 'targetClass' => User::class,
                'targetAttribute' => ['createdBy' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => Characteristic::class,
                'targetAttribute' => ['type' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'courseId' => 'Course ID',
            'url' => 'Url',
            'description' => 'Description',
            'type' => 'Type',
            'createdBy' => 'Created By',
            'updateBy' => 'Update By',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if($insert){
            $this->createdAt = new Expression('CURRENT_DATE');
        }
        $this->updatedAt = new Expression('CURRENT_DATE');

        return true;
    }

    /**
     * Gets query for [[Course]].
     *
     * @return ActiveQuery
     */
    public function getCourse(): ActiveQuery
    {
        return $this->hasOne(Course::class, ['id' => 'courseId']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return ActiveQuery
     */
    public function getCreator(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'createdBy']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return ActiveQuery
     */
    public function getCharacteristic(): ActiveQuery
    {
        return $this->hasOne(Characteristic::class, ['id' => 'type']);
    }
}
