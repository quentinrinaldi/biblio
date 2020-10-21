<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201021094858 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE author_books DROP FOREIGN KEY FK_5C930A5FF675F31B');
        $this->addSql('CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artist_documents (artist_id INT NOT NULL, document_id INT NOT NULL, INDEX IDX_BE641900B7970CF8 (artist_id), INDEX IDX_BE641900C33F7837 (document_id), PRIMARY KEY(artist_id, document_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE borrowing (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at DATE NOT NULL, expected_return_date DATE NOT NULL, returned_at DATE DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_226E5897A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE copy (id INT AUTO_INCREMENT NOT NULL, document_id INT NOT NULL, physical_state VARCHAR(255) NOT NULL, INDEX IDX_4DBABB82C33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT NOT NULL, title VARCHAR(255) NOT NULL, published_date DATE DEFAULT NULL, thumbnail_url VARCHAR(255) NOT NULL, categories VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, stars SMALLINT DEFAULT NULL, publisher VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dvd (id INT AUTO_INCREMENT NOT NULL, duration TIME DEFAULT NULL, has_bonus TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE penalty (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type VARCHAR(255) NOT NULL, amount INT NOT NULL, end_date DATE DEFAULT NULL, INDEX IDX_AFE28FD8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone_number VARCHAR(12) NOT NULL, created_at DATE NOT NULL, subscription_expiration_date DATE NOT NULL, address VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artist_documents ADD CONSTRAINT FK_BE641900B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('ALTER TABLE artist_documents ADD CONSTRAINT FK_BE641900C33F7837 FOREIGN KEY (document_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE borrowing ADD CONSTRAINT FK_226E5897A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE copy ADD CONSTRAINT FK_4DBABB82C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE penalty ADD CONSTRAINT FK_AFE28FD8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE author_books');
        $this->addSql('ALTER TABLE book DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE book ADD pages_count INT NOT NULL, DROP title, DROP published_date, DROP thumbnail_url, DROP categories, DROP short_description, DROP long_description, DROP stars, CHANGE page_count id INT NOT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331BF396750 FOREIGN KEY (id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_documents DROP FOREIGN KEY FK_BE641900B7970CF8');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331BF396750');
        $this->addSql('ALTER TABLE copy DROP FOREIGN KEY FK_4DBABB82C33F7837');
        $this->addSql('ALTER TABLE borrowing DROP FOREIGN KEY FK_226E5897A76ED395');
        $this->addSql('ALTER TABLE penalty DROP FOREIGN KEY FK_AFE28FD8A76ED395');
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE author_books (author_id INT NOT NULL, book_isbn VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_5C930A5FF675F31B (author_id), INDEX IDX_5C930A5FD581BFEE (book_isbn), PRIMARY KEY(author_id, book_isbn)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE author_books ADD CONSTRAINT FK_5C930A5FD581BFEE FOREIGN KEY (book_isbn) REFERENCES book (isbn)');
        $this->addSql('ALTER TABLE author_books ADD CONSTRAINT FK_5C930A5FF675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE artist_documents');
        $this->addSql('DROP TABLE borrowing');
        $this->addSql('DROP TABLE copy');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE dvd');
        $this->addSql('DROP TABLE penalty');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE book DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE book ADD title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD published_date DATE DEFAULT NULL, ADD thumbnail_url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD page_count INT NOT NULL, ADD categories VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD short_description VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD long_description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD stars SMALLINT DEFAULT NULL, DROP id, DROP pages_count');
        $this->addSql('ALTER TABLE book ADD PRIMARY KEY (isbn)');
    }
}
