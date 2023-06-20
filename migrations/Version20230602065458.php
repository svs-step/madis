<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230602065458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $id = Uuid::uuid4();
        $this->addSql('INSERT INTO maturity_referentiel(id, name, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?)', [$id, 'Indice de maturité', null, date_create()->format('Y-m-d H:i:s'), date_create()->format('Y-m-d H:i:s')]);
        $this->addSql('UPDATE maturity_domain SET referentiel_id=? WHERE referentiel_id IS NULL', [$id]);
        $this->addSql('UPDATE maturity SET referentiel_id=? WHERE referentiel_id IS NULL', [$id]);
        $this->addSql('UPDATE maturity_survey SET referentiel_id=? WHERE referentiel_id IS NULL', [$id]);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }
}
