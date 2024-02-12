<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\CriterePrincipeFondamental;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AnalyseCriterePrincipeFondamentalType extends AbstractType
{
    protected string $maxSize;

    public function __construct(string $maxSize)
    {
        $this->maxSize = $maxSize;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('reponse', DictionaryType::class, [
            'name' => 'reponse_critere_fondamental',
        ])
            ->add('justification', TextareaType::class, [
                'required' => false,
                'attr'     => [
                    'maxlength' => 1000,
                    'rows'      => 1,
                    'class'     => 'textareaheight',
                ],
                'purify_html' => true,
            ])
            ->add('fichierFile', FileType::class, [
                'required' => false,
                'label'    => false,
                'attr'     => [
                    'accept' => 'image/*',
                ],
                'constraints' => [
                    new File([
                        'maxSize'   => $this->maxSize,
                        'groups'    => ['default'],
                        'mimeTypes' => [
                            'image/png', // .png
                            'image/jpg', // .jpg
                            'image/jpeg', // .jpeg
                        ],
                        'mimeTypesMessage' => 'aipd_validator.fichier.file',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => CriterePrincipeFondamental::class,
                'validation_groups' => [
                    'default',
                    'critere',
                ],
            ]);
    }
}
