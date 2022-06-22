<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220622103216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_tag (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_tag_post (post_tag_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_B685A9A8AF08774 (post_tag_id), INDEX IDX_B685A9A4B89032C (post_id), PRIMARY KEY(post_tag_id, post_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_tag_tag (post_tag_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_3FBF9AB28AF08774 (post_tag_id), INDEX IDX_3FBF9AB2BAD26311 (tag_id), PRIMARY KEY(post_tag_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post_tag_post ADD CONSTRAINT FK_B685A9A8AF08774 FOREIGN KEY (post_tag_id) REFERENCES post_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tag_post ADD CONSTRAINT FK_B685A9A4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tag_tag ADD CONSTRAINT FK_3FBF9AB28AF08774 FOREIGN KEY (post_tag_id) REFERENCES post_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_tag_tag ADD CONSTRAINT FK_3FBF9AB2BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post_tag_post DROP FOREIGN KEY FK_B685A9A8AF08774');
        $this->addSql('ALTER TABLE post_tag_tag DROP FOREIGN KEY FK_3FBF9AB28AF08774');
        $this->addSql('ALTER TABLE post_tag_tag DROP FOREIGN KEY FK_3FBF9AB2BAD26311');
        $this->addSql('DROP TABLE post_tag');
        $this->addSql('DROP TABLE post_tag_post');
        $this->addSql('DROP TABLE post_tag_tag');
        $this->addSql('DROP TABLE tag');
    }
}
