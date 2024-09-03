<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec cet email')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'IdUser', orphanRemoval: true)]
    private Collection $IdProject;

    #[ORM\ManyToMany(targetEntity: Client::class, mappedBy: 'IdUser')]
    private Collection $IdClient;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $fistname = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $zip = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $profession = null;

    public function __construct()
    {
        $this->IdProject = new ArrayCollection();
        $this->IdClient = new ArrayCollection();
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

    public function addRoles(string $roles): self
    {
        if (!in_array($roles, $this->roles)) {
            $this->roles[] = $roles;
        }
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getIdProject(): Collection
    {
        return $this->IdProject;
    }

    public function addIdProject(Project $idProject): self
    {
        if (!$this->IdProject->contains($idProject)) {
            $this->IdProject[] = $idProject;
            $idProject->setIdUser($this);
        }
        return $this;
    }

    public function removeIdProject(Project $idProject): self
    {
        if ($this->IdProject->contains($idProject)) {
            $this->IdProject->removeElement($idProject);
            // set the owning side to null (unless already changed)
            if ($idProject->getIdUser() === $this) {
                $idProject->setIdUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getIdClient(): Collection
    {
        return $this->IdClient;
    }

    public function addIdClient(Client $idClient): self
    {
        if (!$this->IdClient->contains($idClient)) {
            $this->IdClient[] = $idClient;
            $idClient->addIdUser($this);
        }
        return $this;
    }

    public function removeIdClient(Client $idClient): self
    {
        if ($this->IdClient->contains($idClient)) {
            $this->IdClient->removeElement($idClient);
            $idClient->removeIdUser($this);
        }
        return $this;
    }

    public function getFistname(): ?string
    {
        return $this->fistname;
    }

    public function setFistname(?string $fistname): self
    {
        $this->fistname = $fistname;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getZip(): ?int
    {
        return $this->zip;
    }

    public function setZip(?int $zip): self
    {
        $this->zip = $zip;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;
        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(?string $profession): self
    {
        $this->profession = $profession;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
