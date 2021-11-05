<?php

namespace App\Controller\Api\V1\BackOffice\SuperAdmin;

use App\Entity\Event;
use App\Repository\AssociationRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    
/**
* @Route("/api/v1/backoffice/superadmin/events", name="api_v1_backoffice_superadmin_events")
*/
class EventsController extends AbstractController
{

    protected $eventsRepository;
    protected $validator;
    protected $serializer;
    protected $entityManager;
    protected $associationRepository;

    public function __construct(ValidatorInterface $validator, EventRepository $eventRepository, AssociationRepository $associationRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $this->eventRepository = $eventRepository;
        $this->associationRepository = $associationRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    /**
    * @Route("/{order}", name="browse" , methods={"GET"}, priority=-1)
    */
    public function browse(string $order = "asc"): Response
    {
        $events = $this->eventRepository->findBy([],["startDate"=>$order]);

        return $this->json($events, Response::HTTP_OK, [], ['groups' => "api_backoffice_superadmin_events_browse"]);
    }

    /**
    * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
    */
    public function read( int $id): Response
    {
        $event = $this->eventRepository->find($id);

        if (is_null($event)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($event, Response::HTTP_OK, [], ['groups' => 'api_backoffice_superadmin_events_browse']);
    }


    /**
     * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit( int $id, Request $request ): Response
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


        // lancer le flush
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $reponseAsArray = [
            'message' => 'Event mis à jour',
            'name' => $event->getName()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }


    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request ): Response
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
            'internalMessage' => 'Cet evenement n\'existe pas',
        ];

        return $this->json($responseArray, Response::HTTP_GONE);
    }
}
