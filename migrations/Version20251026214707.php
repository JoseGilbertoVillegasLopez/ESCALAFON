<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251026214707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE informacion_laboral ADD puesto_id INT NOT NULL, ADD categoria_id INT NOT NULL');
        $this->addSql('ALTER TABLE informacion_laboral ADD CONSTRAINT FK_984389215035E9DA FOREIGN KEY (puesto_id) REFERENCES puesto (id)');
        $this->addSql('ALTER TABLE informacion_laboral ADD CONSTRAINT FK_984389213397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('CREATE INDEX IDX_984389215035E9DA ON informacion_laboral (puesto_id)');
        $this->addSql('CREATE INDEX IDX_984389213397707A ON informacion_laboral (categoria_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE informacion_laboral DROP FOREIGN KEY FK_984389215035E9DA');
        $this->addSql('ALTER TABLE informacion_laboral DROP FOREIGN KEY FK_984389213397707A');
        $this->addSql('DROP INDEX IDX_984389215035E9DA ON informacion_laboral');
        $this->addSql('DROP INDEX IDX_984389213397707A ON informacion_laboral');
        $this->addSql('ALTER TABLE informacion_laboral DROP puesto_id, DROP categoria_id');
    }
}
