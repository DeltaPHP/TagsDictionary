<?php

use Phinx\Migration\AbstractMigration;

class TagsDictionaryTagRelation extends AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE tag_dictionary_tag_relations
(
  CONSTRAINT tag_dictionary_tag_relations_pkey PRIMARY KEY (id),
  CONSTRAINT tag_dictionary_tag_relations_first_fkey FOREIGN KEY (first)
      REFERENCES tag_dictionaries (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT tag_dictionary_tag_relations_second_fkey FOREIGN KEY (second)
      REFERENCES tags (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (entity_tag_relations);
SQL;
        $this->execute($sql);

        $sql = "CREATE SEQUENCE uuid_complex_short_tables_103";
        $this->execute($sql);
    }
}
