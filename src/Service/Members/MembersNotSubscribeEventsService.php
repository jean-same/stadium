<?php

namespace App\Service\Members;

use App\Repository\EventRepository;
use Doctrine\ORM\EntityNotFoundException;

class MembersNotSubscribeEventsService
{
    private $eventsRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventsRepository = $eventRepository;
    }

    public function getEventsNotSubsribedByConnecteduser($profile)
    {
        $eventNotSubscribedByTheProfile = [];
        $profileEventsId = [];
        $profileAssociationEventsId = [];

        if ($profile->getEvent()) {
            foreach ($profile->getEvent() as $profileEvent) {
                $profileEventsId[] = $profileEvent->getId();
            }
        }

        $association = $profile->getAssociation();

        if ($association == null) {
            //throw new EntityNotFoundException("Cet adherent fait parti d'aucune association");
            return null;
        } else {
            if ($profile->getAssociation()->getEvents()) {
                foreach ($profile->getAssociation()->getEvents() as $event) {
                    $profileAssociationEventsId[] = $event->GetId();
                }
            }
        }


        foreach ($profileAssociationEventsId as $id) {
            if (!in_array($id, $profileEventsId)) {
                $eventNotSubscribedByTheProfile[] = $this->eventsRepository->find($id);
            }
        }

        return $eventNotSubscribedByTheProfile;
    }
}
