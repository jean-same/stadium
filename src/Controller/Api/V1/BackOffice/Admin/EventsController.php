<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use App\Service\Admin\AssociationServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/api/v1/backoffice/admin/association/events", name="api_v1_backoffice_admin_association_events")
 */
class EventsController extends AbstractController
{
    protected $associationRepository;
    protected $eventRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;
    protected $associationServices;

    public function __construct(ValidatorInterface $validator, AssociationRepository $associationRepository, EventRepository $eventRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager,  AssociationServices $associationServices)
    {
        $this->associationRepository    = $associationRepository;
        $this->eventRepository          = $eventRepository;
        $this->validator                = $validator;
        $this->serializer               = $serializer;
        $this->entityManager            = $entityManager;
        $this->associationServices      = $associationServices;
    }

    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(): Response
    {

        $association = $this->associationServices->getAssocFromUser();

        $events = $association->getEvents();

        return $this->json($events, Response::HTTP_OK, [], ['groups' => "api_backoffice_admin_association_events_browse"]);
    }

    /**
     * @Route("/{eventId}", name="read", methods={"GET"}, requirements={"eventId"="\d+"})
     */
    public function read($eventId): Response
    {
        $event = $this->eventRepository->find($eventId);

        if (is_null($event)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($event);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }

        return $this->json($event, Response::HTTP_OK, [], ['groups' => 'api_backoffice_admin_association_events_browse']);
    }


    /**
     * @Route("/{eventId}", name="edit", methods={"PATCH"}, requirements={"eventId"="\d+"})
     */
    public function edit(int $eventId, Request $request): Response
    {

        $event = $this->eventRepository->find($eventId);

        if (is_null($event)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($event);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }

        $jsonContent = $request->getContent();

        $this->serializer->deserialize($jsonContent, Event::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $event
        ]);

        $errors = $this->validator->validate($event);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Event mis à jour',
            'name' => $event->getName()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        $association = $this->associationServices->getAssocFromUser();
        $jsonContent = $request->getContent();
        $event = $this->serializer->deserialize($jsonContent, Event::class, 'json');
        $event->setAssociation($association);

        $errors = $this->validator->validate($event);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Event créé',
            'name' => $event->getName()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{eventId}", name="delete", methods={"DELETE"}, requirements={"eventId"="\d+"})
     */
    public function delete(int $eventId): Response
    {
        $event = $this->eventRepository->find($eventId);

        if (is_null($event)) {
            return $this->getNotFoundResponse();
        }

        $match = $this->associationServices->checkAssocMatch($event);

        if (!$match) {
            return $this->json("Accès interdit", Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Event supprimé',
            'name' => $event->getName()
        ];

        return $this->json($reponseAsArray);
    }

    private function getNotFoundResponse()
    {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvée',
            'internalMessage' => 'Cet évènement n\'existe pas',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
