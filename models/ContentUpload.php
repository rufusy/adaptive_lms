<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\FileHelper;

/**
 * @property array $contentFiles
 */
class ContentUpload extends Model
{
    public $contentFiles;

    /**
     * {@inheritdoc}
     *
     * Allow all file types
     * Limit file size to 10Mb
     * A maximum of 5 files at a time
     */
    public function rules(): array
    {
        return [
            [['contentFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 5, 'maxSize' => 10485760,
                'mimeTypes' => [
                    'image/*',
                    'application/*',
                    'text/*',
                    'audio/*',
                    'video/*',
                    'multipart/*'
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'contentFiles' => 'File(s)'
        ];
    }

    /**
     * @param int $contentId
     * @param bool $update
     * @return bool
     * @throws Exception
     */
    public function upload(int $contentId, bool $update = false): bool
    {
        if ($this->validate()) {
            $path = Yii::getAlias('@webroot') . '/uploads/content/' . $contentId;
            FileHelper::createDirectory($path);
            foreach ($this->contentFiles as $file) {
                $fileName = preg_replace('/\s/','_', $file->baseName) . '.' . $file->extension;
                if($update){
                    if(!in_array($fileName, scandir($path))){
                        $file->saveAs($path . '/' . $fileName, true);
                    }
                }else{
                    $file->saveAs($path . '/' . $fileName, true);
                }
            }
            return true;
        } else {
            return false;
        }
    }
}