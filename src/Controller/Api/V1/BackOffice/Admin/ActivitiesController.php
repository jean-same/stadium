<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use App\Entity\Activity;
use App\Repository\AccountRepository;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use App\Service\Admin\AssociationServices;
use App\Service\IconActivity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/api/v1/backoffice/admin/association/activities", name="api_v1_backoffice_admin_association_activities")
 */
class ActivitiesController extends AbstractController
{

    protected $accountRepository;
    protected $associationRepository;
    protected $activityRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;
    protected $associationServices;
    protected $security;

    public function __construct(ValidatorInterface $validator, AccountRepository $accountRepository, AssociationRepository $associationRepository, ActivityRepository $activityRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager, AssociationServices $associationServices, Security $security)
    {
        $this->accountRepository        = $accountRepository;
        $this->associationRepository    = $associationRepository;
        $this->activityRepository       = $activityRepository;
        $this->validator                = $validator;
        $this->serializer               = $serializer;
        $this->entityManager            = $entityManager;
        $this->associationServices      = $associationServices;
        $this->security = $security;
    }

    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(): Response
    {

        $association = $this->associationServices->getAssocFromUser();
        $activities = $association->getActivities();
        return $this->json($activities, Response::HTTP_OK, [], ['groups' => "api_backoffice_admin_association_activities_browse"]);
    }

    /**
     * @Route("/{activityId}", name="read", methods={"GET"}, requirements={"activityId"="\d+"})
     */
    public function read($activityId): Response
    {
        $activity = $this->activityRepository->find($activityId);

        if (is_null($activity)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($activity);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }



        return $this->json($activity, Response::HTTP_OK, [], ['groups' => 'api_backoffice_admin_association_activities_browse']);
    }

    /**
     * @Route("/{activityId}", name="edit", methods={"PATCH"}, requirements={"activityId"="\d+"})
     */
    public function edit(int $activityId, Request $request): Response
    {

        $activity = $this->activityRepository->find($activityId);

        if (is_null($activity)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($activity);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Activity::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $activity
        ]);


        $errors = $this->validator->validate($activity);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Activité mise à jour',
            'name' => $activity->getName()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(Request $request, IconActivity $iconActivity): Response
    {
        $association = $this->associationServices->getAssocFromUser();
        $jsonContent = $request->getContent();
        $activity = $this->serializer->deserialize($jsonContent, Activity::class, 'json');

        $activity->setAssociation($association);


        $errors = $this->validator->validate($activity);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($activity);
        $icon = $iconActivity->getIconForActivity($activity->getName());
        $activity->setIcon($icon);
        
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Activité créé',
            'name' => $activity->getName()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{activityId}", name="delete", methods={"DELETE"}, requirements={"activityId"="\d+"})
     */
    public function delete(int $activityId): Response
    {
        $activity = $this->activityRepository->find($activityId);

        if (is_null($activity)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($activity);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($activity);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Activité supprimée',
            'name' => $activity->getName()
        ];

        return $this->json($reponseAsArray);
    }


    private function getNotFoundResponse()
    {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Cette activité n\'existe pas',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
