<?php
/**
 * @author Rufusy Idachi <idachirufus@gmail.com>
 */
namespace app\models\search;

use app\models\Content;
use DateTime;
use Exception;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

class ReadingMaterialSearch extends Content
{
    /**
     * Add relation search attributes
     * @return array
     */
    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'course.code',
            'course.name',
            'creator.username'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [
                [
                    'url',
                    'createdBy',
                    'createdAt',
                    'topic',
                    'course.code',
                    'course.name',
                    'creator.username',
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
     * @throws Exception
     */
    public function search(array $params, array $additionalParams): ActiveDataProvider
    {
        $query = Content::find()->alias('cont')->select([
                'cont.id',
                'cont.courseId',
                'cont.url',
                'cont.description',
                'cont.createdBy',
                'cont.createdAt',
                'cont.type',
                'cont.topic'
            ])
            ->joinWith(['course cs' => function(ActiveQuery $q){
                $q->select(['cs.id', 'cs.code', 'cs.name']);
            }], true, 'INNER JOIN')
            ->joinWith(['creator cr' => function(ActiveQuery $q){
                $q->select(['cr.id', 'cr.username']);
            }], true, 'INNER JOIN')
            ->where(['in' , 'cont.type', $additionalParams['types']])
            ->orderBy(['cont.id' => SORT_DESC])->asArray();

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

        $query->andFilterWhere(['like', 'cont.url', $this->url]);
        $query->andFilterWhere(['like', 'cs.code', $this->getAttribute('course.code')]);
        $query->andFilterWhere(['like', 'cs.name', $this->getAttribute('course.name')]);
        $query->andFilterWhere(['like', 'cr.username', $this->getAttribute('creator.username')]);

        if(!empty($params['ReadingMaterialSearch']['createdAt'])) {
            $contentDate = $params['ReadingMaterialSearch']['createdAt'];
            $contentDateStart = new DateTime(substr($contentDate, 0, 10));
            $contentDateEnd = new DateTime(substr($contentDate, 13));
            $query->andFilterWhere(['between', 'cont.createdAt', $contentDateStart->format('Y-m-d'),
                $contentDateEnd->format('Y-m-d')]);
        }

        return $dataProvider;
    }
}