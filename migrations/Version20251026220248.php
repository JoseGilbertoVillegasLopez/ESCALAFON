<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251026220248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE formacion_academica (id INT AUTO_INCREMENT NOT NULL, informacion_personal_id INT NOT NULL, escolaridad VARCHAR(255) NOT NULL, certificado VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2F2EA49B3F427F68 (informacion_personal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE formacion_academica ADD CONSTRAINT FK_2F2EA49B3F427F68 FOREIGN KEY (informacion_personal_id) REFERENCES informacion_personal (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formacion_academica DROP FOREIGN KEY FK_2F2EA49B3F427F68');
        $this->addSql('DROP TABLE formacion_academica');
    }
}
