<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\controllers;

use app\models\Characteristic;
use app\models\Content;
use app\models\Course;
use app\models\search\ContentSearch;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

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

            return $this->render('editContent', [
                'title'=> $this->createPageTitle('Edit content'),
                'content' => $this->getContent($id),
                'listOfCharacteristics' => $this->getCharacteristics(),
                'listOfCourses' => $this->getCourses()
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
        try{
            $content = $this->storeUpdate(new Content(), Yii::$app->request->post());
            if(!$content->save()){
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
            if(!$content->save()){
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
     * Save or update content
     * @param Content $content
     * @param array $post
     * @return Content
     */
    private function storeUpdate(Content $content, array $post): Content
    {
        $content->url = $post['Content']['url'];
        $content->courseId = $post['Content']['courseId'];
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
        $characteristics = Characteristic::find()->select(['id','name'])->all();
        return ArrayHelper::map($characteristics, 'id', function($characteristic){
            return $characteristic->name;
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
}