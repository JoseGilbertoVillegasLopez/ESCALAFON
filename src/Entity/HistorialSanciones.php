<?php

namespace App\Entity;

use App\Repository\HistorialSancionesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistorialSancionesRepository::class)]
class HistorialSanciones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fecha = null;

    #[ORM\Column(length: 255)]
    private ?string $motivo = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255)]
    private ?string $comprovante = null;

    #[ORM\ManyToOne(inversedBy: 'historialSanciones')]
    private ?InformacionLaboral $informacionLaboral = null;

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

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function setMotivo(string $motivo): static
    {
        $this->motivo = $motivo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getComprovante(): ?string
    {
        return $this->comprovante;
    }

    public function setComprovante(string $comprovante): static
    {
        $this->comprovante = $comprovante;

        return $this;
    }

    public function getInformacionLaboral(): ?InformacionLaboral
    {
        return $this->informacionLaboral;
    }

    public function setInformacionLaboral(?InformacionLaboral $informacionLaboral): static
    {
        $this->informacionLaboral = $informacionLaboral;

        return $this;
    }
}
