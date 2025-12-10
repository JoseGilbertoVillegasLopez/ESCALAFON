<?php

namespace App\Entity;

use App\Repository\FormacionAcademicaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormacionAcademicaRepository::class)]
class FormacionAcademica
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $escolaridad = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $certificado = null;

    #[ORM\OneToOne(inversedBy: 'formacionAcademica', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?InformacionPersonal $informacionPersonal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEscolaridad(): ?string
    {
        return $this->escolaridad;
    }

    public function setEscolaridad(?string $escolaridad): static
    {
        $this->escolaridad = $escolaridad;

        return $this;
    }

    public function getCertificado(): ?string
    {
        return $this->certificado;
    }

    public function setCertificado(?string $certificado): static
    {
        $this->certificado = $certificado;

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
