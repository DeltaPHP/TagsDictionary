<?php

use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Worker\WorkerInterface;
use DeltaPhp\Operator\WorkersContainerInterface;
use DeltaPhp\Operator\Command\PreCommandInterface;
use DeltaPhp\TagsDictionary\Entity\Tag;
use DeltaPhp\TagsDictionary\Entity\Dictionary;
use \DeltaPhp\TagsDictionary\Entity\DictionaryTagRelation;
use DeltaPhp\Operator\Command\RelationLoadCommand;

return [
    "TagEntitiesWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            $w->setTable("tags");
            $w->addFields(["title", "description"]);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 15,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => Tag::class,
            PreCommandInterface::PREFIX_COMMAND_PRE . CommandInterface::COMMAND_FIND => Tag::class,
            CommandInterface::COMMAND_GET => Tag::class,
            CommandInterface::COMMAND_COUNT => Tag::class,
            CommandInterface::COMMAND_SAVE => Tag::class,
            CommandInterface::COMMAND_DELETE => Tag::class,
            CommandInterface::COMMAND_LOAD => Tag::class,
            CommandInterface::COMMAND_RESERVE => Tag::class,
            CommandInterface::COMMAND_GENERATE_ID => Tag::class,
            CommandInterface::COMMAND_WORKER_INFO => Tag::class,
        ],
    ],

    "TagsDictionaryEntitiesWorker" => [
        function (WorkersContainerInterface $s) {
            $w = new \DeltaPhp\Operator\Worker\PostgresWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            $w->setTable("tag_dictionaries");
            $w->addFields(["title", "description"]);
            return $w;
        },
        WorkerInterface::PARAM_TABLEID => 16,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            CommandInterface::COMMAND_FIND => Dictionary::class,
            PreCommandInterface::PREFIX_COMMAND_PRE . CommandInterface::COMMAND_FIND => Dictionary::class,
            CommandInterface::COMMAND_GET => Dictionary::class,
            CommandInterface::COMMAND_COUNT => Dictionary::class,
            CommandInterface::COMMAND_SAVE => Dictionary::class,
            CommandInterface::COMMAND_DELETE => Dictionary::class,
            CommandInterface::COMMAND_LOAD => Dictionary::class,
            CommandInterface::COMMAND_RESERVE => Dictionary::class,
            CommandInterface::COMMAND_GENERATE_ID => Dictionary::class,
            CommandInterface::COMMAND_WORKER_INFO => Dictionary::class,
        ],
    ],

    "TagsDictionaryTagWorker" => [
        function ($s) {
            $worker = new \DeltaPhp\Operator\Worker\RelationsWorker(Dictionary::class, Tag::class, DictionaryTagRelation::class, "tag_dictionary_tag_relations");
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $worker->setAdapter($adapter);
            return $worker;
        },
        WorkerInterface::PARAM_TABLEID => 101,
        WorkerInterface::PARAM_ACTIONS_MAP => [
            RelationLoadCommand::COMMAND_RELATION_LOAD => DictionaryTagRelation::class,
            CommandInterface::COMMAND_FIND => DictionaryTagRelation::class,
            CommandInterface::COMMAND_LOAD => DictionaryTagRelation::class,
            CommandInterface::COMMAND_RESERVE => DictionaryTagRelation::class,
            CommandInterface::COMMAND_GENERATE_ID => DictionaryTagRelation::class,
            CommandInterface::COMMAND_GET => DictionaryTagRelation::class,
            CommandInterface::COMMAND_COUNT => DictionaryTagRelation::class,
            CommandInterface::COMMAND_SAVE => DictionaryTagRelation::class,
            CommandInterface::COMMAND_DELETE => DictionaryTagRelation::class,
            \DeltaPhp\Operator\Command\RelationParamsCommand::COMMAND_RELATION_PARAMS => DictionaryTagRelation::class,
            CommandInterface::COMMAND_WORKER_INFO => DictionaryTagRelation::class,
        ],
    ],

];
