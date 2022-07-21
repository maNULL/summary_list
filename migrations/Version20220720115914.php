<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220720115914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Справочник типов происшествий';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            '
            CREATE TABLE crime_type (
                id NUMBER(10) NOT NULL,
                title VARCHAR2(255) NOT NULL,
                marker_color VARCHAR2(30) NOT NULL,
                marker_icon VARCHAR2(100) NOT NULL,
                PRIMARY KEY(id)
            )'
        );
        $this->addSql('COMMENT ON COLUMN crime_type.id IS \'УИ записи\'');
        $this->addSql('COMMENT ON COLUMN crime_type.title IS \'Наименование типа происшествия\'');
        $this->addSql('COMMENT ON COLUMN crime_type.marker_color IS \'Цвет иконки для отображения на интерфейсе\'');
        $this->addSql('COMMENT ON COLUMN crime_type.marker_icon IS \'Классы иконок FontAwesome\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE crime_type');
    }
}
