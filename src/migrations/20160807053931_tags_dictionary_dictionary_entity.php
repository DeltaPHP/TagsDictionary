<?php

use Phinx\Migration\AbstractMigration;

class TagsDictionaryDictionaryEntity extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE tag_dictionaries
(
-- Унаследована from table named:  id bigint NOT NULL,
-- Унаследована from table named:  created timestamp without time zone,
-- Унаследована from table named:  changed timestamp without time zone,
-- Унаследована from table named:  active boolean DEFAULT true,
-- Унаследована from table named:  title text,
-- Унаследована from table named:  description text,
  CONSTRAINT tag_dictionaries_pkey PRIMARY KEY (id)
)
INHERITS (named);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_16";
        $this->execute($sql);
    }
}
