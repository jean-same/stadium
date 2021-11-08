<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProfilRepository::class)
 */
class Profil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_profiles_browse" , 
     *          "api_backoffice_superadmin_files_browse",
     *          "api_backoffice_superadmin_accounts_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_admin_association_profiles_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_profiles_browse" , 
     *          "api_backoffice_superadmin_files_browse",
     *          "api_backoffice_superadmin_accounts_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_events_browse",
*               "api_backoffice_admin_association_profiles_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le prenom est obligatoire")
     * @Assert\Length(min=3, minMessage="Le prenom doit faire entre 3 et 255 caracteres", max=255, maxMessage="Le prenom doit faire entre 3 et 255 caracteres")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_profiles_browse" , 
     *          "api_backoffice_superadmin_files_browse",
     *          "api_backoffice_superadmin_accounts_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_admin_association_profiles_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le prenom est obligatoire")
     * @Assert\Length(min=3, minMessage="Le nom doit faire entre 3 et 255 caracteres", max=255, maxMessage="Le nom doit faire entre 3 et 255 caracteres")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_profiles_browse" , 
     *          "api_backoffice_superadmin_files_browse",
     *          "api_backoffice_superadmin_accounts_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_events_browse",
     *          "api_backoffice_admin_association_profiles_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_events_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     */
    private $picture;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="profil", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_profiles_browse"
     *      }
     * )
     */
    private $account;

    /**
     * @ORM\OneToOne(targetEntity=File::class, inversedBy="profil", cascade={"persist", "remove"}, fetch="EAGER")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_admin_association_profiles_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity=Association::class, inversedBy="profils")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     */
    private $association;

    /**
     * @ORM\ManyToMany(targetEntity=Event::class, inversedBy="profiles")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_member_profiles_browse"
     *      }
     * )
     */
    private $event;

    /**
     * @ORM\ManyToMany(targetEntity=Activity::class, inversedBy="profiles", fetch="EAGER")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_member_activities_browse",
     *          "api_member_profiles_browse",
     *      }
     * )
     */
    private $activity;

    /**
     * @ORM\ManyToMany(targetEntity=Lesson::class, inversedBy="profiles" , fetch="EAGER")
     */
    private $lesson;

    public function __construct()
    {
        $this->event = new ArrayCollection();
        $this->activity = new ArrayCollection();
        $this->lesson = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

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

    /**
     * @return Collection|Event[]
     */
    public function getEvent(): Collection
    {
        return $this->event;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->event->contains($event)) {
            $this->event[] = $event;
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        $this->event->removeElement($event);

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getActivity(): Collection
    {
        return $this->activity;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activity->contains($activity)) {
            $this->activity[] = $activity;
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        $this->activity->removeElement($activity);

        return $this;
    }

    /**
     * @return Collection|Lesson[]
     */
    public function getLesson(): Collection
    {
        return $this->lesson;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lesson->contains($lesson)) {
            $this->lesson[] = $lesson;
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        $this->lesson->removeElement($lesson);

        return $this;
    }
}
