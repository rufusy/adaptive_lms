<?php

namespace app\models;

use yii\base\Model;

class UsersUpload extends Model
{
    public $userGroupId;
    public $usersFile;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['userGroupId'], 'required'],
            [['usersFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'userGroupId' => 'User role',
            'usersFile' => 'Users file'
        ];
    }

}