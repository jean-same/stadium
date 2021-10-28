<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FileRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_backoffice_superadmin_files_browse" , "api_backoffice_superadmin_profiles_browse" })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"api_backoffice_superadmin_files_browse" , "api_backoffice_superadmin_profiles_browse" })
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="date")
     * @Groups({"api_backoffice_superadmin_files_browse" , "api_backoffice_superadmin_profiles_browse" })
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api_backoffice_superadmin_files_browse" , "api_backoffice_superadmin_profiles_browse" })
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api_backoffice_superadmin_files_browse" , "api_backoffice_superadmin_profiles_browse" })
     */
    private $emergencyContactName;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"api_backoffice_superadmin_files_browse" , "api_backoffice_superadmin_profiles_browse" })
     */
    private $emergencyContactPhoneNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api_backoffice_superadmin_files_browse" , "api_backoffice_superadmin_profiles_browse" })
     */
    private $medicalCertificate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api_backoffice_superadmin_files_browse" , "api_backoffice_superadmin_profiles_browse" })
     */
    private $rulesOfProcedure;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"api_backoffice_superadmin_files_browse" , "api_backoffice_superadmin_profiles_browse" })
     */
    private $isPaid;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"api_backoffice_superadmin_files_browse" , "api_backoffice_superadmin_profiles_browse" })
     */
    private $isValid;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"api_backoffice_superadmin_files_browse" , "api_backoffice_superadmin_profiles_browse" })
     */
    private $isComplete;

    /**
     * @ORM\OneToOne(targetEntity=Profil::class, mappedBy="file", cascade={"persist", "remove"})
     */
    private $profil;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

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

    public function getEmergencyContactName(): ?string
    {
        return $this->emergencyContactName;
    }

    public function setEmergencyContactName(string $emergencyContactName): self
    {
        $this->emergencyContactName = $emergencyContactName;

        return $this;
    }

    public function getEmergencyContactPhoneNumber(): ?string
    {
        return $this->emergencyContactPhoneNumber;
    }

    public function setEmergencyContactPhoneNumber(string $emergencyContactPhoneNumber): self
    {
        $this->emergencyContactPhoneNumber = $emergencyContactPhoneNumber;

        return $this;
    }

    public function getMedicalCertificate(): ?string
    {
        return $this->medicalCertificate;
    }

    public function setMedicalCertificate(?string $medicalCertificate): self
    {
        $this->medicalCertificate = $medicalCertificate;

        return $this;
    }

    public function getRulesOfProcedure(): ?string
    {
        return $this->rulesOfProcedure;
    }

    public function setRulesOfProcedure(?string $rulesOfProcedure): self
    {
        $this->rulesOfProcedure = $rulesOfProcedure;

        return $this;
    }

    public function getIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getIsComplete(): ?bool
    {
        return $this->isComplete;
    }

    public function setIsComplete(bool $isComplete): self
    {
        $this->isComplete = $isComplete;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        // unset the owning side of the relation if necessary
        if ($profil === null && $this->profil !== null) {
            $this->profil->setFile(null);
        }

        // set the owning side of the relation if necessary
        if ($profil !== null && $profil->getFile() !== $this) {
            $profil->setFile($this);
        }

        $this->profil = $profil;

        return $this;
    }
}
