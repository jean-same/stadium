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
    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(ActivityRepository $activityRepository): Response
    {
        $allActivities = $activityRepository->findAll();

        //dd($allActivities);
        return $this->json($allActivities, Response::HTTP_OK, [], ['groups' => 'api_backoffice_superadmin_activities_browse']);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id, ActivityRepository $activityRepository): Response
    {
        $activity = $activityRepository->find($id);

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
    public function edit(int $id, ActivityRepository $activityRepository, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
    {
        $activity = $activityRepository->find($id);

        if (is_null($activity)) {
            return $this->getNotFoundResponse();
        }

        // Retrieving the client's JSON
        $jsonContent = $request->getContent();

        $serializer->deserialize($jsonContent, Activity::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $activity]);

        $errors = $validator->validate($activity);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->flush();

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
    public function add(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, AssociationRepository $associationRepository): Response
    {
        $jsonContent = $request->getContent();

        $activity = $serializer->deserialize($jsonContent, Activity::class, 'json');

        // $associationId = json_decode($jsonContent)->associationId;
        // $association = $associationRepository->find($associationId);
        //dd($association);
        //$activity->setAssociation($association);


        $errors = $validator->validate($activity);

        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($activity);
        $entityManager->flush();

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
    public function delete(int $id, ActivityRepository $activityRepository, EntityManagerInterface $entityManager): Response
    {
        $activity = $activityRepository->find($id);

        if (is_null($activity)) {
            return $this->getNotFoundResponse();
        }

        $entityManager->remove($activity);
        $entityManager->flush();

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
