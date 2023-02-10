<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230210132707 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_survey ADD referentiel_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54A805DB139 FOREIGN KEY (referentiel_id) REFERENCES referentiel (id)');
        $this->addSql('CREATE INDEX IDX_E279C54A805DB139 ON maturity_survey (referentiel_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_survey DROP FOREIGN KEY FK_E279C54A805DB139');
        $this->addSql('DROP INDEX IDX_E279C54A805DB139 ON maturity_survey');
        $this->addSql('ALTER TABLE maturity_survey DROP referentiel_id');
    }
}
