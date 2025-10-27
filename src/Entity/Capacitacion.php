<?php

namespace App\Entity;

use App\Repository\CapacitacionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CapacitacionRepository::class)]
class Capacitacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'capacitacion')]
    #[ORM\JoinColumn(nullable: false)]
    private ?InformacionPersonal $informacionPersonal = null;

    #[ORM\ManyToOne(inversedBy: 'capacitacion')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cursos $curso = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInformacionPersonal(): ?InformacionPersonal
    {
        return $this->informacionPersonal;
    }

    public function setInformacionPersonal(?InformacionPersonal $informacionPersonal): static
    {
        $this->informacionPersonal = $informacionPersonal;

        return $this;
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
}
