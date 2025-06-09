<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250605090618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE carrito (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, id_producto_id INT NOT NULL, precio_unidad NUMERIC(10, 2) NOT NULL, cantidad INT NOT NULL, precio_total NUMERIC(10, 2) NOT NULL, comprado TINYINT(1) NOT NULL, INDEX IDX_77E6BED579F37AE5 (id_user_id), INDEX IDX_77E6BED56E57A479 (id_producto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE categoria (id INT AUTO_INCREMENT NOT NULL, imagen VARCHAR(255) DEFAULT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE direccion (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, ciudad VARCHAR(255) NOT NULL, calle VARCHAR(255) NOT NULL, numero VARCHAR(255) NOT NULL, piso VARCHAR(255) DEFAULT NULL, codigo_postal VARCHAR(255) NOT NULL, activo TINYINT(1) NOT NULL, INDEX IDX_F384BE9579F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE pago (id INT AUTO_INCREMENT NOT NULL, id_user_id INT NOT NULL, nombre_titular VARCHAR(255) DEFAULT NULL, numero_tarjeta VARCHAR(16) DEFAULT NULL, fecha_vencimiento VARCHAR(255) DEFAULT NULL, cvv VARCHAR(3) DEFAULT NULL, paypal VARCHAR(255) DEFAULT NULL, regalo VARCHAR(255) DEFAULT NULL, cuenta_corriente VARCHAR(255) DEFAULT NULL, activo TINYINT(1) NOT NULL, INDEX IDX_F4DF5F3E79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE producto (id INT AUTO_INCREMENT NOT NULL, id_cat_id INT NOT NULL, id_user_id INT DEFAULT NULL, imagen VARCHAR(255) DEFAULT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) NOT NULL, cantidad INT NOT NULL, precio NUMERIC(10, 2) NOT NULL, precio_anterior NUMERIC(10, 2) DEFAULT NULL, oferta TINYINT(1) NOT NULL, activo TINYINT(1) NOT NULL, INDEX IDX_A7BB0615C09A1CAE (id_cat_id), INDEX IDX_A7BB061579F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, telefono VARCHAR(20) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), UNIQUE INDEX UNIQ_IDENTIFIER_TELEFONO (telefono), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carrito ADD CONSTRAINT FK_77E6BED579F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carrito ADD CONSTRAINT FK_77E6BED56E57A479 FOREIGN KEY (id_producto_id) REFERENCES producto (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE direccion ADD CONSTRAINT FK_F384BE9579F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pago ADD CONSTRAINT FK_F4DF5F3E79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto ADD CONSTRAINT FK_A7BB0615C09A1CAE FOREIGN KEY (id_cat_id) REFERENCES categoria (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto ADD CONSTRAINT FK_A7BB061579F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE carrito DROP FOREIGN KEY FK_77E6BED579F37AE5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE carrito DROP FOREIGN KEY FK_77E6BED56E57A479
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE direccion DROP FOREIGN KEY FK_F384BE9579F37AE5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pago DROP FOREIGN KEY FK_F4DF5F3E79F37AE5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto DROP FOREIGN KEY FK_A7BB0615C09A1CAE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto DROP FOREIGN KEY FK_A7BB061579F37AE5
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE carrito
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categoria
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE direccion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pago
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE producto
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
