<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: CategoriaRepository::class)]
class Categoria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
private ?string $descripcion = null;

    /**
 * @var Collection<int, InformacionLaboral>
 */
#[ORM\OneToMany(mappedBy: 'categoria', targetEntity: InformacionLaboral::class)]
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
    public function getDescripcion(): ?string
{
    return $this->descripcion;
}

public function setDescripcion(?string $descripcion): static
{
    $this->descripcion = $descripcion;

    return $this;
}
    public function __construct()
{
    $this->informacionLaboral = new ArrayCollection();
}

/**
 * @return Collection<int, InformacionLaboral>
 */
public function getInformacionLaboral(): Collection
{
    return $this->informacionLaboral;
}

public function addInformacionLaboral(InformacionLaboral $informacionLaboral): static
{
    if (!$this->informacionLaboral->contains($informacionLaboral)) {
        $this->informacionLaboral->add($informacionLaboral);
        $informacionLaboral->setCategoria($this);
    }

    return $this;
}

public function removeInformacionLaboral(InformacionLaboral $informacionLaboral): static
{
    if ($this->informacionLaboral->removeElement($informacionLaboral)) {
        if ($informacionLaboral->getCategoria() === $this) {
            $informacionLaboral->setCategoria(null);
        }
    }

    return $this;
}
public function __toString(): string
{
    return $this->nombre;
}

}
