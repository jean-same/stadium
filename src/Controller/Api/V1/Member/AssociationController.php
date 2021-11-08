<?php

namespace App\Controller\Api\V1\Member;

use App\Entity\Association;
use App\Repository\AssociationRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/member/profil/{profilId}/association", name="api_v1_member_account_profil_association")
 */
class AssociationController extends AbstractController
{

    private $profilRepository;
    private $associationRepository;
    private $serializer;
    private $validator;
    private $entityManager;

    public function __construct(AssociationRepository $associationRepository, ProfilRepository $profilRepository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->associationRepository = $associationRepository;
        $this->profilRepository = $profilRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/", name="read", methods={"GET"})
     */
    public function read($profilId): Response
    {

        $profil = $this->profilRepository->find($profilId);

        $this->denyAccessUnlessGranted('CAN_READ', $profil, "Accès interdit");
        $association = $profil->getAssociation();

        return $this->json($association, Response::HTTP_OK, [], ['groups' => 'api_member_association_browse']);
    }

    /**
     * @Route("/{associationId}/register", name="register", methods={"POST"})
     */
    public function register($profilId, $associationId): Response
    {
        $profil = $this->profilRepository->find($profilId);
        $association = $this->associationRepository->find($associationId);

        $this->denyAccessUnlessGranted('CAN_READ', $profil, "Accès interdit");

        if ($profil->getAssociation() == null) {
            $profil->setAssociation($association);
            $this->entityManager->flush();
        }
        else {
            $nameAsso = "Vous êtes déjà incrit dans l'association : " . $profil->getAssociation()->getName();
            return $this->json($nameAsso, Response::HTTP_FORBIDDEN);
        }

        $responseAsArray = [
            'message' => 'Association ajoutée',
            'name' => $association->getName(),
        ];

        return $this->json($responseAsArray, Response::HTTP_OK);
    }
}
