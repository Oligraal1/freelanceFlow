<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\ProjectRepository")]
class Project
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $startDate;

    #[ORM\Column(type: 'float')]
    private float $price;

    #[ORM\OneToMany(targetEntity: "App\Entity\Tasks", mappedBy: "idTask", orphanRemoval: true)]
    private Collection $idProject;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Client", inversedBy: "idProjectForClient")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $IdClient = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $forfait = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "IdProject")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $IdUser = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    public function __construct()
    {
        $this->idProject = new ArrayCollection();
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

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return Collection<int, Tasks>
     */
    public function getIdProject(): Collection
    {
        return $this->idProject;
    }

    public function addIdProject(Tasks $idProject): self
    {
        if (!$this->idProject->contains($idProject)) {
            $this->idProject[] = $idProject;
            $idProject->setIdTask($this);
        }

        return $this;
    }

    public function removeIdProject(Tasks $idProject): self
    {
        if ($this->idProject->contains($idProject)) {
            $this->idProject->removeElement($idProject);
            if ($idProject->getIdTask() === $this) {
                $idProject->setIdTask(null);
            }
        }

        return $this;
    }

    public function getIdClient(): ?Client
    {
        return $this->IdClient;
    }

    public function setIdClient(?Client $IdClient): self
    {
        $this->IdClient = $IdClient;
        return $this;
    }

    public function getForfait(): ?int
    {
        return $this->forfait;
    }

    public function setForfait(?int $forfait): self
    {
        $this->forfait = $forfait;
        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->IdUser;
    }

    public function setIdUser(?User $IdUser): self
    {
        $this->IdUser = $IdUser;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }
}
