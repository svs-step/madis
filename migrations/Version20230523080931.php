<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230523080931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE maturity_domain DROP FOREIGN KEY FK_E88F40BD805DB139');
        $this->addSql('ALTER TABLE maturity_domain ADD CONSTRAINT FK_E88F40BD805DB139 FOREIGN KEY (referentiel_id) REFERENCES maturity_referentiel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE maturity_question DROP FOREIGN KEY FK_88BB73A5115F0EE5');
        $this->addSql('ALTER TABLE maturity_question ADD CONSTRAINT FK_88BB73A5115F0EE5 FOREIGN KEY (domain_id) REFERENCES maturity_domain (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE maturity_domain DROP FOREIGN KEY FK_E88F40BD805DB139');
        $this->addSql('ALTER TABLE maturity_domain ADD CONSTRAINT FK_E88F40BD805DB139 FOREIGN KEY (referentiel_id) REFERENCES maturity_referentiel (id)');
        $this->addSql('ALTER TABLE maturity_question DROP FOREIGN KEY FK_88BB73A5115F0EE5');
        $this->addSql('ALTER TABLE maturity_question ADD CONSTRAINT FK_88BB73A5115F0EE5 FOREIGN KEY (domain_id) REFERENCES maturity_domain (id)');
    }
}
