<?php

namespace App\Domain\Notification\Event;

use App\Domain\Maturity\Model\Survey;
use App\Domain\Registry\Model\Request;
use Symfony\Contracts\EventDispatcher\Event;

class LateSurveyEvent extends Event
{
    /**
     * @var Survey
     *              The object that generated the notification
     */
    protected Survey $survey;

    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
    }

    /**
     * @return Survey
     */
    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    /**
     * @param Survey $survey
     */
    public function setSurvey(Survey $survey): void
    {
        $this->survey = $survey;
    }
}
