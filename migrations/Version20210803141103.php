<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210803141103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, division_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', create_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2D5B023441859289 (division_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, locality_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, mail_address VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, create_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', acronym VARCHAR(255) DEFAULT NULL, code VARCHAR(255) NOT NULL, INDEX IDX_4FBF094F8BAC62AF (city_id), INDEX IDX_4FBF094F88823A92 (locality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company_division (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_8805279979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE directory (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, update_at DATETIME NOT NULL, description VARCHAR(255) DEFAULT NULL, size VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, extension VARCHAR(255) DEFAULT NULL, permissions VARCHAR(255) DEFAULT NULL, path VARCHAR(255) NOT NULL, is_file TINYINT(1) DEFAULT NULL, file_type VARCHAR(255) DEFAULT NULL, create_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE directory_company_division (directory_id INT NOT NULL, company_division_id INT NOT NULL, INDEX IDX_E917DFE42C94069F (directory_id), INDEX IDX_E917DFE458542A11 (company_division_id), PRIMARY KEY(directory_id, company_division_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE division (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1017471498260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE locality (id INT AUTO_INCREMENT NOT NULL, subdivision_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, create_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E1D6B8E6E05F13C (subdivision_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F62F176F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subdivision (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', create_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1B87FA9D8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, divison_id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, create_at DATETIME NOT NULL, is_verified TINYINT(1) NOT NULL, update_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), INDEX IDX_8D93D6496B7A6943 (divison_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B023441859289 FOREIGN KEY (division_id) REFERENCES division (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F88823A92 FOREIGN KEY (locality_id) REFERENCES locality (id)');
        $this->addSql('ALTER TABLE company_division ADD CONSTRAINT FK_8805279979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE directory_company_division ADD CONSTRAINT FK_E917DFE42C94069F FOREIGN KEY (directory_id) REFERENCES directory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE directory_company_division ADD CONSTRAINT FK_E917DFE458542A11 FOREIGN KEY (company_division_id) REFERENCES company_division (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE division ADD CONSTRAINT FK_1017471498260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE locality ADD CONSTRAINT FK_E1D6B8E6E05F13C FOREIGN KEY (subdivision_id) REFERENCES subdivision (id)');
        $this->addSql('ALTER TABLE region ADD CONSTRAINT FK_F62F176F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE subdivision ADD CONSTRAINT FK_1B87FA9D8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496B7A6943 FOREIGN KEY (divison_id) REFERENCES company_division (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F8BAC62AF');
        $this->addSql('ALTER TABLE subdivision DROP FOREIGN KEY FK_1B87FA9D8BAC62AF');
        $this->addSql('ALTER TABLE company_division DROP FOREIGN KEY FK_8805279979B1AD6');
        $this->addSql('ALTER TABLE directory_company_division DROP FOREIGN KEY FK_E917DFE458542A11');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496B7A6943');
        $this->addSql('ALTER TABLE region DROP FOREIGN KEY FK_F62F176F92F3E70');
        $this->addSql('ALTER TABLE directory_company_division DROP FOREIGN KEY FK_E917DFE42C94069F');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B023441859289');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F88823A92');
        $this->addSql('ALTER TABLE division DROP FOREIGN KEY FK_1017471498260155');
        $this->addSql('ALTER TABLE locality DROP FOREIGN KEY FK_E1D6B8E6E05F13C');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_division');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE directory');
        $this->addSql('DROP TABLE directory_company_division');
        $this->addSql('DROP TABLE division');
        $this->addSql('DROP TABLE locality');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE subdivision');
        $this->addSql('DROP TABLE user');
    }
}
