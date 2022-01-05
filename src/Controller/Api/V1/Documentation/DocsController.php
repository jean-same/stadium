<?php

namespace App\Controller\Api\V1\Documentation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/docs", name="api_v1_docs_")
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
     * @Route("/v1/superadmin", name="superadmin")
     */
    public function superadmin(): Response
    {
        return $this->render('api/v1/documentation/docs/superadmin.html.twig', [
            'controller_name' => 'DocsController',
        ]);
    }

    /**
     * @Route("/v1/admin", name="admin")
     */
    public function admin(): Response
    {
        return $this->render('api/v1/documentation/docs/admin.html.twig', [
            'controller_name' => 'DocsController',
        ]);
    }

    /**
     * @Route("/v1/member", name="member")
     */
    public function member(): Response
    {
        return $this->render('api/v1/documentation/docs/member.html.twig', [
            'controller_name' => 'DocsController',
        ]);
    }
}
