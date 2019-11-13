<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191113191955 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE snowtrick_category (snowtrick_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_EEE6F24D492EB8F3 (snowtrick_id), INDEX IDX_EEE6F24D12469DE2 (category_id), PRIMARY KEY(snowtrick_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE snowtrick_category ADD CONSTRAINT FK_EEE6F24D492EB8F3 FOREIGN KEY (snowtrick_id) REFERENCES snowtrick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE snowtrick_category ADD CONSTRAINT FK_EEE6F24D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE snowtrick_category DROP FOREIGN KEY FK_EEE6F24D12469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE snowtrick_category');
    }
}
