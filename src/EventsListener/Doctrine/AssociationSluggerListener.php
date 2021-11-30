<?php

namespace App\EventsListener\Doctrine;

use App\Entity\Association;
use Symfony\Component\String\Slugger\SluggerInterface;

class AssociationSluggerListener
{

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Association $association)
    {
        $association->setSlug($this->slugger->slug(strtolower($association->getName())));
    }

    public function preUpdate(Association $association)
    {
        $association->setSlug($this->slugger->slug(strtolower($association->getName())));
    }
}
