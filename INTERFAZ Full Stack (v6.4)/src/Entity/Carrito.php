<?php

namespace App\Entity;

use App\Repository\CarritoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarritoRepository::class)]
class Carrito
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'carritos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idUser = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Producto $idProducto = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $precio_unidad = null;

    #[ORM\Column]
    private ?int $cantidad = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $precio_total = null;

    #[ORM\Column]
    private ?bool $comprado = null;

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

    public function getIdProducto(): ?Producto
    {
        return $this->idProducto;
    }

    public function setIdProducto(?Producto $idProducto): static
    {
        $this->idProducto = $idProducto;

        return $this;
    }

    public function getPrecioUnidad(): ?string
    {
        return $this->precio_unidad;
    }

    public function setPrecioUnidad(string $precio_unidad): static
    {
        $this->precio_unidad = $precio_unidad;

        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): static
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getPrecioTotal(): ?string
    {
        return $this->precio_total;
    }

    public function setPrecioTotal(string $precio_total): static
    {
        $this->precio_total = $precio_total;

        return $this;
    }

    public function isComprado(): ?bool
    {
        return $this->comprado;
    }

    public function setComprado(bool $comprado): static
    {
        $this->comprado = $comprado;

        return $this;
    }
}
