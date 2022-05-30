<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Flow;

use App\Domain\AIPD\Form\Type\AnalyseImpactType;
use App\Domain\AIPD\Model\AnalyseImpact;
use App\Domain\AIPD\Model\CriterePrincipeFondamental;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class AnalyseImpactFlow extends FormFlow
{
    protected $allowDynamicStepNavigation = true;

    protected function loadStepsConfig()
    {
        return [
            [
                'label'     => 'description',
                'form_type' => AnalyseImpactType::class,
                'skip'      => function ($estimatedCurrentStepNumber, FormFlowInterface $flow) {
                    /**
                     * @var AnalyseImpact $aipd
                     */
                    $aipd = $flow->getFormData();
                    $visible = array_filter($aipd->getCriterePrincipeFondamentaux()->toArray(), function (CriterePrincipeFondamental $critere) {
                        return $critere->isVisible();
                    });

                    return 0 === count($visible);
                },
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
