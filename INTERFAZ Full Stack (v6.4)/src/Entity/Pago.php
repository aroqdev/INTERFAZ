<?php

namespace App\Entity;

use App\Repository\PagoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PagoRepository::class)]
class Pago
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pagos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idUser = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombre_titular = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $numero_tarjeta = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fecha_vencimiento = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $cvv = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paypal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $regalo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cuenta_corriente = null;

    #[ORM\Column]
    private ?bool $activo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getNombreTitular(): ?string
    {
        return $this->nombre_titular;
    }

    public function setNombreTitular(?string $nombre_titular): static
    {
        $this->nombre_titular = $nombre_titular;

        return $this;
    }

    public function getNumeroTarjeta(): ?string
    {
        return $this->numero_tarjeta;
    }

    public function setNumeroTarjeta(?string $numero_tarjeta): static
    {
        $this->numero_tarjeta = $numero_tarjeta;

        return $this;
    }

    public function getFechaVencimiento(): ?string
    {
        return $this->fecha_vencimiento;
    }

    public function setFechaVencimiento(?string $fecha_vencimiento): static
    {
        $this->fecha_vencimiento = $fecha_vencimiento;

        return $this;
    }

    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    public function setCvv(?string $cvv): static
    {
        $this->cvv = $cvv;

        return $this;
    }

    public function getPaypal(): ?string
    {
        return $this->paypal;
    }

    public function setPaypal(?string $paypal): static
    {
        $this->paypal = $paypal;

        return $this;
    }

    public function getRegalo(): ?string
    {
        return $this->regalo;
    }

    public function setRegalo(?string $regalo): static
    {
        $this->regalo = $regalo;

        return $this;
    }

    public function getCuentaCorriente(): ?string
    {
        return $this->cuenta_corriente;
    }

    public function setCuentaCorriente(?string $cuenta_corriente): static
    {
        $this->cuenta_corriente = $cuenta_corriente;

        return $this;
    }

    public function isActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): static
    {
        $this->activo = $activo;

        return $this;
    }
}
