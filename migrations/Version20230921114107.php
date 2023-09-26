<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230921114107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_item ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE shopping_item ADD CONSTRAINT FK_6612795FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6612795FA76ED395 ON shopping_item (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_item DROP FOREIGN KEY FK_6612795FA76ED395');
        $this->addSql('DROP INDEX IDX_6612795FA76ED395 ON shopping_item');
        $this->addSql('ALTER TABLE shopping_item DROP user_id');
    }
}
