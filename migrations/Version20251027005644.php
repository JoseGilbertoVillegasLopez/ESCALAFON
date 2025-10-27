<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251027005644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE capacitacion (id INT AUTO_INCREMENT NOT NULL, informacion_personal_id INT NOT NULL, curso_id INT NOT NULL, INDEX IDX_5A430D213F427F68 (informacion_personal_id), INDEX IDX_5A430D2187CB4A1F (curso_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE capacitacion ADD CONSTRAINT FK_5A430D213F427F68 FOREIGN KEY (informacion_personal_id) REFERENCES informacion_personal (id)');
        $this->addSql('ALTER TABLE capacitacion ADD CONSTRAINT FK_5A430D2187CB4A1F FOREIGN KEY (curso_id) REFERENCES cursos (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE capacitacion DROP FOREIGN KEY FK_5A430D213F427F68');
        $this->addSql('ALTER TABLE capacitacion DROP FOREIGN KEY FK_5A430D2187CB4A1F');
        $this->addSql('DROP TABLE capacitacion');
    }
}
