<?php

namespace App\Application\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;


class SitemapController extends AbstractController
{

    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(RouterInterface $router) {
        parent::__construct();
        $this->router = $router;
    }
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request) {

        $hostname = $request->getSchemeAndHttpHost();

        $urls = array();
        $urls[] = array('loc' => $this->generateUrl('registry_request_list'));
        $urls[] = array('loc' => $this->generateUrl('registry_violation_list'));
        $urls[] = array('loc' => $this->generateUrl('registry_proof_list'));
        // $this->router->generate('registry_contractor_list_datatables')

        // return response in XML format
        $response = new Response(
            $this->renderView('sitemap.html.twig', ['urls' => $urls, 'hostname' => $hostname]),
            200
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}