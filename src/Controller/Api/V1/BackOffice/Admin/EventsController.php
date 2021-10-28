<?php

namespace App\Controller\Api\V1\BackOffice\Admin;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/api/v1/backoffice/admin/association/{associationId}/events", name="api_v1_backoffice_admin_association_events")
 */
class EventsController extends AbstractController
{
    protected $associationRepository;
    protected $eventRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;

    public function __construct(ValidatorInterface $validator, AssociationRepository $associationRepository, EventRepository $eventRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->associationRepository    = $associationRepository;
        $this->eventRepository          = $eventRepository;
        $this->validator                = $validator;
        $this->serializer               = $serializer;
        $this->entityManager            = $entityManager;
    }

    /**
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse($associationId): Response
    {

        $association = $this->associationRepository->find($associationId);

        $events = $association->getEvents();

        return $this->json($events, Response::HTTP_OK, [], ['groups' => "api_backoffice_admin_association_events"]);
    }

    /**
    * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
    */
    public function read($id): Response
    {
        $event = $this->eventRepository->find($id);

        if (is_null($event)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($event, Response::HTTP_OK, [], ['groups' => 'api_backoffice_admin_association_events']);
    }


    /**
    * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
    */
    public function edit(int $id, Request $request): Response
    {
        
        $event = $this->eventRepository->find($id);

        if (is_null($event)) {
            return $this->getNotFoundResponse();
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
        $jsonContent = $request->getContent();
        $event = $this->serializer->deserialize($jsonContent, Event::class, 'json');

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
    * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
    */
    public function delete(int $id): Response
    {
        $event = $this->eventRepository->find($id);

        if (is_null($event)) {
            return $this->getNotFoundResponse();
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'Event supprimé',
            'name' => $event->getName()
        ];

        return $this->json($reponseAsArray);
    }


    private function getNotFoundResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Cette activité n\'existe pas',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
