<?php

use Phinx\Migration\AbstractMigration;

class TagsDictionaryTagEntity extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE tags
(
  CONSTRAINT tags_pkey PRIMARY KEY (id)
)
INHERITS (named);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_15";
        $this->execute($sql);
    }
}
