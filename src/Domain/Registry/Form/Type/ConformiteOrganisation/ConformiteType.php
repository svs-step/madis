<?php

namespace App\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Domain\Registry\Dictionary\MesurementStatusDictionary;
use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\Mesurement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConformiteType extends AbstractType
{
    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reponses', CollectionType::class, [
                    'required'   => false,
                    'entry_type' => ReponseType::class,
                ]
            );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $parentForm   = $event->getForm()->getParent()->getParent();
            $collectivity = $parentForm->getData()->getCollectivity();
            $event->getForm()->add('actionProtections', EntityType::class, [
                'required'      => false,
                'label'         => false,
                'class'         => Mesurement::class,
                'query_builder' => function (EntityRepository $er) use ($collectivity) {
                    return $er->createQueryBuilder('m')
                        ->andWhere('m.collectivity = :collectivity')
                        ->setParameter('collectivity', $collectivity)
                        ->andWhere('m.status = :nonApplied')
                        ->setParameter('nonApplied', MesurementStatusDictionary::STATUS_NOT_APPLIED)
                        ->orderBy('m.name', 'ASC');
                },
                'choice_label' => 'name',
                'expanded'     => false,
                'multiple'     => true,
                'attr'         => [
                    'class'            => 'selectpicker',
                    'title'            => 'global.placeholder.multiple_select',
                    'data-live-search' => true,
                    'aria-label'       => 'Actions de protection',
                    'data-width'       => 'calc(100% - 40px)',
                ],
                'choice_attr' => function (Mesurement $choice) {
                    $name = $choice->getName();
                    if (\mb_strlen($name) > 85) {
                        $name = \mb_substr($name, 0, 85) . '...';
                    }

                    return ['data-content' => $name];
                },
            ]);
        });
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Conformite::class,
                'validation_groups' => [
                    'default',
                ],
            ]);
    }
}
