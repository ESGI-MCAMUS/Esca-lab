<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220625135219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT fk_65d29b32523cab89');
        $this->addSql('ALTER TABLE "Gym" DROP CONSTRAINT fk_7880253e523cab89');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_327c5de7523cab89');
        $this->addSql('ALTER TABLE route DROP CONSTRAINT fk_2c42079bd2f03');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT fk_3bae0aa7bd2f03');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_327c5de7bd2f03');
        $this->addSql('ALTER TABLE event_user DROP CONSTRAINT fk_92589ae2a76ed395');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307f9d86650f');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT fk_6a2ca10c9d86650f');
        $this->addSql('ALTER TABLE user_user DROP CONSTRAINT fk_f7129a803ad8644e');
        $this->addSql('ALTER TABLE user_user DROP CONSTRAINT fk_f7129a80233d34c1');
        $this->addSql('ALTER TABLE "Gym" DROP CONSTRAINT fk_7880253e642b8210');
        $this->addSql('ALTER TABLE user_route DROP CONSTRAINT fk_e006db21a76ed395');
        $this->addSql('DROP SEQUENCE Gym_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE "public"."Gym_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reactions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "public"."user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "public"."Franchise" (id INT NOT NULL, admin INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "public"."Gym" (id INT NOT NULL, franchise_id INT DEFAULT NULL, admin_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, size BIGINT DEFAULT NULL, pc VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7880253E523CAB89 ON "public"."Gym" (franchise_id)');
        $this->addSql('CREATE INDEX IDX_7880253E642B8210 ON "public"."Gym" (admin_id)');
        $this->addSql('CREATE TABLE reactions (id INT NOT NULL, user_id_id INT DEFAULT NULL, route_id_id INT NOT NULL, html_reaction VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_38737FB39D86650F ON reactions (user_id_id)');
        $this->addSql('CREATE INDEX IDX_38737FB3E23BACF9 ON reactions (route_id_id)');
        $this->addSql('CREATE TABLE "public"."user" (id INT NOT NULL, franchise_id INT DEFAULT NULL, gym_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, birthdate DATE NOT NULL, username VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, otp INT DEFAULT NULL, is_deleted BOOLEAN NOT NULL, is_activated BOOLEAN NOT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_327C5DE7E7927C74 ON "public"."user" (email)');
        $this->addSql('CREATE INDEX IDX_327C5DE7523CAB89 ON "public"."user" (franchise_id)');
        $this->addSql('CREATE INDEX IDX_327C5DE7BD2F03 ON "public"."user" (gym_id)');
        $this->addSql('ALTER TABLE "public"."Gym" ADD CONSTRAINT FK_7880253E523CAB89 FOREIGN KEY (franchise_id) REFERENCES "public"."Franchise" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "public"."Gym" ADD CONSTRAINT FK_7880253E642B8210 FOREIGN KEY (admin_id) REFERENCES "public"."user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reactions ADD CONSTRAINT FK_38737FB39D86650F FOREIGN KEY (user_id_id) REFERENCES "public"."user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reactions ADD CONSTRAINT FK_38737FB3E23BACF9 FOREIGN KEY (route_id_id) REFERENCES route (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "public"."user" ADD CONSTRAINT FK_327C5DE7523CAB89 FOREIGN KEY (franchise_id) REFERENCES "public"."Franchise" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "public"."user" ADD CONSTRAINT FK_327C5DE7BD2F03 FOREIGN KEY (gym_id) REFERENCES "public"."Gym" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE "Franchise"');
        $this->addSql('DROP TABLE "Gym"');
        $this->addSql('DROP TABLE "user"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "public"."Gym" DROP CONSTRAINT FK_7880253E523CAB89');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B32523CAB89');
        $this->addSql('ALTER TABLE "public"."user" DROP CONSTRAINT FK_327C5DE7523CAB89');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA7BD2F03');
        $this->addSql('ALTER TABLE route DROP CONSTRAINT FK_2C42079BD2F03');
        $this->addSql('ALTER TABLE "public"."user" DROP CONSTRAINT FK_327C5DE7BD2F03');
        $this->addSql('ALTER TABLE "public"."Gym" DROP CONSTRAINT FK_7880253E642B8210');
        $this->addSql('ALTER TABLE event_user DROP CONSTRAINT FK_92589AE2A76ED395');
        $this->addSql('ALTER TABLE media DROP CONSTRAINT FK_6A2CA10C9D86650F');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F9D86650F');
        $this->addSql('ALTER TABLE reactions DROP CONSTRAINT FK_38737FB39D86650F');
        $this->addSql('ALTER TABLE user_user DROP CONSTRAINT FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE user_user DROP CONSTRAINT FK_F7129A80233D34C1');
        $this->addSql('ALTER TABLE user_route DROP CONSTRAINT FK_E006DB21A76ED395');
        $this->addSql('DROP SEQUENCE "public"."Gym_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE reactions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "public"."user_id_seq" CASCADE');
        $this->addSql('CREATE SEQUENCE Gym_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "Franchise" (id INT NOT NULL, admin INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "Gym" (id INT NOT NULL, franchise_id INT DEFAULT NULL, admin_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, size BIGINT DEFAULT NULL, pc VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7880253e642b8210 ON "Gym" (admin_id)');
        $this->addSql('CREATE INDEX idx_7880253e523cab89 ON "Gym" (franchise_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, franchise_id INT DEFAULT NULL, gym_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, birthdate DATE NOT NULL, username VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, otp INT DEFAULT NULL, is_deleted BOOLEAN NOT NULL, is_activated BOOLEAN NOT NULL, picture VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_327c5de7523cab89 ON "user" (franchise_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_327c5de7e7927c74 ON "user" (email)');
        $this->addSql('CREATE INDEX idx_327c5de7bd2f03 ON "user" (gym_id)');
        $this->addSql('ALTER TABLE "Gym" ADD CONSTRAINT fk_7880253e523cab89 FOREIGN KEY (franchise_id) REFERENCES "Franchise" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "Gym" ADD CONSTRAINT fk_7880253e642b8210 FOREIGN KEY (admin_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_327c5de7523cab89 FOREIGN KEY (franchise_id) REFERENCES "Franchise" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_327c5de7bd2f03 FOREIGN KEY (gym_id) REFERENCES "Gym" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE "public"."Franchise"');
        $this->addSql('DROP TABLE "public"."Gym"');
        $this->addSql('DROP TABLE reactions');
        $this->addSql('DROP TABLE "public"."user"');
    }
}
