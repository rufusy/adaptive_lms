<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "student_characteristics".
 *
 * @property int $id
 * @property int $studentId
 * @property int $characteristicId
 * @property float $value
 *
 * @property Characteristic $characteristic
 * @property User $student
 */
class StudentCharacteristic extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'student_characteristics';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['studentId', 'characteristicId', 'value'], 'required'],
            [['studentId', 'characteristicId'], 'integer'],
            [['value'], 'number'],
            [['studentId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class,
                'targetAttribute' => ['studentId' => 'id']],
            [['characteristicId'], 'exist', 'skipOnError' => true, 'targetClass' => Characteristic::class,
                'targetAttribute' => ['characteristicId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'studentId' => 'Student ID',
            'characteristicId' => 'Characteristic ID',
            'value' => 'Value',
        ];
    }

    /**
     * Gets query for [[Characteristic]].
     *
     * @return ActiveQuery
     */
    public function getCharacteristic(): ActiveQuery
    {
        return $this->hasOne(Characteristic::class, ['id' => 'characteristicId']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return ActiveQuery
     */
    public function getStudent(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'studentId']);
    }
}
