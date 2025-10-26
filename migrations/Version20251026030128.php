<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251026030128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categoria (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historial_ascenso (id INT AUTO_INCREMENT NOT NULL, informacion_laboral_id INT DEFAULT NULL, fecha DATE NOT NULL, puesto_anterior VARCHAR(255) NOT NULL, puesto_ascenso VARCHAR(255) NOT NULL, INDEX IDX_779620925A5ADA57 (informacion_laboral_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE informacion_laboral (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, puesto_id INT NOT NULL, numero_afiliado VARCHAR(255) NOT NULL, fecha_incorporacion DATE NOT NULL, tipo_plaza TINYINT(1) NOT NULL, turno_actual VARCHAR(255) NOT NULL, jornada VARCHAR(255) NOT NULL, INDEX IDX_984389213397707A (categoria_id), INDEX IDX_984389215035E9DA (puesto_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE puesto (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE historial_ascenso ADD CONSTRAINT FK_779620925A5ADA57 FOREIGN KEY (informacion_laboral_id) REFERENCES informacion_laboral (id)');
        $this->addSql('ALTER TABLE informacion_laboral ADD CONSTRAINT FK_984389213397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('ALTER TABLE informacion_laboral ADD CONSTRAINT FK_984389215035E9DA FOREIGN KEY (puesto_id) REFERENCES puesto (id)');
        $this->addSql('ALTER TABLE informacion_personal ADD informacion_laboral_id INT DEFAULT NULL, ADD apellido_paterno VARCHAR(255) NOT NULL, ADD apellido_materno VARCHAR(255) NOT NULL, DROP ap_paterno, DROP ap_materno, CHANGE telefono_fijo telefono_fijo VARCHAR(20) NOT NULL, CHANGE telefono_celular telefono_celular VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE informacion_personal ADD CONSTRAINT FK_BFEB5F745A5ADA57 FOREIGN KEY (informacion_laboral_id) REFERENCES informacion_laboral (id)');
        $this->addSql('CREATE INDEX IDX_BFEB5F745A5ADA57 ON informacion_personal (informacion_laboral_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE informacion_personal DROP FOREIGN KEY FK_BFEB5F745A5ADA57');
        $this->addSql('ALTER TABLE historial_ascenso DROP FOREIGN KEY FK_779620925A5ADA57');
        $this->addSql('ALTER TABLE informacion_laboral DROP FOREIGN KEY FK_984389213397707A');
        $this->addSql('ALTER TABLE informacion_laboral DROP FOREIGN KEY FK_984389215035E9DA');
        $this->addSql('DROP TABLE categoria');
        $this->addSql('DROP TABLE historial_ascenso');
        $this->addSql('DROP TABLE informacion_laboral');
        $this->addSql('DROP TABLE puesto');
        $this->addSql('DROP INDEX IDX_BFEB5F745A5ADA57 ON informacion_personal');
        $this->addSql('ALTER TABLE informacion_personal ADD ap_paterno VARCHAR(255) NOT NULL, ADD ap_materno VARCHAR(255) NOT NULL, DROP informacion_laboral_id, DROP apellido_paterno, DROP apellido_materno, CHANGE telefono_fijo telefono_fijo VARCHAR(20) DEFAULT NULL, CHANGE telefono_celular telefono_celular VARCHAR(20) DEFAULT NULL');
    }
}
