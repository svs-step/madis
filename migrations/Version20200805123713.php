<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200805123713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_collectivite_referent (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_633E7274A76ED395 (user_id), INDEX IDX_633E7274BD56F776 (collectivity_id), PRIMARY KEY(user_id, collectivity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_collectivite_referent ADD CONSTRAINT FK_633E7274A76ED395 FOREIGN KEY (user_id) REFERENCES user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_collectivite_referent ADD CONSTRAINT FK_633E7274BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_collectivite_referent');
        $this->addSql('ALTER TABLE registry_conformite_organisation_evaluation CHANGE created_at created_at DATETIME DEFAULT \'2020-07-29 09:56:01\' NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT \'2020-07-29 09:56:01\' NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
