<?php

namespace App\Entity;

use App\Repository\VacantesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VacantesRepository::class)]
class Vacantes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Puesto $puesto = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;

    #[ORM\Column]
    private ?int $antiguedad = null;

    /**
     * @var Collection<int, RequisitosVacantes>
     */
    #[ORM\OneToMany(targetEntity: RequisitosVacantes::class, mappedBy: 'vacante',cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $requisitos;

    public function __construct()
    {
        $this->requisitos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

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

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): static
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getAntiguedad(): ?int
    {
        return $this->antiguedad;
    }

    public function setAntiguedad(int $antiguedad): static
    {
        $this->antiguedad = $antiguedad;

        return $this;
    }

    /**
     * @return Collection<int, RequisitosVacantes>
     */
    public function getRequisitos(): Collection
    {
        return $this->requisitos;
    }

    public function addRequisito(RequisitosVacantes $requisito): static
    {
        if (!$this->requisitos->contains($requisito)) {
            $this->requisitos->add($requisito);
            $requisito->setVacante($this);
        }

        return $this;
    }

    public function removeRequisito(RequisitosVacantes $requisito): static
    {
        if ($this->requisitos->removeElement($requisito)) {
            // set the owning side to null (unless already changed)
            if ($requisito->getVacante() === $this) {
                $requisito->setVacante(null);
            }
        }

        return $this;
    }
}
