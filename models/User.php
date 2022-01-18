<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property int|null $userGroupId
 * @property int|null $cluster
 *
 * @property Content[] $content
 * @property Characteristic[] $Characteristics
 * @property UserGroup $userGroup
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            [['userGroupId', 'cluster'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 256],
            [['username'], 'unique'],
            [['userGroupId'], 'exist', 'skipOnError' => true, 'targetClass' => UserGroup::class, 'targetAttribute' =>
                ['userGroupId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'userGroupId' => 'User Group ID',
            'cluster' => 'Cluster'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null){}

    /**
     * Finds user by username
     *
     * @param string $username
     * @return ActiveRecord|array|null
     */
    public static function findByUsername(string $username): ?User
    {
        return self::find()->where(['username' => $username])->one();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(){}

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey){}

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        if (Yii::$app->getSecurity()->validatePassword($password, $this->password)) {
            return true;
        } else{
            return false;
        }
    }

    /**
     * Gets query for [[Content]].
     *
     * @return ActiveQuery
     */
    public function getContent(): ActiveQuery
    {
        return $this->hasMany(Content::class, ['createdBy' => 'id']);
    }

    /**
     * Gets query for [[Characteristic]].
     *
     * @return ActiveQuery
     */
    public function getCharacteristics(): ActiveQuery
    {
        return $this->hasMany(Characteristic::class, ['studentId' => 'id']);
    }

    /**
     * Gets query for [[UserGroup]].
     *
     * @return ActiveQuery
     */
    public function getUserGroup(): ActiveQuery
    {
        return $this->hasOne(UserGroup::class, ['id' => 'userGroupId']);
    }
}
