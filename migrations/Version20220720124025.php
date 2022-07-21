<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220720124025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Справочник подразделений МВД';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            CREATE TABLE department (
                id NUMBER(10) NOT NULL,
                name VARCHAR2(500) NOT NULL,
                short_name VARCHAR2(255) NOT NULL,
                extra_short_name VARCHAR2(255) NOT NULL,
                PRIMARY KEY(id)
            )'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE department');
    }
}
