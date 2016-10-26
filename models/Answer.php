<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "answers".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $questionId
 * @property double $fiftyStart
 * @property double $fiftyEnd
 * @property double $ninetyStart
 * @property double $ninetyEnd
 * @property integer $isCorrect
 * @property double $score
 * @property integer $dateSubmitted
 *
 * @property Question $question
 * @property User $user
 */
class Answer extends \yii\db\ActiveRecord
{
    
    const SCORE_BASE = 100;
    
    const FIFTY_MODIFIER = 2;
    const NINETY_MODIFIER = 10;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'answers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'questionId', 'fiftyStart', 'fiftyEnd', 'ninetyStart', 'ninetyEnd', 'isCorrect', 'score'], 'required'],
            [['userId', 'questionId', 'isCorrect', 'dateSubmitted'], 'integer', 'min' => 0],
            [['fiftyStart', 'fiftyEnd', 'ninetyStart', 'ninetyEnd', 'score'], 'number'],
            [['userId', 'questionId'], 'unique', 'targetAttribute' => ['userId', 'questionId'], 'message' => 'The combination of User ID and Question ID has already been taken.'],
            [['questionId'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['questionId' => 'id']],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
            [['fiftyStart'], 'fiftyStartValidator'],
            [['fiftyEnd'], 'fiftyEndValidator'],
            [['ninetyEnd'], 'ninetyEndValidator'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'userId' => Yii::t('app', 'User ID'),
            'questionId' => Yii::t('app', 'Question ID'),
            'fiftyStart' => Yii::t('app', 'Fifty Start'),
            'fiftyEnd' => Yii::t('app', 'Fifty End'),
            'ninetyStart' => Yii::t('app', 'Ninety Start'),
            'ninetyEnd' => Yii::t('app', 'Ninety End'),
            'isCorrect' => Yii::t('app', 'Is Correct'),
            'score' => Yii::t('app', 'Score'),
            'dateSubmitted' => Yii::t('app', 'Date Submitted'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'questionId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
    
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->dateSubmitted = time();
                       
            $this->calcIsCorrect();
            $this->calcScore();
        }
        return parent::beforeSave($insert);
    }
    
    public function ninetyEndValidator($attribute)
    {        
        if ($this->ninetyEnd <= $this->ninetyStart) {
            $this->addError($attribute, Yii::t('app', 'End must be more than start'));
            return false;
        }
        return true;
    }
    
    public function fiftyStartValidator($attribute)
    {
        if ($this->fiftyStart < $this->ninetyStart || $this->fiftyStart > $this->ninetyEnd) {
            $this->addError($attribute, Yii::t('app', '50% intevral must be inside 90%'));
            return false;
        }
        return true;
    }
    
    public function fiftyEndValidator($attribute)
    {
        if ($this->fiftyEnd > $this->ninetyEnd || $this->fiftyEnd < $this->ninetyStart) {
            $this->addError($attribute, Yii::t('app', '50% intevral must be inside 90%'));
            return false;
        }
        if ($this->fiftyEnd <= $this->fiftyStart) {
            $this->addError($attribute, Yii::t('app', 'End must be more than start'));
            return false;
        }
        return true;
    }
    
    private function calcIsCorrect()
    {        
        $rightAnswer = $this->question->answer;
        if ($rightAnswer >= $this->ninetyStart && $rightAnswer <= $this->ninetyEnd) {
            $this->isCorrect = 1;
        }
        if ($rightAnswer >= $this->fiftyStart && $rightAnswer <= $this->fiftyEnd) {
            $this->isCorrect = 2;
        }
    }
    
    private function calcScore()
    {        
        if (!$this->isCorrect) {
            $this->score = 0;
            return;
        }
        $rightAnswer = $this->question->answer;
        $fiftyDiapason = $this->fiftyEnd - $this->fiftyStart;
        $ninetyDiapason = $this->ninetyEnd - $this->ninetyStart;
        
        $score = static::SCORE_BASE;
        
        if ($this->isCorrect < 2) {
            $score /= 2;
            if ($ninetyDiapason > $rightAnswer*static::NINETY_MODIFIER) {
                $score /= $ninetyDiapason/($rightAnswer*static::NINETY_MODIFIER);
            }
        } else {
            if ($fiftyDiapason> $rightAnswer*static::FIFTY_MODIFIER) {
                $score /= $fiftyDiapason/($rightAnswer*static::FIFTY_MODIFIER);
            }
        }
        
        $this->score = $score;
    }
    
}