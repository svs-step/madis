<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class AnalyseImpactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['flow_step']) {
            case 1:
                $builder
                    ->add('criterePrincipeFondamentaux', CollectionType::class, [
                        'entry_type' => AnalyseCriterePrincipeFondamentalType::class,
                        'required'   => true,
                    ]);
                    // no break
            case 2:
                $builder
                    ->add('questionConformites', CollectionType::class, [
                        'entry_type' => AnalyseQuestionConformiteType::class,
                        'required'   => true,
                    ]);
        }
    }
}
