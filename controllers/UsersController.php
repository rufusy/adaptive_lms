<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\controllers;

use app\models\Characteristic;
use app\models\search\UsersSearch;
use app\models\StudentCharacteristic;
use app\models\User;
use app\models\UserGroup;
use app\models\UsersUpload;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class UsersController extends BaseController
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
     * List users
     * @param string $group
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionIndex(string $group): string
    {
        try{
            if($group !== 'student' && $group !== 'tutor'){
                throw new Exception('The correct group must be specified.');
            }

            $title = 'Students';
            if($group === 'tutor'){
                $title = 'Tutors';
            }

            $userSearchModel = new UsersSearch();
            $usersDataProvider = $userSearchModel->search(Yii::$app->request->queryParams, ['group' => $group]);
            return $this->render('index', [
                'title'=> $this->createPageTitle($title),
                'userSearchModel' => $userSearchModel,
                'usersDataProvider' => $usersDataProvider,
                'group' => $group
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
     * Display create new user page
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionCreate(): string
    {
        try {
            return $this->renderAjax('userCreateForm', [
                'title' => $this->createPageTitle('Create user'),
                'user' => new user(),
                'listOfRoles' => $this->getRoles()
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
     * Save new user
     * @return Response
     * @throws ServerErrorHttpException
     */
    public function actionStore(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $post = Yii::$app->request->post();
            $user = new User();
            $user = $this->storeUpdate($user, $post);
            $password = 'password';
            try{
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
            }catch(Exception $ex){
                $message = 'Bad password';
                if(YII_ENV_DEV){
                    $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
                }
                throw new Exception($message);
            }
            if(!$user->save()){
                throw new Exception('Failed to create user.');
            }
            $transaction->commit();
            $this->setFlash('success', 'Create user', 'User created successfully.');
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }catch (Exception $ex){
            $transaction->rollBack();
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * Display edit user page
     * @param string $id
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionEdit(string $id): string
    {
        try{
            if(empty($id)){
                throw new Exception('User id must be provided.');
            }
            return $this->renderAjax('userUpdateForm', [
                'title' => $this->createPageTitle('Update user'),
                'user' => $this->getUser($id),
                'listOfRoles' => $this->getRoles()
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
     * @throws ServerErrorHttpException
     */
    public function actionCreateFromExcel(): string
    {
        try{
            return $this->renderAjax('usersUploadForm', [
                'title' => $this->createPageTitle('Upload users'),
                'user' => new UsersUpload(),
                'listOfRoles' => $this->getRoles()
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
     * @return Response
     * @throws ServerErrorHttpException|\Throwable
     */
    public function actionUpload(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $characteristics = Characteristic::find()->asArray()->all();
            $characteristicIds = [];
            foreach ($characteristics as $characteristic){
                $characteristicIds[$characteristic['name']] = $characteristic['id'];
            }

            $post = Yii::$app->request->post();
            $userGroupId = $post['UsersUpload']['userGroupId'];
            $group = UserGroup::findOne($userGroupId);
            $userUpload = new UsersUpload();
            $file = UploadedFile::getInstance($userUpload, 'usersFile');
            $userUpload->usersFile = $file;
            $userUpload->userGroupId = $userGroupId;
            if($userUpload->validate()){
                // upload file
                $path = Yii::getAlias('@app') . '/uploads';
                FileHelper::createDirectory($path);
                $fileName = preg_replace('/\s/','_', $file->baseName) . '.' . $file->extension;
                $file->saveAs($path . '/' . $fileName, true);

                // read file and create users
                $inputFileName = $path . '/' . $fileName;
                $inputFileType = IOFactory::identify($inputFileName);
                $reader = IOFactory::createReader($inputFileType);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($inputFileName);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                if($group->name === 'student'){
                    $characteristics = $sheetData[1];
                    array_shift($sheetData);
                    $allStudentsData = $sheetData;
                    foreach ($allStudentsData as $studentDatum){
                        $user = User::find()->where(['username' => $studentDatum['B']])->one();
                        if(is_null($user)){
                            $user = new User();
                        }
                        $user->username = $studentDatum['B'];
                        $user->userGroupId = $userGroupId;

                        $password = 'password';
                        try{
                            $user->password = Yii::$app->getSecurity()->generatePasswordHash($password);
                        }catch(Exception $ex){
                            $message = 'Bad password';
                            if(YII_ENV_DEV){
                                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
                            }
                            throw new Exception($message);
                        }

                        if($user->save()){
                            $userPk = $user->getPrimaryKey();
                            foreach ($studentDatum as $studentDataKey => $studentData){
                                if($studentDataKey === 'A' || $studentDataKey === 'B'){
                                    continue;
                                }
                                $characteristicName = $characteristics[$studentDataKey];
                                if(!array_key_exists($characteristicName, $characteristicIds)){
                                    throw new Exception('This characteristic does not exist.');
                                } else {
                                    $characteristicPk = (int)$characteristicIds[$characteristicName];
                                    $studentCharacteristic = StudentCharacteristic::find()
                                        ->where([
                                            'studentId' => $userPk,
                                            'characteristicId' => $characteristicPk
                                        ])
                                        ->one();
                                    if (is_null($studentCharacteristic)) {
//                                        if ($studentData === 0) {
//                                            continue;
//                                        }
                                        $studentCharacteristic = new StudentCharacteristic();
                                    }
//                                    if ($studentData === 0) {
//                                       if(!$studentCharacteristic->delete()){
//                                           throw new Exception('Failed to remove student with characteristic of zero.');
//                                       }
//                                    }else{
                                        $studentCharacteristic->studentId = $userPk;
                                        $studentCharacteristic->characteristicId = $characteristicPk;
                                        $studentCharacteristic->value = $studentData;
                                        if (!$studentCharacteristic->save()) {
                                            throw new Exception('Failed to save student characteristic data.');
                                        }
//                                    }
                                }
                            }
                        }else{
                            throw new Exception('Failed to save student data.');
                        }
                    }
                }else{
                    // Upload tutors data
                }
            }
            $transaction->commit();
            $this->setFlash('success', 'Create users', 'Users uploaded successfully.');
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }catch (Exception $ex){
            $transaction->rollBack();
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * Update user
     * @return Response
     * @throws ServerErrorHttpException
     */
    public function actionUpdate(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $post = Yii::$app->request->post();
            $user = $this->getUser($post['User']['id']);
            $user = $this->storeUpdate($user, $post);
            if(!$user->save()){
                throw new Exception('Failed to update user.');
            }
            $transaction->commit();
            $this->setFlash('success', 'Update user', 'User updated successfully.');
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }catch (Exception $ex){
            $transaction->rollBack();
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * Change model status
     * @return Response
     * @throws \Throwable
     */
    public function actionDelete(): Response
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $post = Yii::$app->request->post();
            $user =$this->getUser($post['id']);
            if(!$user->delete()){
                throw new Exception('Failed to delete user.');
            }
            $transaction->commit();
            $this->setFlash('success', 'Status update', 'User deleted successfully');
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
     * @param User $user
     * @param array $post
     * @return User
     */
    private function storeUpdate(User $user, array $post): User
    {
        $user->username = $post['User']['username'];
        $user->userGroupId = $post['User']['userGroupId'];
        return $user;
    }

    /**
     * @param string $id
     * @return User
     * @throws Exception
     */
    private function getUser(string $id): User
    {
        $user = User::findOne($id);
        if(is_null($user)){
            throw new Exception('User not found.');
        }
        return $user;
    }

    /**
     * @return array
     */
    private function getRoles(): array
    {
        $roles = UserGroup::find()->select(['id','name'])->all();
        return ArrayHelper::map($roles, 'id', function($role){
            return $role->name;
        });
    }
}