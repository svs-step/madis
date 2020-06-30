<?php

namespace App\Domain\Registry\Form\Type\ConformiteOrganisation;

use App\Domain\Registry\Model\ConformiteOrganisation\Conformite;
use App\Domain\Registry\Model\Mesurement;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ConformiteType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('actionProtections', EntityType::class, [
                'required'      => false,
                'label'         => false,
                'class'         => Mesurement::class,
                'query_builder' => function (EntityRepository $er) {
                    /** @var User $user */
                    $user = $this->security->getUser();

                    return $er->createQueryBuilder('m')
                        ->andWhere('m.collectivity = :collectivity')
                        ->setParameter('collectivity', $user->getCollectivity())
                        ->orderBy('m.name', 'ASC');
                },
                'choice_label' => 'name',
                'expanded'     => false,
                'multiple'     => true,
                'attr'         => [
                    'class'            => 'selectpicker',
                    'title'            => 'placeholder.multiple_select',
                    'data-live-search' => true,
                    'data-width'       => '450px',
                ],
            ])
            ->add('reponses', CollectionType::class, [
                    'required'   => false,
                    'entry_type' => ReponseType::class,
                ]
            );
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
