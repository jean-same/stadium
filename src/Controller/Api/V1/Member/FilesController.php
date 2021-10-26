<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilesController extends AbstractController
{
    /**
     * @Route("/api/v1/member/files", name="api_v1_member_files")
     */
    public function index(): Response
    {
        return $this->render('api/v1/member/files/index.html.twig', [
            'controller_name' => 'FilesController',
        ]);
    }
}
