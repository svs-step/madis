<?php

/**
 * This file is part of the SOLURIS - RGPD Management application.
 *
 * (c) Donovan Bourlard <donovan.bourlard@outlook.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Domain\User\Form\Type;

use App\Domain\User\Form\DataTransformer\RoleTransformer;
use App\Domain\User\Model\Collectivity;
use Knp\DictionaryBundle\Form\Type\DictionaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label'    => 'user.user.form.first_name',
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label'    => 'user.user.form.last_name',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label'    => 'user.user.form.email',
                'required' => true,
            ])
            ->add('collectivity', EntityType::class, [
                'class'    => Collectivity::class,
                'label'    => 'user.user.form.collectivity',
                'required' => true,
            ])
            ->add('roles', DictionaryType::class, [
                'label'    => 'user.user.form.roles',
                'required' => true,
                'name'     => 'user_user_role',
                'multiple' => false,
                'expanded' => true,
            ])
        ;

        $builder
            ->get('roles')
            ->addModelTransformer(new RoleTransformer())
        ;
    }
}
