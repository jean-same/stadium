<?php

namespace App\Controller\Api\V1\BackOffice\SuperAdmin;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use App\Repository\AssociationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

//* BREAD Activities

/**
 * @Route("/api/v1/backoffice/superadmin/activities", name="api_v1_backoffice_superadmin_activities_")
 */

class ActivitiesController extends AbstractController
{
    protected $activityRepository;
    protected $serializer;
    protected $validator;
    protected $entityManager;

    public function __construct(ActivityRepository $activityRepository, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->activityRepository = $activityRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/{order}", name="browse", methods={"GET"},priority=-1)
     */
    public function browse($order = "asc"): Response
    {
        $allActivities = $this->activityRepository->findBy([] , [ "name" => $order ]);

        return $this->json($allActivities, Response::HTTP_OK, [], ['groups' => 'api_backoffice_superadmin_activities_browse']);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id): Response
    {
        $activity = $this->activityRepository->find($id);

        // If an activity is null, then we return an error message in JSON with the method getNotFoundResponse
        if (is_null($activity)) {
            return $this->getNotFoundResponse();
        }
        // Else we display the answer found
        return $this->json($activity, Response::HTTP_OK, [], ['groups' => 'api_backoffice_superadmin_activities_browse']);
    }

    /**
     * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(int $id, Request $request): Response
    {
        $activity = $this->activityRepository->find($id);

        if (is_null($activity)) {
            return $this->getNotFoundResponse();
        }

        // Retrieving the client's JSON
        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Activity::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $activity]);

        $errors = $this->validator->validate($activity);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Activité mise à jour',
            'name' => $activity->getName(),
            'picture' => $activity->getPicture(),
        ];
        return $this->json($responseAsArray, Response::HTTP_OK);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $jsonContent = $request->getContent();

        $activity = $this->serializer->deserialize($jsonContent, Activity::class, 'json');

        $errors = $this->validator->validate($activity);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($activity);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Activité crée',
            'name' => $activity->getName(),
            'picture' => $activity->getPicture(),
        ];
        return $this->json($responseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
    public function delete(int $id): Response
    {
        $activity = $this->activityRepository->find($id);

        if (is_null($activity)) {
            return $this->getNotFoundResponse();
        }

        $this->entityManager->remove($activity);
        $this->entityManager->flush();

        $responseAsArray = [
            'message' => 'Activité supprimée',
            'name' => $activity->getName()
        ];
        return $this->json($responseAsArray);
    }

    private function getNotFoundResponse()
    {
        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvée',
            'internalMessage' => 'Cette activitée n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
