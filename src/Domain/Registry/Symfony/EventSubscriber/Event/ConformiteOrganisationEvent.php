<?php

namespace App\Domain\Registry\Symfony\EventSubscriber\Event;

use App\Domain\Registry\Model\ConformiteOrganisation\Evaluation;
use Symfony\Contracts\EventDispatcher\Event;

class ConformiteOrganisationEvent extends Event
{
    /**
     * @var Evaluation
     */
    protected $evaluation;

    public function __construct(Evaluation $evaluation)
    {
        $this->evaluation = $evaluation;
    }

    public function getEvaluation(): Evaluation
    {
        return $this->evaluation;
    }
}
