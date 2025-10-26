<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251026212300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contactos_emergencia (id INT AUTO_INCREMENT NOT NULL, informacion_personal_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, parentesco VARCHAR(100) NOT NULL, telefono VARCHAR(20) NOT NULL, correo VARCHAR(255) DEFAULT NULL, INDEX IDX_CA56163E3F427F68 (informacion_personal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE informacion_laboral (id INT AUTO_INCREMENT NOT NULL, informacion_personal_id INT NOT NULL, numero_afiliado VARCHAR(255) NOT NULL, fecha_incorporacion DATE NOT NULL, tipo_plaza TINYINT(1) NOT NULL, turnoactual VARCHAR(255) NOT NULL, jornada VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_984389213F427F68 (informacion_personal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contactos_emergencia ADD CONSTRAINT FK_CA56163E3F427F68 FOREIGN KEY (informacion_personal_id) REFERENCES informacion_personal (id)');
        $this->addSql('ALTER TABLE informacion_laboral ADD CONSTRAINT FK_984389213F427F68 FOREIGN KEY (informacion_personal_id) REFERENCES informacion_personal (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contactos_emergencia DROP FOREIGN KEY FK_CA56163E3F427F68');
        $this->addSql('ALTER TABLE informacion_laboral DROP FOREIGN KEY FK_984389213F427F68');
        $this->addSql('DROP TABLE contactos_emergencia');
        $this->addSql('DROP TABLE informacion_laboral');
    }
}
