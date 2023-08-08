<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230808135454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__template AS SELECT id, type, content, name FROM template');
        $this->addSql('DROP TABLE template');
        $this->addSql('CREATE TABLE template (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(50) NOT NULL, content CLOB NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO template (id, type, content, name) SELECT id, type, content, name FROM __temp__template');
        $this->addSql('DROP TABLE __temp__template');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TEMPORARY TABLE __temp__template AS SELECT id, type, name, content FROM template');
        $this->addSql('DROP TABLE template');
        $this->addSql('CREATE TABLE template (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, content VARCHAR(2000) NOT NULL)');
        $this->addSql('INSERT INTO template (id, type, name, content) SELECT id, type, name, content FROM __temp__template');
        $this->addSql('DROP TABLE __temp__template');
    }
}
