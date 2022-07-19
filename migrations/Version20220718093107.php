<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220718093107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Места';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            CREATE TABLE place (
                fias_guid CHAR(36) NOT NULL,
                latitude NUMERIC(10, 7) NOT NULL,
                longitude NUMERIC(10, 7) NOT NULL,
                PRIMARY KEY(fias_guid)
            )'
        );
        $this->addSql('COMMENT ON COLUMN place.fias_guid IS \'(DC2Type:guid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE place');
    }
}
