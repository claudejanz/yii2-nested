<?php

namespace app\models;

use app\extentions\behaviors\WeekPublishBehavior;
use app\extentions\helpers\EuroDateTime;
use app\models\base\WeekBase;
use DateTime;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * @property Day[] $daysByDate
 * @property Reporting[] $reportingsByDate
 * @property int[] $loadsByDate
 * @property int[] $kmByDate
 */
class Week extends WeekBase
{

    public function behaviors()
    {
        return array(
            'publish' => [
                'class' => WeekPublishBehavior::className(),
            ],
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
            ],
        );
    }

    public function rules()
    {

        return array_merge([
            ['title', 'validateTitle', 'skipOnEmpty' => false],
                ], parent::rules());
    }

    public function validateTitle($attribute, $params)
    {
        if (!isset($this->{$attribute})) {


            if (!isset($this->date_begin)) {
                $this->addError($attribute, Yii::t('app', 'date_begin must be set for {attribute} to be set'));
                return false;
            }

            $date = new DateTime($this->date_begin);
            $this->{$attribute} = 'W' . $date->format('w');
        }
    }

    /**
     * @return ActiveQuery
     */
    public function getDaysByDate()
    {
        return $this->hasMany(Day::className(), ['week_id' => 'id'])->with(['trainings'])->indexBy('date');
    }

    
    public function getReportingsByDate()
    {
        $models = $this->reportings;
        return ($models) ? ArrayHelper::index($models, 'id', ['date']) : null;
    }

    public function getLoadsByDate()
    {
        if(!$this->getReportingsByDate())return null;
        $data = [];
        foreach ($this->getReportingsByDate() as $key => $reportings) {
            $data[$key]=0;
            foreach ($reportings as $reporting) {
                /* @var $reporting Reporting */
                $data[$key]+=$reporting->load;
            }
        }
        return $data;
    }
    public function getKmByDate()
    {
        if(!$this->getReportingsByDate())return null;
        $data = [];
        foreach ($this->getReportingsByDate() as $key => $reportings) {
            $data[$key]=0;
            foreach ($reportings as $reporting) {
                /* @var $reporting Reporting */
                $data[$key]+=$reporting->km;
            }
        }
        return $data;
    }

    public function sendWeekMail($user)
    {
        $date = new EuroDateTime($this->date_begin);
        $dateEnd = clone $date;
        $dateEnd->modify('+6 days');
        $title = Yii::t('app', 'Your new planning from {begin_date} to {end_date}', ['begin_date' => Yii::$app->formatter->asDate($date), 'end_date' => Yii::$app->formatter->asDate($dateEnd)]);
        return Yii::$app->mailer->compose('sendWeek', ['model' => $this, 'user' => $user, 'date' => $date, 'date_begin' => $this->date_begin, 'title' => $title])
                        ->setFrom([Yii::$app->params['mailerEmail'] => Yii::$app->params['mailerName']])
                        ->setTo($user->email)
                        ->setSubject($title)
                        ->send();
    }

    public function publish($value = WeekPublishBehavior::PUBLISHED_PLANING_DONE)
    {
        $this->published = $value;
        if ($this->save()) {
            foreach ($this->days as $day)
                if (!$day->publish($value))
                    return false;

            return true;
        }
        return false;
    }
    

    

}
