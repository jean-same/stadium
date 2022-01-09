<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AssociationRepository::class)
 */
class Association
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse", 
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_superadmin_accounts_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_admin_association_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_association_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse", 
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_superadmin_accounts_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_admin_association_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_association_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le nom de l'association est obligatoire.")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse", 
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_superadmin_accounts_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_admin_association_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_association_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le nom du président(e) est obligatoire.")
     */
    private $presidentLastName;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse", 
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_superadmin_accounts_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_admin_association_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_association_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le prénom du président(e) est obligatoire.")
     */
    private $presidentFirstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse", 
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_superadmin_accounts_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_admin_association_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_association_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="L'adresse de l'association est obligatoire.")
     * @Assert\Length(
     * min=15)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse", 
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_superadmin_accounts_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_admin_association_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_association_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le numéro de téléphone est obligatoire.")
     * @Assert\Length(
     *      min = 10,
     *      max = 10,
     *      minMessage = "Veuillez entrer un numéro de teléphone valide."
     *      )
     */
    private $phoneNumber;

    /**
     * @ORM\OneToOne(targetEntity=Account::class, mappedBy="association", cascade={"persist", "remove"})
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse"
     *      }
     * )
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity=Profil::class, mappedBy="association")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse"
     *      }
     * )
     */
    private $profils;

    /**
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="association", orphanRemoval=true, fetch="EAGER")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse"
     *      }
     * )
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="association", orphanRemoval=true , fetch="EAGER")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse"
     *      }
     * )
     */
    private $events;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_admin_association_browse",
     *          "api_backoffice_superadmin_accounts_browse"
     *      }
     * )
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="La description est obligatoire.")
     * @Assert\Length(
     *      min = 250,
     *      max = 1000,
     *      minMessage = "La description doit faire au moins 250 caracteres.",
     *      maxMessage = "La description doit faire max 1000 caracteres."
     *      )
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="float" , scale=4 , precision=6 , nullable=true)
     * @Assert\NotBlank(message="L'adresse n'est pas valide.")
     */
    private $lat;

    /**
     * @ORM\Column(type="float", scale=4 , precision=7  , nullable=true)
     */
    private $lng;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $postCode;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $city;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getPostCode(): ?int
    {
        return $this->postCode;
    }

    public function setPostCode(?int $postCode): self
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }
}
