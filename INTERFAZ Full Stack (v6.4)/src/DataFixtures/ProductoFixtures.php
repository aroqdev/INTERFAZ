<?php

namespace App\DataFixtures;

use App\Entity\Producto;
use App\Entity\User;
use App\Entity\Categoria;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductoFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // ✅ Obtener usuarios
        $admin = $manager->getRepository(User::class)->findOneBy(['email' => 'admin@interfaz.com']);
        $seller1 = $manager->getRepository(User::class)->findOneBy(['email' => 'seller1@example.com']);
        $seller2 = $manager->getRepository(User::class)->findOneBy(['email' => 'seller2@example.com']);

        // ✅ Obtener categorías
        $categorias = $manager->getRepository(Categoria::class)->findAll();
        $categoriasMap = [];
        foreach ($categorias as $categoria) {
            $categoriasMap[$categoria->getNombre()] = $categoria;
        }

        // ✅ Datos de productos
        $productos = [
            ['nombre' => 'Smartphone', 'descripcion' => 'Teléfono móvil de última generación.', 'imagen' => 'https://th.bing.com/th/id/OIP.tXefyRx69wGNHqj5MoXYDAHaLl?r=0&rs=1&pid=ImgDetMain', 'cantidad' => 50, 'precio' => 699.99, 'precio_anterior' => 799.99, 'oferta' => true, 'idUser' => $admin, 'idCat' => $categoriasMap['Electrónica'], 'activo' => true],
            ['nombre' => 'Auriculares Bluetooth', 'descripcion' => 'Sonido envolvente y conexión inalámbrica.', 'imagen' => 'https://png.pngtree.com/png-clipart/20230417/original/pngtree-bluetooth-earphone-electronic-device-fashion-transparent-png-image_9062645.png', 'cantidad' => 100, 'precio' => 49.99, 'precio_anterior' => null, 'oferta' => false, 'idUser' => $admin, 'idCat' => $categoriasMap['Electrónica'], 'activo' => true],
            ['nombre' => 'Camiseta Deportiva', 'descripcion' => 'Tela transpirable y diseño moderno.', 'imagen' => 'https://w7.pngwing.com/pngs/412/104/png-transparent-t-shirt-sports-fan-jersey-electronic-sports-sleeve-t-shirt-tshirt-team-active-shirt.png', 'cantidad' => 80, 'precio' => 19.99, 'precio_anterior' => null, 'oferta' => false, 'idUser' => $seller1, 'idCat' => $categoriasMap['Moda'], 'activo' => true],
            ['nombre' => 'Zapatos Elegantes', 'descripcion' => 'Calzado cómodo para cualquier ocasión.', 'imagen' => 'https://w7.pngwing.com/pngs/2/47/png-transparent-shoe-light-sheep-suede-black-classy-leather-suede-sheep.png', 'cantidad' => 60, 'precio' => 89.99, 'precio_anterior' => 109.99, 'oferta' => true, 'idUser' => $seller1, 'idCat' => $categoriasMap['Moda'], 'activo' => true],
            ['nombre' => 'Sofá Moderno', 'descripcion' => 'Diseño elegante para tu sala.', 'imagen' => 'https://static.vecteezy.com/system/resources/previews/022/972/625/non_2x/modern-sofa-isolated-png.png', 'cantidad' => 20, 'precio' => 499.99, 'precio_anterior' => 599.99, 'oferta' => true, 'idUser' => $admin, 'idCat' => $categoriasMap['Hogar'], 'activo' => true],
            ['nombre' => 'Mesa de Comedor', 'descripcion' => 'Madera resistente y diseño minimalista.', 'imagen' => 'https://www.pngkit.com/png/full/421-4215626_comedor-mesa-de-comedor-png.png', 'cantidad' => 30, 'precio' => 299.99, 'precio_anterior' => null, 'oferta' => false, 'idUser' => $admin, 'idCat' => $categoriasMap['Hogar'], 'activo' => true],
            ['nombre' => 'Pelota de Fútbol', 'descripcion' => 'Balón oficial con diseño profesional.', 'imagen' => 'https://www.pnguniverse.com/wp-content/uploads/2020/09/Pelota-de-futbol-1.png', 'cantidad' => 120, 'precio' => 24.99, 'precio_anterior' => null, 'oferta' => false, 'idUser' => $seller2, 'idCat' => $categoriasMap['Deportes'], 'activo' => true],
            ['nombre' => 'Bicicleta de Montaña', 'descripcion' => 'Ligera y perfecta para aventuras.', 'imagen' => 'https://w7.pngwing.com/pngs/129/700/png-transparent-27-5-mountain-bike-diamondback-bicycles-hardtail-bicycle-bicycle-frame-bicycle-mountain-biking.png', 'cantidad' => 15, 'precio' => 399.99, 'precio_anterior' => 499.99, 'oferta' => true, 'idUser' => $seller2, 'idCat' => $categoriasMap['Deportes'], 'activo' => true],
            ['nombre' => 'Muñeca Interactiva', 'descripcion' => 'Juguete educativo con sonido.', 'imagen' => 'https://static.vecteezy.com/system/resources/previews/012/664/864/non_2x/hand-drawn-baby-girl-doll-illustration-png.png', 'cantidad' => 50, 'precio' => 29.99, 'precio_anterior' => null, 'oferta' => false, 'idUser' => $admin, 'idCat' => $categoriasMap['Juguetes'], 'activo' => true],
            ['nombre' => 'Auto de Juguete', 'descripcion' => 'Vehículo a escala con luces y sonido.', 'imagen' => 'https://th.bing.com/th/id/R.58c4c902761908714bbb1426559c2f37?rik=je3n1tTVAtEcFA&riu=http%3a%2f%2fpluspng.com%2fimg-png%2fcar-png-car-front-png-image-32725-1700.png&ehk=njeoi9gYgdE73JuSjeYPwxT%2bKpcYN4OHC04vOpzplf4%3d&risl=&pid=ImgRaw&r=0', 'cantidad' => 70, 'precio' => 39.99, 'precio_anterior' => 49.99, 'oferta' => true, 'idUser' => $admin, 'idCat' => $categoriasMap['Juguetes'], 'activo' => true],
        ];

        // ✅ Insertar productos con relaciones correctas
        foreach ($productos as $data) {
            $producto = new Producto();
            $producto->setNombre($data['nombre']);
            $producto->setDescripcion($data['descripcion']);
            $producto->setImagen($data['imagen']);
            $producto->setCantidad($data['cantidad']);
            $producto->setPrecio($data['precio']);
            $producto->setPrecioAnterior($data['precio_anterior']);
            $producto->setOferta($data['oferta']);
            $producto->setIdUser($data['idUser']);
            $producto->setIdCat($data['idCat']);
            $producto->setActivo($data['activo']);

            $manager->persist($producto);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoriaFixtures::class,
        ];
    }
}
