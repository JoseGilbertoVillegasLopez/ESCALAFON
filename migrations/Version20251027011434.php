<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251027011434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historial_ascenso (id INT AUTO_INCREMENT NOT NULL, informacion_personal_id INT DEFAULT NULL, fecha DATE NOT NULL, puesto_anterior VARCHAR(255) NOT NULL, puesto_ascenso VARCHAR(255) NOT NULL, INDEX IDX_779620923F427F68 (informacion_personal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historial_sanciones (id INT AUTO_INCREMENT NOT NULL, informacion_personal_id INT DEFAULT NULL, fecha DATE NOT NULL, motivo VARCHAR(255) NOT NULL, descripcion LONGTEXT NOT NULL, comprovante VARCHAR(255) NOT NULL, INDEX IDX_4A4D92213F427F68 (informacion_personal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE historial_ascenso ADD CONSTRAINT FK_779620923F427F68 FOREIGN KEY (informacion_personal_id) REFERENCES informacion_personal (id)');
        $this->addSql('ALTER TABLE historial_sanciones ADD CONSTRAINT FK_4A4D92213F427F68 FOREIGN KEY (informacion_personal_id) REFERENCES informacion_personal (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historial_ascenso DROP FOREIGN KEY FK_779620923F427F68');
        $this->addSql('ALTER TABLE historial_sanciones DROP FOREIGN KEY FK_4A4D92213F427F68');
        $this->addSql('DROP TABLE historial_ascenso');
        $this->addSql('DROP TABLE historial_sanciones');
    }
}
