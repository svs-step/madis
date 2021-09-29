<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Flow;

use App\Domain\AIPD\Form\Type\AnalyseImpactType;
use Craue\FormFlowBundle\Form\FormFlow;

class AnalyseImpactFlow extends FormFlow
{
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
        ];
    }
}
