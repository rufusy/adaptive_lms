<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\controllers;

use app\models\Characteristic;
use app\models\Content;
use app\models\ContentUpload;
use app\models\Course;
use app\models\search\ContentSearch;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class ContentController extends BaseController
{
    /**
     * Configure controller behaviours
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * List content
     * @param string|null $id
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionIndex(string $id = null): string
    {
        try{
            $contentSearchModel = new ContentSearch();
            $contentDataProvider = $contentSearchModel->search(Yii::$app->request->queryParams,
                ['id' => $id]);
            return $this->render('index', [
                'title'=> $this->createPageTitle('content'),
                'contentSearchModel' => $contentSearchModel,
                'contentDataProvider' => $contentDataProvider,
                'listOfCharacteristics' => $this->getCharacteristics()
            ]);
        }catch(Exception $ex){
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * Display page to create new content
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionCreate(): string
    {
        try{
            return $this->render('createContent', [
                'title'=> $this->createPageTitle('New content'),
                'content' => new Content(),
                'contentUpload' => new ContentUpload(),
                'listOfCharacteristics' => $this->getCharacteristics(),
                'listOfCourses' => $this->getCourses()
            ]);
        }catch(Exception $ex){
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * Display page to edit content
     * @param string $id
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionEdit(string $id): string
    {
        try{
            if(empty($id)){
                throw new Exception('Content id must be provided.');
            }
            // Get uploaded content files
            $path = Yii::getAlias('@webroot') . '/uploads/content/' . $id . '/';
            $initialPreview = [];
            $initialPreviewConfig = [];
            $docConfig = [];
            $captionNames = '';
            if(is_dir($path)){
                $fileNames = array_diff(scandir($path), ['.', '..']);
                if(!empty($fileNames)){
                    foreach ($fileNames as $fileName){
                        $initialPreview[] = $path . $fileName;
                        $initialPreviewConfig[] = [
                            'key' => $id,
                            'caption' => $fileName,
                            'type' => $this->setPreviewType($fileName),
                            'extra' => ['name' => $fileName]
                        ];
                    }
                    $captionNames = implode(', ', $fileNames);
                }
            }

            $docConfig['initialPreview'] = $initialPreview;
            $docConfig['initialPreviewConfig'] = $initialPreviewConfig;
            $docConfig['initialCaption'] = $captionNames;
            $docConfig['initialPreviewAsData'] = true;
            $docConfig['overwriteInitial'] = false;
            $docConfig['maxFilePreviewSize'] = 0;
            $docConfig['showUpload'] = false;
            $docConfig['browseClass'] = 'btn';
            $docConfig['browseIcon'] = '<i class="fas fa-file"></i>';
            $docConfig['browseLabel'] = 'Select new file(s)';
            $docConfig['showCaption'] = false;
            $docConfig['deleteUrl'] = 'delete-file';

            return $this->render('editContent', [
                'title'=> $this->createPageTitle('Edit content'),
                'content' => $this->getContent($id),
                'listOfCharacteristics' => $this->getCharacteristics(),
                'listOfCourses' => $this->getCourses(),
                'contentUpload' => new ContentUpload(),
                'docConfig' => $docConfig
            ]);
        }catch (Exception $ex){
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * Save new content
     * @return Response
     */
    public function actionStore(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        $content = $this->storeUpdate(new Content(), Yii::$app->request->post());
        try{
            if($content->save()){
                $this->storeUpdateFiles($content);
            }else{
                throw new Exception('Failed to create content.');
            }
            $transaction->commit();
            $this->setFlash('success', 'Create content', 'Content created successfully.');
            return $this->redirect(['/content/index']);
        }catch (Exception $ex){
            $transaction->rollBack();
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            return $this->asJson(['status' => 500, 'message' => $message]);
        }
    }

    /**
     * Update content
     * @return Response
     */
    public function actionUpdate(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $post = Yii::$app->request->post();
            $content = $this->getContent($post['Content']['id']);
            $content = $this->storeUpdate($content, $post);
            if($content->save()){
                $this->storeUpdateFiles($content);
            }else{
                throw new Exception('Failed to update content.');
            }
            $transaction->commit();
            $this->setFlash('success', 'Update content', 'Content updated successfully.');
            return $this->redirect(['/content/index']);
        }catch (Exception $ex){
            $transaction->rollBack();
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            return $this->asJson(['status' => 500, 'message' => $message]);
        }
    }

    /**
     * Download content files
     * @param string $id
     * @param string $name
     * @return Response
     * @throws ServerErrorHttpException
     * @see https://linuxhint.com/download_file_php/
     */
    public function actionDownloadFile(string $id, string $name): Response
    {
        try{
            if(empty($id) || empty($name)){
                throw new Exception('Content id and file name must be provided.');
            }
            $filename = Yii::getAlias('@webroot') . '/uploads/content/' . $id . '/' . $name;
            return Yii::$app->response->sendFile($filename, $name, ['inline' => true]);
        }catch (Exception $ex){
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * @return void
     * @throws ServerErrorHttpException
     */
    public function actionDeleteFile()
    {
        try{
            $post = Yii::$app->request->post();
            $path = Yii::getAlias('@webroot') . '/uploads/content/' . $post['key'] . '/' . $post['name'];
            $out=[];
            if (!file_exists($path) || !@unlink($path)) {
                $out= ['error'=>'The file does not exist or it was deleted previously! Refresh the Page to confirm!'];
            }
            echo Json::encode($out);
        }catch (Exception $ex){
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * Delete content
     * @return Response
     * @throws \Throwable
     */
    public function actionDelete(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $post = Yii::$app->request->post();
            $content =$this->getContent($post['id']);
            if(!$content->delete()){
                throw new Exception('Failed to delete content.');
            }
            $transaction->commit();
            $this->setFlash('success', 'Status update', 'Content deleted successfully');
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }catch (Exception $ex){
            $transaction->rollBack();
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            return $this->asJson(['status' => 500, 'message' => $message]);
        }
    }

    /**
     * @param Content $content
     * @return void
     * @throws Exception
     */
    private function storeUpdateFiles(Content $content)
    {
        $contentUpload = new ContentUpload();
        $contentUpload->contentFiles = UploadedFile::getInstances($contentUpload, 'contentFiles');
        if (!$contentUpload->upload($content->getPrimaryKey(), true)) {
            throw new Exception('Failed to upload content files.');
        }
    }

    /**
     * Save or update content
     * @param Content $content
     * @param array $post
     * @return Content
     */
    private function storeUpdate(Content $content, array $post): Content
    {
        $content->url = $post['Content']['url'];
        $content->courseId = $post['Content']['courseId'];
        $content->topic = $post['Content']['topic'];
        $content->type = $post['Content']['type'];
        $content->description = $post['description'];
        if(is_null($content->id)){
            $content->createdBy = Yii::$app->user->identity->id;
            $content->updateBy = Yii::$app->user->identity->id;
            $content->beforeSave(true);
        }else{
            $content->updateBy = Yii::$app->user->identity->id;
            $content->beforeSave(false);
        }
        return $content;
    }

    /**
     * @param string $id
     * @return Content
     * @throws Exception
     */
    private function getContent(string $id): Content
    {
        $content = Content::findOne($id);
        if(is_null($content)){
            throw new Exception('Content not found.');
        }
        return $content;
    }

    /**
     * @return array
     */
    private function getCharacteristics(): array
    {
        $characteristics = Characteristic::find()->select(['id','name','description'])
            ->where(['not', ['description' => null]])->all();
        return ArrayHelper::map($characteristics, 'id', function($characteristic){
            return $characteristic->description;
        });
    }

    /**
     * @return array
     */
    private function getCourses(): array
    {
        $courses = Course::find()->select(['id','code', 'name'])->all();
        return ArrayHelper::map($courses, 'id', function($course){
            return $course->name . ' ('.  $course->code . ')';
        });
    }

    /**
     * Set the file input preview type from a file extension
     * @param string $fileName
     * @return string preview type
     */
    private function setPreviewType(string $fileName): string
    {
        $filePreviewConfig = [
            'text' => ['txt', 'csv'],
            'pdf' => ['pdf'],
            'image' => ['jpg', 'jpeg', 'png', 'svg'],
            'html' => ['html', 'htm', 'css'],
            'office' => ['doc', 'docx', 'odt', 'xls', 'xlsx', 'ppt', 'pptx', 'ods'],
            'video' => ['mp4', 'mov', 'wmv', 'avi', 'mkv', 'mp3', 'wav', 'wma'],
        ];
        $extension = pathinfo($fileName)['extension'];
        $previewType = 'other';
        foreach ($filePreviewConfig as $key => $configExtensions){
            foreach ($configExtensions as $configExtension){
                if($extension === $configExtension){
                    $previewType = $key;
                    break 2;
                }
            }
        }
        return $previewType;
    }
}