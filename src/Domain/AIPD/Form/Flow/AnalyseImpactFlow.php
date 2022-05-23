<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Flow;

use App\Domain\AIPD\Form\Type\AnalyseImpactType;
use Craue\FormFlowBundle\Form\FormFlow;

class AnalyseImpactFlow extends FormFlow
{
    protected $allowDynamicStepNavigation = true;

    protected function loadStepsConfig()
    {
        return [
            [
                'label'     => 'description',
                'form_type' => AnalyseImpactType::class,
            ],
            [
                'label'     => 'conformite',
                'form_type' => AnalyseImpactType::class,
            ],
            [
                'label'     => 'risques',
                'form_type' => AnalyseImpactType::class,
            ],
            [
                'label'     => 'mesures',
                'form_type' => AnalyseImpactType::class,
            ],
        ];
    }
}
