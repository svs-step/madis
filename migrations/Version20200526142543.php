<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200526142543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE conformite_traitement_reponse_action_protection_not_seen (reponse_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', mesurement_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_C2CDE080CF18BB82 (reponse_id), INDEX IDX_C2CDE0802EA38911 (mesurement_id), PRIMARY KEY(reponse_id, mesurement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection_not_seen ADD CONSTRAINT FK_C2CDE080CF18BB82 FOREIGN KEY (reponse_id) REFERENCES conformite_traitement_reponse (id)');
        $this->addSql('ALTER TABLE conformite_traitement_reponse_action_protection_not_seen ADD CONSTRAINT FK_C2CDE0802EA38911 FOREIGN KEY (mesurement_id) REFERENCES registry_mesurement (id)');
        $this->addSql('ALTER TABLE conformite_traitement ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE conformite_traitement_reponse_action_protection_not_seen');
        $this->addSql('ALTER TABLE conformite_traitement DROP created_at, DROP updated_at');
    }
}
