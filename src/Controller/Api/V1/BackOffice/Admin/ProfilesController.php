<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use App\Repository\AssociationRepository;
use App\Repository\ProfilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/backoffice/admin/association/{associationId}", name="api_v1_backoffice_admin_profiles_")
 */
class ProfilesController extends AbstractController
{
    /**
     * @Route("/profiles", name="browse")
     */
    public function browse(AssociationRepository $associationRepository, $associationId): Response
    {
        $association = $associationRepository->find($associationId);

        $profiles = $association->getProfils();
        //dd($activities);

        return $this->json($profiles, Response::HTTP_OK, [], ['groups'=>'api_backoffice_admin_profiles_browse']);
    }

    /**
     * @Route("/profiles/{profilId}", name="read", methods={"GET"})
     */
    public function read(AssociationRepository $associationRepository, $associationId, int $profilId, ProfilRepository $profilRepository)
    {

        $association = $associationRepository->find($associationId);
        $profil = $profilRepository->find($profilId);

        $profiles = $association->getProfils();
        if($profiles->getId() == $profilId){
            return $this->json($profil, Response::HTTP_OK, [], ['groups'=>'api_backoffice_admin_profiles_browse']);
        }
        

    }

    /**
     * @Route("/profiles/{profilId}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(AssociationRepository $associationRepository, int $associationId)
    {
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(AssociationRepository $associationRepository, int $associationId)
    {
    }

    /**
     * @Route("/profiles/{profilId}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete()
    {
    }
}

