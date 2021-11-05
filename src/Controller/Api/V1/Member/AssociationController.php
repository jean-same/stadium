<?php

namespace App\Controller\Api\V1\Member;

use App\Repository\AssociationRepository;
use App\Repository\ProfilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/member/profil/{profilId}/association", name="api_v1_member_account_profil_association")
 */
class AssociationController extends AbstractController
{

    private $profilRepository;
    private $associationRepository;

    public function __construct( AssociationRepository $associationRepository, ProfilRepository $profilRepository )
    {
        $this->associationRepository = $associationRepository;
        $this->profilRepository = $profilRepository;
    }
    /**
     * @Route("/", name="read", methods={"GET"})
     */
    public function read($profilId): Response
    {

        $profil = $this->profilRepository->find($profilId);

        $this->denyAccessUnlessGranted('CAN_READ', $profil , "Accès interdit");
        $association = $profil->getAssociation();

        return $this->json($association, Response::HTTP_OK, [], ['groups' => 'api_member_association_browse']);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(): Response
    {
        return $this->render('api/v1/member/association/index.html.twig', [
            'controller_name' => 'AssociationController',
        ]);
    }
}
