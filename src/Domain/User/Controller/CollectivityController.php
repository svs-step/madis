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

namespace App\Domain\User\Controller;

use App\Domain\User\Form\Type\CollectivityType;
use App\Domain\User\Model\Collectivity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectivityController extends Controller
{
    public function listAction(): Response
    {
        $objects = $this->getDoctrine()->getRepository(Collectivity::class)->findAll();

        return $this->render('User/Collectivity/list.html.twig', [
            'objects' => $objects,
        ]);
    }

    public function createAction(Request $request): Response
    {
        $object = new Collectivity();
        $form   = $this->createForm(CollectivityType::class, $object);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            return $this->redirectToRoute('user_collectivity_list');
        }

        return $this->render('User/Collectivity/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editAction(Request $request, string $id): Response
    {
        $object = $this->getDoctrine()->getRepository(Collectivity::class)->find($id);
        $form   = $this->createForm(CollectivityType::class, $object);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            return $this->redirectToRoute('user_collectivity_list');
        }

        return $this->render('User/Collectivity/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
