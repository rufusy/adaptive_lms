<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "characteristics".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $pairedWith
 *
 * @property Content[] $contents
 * @property Characteristic $pairedCharacteristic
 * @property Characteristic[] $studentCharacteristics
 */
class Characteristic extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'characteristic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'level'], 'required'],
            [['pairedWith', 'level'], 'integer'],
            [['name', 'description'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['pairedWith'], 'exist', 'skipOnError' => true, 'targetClass' => Characteristic::class,
                'targetAttribute' => ['pairedWith' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'pairedWith' => 'Paired With',
            'level' => 'Level',
        ];
    }

    /**
     * Gets query for [[Contents]].
     *
     * @return ActiveQuery
     */
    public function getContents(): ActiveQuery
    {
        return $this->hasMany(Content::class, ['type' => 'id']);
    }

    /**
     * Gets query for [[PairedCharacteristic]].
     *
     * @return ActiveQuery
     */
    public function getPairedCharacteristic(): ActiveQuery
    {
        return $this->hasOne(Characteristic::class, ['id' => 'pairedWith']);
    }

    /**
     * Gets query for [[StudentCharacteristic]].
     *
     * @return ActiveQuery
     */
    public function getStudentCharacteristics(): ActiveQuery
    {
        return $this->hasMany(StudentCharacteristic::class, ['characteristicId' => 'id']);
    }
}
