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

class DocumentType extends AbstractType implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private string $maxSize;

    public function __construct(RequestStack $requestStack, string $maxSize)
    {
        $this->requestStack = $requestStack;
        $this->maxSize      = $maxSize;
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
                'label'   => 'documentation.document.form.label.name',
            ])
            ->add('categories', EntityType::class, [
                'label'        => 'documentation.document.form.label.categories',
                'class'        => 'App\Domain\Documentation\Model\Category',
                'choice_label' => 'name',
                'multiple'     => true,
                'required'     => false,
                'expanded'     => false,
                'attr' => [
                    'class' => 'selectpicker',
                    'title' => 'placeholder.multiple_select',
                ],
            ])
            ->add('thumbUploadedFile', FileType::class, [
                'label'       => 'documentation.document.form.label.thumbnail',
                'required'    => false,
                'constraints' => [
                    new Image(['groups' => ['default']]),
                    new File([
                        'maxSize'   => $this->maxSize,
                        'groups'    => ['default'],
                    ]),
                ],
                'attr'     => [
                    'accept' => 'image/*',
                ],
            ])

            ->add('pinned', CheckboxType::class, [
                'label'    => 'documentation.document.form.label.pinned',
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
        //$data->setIsLink($isLink);
        $event->setData($data);

        $form = $event->getForm();
        if ($data->getThumbUrl()) {
            $form->add('removeThumb', CheckboxType::class, [
                'label'    => 'documentation.document.form.label.removeThumb',
                'required' => false,
            ]);
        }
        if ($isLink || (true === $data->getIsLink())) {
            $form->add('url', UrlType::class, [
                'label'    => 'documentation.document.form.label.url',
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
                'label'       => 'documentation.document.form.label.file',
                'required'    => false,
                'constraints' => [
                    new File([
                        'maxSize'   => $this->maxSize,
                        'groups'    => ['default'],
                    ]),
                ],
            ]);
        }
    }

    public function ensureOneFieldIsSubmitted(FormEvent $event)
    {
        $submittedData = $event->getData();

        if (!$submittedData->getUploadedFile() && !$submittedData->getUrl()) {
            throw new TransformationFailedException('documentation.document.form.error.fileorurl', 400, /* code */ null, /* previous */ 'documentation.document.form.error.fileorurl', /* user message */ ['{{ what }}' => 'aa'] /* message context for the translater */);
        }

        if (true === $submittedData->getIsLink() && !$submittedData->getUrl()) {
            throw new TransformationFailedException('documentation.document.form.error.missingurl', 401, /* code */ null, /* previous */ 'documentation.document.form.error.missingurl', /* user message */ ['{{ what }}' => 'aa'] /* message context for the translater */);
        }
    }
}
