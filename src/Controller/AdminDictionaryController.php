<?php

namespace DeltaPhp\TagsDictionary\Controller;

use DeltaCore\AbstractController;
use DeltaPhp\Operator\EntityOperatorInterface;
use DeltaPhp\Operator\OperatorDiTrait;
use DeltaPhp\TagsDictionary\Entity\Dictionary;
use DeltaCore\AdminControllerInterface;
use UUID\Model\UuidComplexShortTables;

class AdminDictionaryController extends AbstractController implements AdminControllerInterface
{
    use OperatorDiTrait;

    public function listAction()
    {
        /** @var EntityOperatorInterface $operator */
        $operator = $this->getOperator();

        $countItems = $operator->count(Dictionary::class);

        $itemsPerPage = $this->getConfig(["TagsDictionary", "Admin", "itemsPerPage"], 10);
        $pageInfo = $this->getPageInfo($countItems, $itemsPerPage);

        $items = $operator->find(Dictionary::class, [], $pageInfo["perPage"], $pageInfo["offsetForPage"], "id");

        $this->getView()->assign("items", $items);
        $this->getView()->assignArray($pageInfo);
        $this->getView()->assign("countItems", $countItems);
        $titleEnd = $pageInfo["page"] == 1 ? "" : " страница " . $pageInfo["page"];
        $this->getView()->assign("pageTitle", "Словари тегов {$titleEnd}");
    }

    public function formAction(array $params = [])
    {
        if (isset($params["id"])) {
            $id = hexdec($params["id"]);
            $nm = $this->getOperator();
            $item = $nm->get(Dictionary::class, $id);
            if (!$item) {
                throw new \RuntimeException("Bad item id {$params["id"]}");
            }
            $this->getView()->assign("item", $item);
        } else {
            $id = $this->getOperator()->genId(Dictionary::class);
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
        
        /** @var Dictionary $item */
        $item = $operator->get(Dictionary::class, $id) ?: $operator->create(Dictionary::class);
        if (empty($item)) {
            throw new \NotFoundException("item not found");
        }
        $operator->load($item, $requestParams);
        $item->setId($id);
        $operator->save($item);

        $this->getResponse()->redirect($this->getRouteUrl("tags_dictionary_list"));
    }

    public function rmAction(array $params = [])
    {
        if (isset($params["id"])) {
            $id = hexdec($params["id"]);
            $nm = $this->getOperator();
            $item = $nm->get(Dictionary::class, $id);
            if (!$item) {
                throw new \RuntimeException("Bad item id {$params["id"]}");
            }
            $this->getOperator()->delete($item);
        }
        $this->getResponse()->redirect($this->getRouteUrl("tags_dictionary_list"));
    }
}
