<?php

namespace App\Entity;

use App\Repository\InformacionLaboralRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?string $turnoActual = null;

    #[ORM\Column(length: 255)]
    private ?string $jornada = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Puesto $puesto = null;

    /**
     * @var Collection<int, HistorialAscenso>
     */
    #[ORM\OneToMany(targetEntity: HistorialAscenso::class, mappedBy: 'informacionLaboral')]
    private Collection $historialAscenso;

    /**
     * @var Collection<int, InformacionPersonal>
     */
    #[ORM\OneToMany(targetEntity: InformacionPersonal::class, mappedBy: 'informacionLaboral')]
    private Collection $informacionPersonals;

    public function __construct()
    {
        $this->historialAscenso = new ArrayCollection();
        $this->informacionPersonals = new ArrayCollection();
    }

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

    public function getTurnoActual(): ?string
    {
        return $this->turnoActual;
    }

    public function setTurnoActual(string $turnoActual): static
    {
        $this->turnoActual = $turnoActual;

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

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): static
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getPuesto(): ?Puesto
    {
        return $this->puesto;
    }

    public function setPuesto(?Puesto $puesto): static
    {
        $this->puesto = $puesto;

        return $this;
    }

    /**
     * @return Collection<int, HistorialAscenso>
     */
    public function getHistorialAscenso(): Collection
    {
        return $this->historialAscenso;
    }

    public function addHistorialAscenso(HistorialAscenso $historialAscenso): static
    {
        if (!$this->historialAscenso->contains($historialAscenso)) {
            $this->historialAscenso->add($historialAscenso);
            $historialAscenso->setInformacionLaboral($this);
        }

        return $this;
    }

    public function removeHistorialAscenso(HistorialAscenso $historialAscenso): static
    {
        if ($this->historialAscenso->removeElement($historialAscenso)) {
            // set the owning side to null (unless already changed)
            if ($historialAscenso->getInformacionLaboral() === $this) {
                $historialAscenso->setInformacionLaboral(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InformacionPersonal>
     */
    public function getInformacionPersonals(): Collection
    {
        return $this->informacionPersonals;
    }

    public function addInformacionPersonal(InformacionPersonal $informacionPersonal): static
    {
        if (!$this->informacionPersonals->contains($informacionPersonal)) {
            $this->informacionPersonals->add($informacionPersonal);
            $informacionPersonal->setInformacionLaboral($this);
        }

        return $this;
    }

    public function removeInformacionPersonal(InformacionPersonal $informacionPersonal): static
    {
        if ($this->informacionPersonals->removeElement($informacionPersonal)) {
            // set the owning side to null (unless already changed)
            if ($informacionPersonal->getInformacionLaboral() === $this) {
                $informacionPersonal->setInformacionLaboral(null);
            }
        }

        return $this;
    }
}
