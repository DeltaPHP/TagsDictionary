<?php

namespace DeltaPhp\TagsDictionary\Controller;

use DeltaCore\AbstractController;
use DeltaDb\D2QL\Criteria;
use DeltaDb\D2QL\Join;
use DeltaDb\D2QL\Where;
use DeltaPhp\Operator\Command\InfoWorkerCommand;
use DeltaPhp\Operator\EntityOperatorInterface;
use DeltaPhp\Operator\OperatorDiTrait;
use DeltaPhp\TagsDictionary\Entity\Dictionary;
use DeltaCore\AdminControllerInterface;
use DeltaPhp\TagsDictionary\Entity\DictionaryTagRelation;
use Pages\Model\Page;
use Pages\Model\PageImageRelation;
use UUID\Model\UuidComplexShortTables;
use DeltaPhp\TagsDictionary\Entity\Tag;

class AdminTagController extends AbstractController implements AdminControllerInterface
{
    use OperatorDiTrait;

    public function listAction(array $params = [])
    {
        /** @var EntityOperatorInterface $operator */
        $operator = $this->getOperator();

        $itemsPerPage = $this->getConfig(["Tags", "Admin", "itemsPerPage"], 10);

        if (isset($params["dictionary"])) {
            $dictionaryId = hexdec($params["dictionary"]);
            /** @var Dictionary $dictionary */
            $dictionary = $operator->get(Dictionary::class, $dictionaryId);
            $items = $dictionary->getTags();
            $this->getView()->assign("dictionary", $dictionary);
            $title = "Словари тегов {$dictionary->getTitle()}";
            $this->getView()->assign("countItems", $dictionary->getTags()->count());
        } else {
            $countItems = $operator->count(Tag::class);
            $pageInfo = $this->getPageInfo($countItems, $itemsPerPage);
            $items = $operator->find(Tag::class, [], $pageInfo["perPage"], $pageInfo["offsetForPage"], "id");
            $this->getView()->assignArray($pageInfo);
            $this->getView()->assign("countItems", $countItems);
            $titleEnd = $pageInfo["page"] == 1 ? "" : " страница " . $pageInfo["page"];
            $title = "Словари тегов {$titleEnd}";
        }

        $this->getView()->assign("items", $items);

        $this->getView()->assign("pageTitle", $title);
    }

    public function formAction(array $params = [])
    {
        /** @var EntityOperatorInterface $operator */
        $operator = $this->getOperator();
        if (isset($params["dictionary"])) {
            $dictionaryId = hexdec($params["dictionary"]);
            /** @var Dictionary $dictionary */
            $dictionary = $operator->get(Dictionary::class, $dictionaryId);
            $this->getView()->assign("dictionary", $dictionary);


            /** @var Criteria $dictCriteria */
            $dictCriteria = $operator->execute(
                new InfoWorkerCommand("relatedCriteria", DictionaryTagRelation::class, ["currentClass" => Tag::class, "joinType" => Join::TYPE_LEFT])
            );

            $dictTable = $operator->execute(
                new InfoWorkerCommand("table", Dictionary::class)
            );
            $dictWhere = $dictCriteria->createWhereGroup();
            $dictWhere->createWhere($dictTable, "id", $dictionaryId, "<>");
            $dictWhere->createWhere($dictTable, "id", "is null", null, Where::REL_OR, Where::TYPE_EXP);


            $tags = $operator->find(Tag::class, $dictCriteria);
            $this->getView()->assign("aTags", $tags);
        }

        if (isset($params["id"])) {
            $id = hexdec($params["id"]);
            $nm = $this->getOperator();
            $item = $nm->get(Tag::class, $id);
            if (!$item) {
                throw new \RuntimeException("Bad item id {$params["id"]}");
            }
            $this->getView()->assign("item", $item);
        } else {
            $id = $this->getOperator()->genId(Tag::class);
            $this->getView()->assign("id", $id);
        }
    }

    public function saveAction()
    {
        $this->autoRenderOff();
        //save item
        $request = $this->getRequest();
        $operator = $this->getOperator();
        $requestParams = $request->getParams();
        if (isset($requestParams["id"])) {
            $id = $operator->create(UuidComplexShortTables::class, ["value" => $requestParams["id"]]);
            unset($requestParams["id"]);
        }

        /** @var Tag $item */
        $item = $operator->get(Tag::class, $id) ?: $operator->create(Tag::class);
        if (empty($item)) {
            throw new \LogicException("item not found");
        }
        $operator->load($item, $requestParams);
        $item->setId($id);
        $operator->save($item);

        $this->getResponse()->redirect($this->getRouteUrl("tags_list"));
    }

    public function rmAction(array $params = [])
    {
        if (isset($params["id"])) {
            $id = hexdec($params["id"]);
            $nm = $this->getOperator();
            $item = $nm->get(Tag::class, $id);
            if (!$item) {
                throw new \RuntimeException("Bad item id {$params["id"]}");
            }
            $this->getOperator()->delete($item);
        }
        $this->getResponse()->redirect($this->getRouteUrl("tags_list"));
    }

    public function addToDictionaryAction(array $params = [])
    {
        $this->autoRenderOff();
        $dictionary = $this->getRequest()->getParam("dictionary");
        $tag = $this->getRequest()->getParam("tag");

        if (!$dictionary) {
            throw new \RuntimeException("not set dictionary");
        } else {
            $dictionary = hexdec($dictionary);
        }
        if (!$tag) {
            throw new \RuntimeException("not set tag");
        } else {
            $tag = hexdec($tag);
        }

        $operator = $this->getOperator();
        $relation = $operator->find(DictionaryTagRelation::class, ["first" => $dictionary, "second" => $tag])->firstOrFalse();
        if ($relation) {
            $this->getResponse()->redirect($this->getRouteUrl("tags_list", ["dictionary" => dechex($dictionary)]));
        }

        $relation = $operator->create(DictionaryTagRelation::class);
        /** @var $relation DictionaryTagRelation */
        $relation->setFirst($dictionary);
        $relation->setSecond($tag);
        $operator->save($relation);
        $this->getResponse()->redirect($this->getRouteUrl("tags_list", ["dictionary" => dechex($dictionary)]));
    }

    public function rmFromDictionaryAction(array $params = [])
    {
        $this->autoRenderOff();
        $dictionary = $this->getRequest()->getParam("dictionary");
        $tag = $this->getRequest()->getParam("tag");

        if (!$dictionary) {
            throw new \RuntimeException("not set dictionary");
        } else {
            $dictionary = hexdec($dictionary);
        }
        if (!$tag) {
            throw new \RuntimeException("not set tag");
        } else {
            $tag = hexdec($tag);
        }

        $operator = $this->getOperator();
        $relations = $operator->find(DictionaryTagRelation::class, ["first" => $dictionary, "second" => $tag]);
        if ($relations->isEmpty()) {
            $this->getResponse()->redirect($this->getRouteUrl("tags_list", ["dictionary" => dechex($dictionary)]));
        }
        foreach ($relations as $relation) {
            $operator->delete($relation);
        }
        $this->getResponse()->redirect($this->getRouteUrl("tags_list", ["dictionary" => dechex($dictionary)]));

    }
}
