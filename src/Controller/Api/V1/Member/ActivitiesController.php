<?php

namespace App\Controller\Api\V1\Member;

use App\Repository\ActivityRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/member/profil/{profilId}/activities", name="api_v1_member_account_profil_activities")
 */
class ActivitiesController extends AbstractController
{
    private $profilRepository;
    private $activityRepository;
    private $serializer;
    private $validator;
    private $entityManager;

    public function __construct(ProfilRepository $profilRepository, ActivityRepository $activityRepository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->profilRepository = $profilRepository;
        $this->activityRepository = $activityRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse($profilId): Response
    {
        $profil = $this->profilRepository->find($profilId);

        $this->denyAccessUnlessGranted('CAN_READ', $profil, "Accès interdit");
        $activities = $profil->getActivity();

        return $this->json($activities, Response::HTTP_OK, [], ['groups' => "api_member_activities_browse"]);
    }

    /**
     * @Route("/{activityId}/register", name="register", methods={"POST"})
     */
    public function register($profilId, $activityId):Response
    {
        $profil = $this->profilRepository->find($profilId);
        $activity = $this->activityRepository->find($activityId);

        $this->denyAccessUnlessGranted('CAN_READ', $profil, "Accès interdit");

        $profil->addActivity($activity);

        $this->entityManager->flush();
        
        $responseAsArray = [
            'message' => 'Inscription prise en compte',
            'name' => $activity->getName(),
        ];

        return $this->json($responseAsArray,Response::HTTP_OK);
    }

    /**
     * @Route("/{activityId}/unregister", name="unregister", methods={"POST"})
     */
    public function unregister($profilId, $activityId):Response
    {
        $profil = $this->profilRepository->find($profilId);
        $activity = $this->activityRepository->find($activityId);

        $this->denyAccessUnlessGranted('CAN_READ', $profil, "Accès interdit");

        $profil->removeActivity($activity);

        $this->entityManager->flush();
        
        $responseAsArray = [
            'message' => 'Désincription prise en compte',
            'name' => $activity->getName(),
        ];

        return $this->json($responseAsArray,Response::HTTP_OK);
    }
}
