<?php

namespace App\Service\Members;

use App\Repository\EventRepository;
use Symfony\Bundle\MakerBundle\EventRegistry;

class MembersNotSubscribeEventsService
{
    private $eventsRepository;

    public function __construct( EventRepository $eventRepository )
    {
        $this->eventsRepository = $eventRepository;
    }

    public function getEventsNotSubsribedByConnecteduser($profile)
    {
        $eventNotSubscribedByTheProfile = [];
        $profileEventsId = [];
        $profileAssociationEventsId = [];

        foreach ($profile->getEvent() as $profileEvent) {
            $profileEventsId[] = $profileEvent->getId();
        }

        
        foreach ($profile->getAssociation()->getEvents() as $event) {
            $profileAssociationEventsId[] = $event->GetId() ;
        }
        //dd($profileAssociationEventsId);

        foreach($profileAssociationEventsId as $id){
            if( !in_array($id , $profileEventsId) ){
                $eventNotSubscribedByTheProfile[] = $this->eventsRepository->find($id);
            }
        } 

        return $eventNotSubscribedByTheProfile;
    }
}
