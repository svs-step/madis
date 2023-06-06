<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230511101357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_referentiel CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE maturity_survey DROP INDEX UNIQ_E279C54A805DB139, ADD INDEX IDX_E279C54A805DB139 (referentiel_id)');
        $this->addSql('ALTER TABLE maturity_survey DROP FOREIGN KEY FK_E279C54A805DB139');
        //$this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54A805DB139 FOREIGN KEY (referentiel_id) REFERENCES maturity_referentiel (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE maturity_referentiel CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE maturity_survey DROP INDEX IDX_E279C54A805DB139, ADD UNIQUE INDEX UNIQ_E279C54A805DB139 (referentiel_id)');
        //$this->addSql('ALTER TABLE maturity_survey DROP FOREIGN KEY FK_E279C54A805DB139');
        $this->addSql('ALTER TABLE maturity_survey CHANGE referentiel_id referentiel_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE collectivity_id collectivity_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', CHANGE creator_id creator_id CHAR(36) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE maturity_survey ADD CONSTRAINT FK_E279C54A805DB139 FOREIGN KEY (referentiel_id) REFERENCES maturity_referentiel (id)');
    }
}
