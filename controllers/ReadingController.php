<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\controllers;

use app\models\Characteristic;
use app\models\search\ClusterMatesSearch;
use app\models\search\ReadingMaterialSearch;
use app\models\StudentCharacteristic;
use app\models\User;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class ReadingController extends BaseController
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
     * Display page with materials to read
     * @return Response|string
     * @throws ServerErrorHttpException
     */
    public function actionIndex()
    {
        try{
            $studentCharacteristics = $this->getStudentCharacteristics(Yii::$app->user->identity->id);

            if(empty($studentCharacteristics)){
                if(is_null(Yii::$app->user->identity->cluster)){
                    return $this->redirect(['/reading/alert',
                        'message' => 'We are unable to find your learning materials because you do not belong in any cluster.']);
                }

                /**
                 * Since we can't have all similar characteristics for all members in a cluster,
                 * we find the characteristics of all cluster members, combine them and remove duplicates.
                 * At the moment we have 16 characteristics in the system.
                 * Best case we get 16 total characteristics.
                 * Worst case we get 16 * no. of cluster members total characteristics.
                 */
                $users = User::find()->select(['id'])->where(['cluster' => Yii::$app->user->identity->cluster])
                    ->andWhere(['not', ['id' => Yii::$app->user->identity->id]])
                    ->asArray()->all();
                $clusterMatesIds = [];
                $clusterMatesCharacteristics = [];
                foreach ($users as $user){
                    $clusterMatesIds[] = $this->getStudentCharacteristics($user['id']);
                }

                foreach ($clusterMatesIds as $clusterMateIds){
                    foreach ($clusterMateIds as $characteristic){
                        $clusterMatesCharacteristics[] = $characteristic;
                    }
                }

                $studentCharacteristics = array_unique($clusterMatesCharacteristics, SORT_REGULAR);
            }

            $morphedCharacteristics = [];
            foreach ($studentCharacteristics as $studentCharacteristic){
                $characteristicId = $studentCharacteristic['characteristicId'];
                $morphedCharacteristics[$characteristicId] = (double)$studentCharacteristic['value'];
            }

            $pairsWithDuplicates = [];
            foreach ($morphedCharacteristics as $key => $morphedCharacteristic){
                // Find the pair to this student characteristic
                $pairedCharacteristic = Characteristic::find()->select(['pairedWith'])
                    ->where(['id' => $key])->asArray()->one();
                $pairedCharacteristicId = $pairedCharacteristic['pairedWith'];

                $pairsWithDuplicates[] = [
                    $key => $morphedCharacteristic,
                    $pairedCharacteristicId => $morphedCharacteristics[$pairedCharacteristicId]
                ];
            }

            $preferredCharacteristics = [];
            $pairs = array_unique($pairsWithDuplicates, SORT_REGULAR);
            foreach ($pairs as $pair){
                $keys = [];
                $idx = 0;
                foreach ($pair as $pairKey => $pairValue){
                     $keys[$idx] = $pairKey;
                     $idx++;
                }

                if($pair[$keys[0]] > $pair[$keys[1]]){
                    $preferredCharacteristics[] = $keys[0];
                }elseif($pair[$keys[0]] < $pair[$keys[1]]){
                    $preferredCharacteristics[] = $keys[1];
                }else{
                    if($pair[$keys[0]] > 0){
                        $preferredCharacteristics[] = $keys[0];
                    }
                }
            }

            $materialSearchModel = new ReadingMaterialSearch();
            $materialDataProvider = $materialSearchModel->search(Yii::$app->request->queryParams, [
                'id' => Yii::$app->user->identity->id,
                'types' => $preferredCharacteristics
            ]);

            return $this->render('index', [
                'title'=> $this->createPageTitle('content'),
                'materialSearchModel' => $materialSearchModel,
                'materialDataProvider' => $materialDataProvider,
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
     * @return string|Response
     * @throws ServerErrorHttpException
     */
    public function actionMyBehaviour()
    {
        try{
            $characteristics = StudentCharacteristic::find()->alias('sc')
                ->select(['sc.value', 'sc.characteristicId'])
                ->joinWith(['characteristic ch'])
                ->where(['sc.studentId' => Yii::$app->user->identity->id])
                ->orderBy(['ch.id' => SORT_ASC])
                ->asArray()->all();

            if(empty($characteristics)){
                return $this->redirect(['/reading/alert',
                    'message' => 'We are unable to find learning behaviours matching your account.']);
            }

            $namesToRemove = [
                'Inductive Reasoning Ability_Low',
                'Information Processing Speed_Low',
                'Associative Learning Ability_single',
                'Working Memory Capacity_nonlinear'
            ];

            $characteristicsToKeep = [];
            foreach ($characteristics as $key => $characteristic){
                $name = $characteristic['characteristic']['name'];
                if(!in_array($name, $namesToRemove)){
                    $characteristicsToKeep[] = $characteristic;
                }
            }

            return $this->render('myBehaviours', [
                'title' => $this->createPageTitle('my learning behaviour'),
                'characteristics' => $characteristicsToKeep,
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
     * Display alert page
     * @throws ServerErrorHttpException
     */
    public function actionAlert(string $message): string
    {
        try{
            if(empty($message)){
                throw new Exception('Alert message must be provided');
            }

            return $this->render('alertPage', [
                'title' => $this->createPageTitle('alert'),
                'message' => $message
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
     * Display page to show cluster mates
     * @return Response|string
     * @throws ServerErrorHttpException
     */
    public function actionClusterMembers()
    {
        try{
            if(is_null(Yii::$app->user->identity->cluster)){
                return $this->redirect(['/reading/alert',
                    'message' => 'We are unable to find your cluster mates because you do not belong in any cluster.']);
            }

            $clusterSearchModel = new ClusterMatesSearch();
            $clusterDataProvider = $clusterSearchModel->search();

            return $this->render('clusterMates', [
                'title' => $this->createPageTitle('my cluster mates'),
                'clusterSearchModel' => $clusterSearchModel,
                'clusterDataProvider' => $clusterDataProvider
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
     * Get learning behaviours
     * @param mixed $studentId
     * @return array
     */
    private function getStudentCharacteristics($studentId): array
    {
        return StudentCharacteristic::find()->select(['characteristicId', 'value'])
            ->where(['studentId' => $studentId])
            ->asArray()->all();
    }
}