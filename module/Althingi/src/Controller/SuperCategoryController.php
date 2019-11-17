<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServiceSuperCategoryAwareInterface;
use Althingi\Service\SuperCategory;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\Helper\Http\Range;
use Rend\View\Model\ItemModel;

class SuperCategoryController extends AbstractRestfulController implements
    ServiceSuperCategoryAwareInterface
{
    use Range;

    /** @var \Althingi\Service\SuperCategory */
    private $superCategoryService;

    /**
     * @param mixed $id
     * @return ErrorModel|ItemModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\SuperCategory
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $superCategory = $this->superCategoryService->get($id);
        return $superCategory
            ? (new ItemModel($superCategory))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * @return CollectionModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\SuperCategory[]
     * @206 Success
     */
    public function getList()
    {
        $superCategories = $this->superCategoryService->fetch();
        return (new CollectionModel($superCategories))
            ->setRange(0, count($superCategories), count($superCategories))
            ->setStatus(206);
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\SuperCategory
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put($id, $data)
    {
        $form = new Form\SuperCategory();
        $form->bindValues(array_merge($data, ['super_category_id' => $id]));
        if ($form->isValid()) {
            $affectedRows = $this->superCategoryService->save($form->getObject());
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * Update one Party
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\SuperCategory
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch($id, $data)
    {
        if (($superCategory = $this->superCategoryService->get($id)) != null) {
            $form = new Form\SuperCategory();
            $form->bind($superCategory);
            $form->setData($data);

            if ($form->isValid()) {
                $this->superCategoryService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return (new ErrorModel('Resource Not Found'))
            ->setStatus(404);
    }

    /**
     * @param SuperCategory $superCategory
     * @return $this
     */
    public function setSuperCategoryService(SuperCategory $superCategory)
    {
        $this->superCategoryService = $superCategory;
        return $this;
    }
}
