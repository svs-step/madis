<?php

namespace App\Domain\Notification\Event;

use App\Domain\Maturity\Model\Survey;
use Symfony\Contracts\EventDispatcher\Event;

class LateSurveyEvent extends Event
{
    protected Survey $survey;

    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
    }

    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    public function setSurvey(Survey $survey): void
    {
        $this->survey = $survey;
    }
}
