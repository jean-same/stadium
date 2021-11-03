<?php

namespace App\Controller\Api\V1\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/member/account/{accountId}/profil/{profilId}/files", name="api_v1_member_account_profil_files")
 */
class FilesController extends AbstractController
{
    /**
     * @Route("/{fileId}", name="read", methods={"GET"})
     */
    public function read(): Response
    {
        return $this->render('api/v1/member/files/index.html.twig', [
            'controller_name' => 'FilesController',
        ]);
    }

    /**
     * @Route("/{fileId}", name="edit", methods={"PATCH"})
     */
    public function edit(): Response
    {
        return $this->render('api/v1/member/files/index.html.twig', [
            'controller_name' => 'FilesController',
        ]);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(): Response
    {
        return $this->render('api/v1/member/files/index.html.twig', [
            'controller_name' => 'FilesController',
        ]);
    }

    /**
     * @Route("/{fileId}", name="delete", methods={"DELETE"})
     */
    public function delete(): Response
    {
        return $this->render('api/v1/member/files/index.html.twig', [
            'controller_name' => 'FilesController',
        ]);
    }
}
