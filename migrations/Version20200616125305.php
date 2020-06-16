<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200616125305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conformite_traitement DROP FOREIGN KEY FK_85B1C39C61220EA6');
        $this->addSql('ALTER TABLE conformite_traitement ADD CONSTRAINT FK_85B1C39C61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conformite_traitement DROP FOREIGN KEY FK_85B1C39C61220EA6');
        $this->addSql('ALTER TABLE conformite_traitement ADD CONSTRAINT FK_85B1C39C61220EA6 FOREIGN KEY (creator_id) REFERENCES user_user (id)');
    }
}
