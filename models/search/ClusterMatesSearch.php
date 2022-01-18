<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\models\search;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class ClusterMatesSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @return ActiveDataProvider
     */
    public function search(): ActiveDataProvider
    {
        $query = User::find()->alias('u')->select([
                'u.id',
                'u.username',
                'u.cluster',
                'u.userGroupId'
            ])
            ->joinWith(['userGroup ug' => function(ActiveQuery $q){
                $q->select(['ug.id']);
            }], true, 'INNER JOIN')
            ->where(['ug.name' => 'student', 'u.cluster' => Yii::$app->user->identity->cluster])
            ->andWhere(['not', ['u.username' => Yii::$app->user->identity->username]])
            ->asArray();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pagesize' => 20,
            ],
        ]);

        if(!$this->validate()) {
            return $dataProvider;
        }

        $query->orderBy(['u.id' => SORT_ASC]);

        return $dataProvider;
    }
}