<?php

namespace App\Entity;

use App\Repository\PuestoRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: PuestoRepository::class)]
class Puesto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    /**
 * @var Collection<int, InformacionLaboral>
 */
#[ORM\OneToMany(mappedBy: 'puesto', targetEntity: InformacionLaboral::class)]
private Collection $informacionLaboral;

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

    public function __construct()
{
    $this->informacionesLaborales = new ArrayCollection();
}
/**
 * @return Collection<int, InformacionLaboral>
 */
public function getInformacionesLaborales(): Collection
{
    return $this->informacionLaboral;
}

public function addInformacionLaboral(InformacionLaboral $informacionLaboral): static
{
    if (!$this->informacionLaboral->contains($informacionLaboral)) {
        $this->informacionLaboral->add($informacionLaboral);
        $informacionLaboral->setPuesto($this);
    }

    return $this;
}

public function removeInformacionLaboral(InformacionLaboral $informacionLaboral): static
{
    if ($this->informacionLaboral->removeElement($informacionLaboral)) {
        if ($informacionLaboral->getPuesto() === $this) {
            $informacionLaboral->setPuesto(null);
        }
    }

    return $this;
}


}
