<?php


namespace DeltaPhp\TagsDictionary\Entity;


use DeltaPhp\Operator\Entity\Entity;
use DeltaPhp\Operator\Entity\RelationEntity;

class EntityTagRelation extends RelationEntity
{
    public function __construct()
    {
        $this->setFirstClass(Entity::class);
        $this->setSecondClass(Tag::class);
    }
}
