<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use app\models\User;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{
    
    public $fullName;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = User::find()
                ->addSelect([
                    '*',
                    'ROUND(ABS(ninetyCount/(answersCount*1.0)-0.9)*100) as ninetyPercentDelta',
                    'ROUND(ABS(fiftyCount/(answersCount*1.0)-0.5)*100) as fiftyPercentDelta',
                ])
                ->andWhere(['>', 'score', 0]);
        
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->setSort(new Sort([
            'attributes' => [
                'name',
                'score',
                'answersCount',
                'ninetyPercent' => [
                    'asc' => ['ninetyPercentDelta' => SORT_ASC],
                    'desc' => ['ninetyPercentDelta' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'fiftyPercent' => [
                    'asc' => ['fiftyPercentDelta' => SORT_ASC],
                    'desc' => ['fiftyPercentDelta' => SORT_DESC],
                    'default' => SORT_ASC
                ],
            ],
            'defaultOrder' => [
                'score' => SORT_DESC,
            ],
        ]));
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
