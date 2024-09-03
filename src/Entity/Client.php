<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: "App\Repository\ClientRepository")]
class Client
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $zip = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $mail = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $phone = null;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'IdClient', orphanRemoval: true)]
    private Collection $idProjectForClient;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'IdClient')]
    private Collection $IdUser;

    public function __construct()
    {
        $this->idProjectForClient = new ArrayCollection();
        $this->IdUser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;
        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getIdProjectForClient(): Collection
    {
        return $this->idProjectForClient;
    }

    public function addIdProjectForClient(Project $idProjectForClient): self
    {
        if (!$this->idProjectForClient->contains($idProjectForClient)) {
            $this->idProjectForClient[] = $idProjectForClient;
            $idProjectForClient->setIdClient($this);
        }

        return $this;
    }

    public function removeIdProjectForClient(Project $idProjectForClient): self
    {
        if ($this->idProjectForClient->contains($idProjectForClient)) {
            $this->idProjectForClient->removeElement($idProjectForClient);
            if ($idProjectForClient->getIdClient() === $this) {
                $idProjectForClient->setIdClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getIdUser(): Collection
    {
        return $this->IdUser;
    }

    public function addIdUser(User $idUser): self
    {
        if (!$this->IdUser->contains($idUser)) {
            $this->IdUser[] = $idUser;
        }

        return $this;
    }

    public function removeIdUser(User $idUser): self
    {
        if ($this->IdUser->contains($idUser)) {
            $this->IdUser->removeElement($idUser);
        }

        return $this;
    }
}
