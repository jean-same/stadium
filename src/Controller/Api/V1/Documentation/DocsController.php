<?php

namespace App\Controller\Api\V1\Documentation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/docs", name="api_v1_docs")
 */
class DocsController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('api/v1/documentation/docs/home.html.twig', [
            'controller_name' => 'DocsController',
        ]);
    }

    /**
     * @Route("/superadmin", name="superadmin")
     */
    public function superadmin(): Response
    {
        return $this->render('api/v1/documentation/docs/superadmin.html.twig', [
            'controller_name' => 'DocsController',
        ]);
    }

    /**
     * @Route("/", name="admin")
     */
    public function admin(): Response
    {
        return $this->render('api/v1/documentation/docs/index.html.twig', [
            'controller_name' => 'DocsController',
        ]);
    }

    /**
     * @Route("/", name="member")
     */
    public function member(): Response
    {
        return $this->render('api/v1/documentation/docs/index.html.twig', [
            'controller_name' => 'DocsController',
        ]);
    }
}
