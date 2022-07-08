<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510181543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY restaurant_menu_fk');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY category_menu_fk');
        $this->addSql('DROP INDEX category_menu_fk ON menu');
        $this->addSql('DROP INDEX restaurant_menu_fk ON menu');
        $this->addSql('ALTER TABLE menu DROP restaurant_id, DROP category_id, DROP description, CHANGE item_image item_image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu ADD restaurant_id INT NOT NULL, ADD category_id INT NOT NULL, ADD description VARCHAR(50) NOT NULL, CHANGE item_image item_image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT restaurant_menu_fk FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT category_menu_fk FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX category_menu_fk ON menu (category_id)');
        $this->addSql('CREATE INDEX restaurant_menu_fk ON menu (restaurant_id)');
    }
}
