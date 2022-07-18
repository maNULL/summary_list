<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718073619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Адрес';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            CREATE TABLE address (
                address_id NUMBER(10) NOT NULL,
                address_text VARCHAR2(1000) DEFAULT NULL NULL,
                fias_guid CHAR(36) DEFAULT NULL NULL,
                apt_number VARCHAR2(10) DEFAULT NULL NULL,
                house NUMBER(1) NOT NULL,
                PRIMARY KEY(address_id)
            )'
        );
        $this->addSql('COMMENT ON COLUMN address.fias_guid IS \'(DC2Type:guid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE address');
    }
}
