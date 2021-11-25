<?php

namespace App\Service\Members;

use App\Repository\ActivityRepository;

class MembersNotSubscribeActivitiesService
{
    private $activityRepository;

    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function getActivitiesNotSubsribedByConnecteduser($profile)
    {
        $activityNotSubscribedByTheProfile = [];
        $profileActivitiesId = [];
        $profileAssociationActivitiesId = [];

        foreach ($profile->getActivity() as $profileActivity) {
            $profileActivitiesId[] = $profileActivity->getId();
        }

        $association = $profile->getAssociation();

        if ($association == null) {
            return null;
        } else {
            foreach ($profile->getAssociation()->getActivities() as $activity) {
                $profileAssociationActivitiesId[] = $activity->GetId();
            }
        }

        foreach ($profileAssociationActivitiesId as $id) {
            if (!in_array($id, $profileActivitiesId)) {
                $activityNotSubscribedByTheProfile[] = $this->activityRepository->find($id);
            }
        }

        return $activityNotSubscribedByTheProfile;
    }
}
