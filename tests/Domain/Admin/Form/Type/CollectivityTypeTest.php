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

namespace App\Tests\Domain\Admin\Form\Type;

use App\Domain\Admin\Form\Type\CollectivityType;
use App\Tests\Utils\FormTypeHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CollectivityTypeTest extends FormTypeHelper
{
    public function testInstanceOf()
    {
        $this->assertInstanceOf(AbstractType::class, new CollectivityType());
    }

    public function testBuildForm()
    {
        $builder = [
            'name' => TextType::class,
        ];

        (new CollectivityType())->buildForm($this->prophesizeBuilder($builder), []);
    }
}
