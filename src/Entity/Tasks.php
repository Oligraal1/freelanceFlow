<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\TasksRepository")]
class Tasks
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $taskDate;

    #[ORM\Column(type: 'integer')]
    private int $hourWorked;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Project", inversedBy: "idProject", cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $idTask = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $totalHour = null;

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

    public function getTaskDate(): \DateTimeInterface
    {
        return $this->taskDate;
    }

    public function setTaskDate(\DateTimeInterface $taskDate): self
    {
        $this->taskDate = $taskDate;
        return $this;
    }

    public function getHourWorked(): int
    {
        return $this->hourWorked;
    }

    public function setHourWorked(int $hourWorked): self
    {
        $this->hourWorked = $hourWorked;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getIdTask(): ?Project
    {
        return $this->idTask;
    }

    public function setIdTask(?Project $idTask): self
    {
        $this->idTask = $idTask;
        return $this;
    }

    public function getTotalHour(): ?int
    {
        return $this->totalHour;
    }

    public function setTotalHour(?int $totalHour): self
    {
        $this->totalHour = $totalHour;
        return $this;
    }
}
