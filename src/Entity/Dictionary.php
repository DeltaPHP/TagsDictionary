<?php


namespace DeltaPhp\TagsDictionary\Entity;


use DeltaPhp\Operator\DelegatingInterface;
use DeltaPhp\Operator\DelegatingTrait;
use DeltaPhp\Operator\Entity\NamedEntity;
use DeltaPhp\Operator\Entity\NamedEntityInterface;
use DeltaUtils\Object\Collection;
use DeltaPhp\Operator\Command\RelationLoadCommand;

class Dictionary extends NamedEntity implements NamedEntityInterface, DelegatingInterface
{
    use DelegatingTrait;

    /** @var  Tag[]|null */
    protected $tags;

    /**
     * @return Tag|Collection
     */
    public function getTags()
    {
        if (null === $this->tags) {
            $command = new RelationLoadCommand(DictionaryTagRelation::class, $this);
            $this->tags = $this->delegate($command);
        }
        return $this->tags;
    }
}
