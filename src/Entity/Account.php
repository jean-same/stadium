<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Account implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups(
     *       {
     *          "api_backoffice_superadmin_accounts_browse" , 
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_member_account_read"
     *      }
     * )
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups(
     *       {
     *          "api_backoffice_superadmin_accounts_browse" , 
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_member_account_read"
     *      }
     * )
     * @Assert\Email(
     *         message = "The email '{{ value}}' is not a valid email.")
     * @Assert\NotBlank(message="L'email est obligatoire.")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups(
     *       {
     *          "api_backoffice_superadmin_accounts_browse" , 
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_member_account_read"
     *      }
     * )
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups(
     *       {
     *          "api_backoffice_superadmin_accounts_browse" , 
     *          "api_backoffice_superadmin_profiles_browse",
     *          "api_backoffice_superadmin_associations_browse",
     *          "api_member_account_read"
     *      }
     * )
     * @Assert\Length(min=8, minMessage="Le mot de passe doit faire 8 caractères minimum")
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity=Association::class, inversedBy="account", cascade={"persist", "remove"})
     * @Groups(
     *       {
     *          "api_backoffice_superadmin_accounts_browse" 
     *      }
     * )
     */
    private $association;

    /**
     * @ORM\OneToMany(targetEntity=Profil::class, mappedBy="account", orphanRemoval=true , fetch="EAGER")
     * @Groups(
     *       {
     *          "api_backoffice_superadmin_accounts_browse" 
     *      }
     * )
     */
    private $profil;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $joinedUsAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    public function __construct()
    {
        $this->profil = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
     * @return Collection|Profil[]
     */
    public function getProfil(): Collection
    {
        return $this->profil;
    }

    public function addProfil(Profil $profil): self
    {
        if (!$this->profil->contains($profil)) {
            $this->profil[] = $profil;
            $profil->setAccount($this);
        }

        return $this;
    }

    public function removeProfil(Profil $profil): self
    {
        if ($this->profil->removeElement($profil)) {
            // set the owning side to null (unless already changed)
            if ($profil->getAccount() === $this) {
                $profil->setAccount(null);
            }
        }

        return $this;
    }

    public function getJoinedUsAt(): ?\DateTimeImmutable
    {
        return $this->joinedUsAt;
    }

    public function setJoinedUsAt(?\DateTimeImmutable $joinedUsAt): self
    {
        $this->joinedUsAt = $joinedUsAt;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
