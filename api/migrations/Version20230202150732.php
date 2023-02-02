<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230202150732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE bid_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sale_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE bid (id INT NOT NULL, bider_id INT NOT NULL, sale_id INT NOT NULL, unit_price DOUBLE PRECISION NOT NULL, date DATE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4AF2B3F37343B0DD ON bid (bider_id)');
        $this->addSql('CREATE INDEX IDX_4AF2B3F34A7E4868 ON bid (sale_id)');
        $this->addSql('CREATE TABLE sale (id INT NOT NULL, seller_id INT NOT NULL, model VARCHAR(255) NOT NULL, unit SMALLINT NOT NULL, base_unit_price DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E54BC0058DE820D9 ON sale (seller_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, user_name VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64924A232CF ON "user" (user_name)');
        $this->addSql('ALTER TABLE bid ADD CONSTRAINT FK_4AF2B3F37343B0DD FOREIGN KEY (bider_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bid ADD CONSTRAINT FK_4AF2B3F34A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale ADD CONSTRAINT FK_E54BC0058DE820D9 FOREIGN KEY (seller_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE bid_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sale_id_seq CASCADE');
        $this->addSql('ALTER TABLE bid DROP CONSTRAINT FK_4AF2B3F37343B0DD');
        $this->addSql('ALTER TABLE bid DROP CONSTRAINT FK_4AF2B3F34A7E4868');
        $this->addSql('ALTER TABLE sale DROP CONSTRAINT FK_E54BC0058DE820D9');
        $this->addSql('DROP TABLE bid');
        $this->addSql('DROP TABLE sale');
        $this->addSql('DROP TABLE "user"');
    }
}
