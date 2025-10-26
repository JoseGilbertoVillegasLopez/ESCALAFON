<?php

namespace App\Entity;

use App\Repository\RequisitosVacantesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequisitosVacantesRepository::class)]
class RequisitosVacantes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cursos $curso = null;

    #[ORM\ManyToOne(inversedBy: 'requisitos')]
    private ?Vacantes $vacante = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurso(): ?Cursos
    {
        return $this->curso;
    }

    public function setCurso(?Cursos $curso): static
    {
        $this->curso = $curso;

        return $this;
    }

    public function getVacante(): ?Vacantes
    {
        return $this->vacante;
    }

    public function setVacante(?Vacantes $vacante): static
    {
        $this->vacante = $vacante;

        return $this;
    }
}
