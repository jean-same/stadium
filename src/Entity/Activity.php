<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ActivityRepository::class)
 */
class Activity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_profiles_browse" , 
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_admin_association_activities_browse"
     *          
     *      }
     * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_profiles_browse" , 
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_admin_association_activities_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le nom de l'activité est obligatoire.")
     * @Assert\Length(min=5)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_superadmin_profiles_browse" , 
     *          "api_backoffice_superadmin_lessons_browse",
                "api_backoffice_superadmin_associations_browse",
                "api_backoffice_admin_association_activities_browse"
     *      }
     * )
     */
    private $picture;

    /**
     * @ORM\ManyToMany(targetEntity=Profil::class, mappedBy="activity")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_activities_browse",
                "api_backoffice_admin_association_activities_browse"
     *      }
     * )
     */
    private $profiles;

    /**
     * @ORM\ManyToOne(targetEntity=Association::class, inversedBy="activities")
     * @ORM\JoinColumn(nullable=false)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_activities_browse",
                "api_backoffice_admin_association_activities_browse"
     *      }
     * )
     */
    private $association;

    /**
     * @ORM\OneToMany(targetEntity=Lesson::class, mappedBy="activity", orphanRemoval=true, fetch="EAGER")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_activities_browse",
                "api_backoffice_admin_association_activities_browse"
     *      }
     * )
     */
    private $lessons;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
        $this->lessons = new ArrayCollection();
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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

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
            $profile->addActivity($this);
        }

        return $this;
    }

    public function removeProfile(Profil $profile): self
    {
        if ($this->profiles->removeElement($profile)) {
            $profile->removeActivity($this);
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

    /**
     * @return Collection|Lesson[]
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
            $lesson->setActivity($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->removeElement($lesson)) {
            // set the owning side to null (unless already changed)
            if ($lesson->getActivity() === $this) {
                $lesson->setActivity(null);
            }
        }

        return $this;
    }
}
