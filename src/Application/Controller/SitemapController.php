<?php

namespace App\Controller;

use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Route;
// use Symfony\Component\Routing\RouterInterface;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage', options: ['sitemap' => true])]
    public function indexAction()
    {
        //...
    }

    #[Route('/faq', name: 'faq', options: ['sitemap' => ['priority' => 0.7]])]
    public function faqAction()
    {
        //...
    }

    #[Route('/about', name: 'about', options: ['sitemap' => ['priority' => 0.7, 'changefreq' => UrlConcrete::CHANGEFREQ_WEEKLY]])]
    public function aboutAction()
    {
        //...
    }

    #[Route('/contact', name: 'contact', options: ['sitemap' => ['priority' => 0.7, 'changefreq' => UrlConcrete::CHANGEFREQ_WEEKLY, 'section' => 'misc']])]
    public function contactAction()
    {
        //...
    }
}