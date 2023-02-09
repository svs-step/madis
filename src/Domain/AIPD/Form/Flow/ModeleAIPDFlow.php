<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Flow;

use App\Domain\AIPD\Form\Type\ModeleAnalyseType;
use Craue\FormFlowBundle\Form\FormFlow;

class ModeleAIPDFlow extends FormFlow
{
    protected function loadStepsConfig(): array
    {
        return [
            [
                'label'     => 'generalites',
                'form_type' => ModeleAnalyseType::class,
            ],
            [
                'label'     => 'conformite',
                'form_type' => ModeleAnalyseType::class,
            ],
            [
                'label'     => 'risques',
                'form_type' => ModeleAnalyseType::class,
            ],
        ];
    }
}
