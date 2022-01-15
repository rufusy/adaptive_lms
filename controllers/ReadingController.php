<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\controllers;

use app\models\Characteristic;
use app\models\search\ReadingMaterialSearch;
use app\models\StudentCharacteristic;
use Exception;
use Yii;
use yii\filters\AccessControl;
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
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionIndex(): string
    {
        try{
            $studentCharacteristics = StudentCharacteristic::find()->select(['characteristicId', 'value'])
                ->where(['studentId' => Yii::$app->user->identity->id])
                ->asArray()->all();

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
     * @return string page to display learner's behaviours
     * @throws ServerErrorHttpException
     */
    public function actionMyBehaviour(): string
    {
        try{
            $characteristics = StudentCharacteristic::find()->alias('sc')
                ->select(['sc.value', 'sc.characteristicId'])
                ->joinWith(['characteristic ch'])
                ->where(['sc.studentId' => Yii::$app->user->identity->id])
                ->orderBy(['ch.id' => SORT_ASC])
                ->asArray()->all();

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
}