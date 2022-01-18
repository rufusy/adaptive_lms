<?php

namespace app\models;

use yii\base\Model;

class ClusterUpload extends Model
{
    public $clusterFile;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['clusterFile'], 'required'],
            [['clusterFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'clusterFile' => 'Student clusters file'
        ];
    }
}