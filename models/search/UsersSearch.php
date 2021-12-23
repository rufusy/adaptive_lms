<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\models\search;

use app\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class UsersSearch extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [
                [
                    'id',
                    'username',
                    'userGroup.id'
                ],
                'safe'
            ],
        ];
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
     * @param array $params
     * @param array $additionalParams
     * @return ActiveDataProvider
     */
    public function search(array $params, array $additionalParams): ActiveDataProvider
    {
        $query = User::find()->alias('u')->select([
                'u.id',
                'u.username',
                'u.userGroupId'
            ])
            ->joinWith(['userGroup ug' => function(ActiveQuery $q){
                $q->select(['ug.id']);
            }], true, 'INNER JOIN')
            ->where(['ug.name' => $additionalParams['group']])
            ->asArray();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pagesize' => 20,
            ],
        ]);

        $this->load($params);

        if(!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'u.username', $this->username]);
        $query->orderBy(['u.id' => SORT_ASC]);

        return $dataProvider;
    }
}