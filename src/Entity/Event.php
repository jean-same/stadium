<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_events_browse"
     *      }
     * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(
     *      {
     *           "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_events_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le nom de l'événement est obligatoire.")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(
     *      {
     *           "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_events_browse"
     *      }
     * )
     * @Assert\NotBlank(message="L'adresse de l'évenement est obligatoire.")
     * @Assert\Length(min=5)
     */
    private $place;

    /**
     * @ORM\Column(type="date")
     * @Groups(
     *      {
     *           "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_events_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     * @Assert\Type(type="\DateTime",
     *              message="Le format de la date n'est pas valide (YYYY-MM-DD)")
     * @Assert\GreaterThan("today", message="La date de début de l'évènement ne peut être antérieure à aujourd'hui")
     */
    private $startDate;

    /**
     * @ORM\Column(type="date")
     * @Groups(
     *      {
     *           "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_events_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     *@Assert\Type("\DateTime",
     *              message="Le format de la date n'est pas valide (YYYY-MM-DD)")
     * @Assert\GreaterThan(propertyPath="startDate",message="La date de fin de l'évènement doit être supérieure à la date de début")
     */
    private $endDate;

    /**
     * @ORM\Column(type="time")
     * @Groups(
     *      {
     *           "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_events_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     * @Assert\Type("\DateTimeInterface",
     *              message="L'heure de début est obligatoire")
     */
    private $schedule;

    /**
     * @ORM\Column(type="integer")
     * @Groups(
     *      {
     *           "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_events_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le nombre de participants est obligatoire.")
     * @Assert\Positive(message="Veuillez entrer un nombre de participants correct")
     */
    private $maxParticipants;

    /**
     * @ORM\ManyToMany(targetEntity=Profil::class, mappedBy="event" , fetch="EAGER")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_admin_association_events_browse"
     *      }
     * )
     */
    private $profiles;

    /**
     * @ORM\ManyToOne(targetEntity=Association::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_events_browse",
     *           "api_backoffice_admin_association_events_browse"
     *      }
     * )
     */
    private $association;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

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

    public function setStartDate($startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate($endDate): self
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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }
}
