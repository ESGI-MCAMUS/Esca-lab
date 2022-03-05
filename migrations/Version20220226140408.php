<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220226140408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE Franchise_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "public"."Gym_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE route_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "public"."user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "public"."Franchise" (id INT NOT NULL, admin INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "public"."Gym" (id INT NOT NULL, franchise_id INT DEFAULT NULL, admin_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, size BIGINT DEFAULT NULL, pc VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7880253E523CAB89 ON "public"."Gym" (franchise_id)');
        $this->addSql('CREATE INDEX IDX_7880253E642B8210 ON "public"."Gym" (admin_id)');
        $this->addSql('CREATE TABLE route (id INT NOT NULL, gym_id INT DEFAULT NULL, opened SMALLINT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, difficulty INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2C42079BD2F03 ON route (gym_id)');
        $this->addSql('CREATE TABLE "public"."user" (id INT NOT NULL, franchise_id INT DEFAULT NULL, gym_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, birthdate DATE NOT NULL, username VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, otp INT DEFAULT NULL, is_deleted BOOLEAN NOT NULL, is_activated BOOLEAN NOT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_327C5DE7E7927C74 ON "public"."user" (email)');
        $this->addSql('CREATE INDEX IDX_327C5DE7523CAB89 ON "public"."user" (franchise_id)');
        $this->addSql('CREATE INDEX IDX_327C5DE7BD2F03 ON "public"."user" (gym_id)');
        $this->addSql('ALTER TABLE "public"."Gym" ADD CONSTRAINT FK_7880253E523CAB89 FOREIGN KEY (franchise_id) REFERENCES "public"."Franchise" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "public"."Gym" ADD CONSTRAINT FK_7880253E642B8210 FOREIGN KEY (admin_id) REFERENCES "public"."user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C42079BD2F03 FOREIGN KEY (gym_id) REFERENCES "public"."Gym" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "public"."user" ADD CONSTRAINT FK_327C5DE7523CAB89 FOREIGN KEY (franchise_id) REFERENCES "public"."Franchise" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "public"."user" ADD CONSTRAINT FK_327C5DE7BD2F03 FOREIGN KEY (gym_id) REFERENCES "public"."Gym" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "public"."Gym" DROP CONSTRAINT FK_7880253E523CAB89');
        $this->addSql('ALTER TABLE "public"."user" DROP CONSTRAINT FK_327C5DE7523CAB89');
        $this->addSql('ALTER TABLE route DROP CONSTRAINT FK_2C42079BD2F03');
        $this->addSql('ALTER TABLE "public"."user" DROP CONSTRAINT FK_327C5DE7BD2F03');
        $this->addSql('ALTER TABLE "public"."Gym" DROP CONSTRAINT FK_7880253E642B8210');
        $this->addSql('DROP SEQUENCE Franchise_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "public"."Gym_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE route_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "public"."user_id_seq" CASCADE');
        $this->addSql('DROP TABLE "public"."Franchise"');
        $this->addSql('DROP TABLE "public"."Gym"');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP TABLE "public"."user"');
    }
}
