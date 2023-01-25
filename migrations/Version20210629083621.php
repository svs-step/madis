<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210629083621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admin_duplicated_object (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', duplication_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', duplicat_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', origin_object_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_423F279CBD56F776 (collectivity_id), INDEX IDX_423F279CA78FD7B3 (duplication_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin_duplicated_object ADD CONSTRAINT FK_423F279CBD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_duplicated_object ADD CONSTRAINT FK_423F279CA78FD7B3 FOREIGN KEY (duplication_id) REFERENCES admin_duplication (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE admin_duplication_collectivity');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE admin_duplication_collectivity (duplication_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', collectivity_id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', INDEX IDX_59EF3717A78FD7B3 (duplication_id), INDEX IDX_59EF3717BD56F776 (collectivity_id), PRIMARY KEY(duplication_id, collectivity_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE admin_duplication_collectivity ADD CONSTRAINT FK_59EF3717A78FD7B3 FOREIGN KEY (duplication_id) REFERENCES admin_duplication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_duplication_collectivity ADD CONSTRAINT FK_59EF3717BD56F776 FOREIGN KEY (collectivity_id) REFERENCES user_collectivity (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE admin_duplicated_object');
    }
}
