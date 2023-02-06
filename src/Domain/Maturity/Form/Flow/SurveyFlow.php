<?php

declare(strict_types=1);

namespace App\Domain\Maturity\Form\Flow;

use App\Domain\Maturity\Form\Type\SurveyType;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class SurveyFlow extends FormFlow
{
    protected function loadStepsConfig()
    {
        return [
            [
                'label'     => 'Sélection du réferentiel',
                'form_type' => SurveyType::class,
            ],
            [
                'label'     => 'Complétion du référentiel',
                'form_type' => SurveyType::class,
            ],
        ];
    }
}
