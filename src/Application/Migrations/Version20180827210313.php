<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180827210313 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_domain ADD color VARCHAR(20) NOT NULL');

        $this->addSql('UPDATE maturity_domain set color="info" where name="Vie privée"');
        $this->addSql('UPDATE maturity_domain set color="success" where name="Organisation"');
        $this->addSql('UPDATE maturity_domain set color="warning" where name="Juridique"');
        $this->addSql('UPDATE maturity_domain set color="primary" where name="Violation de données"');
        $this->addSql('UPDATE maturity_domain set color="danger" where name="Technique"');
        $this->addSql('UPDATE maturity_domain set color="info" where name="Sensibilisation Formation"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE maturity_domain DROP color');
    }
}
