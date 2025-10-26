<?php

namespace App\Entity;

use App\Repository\InformacionPersonalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InformacionPersonalRepository::class)]
class InformacionPersonal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidoPaterno = null;

    #[ORM\Column(length: 255)]
    private ?string $apellidoMaterno = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telefonoFijo = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telefonoCelular = null;

    #[ORM\Column(length: 255)]
    private ?string $correo = null;

    #[ORM\Column(length: 18)]
    private ?string $curp = null;

    #[ORM\Column(length: 13)]
    private ?string $rfc = null;

    #[ORM\Column(length: 11)]
    private ?string $nss = null;

    #[ORM\Column]
    private ?bool $estadoCivil = null;

    #[ORM\Column(length: 255)]
    private ?string $domicilio = null;

    #[ORM\Column(length: 255)]
    private ?string $imagen = null;

    #[ORM\OneToOne(mappedBy: 'trabajador', cascade: ['persist', 'remove'])]
    private ?Usuario $usuario = null;

    /**
     * @var Collection<int, ContactosEmergencia>
     */
    #[ORM\OneToMany(targetEntity: ContactosEmergencia::class, mappedBy: 'informacionPersonal', orphanRemoval: true)]
    private Collection $contactosEmergencias;

    #[ORM\OneToOne(mappedBy: 'informacionPersonal', cascade: ['persist', 'remove'])]
    private ?InformacionLaboral $informacionLaboral = null;

    public function __construct()
    {
        $this->contactosEmergencias = new ArrayCollection();
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

    public function getApellidoPaterno(): ?string
    {
        return $this->apellidoPaterno;
    }

    public function setApellidoPaterno(string $apellidoPaterno): static
    {
        $this->apellidoPaterno = $apellidoPaterno;

        return $this;
    }

    public function getApellidoMaterno(): ?string
    {
        return $this->apellidoMaterno;
    }

    public function setApellidoMaterno(string $apellidoMaterno): static
    {
        $this->apellidoMaterno = $apellidoMaterno;

        return $this;
    }

    public function getTelefonoFijo(): ?string
    {
        return $this->telefonoFijo;
    }

    public function setTelefonoFijo(?string $telefonoFijo): static
    {
        $this->telefonoFijo = $telefonoFijo;

        return $this;
    }

    public function getTelefonoCelular(): ?string
    {
        return $this->telefonoCelular;
    }

    public function setTelefonoCelular(?string $telefonoCelular): static
    {
        $this->telefonoCelular = $telefonoCelular;

        return $this;
    }

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): static
    {
        $this->correo = $correo;

        return $this;
    }

    public function getCurp(): ?string
    {
        return $this->curp;
    }

    public function setCurp(string $curp): static
    {
        $this->curp = $curp;

        return $this;
    }

    public function getRfc(): ?string
    {
        return $this->rfc;
    }

    public function setRfc(string $rfc): static
    {
        $this->rfc = $rfc;

        return $this;
    }

    public function getNss(): ?string
    {
        return $this->nss;
    }

    public function setNss(string $nss): static
    {
        $this->nss = $nss;

        return $this;
    }

    public function isEstadoCivil(): ?bool
    {
        return $this->estadoCivil;
    }

    public function setEstadoCivil(bool $estadoCivil): static
    {
        $this->estadoCivil = $estadoCivil;

        return $this;
    }

    public function getDomicilio(): ?string
    {
        return $this->domicilio;
    }

    public function setDomicilio(string $domicilio): static
    {
        $this->domicilio = $domicilio;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(string $imagen): static
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        // desasociar si es necesario
        if ($usuario === null && $this->usuario !== null) {
            $this->usuario->setTrabajador(null);
        }

        // asociar si es necesario
        if ($usuario !== null && $usuario->getTrabajador() !== $this) {
            $usuario->setTrabajador($this);
        }

        $this->usuario = $usuario;
        return $this;
    }

    /**
     * @return Collection<int, ContactosEmergencia>
     */
    public function getContactosEmergencias(): Collection
    {
        return $this->contactosEmergencias;
    }

    public function addContactosEmergencia(ContactosEmergencia $contactosEmergencia): static
    {
        if (!$this->contactosEmergencias->contains($contactosEmergencia)) {
            $this->contactosEmergencias->add($contactosEmergencia);
            $contactosEmergencia->setInformacionPersonal($this);
        }

        return $this;
    }

    public function removeContactosEmergencia(ContactosEmergencia $contactosEmergencia): static
    {
        if ($this->contactosEmergencias->removeElement($contactosEmergencia)) {
            // set the owning side to null (unless already changed)
            if ($contactosEmergencia->getInformacionPersonal() === $this) {
                $contactosEmergencia->setInformacionPersonal(null);
            }
        }

        return $this;
    }

    public function getInformacionLaboral(): ?InformacionLaboral
    {
        return $this->informacionLaboral;
    }

    public function setInformacionLaboral(InformacionLaboral $informacionLaboral): static
    {
        // set the owning side of the relation if necessary
        if ($informacionLaboral->getInformacionPersonal() !== $this) {
            $informacionLaboral->setInformacionPersonal($this);
        }

        $this->informacionLaboral = $informacionLaboral;

        return $this;
    }
}
