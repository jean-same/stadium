<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211026114421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profil_event (profil_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_75A2D1E3275ED078 (profil_id), INDEX IDX_75A2D1E371F7E88B (event_id), PRIMARY KEY(profil_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil_activity (profil_id INT NOT NULL, activity_id INT NOT NULL, INDEX IDX_B09D7CF7275ED078 (profil_id), INDEX IDX_B09D7CF781C06096 (activity_id), PRIMARY KEY(profil_id, activity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE profil_event ADD CONSTRAINT FK_75A2D1E3275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profil_event ADD CONSTRAINT FK_75A2D1E371F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profil_activity ADD CONSTRAINT FK_B09D7CF7275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profil_activity ADD CONSTRAINT FK_B09D7CF781C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE account ADD association_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A4EFB9C8A5 ON account (association_id)');
        $this->addSql('ALTER TABLE activity ADD association_id INT NOT NULL');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AEFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('CREATE INDEX IDX_AC74095AEFB9C8A5 ON activity (association_id)');
        $this->addSql('ALTER TABLE event ADD association_id INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7EFB9C8A5 ON event (association_id)');
        $this->addSql('ALTER TABLE lesson ADD activity_id INT NOT NULL');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F381C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('CREATE INDEX IDX_F87474F381C06096 ON lesson (activity_id)');
        $this->addSql('ALTER TABLE profil ADD account_id INT NOT NULL, ADD file_id INT DEFAULT NULL, ADD association_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B2979B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B29793CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE profil ADD CONSTRAINT FK_E6D6B297EFB9C8A5 FOREIGN KEY (association_id) REFERENCES association (id)');
        $this->addSql('CREATE INDEX IDX_E6D6B2979B6B5FBA ON profil (account_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6D6B29793CB796C ON profil (file_id)');
        $this->addSql('CREATE INDEX IDX_E6D6B297EFB9C8A5 ON profil (association_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE profil_event');
        $this->addSql('DROP TABLE profil_activity');
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A4EFB9C8A5');
        $this->addSql('DROP INDEX UNIQ_7D3656A4EFB9C8A5 ON account');
        $this->addSql('ALTER TABLE account DROP association_id');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AEFB9C8A5');
        $this->addSql('DROP INDEX IDX_AC74095AEFB9C8A5 ON activity');
        $this->addSql('ALTER TABLE activity DROP association_id');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7EFB9C8A5');
        $this->addSql('DROP INDEX IDX_3BAE0AA7EFB9C8A5 ON event');
        $this->addSql('ALTER TABLE event DROP association_id');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F381C06096');
        $this->addSql('DROP INDEX IDX_F87474F381C06096 ON lesson');
        $this->addSql('ALTER TABLE lesson DROP activity_id');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B2979B6B5FBA');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B29793CB796C');
        $this->addSql('ALTER TABLE profil DROP FOREIGN KEY FK_E6D6B297EFB9C8A5');
        $this->addSql('DROP INDEX IDX_E6D6B2979B6B5FBA ON profil');
        $this->addSql('DROP INDEX UNIQ_E6D6B29793CB796C ON profil');
        $this->addSql('DROP INDEX IDX_E6D6B297EFB9C8A5 ON profil');
        $this->addSql('ALTER TABLE profil DROP account_id, DROP file_id, DROP association_id');
    }
}
