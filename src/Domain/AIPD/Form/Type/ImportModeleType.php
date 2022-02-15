<?php

declare(strict_types=1);

namespace App\Domain\AIPD\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class ImportModeleType extends AbstractType
{
    protected string $maxSize;

    public function __construct(string $maxSize)
    {
        $this->maxSize = $maxSize;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'required'    => true,
                'label'       => 'Sélectionner un fichier XML à importer',
                'constraints' => [
                    new File([
                        'maxSize'   => $this->maxSize,
                        'mimeTypes' => [
                            'application/xml',
                            'text/xml',
                        ],
                        'mimeTypesMessage' => 'Merci d\'importer un fichier XML',
                    ]),
                ],
            ])
        ;
    }
}
