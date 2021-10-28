<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AssociationRepository::class)
 */
class Association
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("api_backoffice_superadmin_profiles_browse","api_backoffice_superadmin_associations_browse")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups("api_backoffice_superadmin_profiles_browse","api_backoffice_superadmin_associations_browse")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups("api_backoffice_superadmin_profiles_browse","api_backoffice_superadmin_associations_browse")
     */
    private $presidentLastName;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups("api_backoffice_superadmin_profiles_browse","api_backoffice_superadmin_associations_browse")
     */
    private $presidentFirstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("api_backoffice_superadmin_profiles_browse","api_backoffice_superadmin_associations_browse")
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups("api_backoffice_superadmin_profiles_browse","api_backoffice_superadmin_associations_browse")
     */
    private $phoneNumber;

    /**
     * @ORM\OneToOne(targetEntity=Account::class, mappedBy="association", cascade={"persist", "remove"})
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity=Profil::class, mappedBy="association")
     */
    private $profils;

    /**
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="association", orphanRemoval=true)
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="association", orphanRemoval=true)
     */
    private $events;

    public function __construct()
    {
        $this->profils = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->events = new ArrayCollection();
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

    public function getPresidentLastName(): ?string
    {
        return $this->presidentLastName;
    }

    public function setPresidentLastName(string $presidentLastName): self
    {
        $this->presidentLastName = $presidentLastName;

        return $this;
    }

    public function getPresidentFirstName(): ?string
    {
        return $this->presidentFirstName;
    }

    public function setPresidentFirstName(string $presidentFirstName): self
    {
        $this->presidentFirstName = $presidentFirstName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        // unset the owning side of the relation if necessary
        if ($account === null && $this->account !== null) {
            $this->account->setAssociation(null);
        }

        // set the owning side of the relation if necessary
        if ($account !== null && $account->getAssociation() !== $this) {
            $account->setAssociation($this);
        }

        $this->account = $account;

        return $this;
    }

    /**
     * @return Collection|Profil[]
     */
    public function getProfils(): Collection
    {
        return $this->profils;
    }

    public function addProfil(Profil $profil): self
    {
        if (!$this->profils->contains($profil)) {
            $this->profils[] = $profil;
            $profil->setAssociation($this);
        }

        return $this;
    }

    public function removeProfil(Profil $profil): self
    {
        if ($this->profils->removeElement($profil)) {
            // set the owning side to null (unless already changed)
            if ($profil->getAssociation() === $this) {
                $profil->setAssociation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setAssociation($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getAssociation() === $this) {
                $activity->setAssociation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setAssociation($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getAssociation() === $this) {
                $event->setAssociation(null);
            }
        }

        return $this;
    }
}
