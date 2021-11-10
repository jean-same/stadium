<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LessonRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LessonRepository::class)
 */
class Lesson
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_lessons_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_profiles_browse"
     *      }
     * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_lessons_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le niveau est obligatoire")
     */
    private $level;

    /**
     * @ORM\Column(type="time")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_lessons_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="L'heure de début est obligatoire")
     * @Assert\Type(type="\DateTime",
     *              message="Le format de l'heure est incorrect (HH:MM)")
     */
    private $startTime;

    /**
     * @ORM\Column(type="time")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_lessons_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="L'heure de fin est obligatoire")
     * @Assert\Type(type="\DateTime",
     *              message="Le format de la date est incorrect (HH:MM)")
     * @Assert\GreaterThan(propertyPath="startTime", message="L'heure de fin doit être supérieure à l'heure de début")
     */
    private $endTime;

    /**
     * @ORM\Column(type="integer")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_lessons_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le jour est obligatoire")
     */
    private $day;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_activities_browse",
     *          "api_backoffice_admin_association_activities_browse",
     *          "api_backoffice_admin_association_lessons_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_backoffice_superadmin_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="Le lieu est obligatoire")
     * @Assert\Length(min=3, minMessage="Le lieu doit faire entre 3 et 255 caracteres", max=255, maxMessage="Le lieu doit faire entre 3 et 255 caracteres")
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity=Activity::class, inversedBy="lessons")
     * @ORM\JoinColumn(nullable=false)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_profiles_browse"
     *      }
     * )
     * @Assert\NotBlank(message="L'activité est obligatoire")
     */
    private $activity;

    /**
     * @ORM\ManyToMany(targetEntity=Profil::class, mappedBy="lesson")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse"
     *      }
     * )
     */
    private $profiles;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime( $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime($endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

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

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

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
            $profile->addLesson($this);
        }

        return $this;
    }

    public function removeProfile(Profil $profile): self
    {
        if ($this->profiles->removeElement($profile)) {
            $profile->removeLesson($this);
        }

        return $this;
    }
}
