<?php

namespace DeltaPhp\TagsDictionary\Controller;

use DeltaCore\AbstractController;
use DeltaPhp\Operator\EntityOperatorInterface;
use DeltaPhp\Operator\OperatorDiTrait;
use DeltaPhp\TagsDictionary\Entity\Dictionary;
use DeltaCore\AdminControllerInterface;
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
}
