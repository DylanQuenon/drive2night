<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231026095500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cars ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE cars ADD CONSTRAINT FK_95C71D14F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_95C71D14F675F31B ON cars (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cars DROP FOREIGN KEY FK_95C71D14F675F31B');
        $this->addSql('DROP INDEX IDX_95C71D14F675F31B ON cars');
        $this->addSql('ALTER TABLE cars DROP author_id');
    }
}
