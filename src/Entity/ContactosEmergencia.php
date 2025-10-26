<?php

namespace App\Entity;

use App\Repository\ContactosEmergenciaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactosEmergenciaRepository::class)]
class ContactosEmergencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 100)]
    private ?string $parentesco = null;

    #[ORM\Column(length: 20)]
    private ?string $telefono = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $correo = null;

    #[ORM\ManyToOne(inversedBy: 'contactosEmergencias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?InformacionPersonal $informacionPersonal = null;

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

    public function getParentesco(): ?string
    {
        return $this->parentesco;
    }

    public function setParentesco(string $parentesco): static
    {
        $this->parentesco = $parentesco;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): static
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(?string $correo): static
    {
        $this->correo = $correo;

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
