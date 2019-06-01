<?php

declare(strict_types=1);

namespace App\Domain\Maturity\Calculator;

use App\Domain\Maturity\Calculator;
use App\Domain\Maturity\Model;

class MaturityHandler
{
    /**
     * @var Calculator\Maturity
     */
    private $calculator;

    public function __construct(Calculator\Maturity $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Handle calculator & generate score by maturity.
     *
     * @param Model\Survey $survey
     */
    public function handle(Model\Survey $survey): void
    {
        $maturityList = $this->calculator->generateMaturityByDomain($survey);
        $survey->setMaturity($maturityList);
        $survey->setScore($this->calculator->getGlobalScore($maturityList));
    }
}
