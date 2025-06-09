<?php

namespace App\DataFixtures;

use App\Entity\Categoria;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriaFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categorias = [
            ['nombre' => 'Electrónica', 'descripcion' => 'Dispositivos y gadgets tecnológicos.', 'imagen' => 'https://via.placeholder.com/300x200?text=Electrónica'],
            ['nombre' => 'Moda', 'descripcion' => 'Ropa, calzado y accesorios de moda.', 'imagen' => 'https://via.placeholder.com/300x200?text=Moda'],
            ['nombre' => 'Hogar', 'descripcion' => 'Muebles, decoración y electrodomésticos.', 'imagen' => 'https://via.placeholder.com/300x200?text=Hogar'],
            ['nombre' => 'Deportes', 'descripcion' => 'Artículos deportivos y ropa deportiva.', 'imagen' => 'https://via.placeholder.com/300x200?text=Deportes'],
            ['nombre' => 'Juguetes', 'descripcion' => 'Juegos y juguetes para todas las edades.', 'imagen' => 'https://via.placeholder.com/300x200?text=Juguetes'],
        ];

        foreach ($categorias as $data) {
            $categoria = new Categoria();
            $categoria->setNombre($data['nombre']);
            $categoria->setDescripcion($data['descripcion']);
            $categoria->setImagen($data['imagen']);

            $manager->persist($categoria);
        }

        $manager->flush();
    }
}
