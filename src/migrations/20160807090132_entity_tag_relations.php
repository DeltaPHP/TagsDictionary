<?php

use Phinx\Migration\AbstractMigration;

class EntityTagRelations extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE entity_tag_relations
(
-- Унаследована from table relations:  id bigint NOT NULL,
-- Унаследована from table relations:  created timestamp without time zone,
-- Унаследована from table relations:  changed timestamp without time zone,
-- Унаследована from table relations:  active boolean DEFAULT true,
-- Унаследована from table relations:  first bigint,
-- Унаследована from table relations:  second bigint,
  CONSTRAINT entity_tag_relations_pkey PRIMARY KEY (id),
  CONSTRAINT entity_tag_relations_first_fkey FOREIGN KEY (first)
      REFERENCES entities (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT entity_tag_relations_second_fkey FOREIGN KEY (second)
      REFERENCES tags (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (relations);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_82";
        $this->execute($sql);
    }
}
