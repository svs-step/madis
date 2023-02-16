<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213132653 extends AbstractMigration
{
    protected $prefIds = [];

    public function getDescription(): string
    {
        return 'Add email notification preference for users that do not have any yet';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $usersWithoutEmailPreferences = $this->connection->query('SELECT `id` FROM user_user WHERE `email_notification_preference_id` IS NULL')->fetchAll();

        foreach ($usersWithoutEmailPreferences as $user) {
            $prefId                     = Uuid::uuid4()->__toString();
            $this->prefIds[$user['id']] = $prefId;
            $this->addSql('INSERT INTO user_notification_preference (id, frequency, enabled, start_hour, start_day, start_week, notification_mask, last_sent) VALUES ("' . $prefId . '", "none", 1, NULL, NULL, NULL, 0, NOW() ) ');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
    }

    public function postUp(Schema $schema): void
    {
        foreach ($this->prefIds as $userId => $prefId) {
            $this->connection->update(
                'user_user',
                [
                    'email_notification_preference_id' => $prefId,
                ],
                ['id' => $userId]
            );
        }
    }
}
