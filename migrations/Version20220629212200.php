<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220629212200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_post ADD content VARCHAR(255) NOT NULL, ADD created_on DATE NOT NULL, ADD head_image VARCHAR(255) NOT NULL, ADD extra_images LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', CHANGE description subtitle VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_post ADD description VARCHAR(255) NOT NULL, DROP subtitle, DROP content, DROP created_on, DROP head_image, DROP extra_images');
    }
}
