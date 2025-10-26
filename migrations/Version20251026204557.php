<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251026204557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE informacion_personal DROP FOREIGN KEY FK_BFEB5F745A5ADA57');
        $this->addSql('CREATE TABLE cursos (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, valor INT NOT NULL, INDEX IDX_B2785A183397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE requisitos_vacantes (id INT AUTO_INCREMENT NOT NULL, curso_id INT NOT NULL, vacante_id INT DEFAULT NULL, INDEX IDX_D8F4D35A87CB4A1F (curso_id), INDEX IDX_D8F4D35A8B34DB71 (vacante_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vacantes (id INT AUTO_INCREMENT NOT NULL, puesto_id INT NOT NULL, categoria_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, antiguedad INT NOT NULL, INDEX IDX_5169DCFE5035E9DA (puesto_id), INDEX IDX_5169DCFE3397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cursos ADD CONSTRAINT FK_B2785A183397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('ALTER TABLE requisitos_vacantes ADD CONSTRAINT FK_D8F4D35A87CB4A1F FOREIGN KEY (curso_id) REFERENCES cursos (id)');
        $this->addSql('ALTER TABLE requisitos_vacantes ADD CONSTRAINT FK_D8F4D35A8B34DB71 FOREIGN KEY (vacante_id) REFERENCES vacantes (id)');
        $this->addSql('ALTER TABLE vacantes ADD CONSTRAINT FK_5169DCFE5035E9DA FOREIGN KEY (puesto_id) REFERENCES puesto (id)');
        $this->addSql('ALTER TABLE vacantes ADD CONSTRAINT FK_5169DCFE3397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('ALTER TABLE historial_ascenso DROP FOREIGN KEY FK_779620925A5ADA57');
        $this->addSql('ALTER TABLE informacion_laboral DROP FOREIGN KEY FK_984389215035E9DA');
        $this->addSql('ALTER TABLE informacion_laboral DROP FOREIGN KEY FK_984389213397707A');
        $this->addSql('DROP TABLE historial_ascenso');
        $this->addSql('DROP TABLE informacion_laboral');
        $this->addSql('DROP INDEX IDX_BFEB5F745A5ADA57 ON informacion_personal');
        $this->addSql('ALTER TABLE informacion_personal DROP informacion_laboral_id, CHANGE telefono_fijo telefono_fijo VARCHAR(20) DEFAULT NULL, CHANGE telefono_celular telefono_celular VARCHAR(20) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historial_ascenso (id INT AUTO_INCREMENT NOT NULL, informacion_laboral_id INT DEFAULT NULL, fecha DATE NOT NULL, puesto_anterior VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, puesto_ascenso VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_779620925A5ADA57 (informacion_laboral_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE informacion_laboral (id INT AUTO_INCREMENT NOT NULL, categoria_id INT NOT NULL, puesto_id INT NOT NULL, numero_afiliado VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, fecha_incorporacion DATE NOT NULL, tipo_plaza TINYINT(1) NOT NULL, turno_actual VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, jornada VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_984389215035E9DA (puesto_id), INDEX IDX_984389213397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE historial_ascenso ADD CONSTRAINT FK_779620925A5ADA57 FOREIGN KEY (informacion_laboral_id) REFERENCES informacion_laboral (id)');
        $this->addSql('ALTER TABLE informacion_laboral ADD CONSTRAINT FK_984389215035E9DA FOREIGN KEY (puesto_id) REFERENCES puesto (id)');
        $this->addSql('ALTER TABLE informacion_laboral ADD CONSTRAINT FK_984389213397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id)');
        $this->addSql('ALTER TABLE cursos DROP FOREIGN KEY FK_B2785A183397707A');
        $this->addSql('ALTER TABLE requisitos_vacantes DROP FOREIGN KEY FK_D8F4D35A87CB4A1F');
        $this->addSql('ALTER TABLE requisitos_vacantes DROP FOREIGN KEY FK_D8F4D35A8B34DB71');
        $this->addSql('ALTER TABLE vacantes DROP FOREIGN KEY FK_5169DCFE5035E9DA');
        $this->addSql('ALTER TABLE vacantes DROP FOREIGN KEY FK_5169DCFE3397707A');
        $this->addSql('DROP TABLE cursos');
        $this->addSql('DROP TABLE requisitos_vacantes');
        $this->addSql('DROP TABLE vacantes');
        $this->addSql('ALTER TABLE informacion_personal ADD informacion_laboral_id INT DEFAULT NULL, CHANGE telefono_fijo telefono_fijo VARCHAR(20) NOT NULL, CHANGE telefono_celular telefono_celular VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE informacion_personal ADD CONSTRAINT FK_BFEB5F745A5ADA57 FOREIGN KEY (informacion_laboral_id) REFERENCES informacion_laboral (id)');
        $this->addSql('CREATE INDEX IDX_BFEB5F745A5ADA57 ON informacion_personal (informacion_laboral_id)');
    }
}
