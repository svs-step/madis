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

namespace App\Domain\Registry\Form\Type;

use App\Domain\Registry\Model;
use App\Domain\Registry\Model\Tool;
use App\Domain\User\Model as UserModel;
use App\Domain\User\Model\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;

class ProofType extends AbstractType
{
    protected string $maxSize;

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security, string $maxSize)
    {
        $this->security = $security;
        $this->maxSize  = $maxSize;
    }

    /**
     * Build type form.
     *
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Model\Proof $proof */
        $proof = $options['data'];

        if (!\is_null($proof->getCollectivity())) {
            $collectivity = $proof->getCollectivity();
        } else {
            /** @var UserModel\User $authenticatedUser */
            $authenticatedUser = $this->security->getUser();
            $collectivity      = $authenticatedUser->getCollectivity();
        }

        $builder
            ->add('name', TextType::class, [
                'label'    => 'registry.proof.form.name',
                'required' => true,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('type', DictionaryType::class, [
                'label'    => 'registry.proof.form.type',
                'name'     => 'registry_proof_type',
                'required' => true,
            ])
            ->add('documentFile', FileType::class, [
                'label'       => false,
                'required'    => false,
                'constraints' => [
                    new File([
                        'maxSize'   => $this->maxSize,
//                        'mimeTypesMessage' => 'registry_proof.document_file.file',
                        'mimeTypes' => [
                            // JPG / PNG
                            'image/jpeg',
                            'image/png',
                            // PDF
                            'application/pdf',
                            // DOC
                            'application/msword',
                            // DOCX
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            // Lors de la génération d'un fichier (Bilan) word son mimetype est doublé.
                            // On conserve le mimetype suivant car il y avait des bugs avec iOS (ipad et iphone) lors du téléchargement
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.documentapplication/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            // ODT
                            'application/vnd.oasis.opendocument.text',
                            // XLS
                            'application/vnd.ms-excel',
                            // XLSX
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            // ODS
                            'application/vnd.oasis.opendocument.spreadsheet',
                            // PPT / PPTX
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        ],
                        'groups'    => ['default'],
                    ]),
                ],
            ])
            ->add('comment', TextType::class, [
                'label'    => 'registry.proof.form.comment',
                'required' => false,
                'attr'     => [
                    'maxlength' => 255,
                ],
            ])
            ->add('treatments', EntityType::class, [
                'label'         => 'registry.proof.form.treatments',
                'class'         => Model\Treatment::class,
                'query_builder' => function (EntityRepository $er) use ($collectivity) {
                    $qb = $er->createQueryBuilder('t');
                    $qb->andWhere(
                        $qb->expr()->eq('t.collectivity', ':collectivity')
                    );
                    $qb->addOrderBy('t.active', Criteria::DESC);
                    $qb->addOrderBy('t.name', Criteria::ASC);
                    $qb->setParameters([
                        'collectivity' => $collectivity,
                    ]);

                    return $qb;
                },
                'choice_label'  => function (Model\Treatment $object) {
                    return $this->formatInactiveObjectLabel($object);
                },
                'attr'          => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'placeholder.multiple_select_traitement',
                ],
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
            ])
            ->add('contractors', EntityType::class, [
                'label'         => 'registry.proof.form.contractors',
                'class'         => Model\Contractor::class,
                'query_builder' => function (EntityRepository $er) use ($collectivity) {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.collectivity = :collectivity')
                        ->orderBy('c.name', Criteria::ASC)
                        ->setParameter('collectivity', $collectivity)
                    ;
                },
                'attr'          => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'placeholder.multiple_select_contractors',
                ],
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
            ])
            ->add('mesurements', EntityType::class, [
                'label'         => 'registry.proof.form.mesurements',
                'class'         => Model\Mesurement::class,
                'query_builder' => function (EntityRepository $er) use ($collectivity) {
                    return $er->createQueryBuilder('m')
                        ->andWhere('m.collectivity = :collectivity')
                        ->orderBy('m.name', Criteria::ASC)
                        ->setParameter('collectivity', $collectivity)
                    ;
                },
                'attr'          => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'placeholder.multiple_select_action_protection',
                ],
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
            ])
            ->add('requests', EntityType::class, [
                'label'         => 'registry.proof.form.requests',
                'class'         => Model\Request::class,
                'query_builder' => function (EntityRepository $er) use ($collectivity) {
                    $qb = $er->createQueryBuilder('r');

                    $qb->andWhere(
                        $qb->expr()->eq('r.collectivity', ':collectivity')
                    );
                    $qb->addOrderBy('r.deletedAt', Criteria::ASC);
                    $qb->addOrderBy('r.applicant.firstName', Criteria::DESC);
                    $qb->addOrderBy('r.applicant.lastName', Criteria::DESC);
                    $qb->setParameters([
                        'collectivity' => $collectivity,
                    ]);

                    return $qb;
                },
                'choice_label'  => function (Model\Request $object) {
                    return $this->formatArchivedObjectLabel($object);
                },
                'attr'          => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'placeholder.multiple_select_demande_personne',
                ],
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
            ])
            ->add('violations', EntityType::class, [
                'label'         => 'registry.proof.form.violations',
                'class'         => Model\Violation::class,
                'query_builder' => function (EntityRepository $er) use ($collectivity) {
                    $qb = $er->createQueryBuilder('v');

                    $qb->andWhere(
                        $qb->expr()->eq('v.collectivity', ':collectivity')
                    );
                    $qb->addOrderBy('v.deletedAt', Criteria::ASC);
                    $qb->addOrderBy('v.createdAt', Criteria::DESC);
                    $qb->setParameters([
                        'collectivity' => $collectivity,
                    ]);

                    return $qb;
                },
                'choice_label'  => function (Model\Violation $object) {
                    return $this->formatArchivedObjectLabel($object);
                },
                'attr'          => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'placeholder.multiple_select_violation',
                ],
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
            ])
        ;

        if ($collectivity->isHasModuleTools()) {
            $builder->add('tools', EntityType::class, [
                'label'         => 'registry.treatment.form.tools',
                'class'         => Tool::class,
                'required'      => false,
                'multiple'      => true,
                'expanded'      => false,
                'query_builder' => function (EntityRepository $er) use ($proof) {
                    $collectivity = null;
                    if (!\is_null($proof->getCollectivity())) {
                        $collectivity = $proof->getCollectivity();
                    } else {
                        /** @var User $authenticatedUser */
                        $authenticatedUser = $this->security->getUser();
                        $collectivity      = $authenticatedUser->getCollectivity();
                    }

                    return $er->createQueryBuilder('c')
                        ->where('c.collectivity = :collectivity')
                        ->addOrderBy('c.name', 'asc')
                        ->setParameter('collectivity', $collectivity)
                    ;
                },
                'attr'          => [
                    'class'            => 'selectpicker',
                    'data-live-search' => 'true',
                    'title'            => 'placeholder.multiple_select',
                ],
            ]);
        }
    }

    /**
     * Prefix every inactive object with "Inactif".
     */
    protected function formatInactiveObjectLabel($object): string
    {
        if (!\method_exists($object, '__toString')) {
            throw new \RuntimeException('The object ' . \get_class($object) . ' must implement __toString() method');
        }

        if (\method_exists($object, 'isActive') && !$object->isActive()) {
            return '(Inactif) ' . $object->__toString();
        }

        return $object->__toString();
    }

    /**
     * Prefix every archived object with "Archivé".
     */
    protected function formatArchivedObjectLabel($object): string
    {
        if (!\method_exists($object, '__toString')) {
            throw new \RuntimeException('The object ' . \get_class($object) . ' must implement __toString() method');
        }

        if (\method_exists($object, 'getDeletedAt') && null !== $object->getDeletedAt()) {
            return '(Archivé) ' . $object->__toString();
        }

        return $object->__toString();
    }

    /**
     * Provide type options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class'        => Model\Proof::class,
                'validation_groups' => ['default', 'proof'],
            ]);
    }
}
