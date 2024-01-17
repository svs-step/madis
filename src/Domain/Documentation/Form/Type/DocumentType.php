<?php

/**
 * This file is part of the MADIS - RGPD Management application.
 *
 * @copyright Copyright (c) 2018-2019 Soluris - Solutions Numériques Territoriales Innovantes
 * @author Donovan Bourlard <donovan@awkan.fr>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace App\Domain\Documentation\Form\Type;

use App\Domain\Documentation\Model;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Contracts\Translation\TranslatorInterface;

class DocumentType extends AbstractType implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private string $maxSize;
    private TranslatorInterface $translator;

    public function __construct(RequestStack $requestStack, string $maxSize, TranslatorInterface $translator)
    {
        $this->requestStack = $requestStack;
        $this->maxSize      = $maxSize;
        $this->translator   = $translator;
    }

    /**
     * Build type form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = $this->requestStack->getCurrentRequest();
        $builder
            ->add('isLink', HiddenType::class, [
                'label'      => false,
                'required'   => false,
                'empty_data' => '0',
            ])
            ->add('name', TextType::class, [
                'label' => 'documentation.document.label.name',
            ])
            ->add('categories', EntityType::class, [
                'label'        => 'documentation.document.label.categories',
                'class'        => 'App\Domain\Documentation\Model\Category',
                'choice_label' => 'name',
                'multiple'     => true,
                'required'     => false,
                'expanded'     => false,
                'attr'         => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'global.placeholder.multiple_select',
                    'aria-label'       => 'Catégories',
                ],
            ])
            ->add('thumbUploadedFile', FileType::class, [
                'label'       => 'documentation.document.label.thumbnail',
                'required'    => false,
                'constraints' => [
                    new Image(['groups' => ['default']]),
                    new File([
                        'maxSize'   => $this->maxSize,
                        'groups'    => ['default'],
                        'mimeTypes' => [
                            'image/png', // .png
                            'image/jpg', // .jpg
                            'image/jpeg', // .jpeg
                        ],
                        'mimeTypesMessage' => 'Les formats autorisés sont .png, .jpg, .jpeg.',
                    ]),
                ],
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])

            ->add('pinned', CheckboxType::class, [
                'label'    => 'documentation.document.label.pinned',
                'required' => false,
            ])

        ;

        $builder->addEventSubscriber($this);
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Model\Document::class,
                'validation_groups' => [
                    'default',
                    'document',
                ],
            ]);
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT       => 'ensureOneFieldIsSubmitted',
            FormEvents::PRE_SET_DATA => 'setIsLink',
        ];
    }

    public function setIsLink(FormEvent $event)
    {
        $isLink = (bool) $this->requestStack->getCurrentRequest()->get('isLink');
        $data   = $event->getData();
        if (!$data->getId()) {
            $data->setIsLink($isLink);
        }
        // $data->setIsLink($isLink);
        $event->setData($data);

        $form = $event->getForm();
        if ($data->getThumbUrl()) {
            $form->add('removeThumb', HiddenType::class, [
                'label'    => 'documentation.document.action.removeThumb',
                'required' => false,
            ]);
        }
        if ($isLink || (true === $data->getIsLink())) {
            $form->add('url', UrlType::class, [
                'label'    => 'documentation.document.label.url',
                'required' => true,
            ]);
            $form->add('isLink', HiddenType::class, [
                'data' => 1,
            ]);
        } else {
            $form->add('isLink', HiddenType::class, [
                'data' => 0,
            ]);
            $form->add('uploadedFile', FileType::class, [
                'label'       => 'documentation.document.label.file',
                'required'    => !$data->getId(),
                'constraints' => [
                    new File([
                        'maxSize'   => $this->maxSize,
                        'groups'    => ['default'],
                        'mimeTypes' => [
                            'image/png', // .png
                            'image/jpg', // .jpg
                            'image/jpeg', // .jpeg
                            'audio/mpeg', // .mp3
                            'audio/ogg', // .ogg
                            'audio/wav', // .wav
                            'audio/m4a', // .m4a
                            'video/mp4', // .mp4
                            'video/quicktime', // .mov
                            'video/avi', // .avi
                            'video/mpeg', // .mpg
                            'video/x-ms-wmv', // .wmv
                            'video/ogg', // .ogv, .ogg
                            'video/webm', // .webm
                            'application/pdf', // .pdf
                            'application/msword', // .doc
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                            'application/vnd.oasis.opendocument.text', // .odt
                            'application/vnd.ms-powerpoint', // .ppt
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
                            'application/vnd.oasis.opendocument.presentation', // .odp
                            'application/vnd.ms-excel', // .xls
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                            'application/vnd.ms-excel.sheet.macroEnabled.12', // .xlsm
                            'application/vnd.oasis.opendocument.spreadsheet', // .ods
                        ],
                        'mimeTypesMessage' => "Ce format de fichier n'est pas autorisé.",
                    ]),
                ],
            ]);
        }
    }

    public function ensureOneFieldIsSubmitted(FormEvent $event)
    {
        $submittedData = $event->getData();

        if (!$submittedData->getUploadedFile() && !$submittedData->getUrl()) {
            $error = $this->translator->trans('documentation.document.form.error.fileorurl');

            throw new TransformationFailedException($error, 400, /* code */ null, /* previous */ $error, /* user message */ ['{{ what }}' => 'aa'] /* message context for the translater */);
        }

        if (true === $submittedData->getIsLink() && !$submittedData->getUrl()) {
            $error = $this->translator->trans('documentation.document.form.error.missingurl');

            throw new TransformationFailedException($error, 400, /* code */ null, /* previous */ $error, /* user message */ ['{{ what }}' => 'aa'] /* message context for the translater */);
        }
    }
}
