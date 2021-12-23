<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\controllers;

use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use app\models\LoginForm;
use yii\web\ServerErrorHttpException;

class SiteController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return Response
     */
    public function actionIndex(): Response
    {
        if(Yii::$app->user->isGuest){
            return $this->redirect(['login']);
        }
        return $this->redirect(['/dash/index']);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (Yii::$app->user->isGuest) {
            $this->layout = 'login';
            return $this->render('login', [
                'title'=> $this->createPageTitle('login'),
                'loginForm' => new LoginForm()
            ]);
        }else{
            return $this->goHome();
        }
    }

    /**
     * Log user in
     * @return void|\yii\console\Response|Response
     * @throws ServerErrorHttpException
     */
    public function actionProcessLogin()
    {
        try{
            $model = new LoginForm();
            if($model->load(Yii::$app->request->post())){
                if($model->validate()){
                    if(Yii::$app->user->login($model->getUser())){
                        Yii::$app->getSession()->setFlash('create', [
                            'type' => 'success',
                            'title' => 'Login',
                            'message' => 'Logged on successfully'
                        ]);
                        return Yii::$app->response->redirect(['/dash']);
                    }else{
                        throw new Exception('An error occurred while trying to log in.');
                    }
                }else{
                    Yii::$app->getSession()->setFlash('create', [
                        'type' => 'danger',
                        'title' => 'Login',
                        'message' => 'Logged on failed. Incorrect username or password.'
                    ]);
                    return $this->redirect(['/login']);
                }
            }
        }catch (Exception $ex){
            $message = $ex->getMessage();
            if(YII_ENV_DEV){
                $message .= ' File: ' . $ex->getFile() . ' Line: ' . $ex->getLine();
            }
            throw new ServerErrorHttpException($message, 500);
        }
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
