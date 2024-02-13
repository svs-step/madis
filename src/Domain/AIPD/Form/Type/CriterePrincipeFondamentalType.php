<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use App\Domain\AIPD\Model\CriterePrincipeFondamental;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CriterePrincipeFondamentalType extends AbstractType
{
    protected string $maxSize;

    public function __construct(string $maxSize)
    {
        $this->maxSize = $maxSize;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class, [
                'purify_html' => true,
            ])
            ->add('labelLivrable', TextType::class, [
                'attr' => [
                    'maxlength' => 255,
                ],
                'purify_html' => true,
            ])
            ->add('reponse', DictionaryType::class, [
                'name' => 'reponse_critere_fondamental',
            ])
            ->add('isVisible', CheckboxType::class, [
                'label'    => false,
                'required' => false,
            ])
            ->add('texteConformite', TextareaType::class, [
                'attr' => [
                    'maxlength' => 1000,
                    'rows'      => 1,
                    'class'     => 'textareaheight',
                ],
                'purify_html' => true,
            ])
            ->add('texteNonConformite', TextareaType::class, [
                'attr' => [
                    'maxlength' => 1000,
                    'rows'      => 1,
                    'class'     => 'textareaheight',
                ],
                'purify_html' => true,
            ])
            ->add('texteNonApplicable', TextareaType::class, [
                'attr' => [
                    'maxlength' => 1000,
                    'rows'      => 1,
                    'class'     => 'textareaheight',
                ],
                'purify_html' => true,
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
            ->add('deleteFile', HiddenType::class, [
                'data' => 0,
            ])
            ->add('fichierFile', FileType::class, [
                'required'    => false,
                'constraints' => [
                    new File([
                        'maxSize'   => $this->maxSize,
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'aipd_validator.fichier.file',
                        'groups'           => ['default'],
                    ]),
                ],
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])
        ;
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => CriterePrincipeFondamental::class,
                'validation_groups' => [
                    'default',
                    'aipd',
                ],
            ]);
    }
}
