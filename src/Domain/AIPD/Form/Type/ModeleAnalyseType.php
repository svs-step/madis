<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Application\Form\Extension\SanitizeTextFormType;
use App\Domain\AIPD\Model\ModeleAnalyse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class ModeleAnalyseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['flow_step']) {
            case 1:
                $builder
                    ->add('nom', SanitizeTextFormType::class, [
                        'required' => true,
                        'attr'     => [
                            'maxlength' => 255,
                        ],
                    ])
                    ->add('description', SanitizeTextFormType::class, [
                        'required' => true,
                        'attr'     => [
                            'maxlength' => 255,
                        ],
                    ])
                    ->add('labelAmeliorationPrevue', SanitizeTextFormType::class, [
                        'required' => true,
                        'attr'     => [
                            'maxlength' => 255,
                        ],
                    ])
                    ->add('labelInsatisfaisant', SanitizeTextFormType::class, [
                        'required' => true,
                        'attr'     => [
                            'maxlength' => 255,
                        ],
                    ])
                    ->add('labelSatisfaisant', SanitizeTextFormType::class, [
                        'required' => true,
                        'attr'     => [
                            'maxlength' => 255,
                        ],
                    ])
                    ->add('criterePrincipeFondamentaux', CollectionType::class, [
                        'entry_type'  => CriterePrincipeFondamentalType::class,
                        'required'    => true,
                        'constraints' => [
                            new Valid([
                                'groups' => ['default'],
                            ]),
                        ],
                    ])
                ;
                break;
            case 2:
                $builder
                    ->add('questionConformites', CollectionType::class, [
                        'entry_type' => ModeleQuestionConformiteType::class,
                    ])
                ;
                break;
            case 3:
                $builder
                    ->add('scenarioMenaces', CollectionType::class, [
                        'entry_type'   => ModeleScenarioMenaceType::class,
                        'required'     => true,
                        'allow_add'    => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                    ])
                ;
                break;
        }
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => ModeleAnalyse::class,
                'validation_groups' => [
                    'default',
                    'aipd',
                ],
            ]);
    }
}
