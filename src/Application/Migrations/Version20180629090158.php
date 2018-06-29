<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180629090158 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment ADD security_access_control_check TINYINT(1) NOT NULL, ADD security_access_control_comment VARCHAR(255) DEFAULT NULL, ADD security_tracability_check TINYINT(1) NOT NULL, ADD security_tracability_comment VARCHAR(255) DEFAULT NULL, ADD security_saving_check TINYINT(1) NOT NULL, ADD security_saving_comment VARCHAR(255) DEFAULT NULL, ADD security_encryptioncheck TINYINT(1) NOT NULL, ADD security_encryptioncomment VARCHAR(255) DEFAULT NULL, ADD security_update_check TINYINT(1) NOT NULL, ADD security_update_comment VARCHAR(255) DEFAULT NULL, ADD security_other_check TINYINT(1) NOT NULL, ADD security_other_comment VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE registry_treatment DROP security_access_control_check, DROP security_access_control_comment, DROP security_tracability_check, DROP security_tracability_comment, DROP security_saving_check, DROP security_saving_comment, DROP security_encryptioncheck, DROP security_encryptioncomment, DROP security_update_check, DROP security_update_comment, DROP security_other_check, DROP security_other_comment');
    }
}
