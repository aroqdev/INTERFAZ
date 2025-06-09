<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // ✅ Administrador
        $admin = new User();
        $admin->setNombre('INTERFAZ');
        $admin->setApellido('S.L.');
        $admin->setEmail('admin@interfaz.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setTelefono('123456789');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // ✅ Usuarios
        $user1 = new User();
        $user1->setNombre('Usuario1');
        $user1->setApellido('Apellido1');
        $user1->setEmail('user1@example.com');
        $user1->setRoles(['ROLE_USER']);
        $user1->setTelefono('987654321');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'user123'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setNombre('Usuario2');
        $user2->setApellido('Apellido2');
        $user2->setEmail('user2@example.com');
        $user2->setRoles(['ROLE_USER']);
        $user2->setTelefono(null);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'user123'));
        $manager->persist($user2);

        // ✅ Vendedores
        $seller1 = new User();
        $seller1->setNombre('Vendedor1');
        $seller1->setApellido('Apellido1');
        $seller1->setEmail('seller1@example.com');
        $seller1->setRoles(['ROLE_SELLER']);
        $seller1->setTelefono('456789123');
        $seller1->setPassword($this->passwordHasher->hashPassword($seller1, 'seller123'));
        $manager->persist($seller1);

        $seller2 = new User();
        $seller2->setNombre('Vendedor2');
        $seller2->setApellido('Apellido2');
        $seller2->setEmail('seller2@example.com');
        $seller2->setRoles(['ROLE_SELLER']);
        $seller2->setTelefono(null);
        $seller2->setPassword($this->passwordHasher->hashPassword($seller2, 'seller123'));
        $manager->persist($seller2);

        $manager->flush();
    }
}
