<?php

namespace App\Entity;

use App\Repository\CursosRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CursosRepository::class)]
class Cursos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El valor del curso es obligatorio.')]
    #[Assert\Range(
        notInRangeMessage: 'El valor debe estar entre {{ min }} y {{ max }} puntos.',
        min: 1,
        max: 10
    )]
    private ?int $valor = null;
    #[ORM\OneToMany(mappedBy: 'curso', targetEntity: Capacitacion::class, cascade: ['persist', 'remove'])]
    private Collection $capacitacion;


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

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): static
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getValor(): ?int
    {
        return $this->valor;
    }

    public function setValor(int $valor): static
    {
        $this->valor = $valor;

        return $this;
    }
    public function __toString(): string
{
    return $this->nombre;
}
}
