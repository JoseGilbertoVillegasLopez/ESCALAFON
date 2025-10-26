<?php

namespace App\Entity;

use App\Repository\InformacionLaboralRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InformacionLaboralRepository::class)]
class InformacionLaboral
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroAfiliado = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $fechaIncorporacion = null;

    #[ORM\Column]
    private ?bool $tipoPlaza = null;

    #[ORM\Column(length: 255)]
    private ?string $turnoactual = null;

    #[ORM\Column(length: 255)]
    private ?string $jornada = null;

    #[ORM\OneToOne(inversedBy: 'informacionLaboral', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?InformacionPersonal $informacionPersonal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroAfiliado(): ?string
    {
        return $this->numeroAfiliado;
    }

    public function setNumeroAfiliado(string $numeroAfiliado): static
    {
        $this->numeroAfiliado = $numeroAfiliado;

        return $this;
    }

    public function getFechaIncorporacion(): ?\DateTime
    {
        return $this->fechaIncorporacion;
    }

    public function setFechaIncorporacion(\DateTime $fechaIncorporacion): static
    {
        $this->fechaIncorporacion = $fechaIncorporacion;

        return $this;
    }

    public function isTipoPlaza(): ?bool
    {
        return $this->tipoPlaza;
    }

    public function setTipoPlaza(bool $tipoPlaza): static
    {
        $this->tipoPlaza = $tipoPlaza;

        return $this;
    }

    public function getTurnoactual(): ?string
    {
        return $this->turnoactual;
    }

    public function setTurnoactual(string $turnoactual): static
    {
        $this->turnoactual = $turnoactual;

        return $this;
    }

    public function getJornada(): ?string
    {
        return $this->jornada;
    }

    public function setJornada(string $jornada): static
    {
        $this->jornada = $jornada;

        return $this;
    }

    public function getInformacionPersonal(): ?InformacionPersonal
    {
        return $this->informacionPersonal;
    }

    public function setInformacionPersonal(InformacionPersonal $informacionPersonal): static
    {
        $this->informacionPersonal = $informacionPersonal;

        return $this;
    }
}
