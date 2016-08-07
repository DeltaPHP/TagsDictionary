<?php


namespace DeltaPhp\TagsDictionary\Entity;


use DeltaPhp\Operator\Entity\RelationEntity;

class DictionaryTagRelation extends RelationEntity
{
    public function __construct()
    {
        $this->setFirstClass(Dictionary::class);
        $this->setSecondClass(Tag::class);
    }
}
