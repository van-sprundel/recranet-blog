<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220705103918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_BA5AE01D63379586');
        $this->addSql('DROP INDEX IDX_BA5AE01DB03A8386');
        $this->addSql('CREATE TEMPORARY TABLE __temp__blog_post AS SELECT id, created_by_id, comments_id, title, subtitle, content, created_on, head_image, extra_images FROM blog_post');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('CREATE TABLE blog_post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_by_id INTEGER DEFAULT NULL, comments_id INTEGER DEFAULT NULL, title VARCHAR(48) NOT NULL, subtitle VARCHAR(48) NOT NULL, content VARCHAR(255) NOT NULL, created_on DATETIME NOT NULL, head_image VARCHAR(255) NOT NULL, extra_images CLOB DEFAULT NULL --(DC2Type:array)
        , CONSTRAINT FK_BA5AE01DB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BA5AE01D63379586 FOREIGN KEY (comments_id) REFERENCES comment (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO blog_post (id, created_by_id, comments_id, title, subtitle, content, created_on, head_image, extra_images) SELECT id, created_by_id, comments_id, title, subtitle, content, created_on, head_image, extra_images FROM __temp__blog_post');
        $this->addSql('DROP TABLE __temp__blog_post');
        $this->addSql('CREATE INDEX IDX_BA5AE01D63379586 ON blog_post (comments_id)');
        $this->addSql('CREATE INDEX IDX_BA5AE01DB03A8386 ON blog_post (created_by_id)');
        $this->addSql('DROP INDEX IDX_9474526CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, user_id, body, created_on FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, body VARCHAR(255) NOT NULL, created_on DATETIME NOT NULL, CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO comment (id, user_id, body, created_on) SELECT id, user_id, body, created_on FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_BA5AE01DB03A8386');
        $this->addSql('DROP INDEX IDX_BA5AE01D63379586');
        $this->addSql('CREATE TEMPORARY TABLE __temp__blog_post AS SELECT id, created_by_id, comments_id, title, subtitle, content, created_on, head_image, extra_images FROM blog_post');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('CREATE TABLE blog_post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, created_by_id INTEGER DEFAULT NULL, comments_id INTEGER DEFAULT NULL, title VARCHAR(48) NOT NULL, subtitle VARCHAR(48) NOT NULL, content VARCHAR(255) NOT NULL, created_on DATETIME NOT NULL, head_image VARCHAR(255) NOT NULL, extra_images CLOB DEFAULT NULL --(DC2Type:array)
        )');
        $this->addSql('INSERT INTO blog_post (id, created_by_id, comments_id, title, subtitle, content, created_on, head_image, extra_images) SELECT id, created_by_id, comments_id, title, subtitle, content, created_on, head_image, extra_images FROM __temp__blog_post');
        $this->addSql('DROP TABLE __temp__blog_post');
        $this->addSql('CREATE INDEX IDX_BA5AE01DB03A8386 ON blog_post (created_by_id)');
        $this->addSql('CREATE INDEX IDX_BA5AE01D63379586 ON blog_post (comments_id)');
        $this->addSql('DROP INDEX IDX_9474526CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, user_id, body, created_on FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, body VARCHAR(255) NOT NULL, created_on DATE NOT NULL)');
        $this->addSql('INSERT INTO comment (id, user_id, body, created_on) SELECT id, user_id, body, created_on FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
    }
}
