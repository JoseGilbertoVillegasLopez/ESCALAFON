<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251108153101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puesto ADD categoria_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE puesto ADD CONSTRAINT FK_47C3D2DE3397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('CREATE INDEX IDX_47C3D2DE3397707A ON puesto (categoria_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE puesto DROP FOREIGN KEY FK_47C3D2DE3397707A');
        $this->addSql('DROP INDEX IDX_47C3D2DE3397707A ON puesto');
        $this->addSql('ALTER TABLE puesto DROP categoria_id');
    }
}
