<?php

declare(strict_types=1);

namespace App\Domain\User\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CollectivityController extends Controller
{
    public function listAction()
    {
        return $this->render('User/Collectivity/list.html.twig');
    }
}
