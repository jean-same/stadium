<?php

namespace App\EventsListener\Doctrine;

use App\Entity\Profil;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilSluggerListener
{

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Profil $profil)
    {
        $profil->setSlug($this->slugger->slug(strtolower($profil->getLastName()) . '-' . strtolower($profil->getFirstName())));
    }

    public function preUpdate(Profil $profil)
    {
        $profil->setSlug($this->slugger->slug(strtolower($profil->getLastName()) . '-' . strtolower($profil->getFirstName())));
    }
}
