<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("api_backoffice_superadmin_events_browse")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("api_backoffice_superadmin_events_browse")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("api_backoffice_superadmin_events_browse")
     */
    private $place;

    /**
     * @ORM\Column(type="date")
     * @Groups("api_backoffice_superadmin_events_browse")
     */
    private $startDate;

    /**
     * @ORM\Column(type="date")
     * @Groups("event_browse")
     */
    private $endDate;

    /**
     * @ORM\Column(type="time")
     * @Groups("api_backoffice_superadmin_events_browse")
     */
    private $schedule;

    /**
     * @ORM\Column(type="integer")
     * @Groups("api_backoffice_superadmin_events_browse")
     */
    private $maxParticipants;

    /**
     * @ORM\ManyToMany(targetEntity=Profil::class, mappedBy="event")
     */
    private $profiles;

    /**
     * @ORM\ManyToOne(targetEntity=Association::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $association;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getSchedule(): ?\DateTimeInterface
    {
        return $this->schedule;
    }

    public function setSchedule(\DateTimeInterface $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(int $maxParticipants): self
    {
        $this->maxParticipants = $maxParticipants;

        return $this;
    }

    /**
     * @return Collection|Profil[]
     */
    public function getProfiles(): Collection
    {
        return $this->profiles;
    }

    public function addProfile(Profil $profile): self
    {
        if (!$this->profiles->contains($profile)) {
            $this->profiles[] = $profile;
            $profile->addEvent($this);
        }

        return $this;
    }

    public function removeProfile(Profil $profile): self
    {
        if ($this->profiles->removeElement($profile)) {
            $profile->removeEvent($this);
        }

        return $this;
    }

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): self
    {
        $this->association = $association;

        return $this;
    }
}
