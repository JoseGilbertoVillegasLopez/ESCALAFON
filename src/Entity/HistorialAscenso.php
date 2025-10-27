<?php

namespace App\Entity;

use App\Repository\HistorialAscensoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistorialAscensoRepository::class)]
class HistorialAscenso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fecha = null;

    #[ORM\Column(length: 255)]
    private ?string $puestoAnterior = null;

    #[ORM\Column(length: 255)]
    private ?string $puestoAscenso = null;

    #[ORM\ManyToOne(inversedBy: 'historialAscensos')]
    private ?InformacionPersonal $informacionPersonal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTime
    {
        return $this->fecha;
    }

    public function setFecha(\DateTime $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getPuestoAnterior(): ?string
    {
        return $this->puestoAnterior;
    }

    public function setPuestoAnterior(string $puestoAnterior): static
    {
        $this->puestoAnterior = $puestoAnterior;

        return $this;
    }

    public function getPuestoAscenso(): ?string
    {
        return $this->puestoAscenso;
    }

    public function setPuestoAscenso(string $puestoAscenso): static
    {
        $this->puestoAscenso = $puestoAscenso;

        return $this;
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
}
