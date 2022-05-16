<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220516133955 extends AbstractMigration
{
    protected $categories;

    private function getData(string $sql): array
    {
        $stmt = $this->connection->query($sql);

        return $stmt->fetchAll();
    }

    public function preUp(Schema $schema): void
    {
        $this->categories = $this->getData('SELECT id from category');
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        if (count($this->categories) > 0) {
            return;
        }

        $data = [
            [
                'name' => 'Traitement',
            ], [
                'name' => 'Sous-traitant',
            ], [
                'name' => 'Action de protection',
            ], [
                'name' => 'Plan d\'action',
            ], [
                'name' => 'Demande',
            ], [
                'name' => 'Preuves',
            ], [
                'name' => 'AIPD',
            ], [
                'name' => 'Bilan',
            ], [
                'name' => 'Conformité de l\'organisation',
            ], [
                'name' => 'Conformité des traitements',
            ],
        ];
        foreach ($data as $k => $item) {
            $this->addSql('INSERT INTO category(id, name, system, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())', [Uuid::uuid4(), $item['name'], true]);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }
}
