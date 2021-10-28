<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LessonRepository;
use Symfony\Component\Serializer\Annotation\Groups;

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
     *          "api_backoffice_superadmin_activities_browse"
     *      }
     * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_activities_browse"
     *      }
     * )
     */
    private $level;

    /**
     * @ORM\Column(type="time")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_activities_browse"
     *      }
     * )
     */
    private $startTime;

    /**
     * @ORM\Column(type="time")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_activities_browse"
     *      }
     * )
     */
    private $endTime;

    /**
     * @ORM\Column(type="integer")
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_activities_browse"
     *      }
     * )
     */
    private $day;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse",
     *          "api_backoffice_superadmin_activities_browse"
     *      }
     * )
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity=Activity::class, inversedBy="lessons")
     * @ORM\JoinColumn(nullable=false)
     * @Groups(
     *      {
     *          "api_backoffice_superadmin_lessons_browse"
     *      }
     * )
     */
    private $activity;

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

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
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
}
