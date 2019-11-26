<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServiceCategoryAwareInterface;
use Althingi\Service\Category;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\CollectionModel;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\Helper\Http\Range;
use Rend\View\Model\ItemModel;

class CategoryController extends AbstractRestfulController implements
    ServiceCategoryAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Category */
    private $categoryService;

    /**
     * @param mixed $id
     * @return ItemModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Category
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $category = $this->categoryService->get($id);
        return $category
            ? (new ItemModel($category))->setStatus(200)
            : (new ErrorModel('Resource not found'))->setStatus(404);
    }

    /**
     * @return CollectionModel|\Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Category[]
     * @206 Success
     */
    public function getList()
    {
        $id = $this->params('super_category_id');
        $categories = $this->categoryService->fetch($id);
        return (new CollectionModel($categories))
            ->setStatus(206)
            ->setRange(0, count($categories), count($categories));
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Category
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put($id, $data)
    {
        $superCategoryId = $this->params('super_category_id');
        $categoryId = $this->params('category_id');

        $form = new Form\Category();
        $form->bindValues(array_merge($data, ['super_category_id' => $superCategoryId, 'category_id' => $categoryId]));
        if ($form->isValid()) {
            $affectedRows = $this->categoryService->save($form->getObject());
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Category
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch($id, $data)
    {
        if (($category = $this->categoryService->get($id)) != null) {
            $form = new Form\Category();
            $form->bind($category);
            $form->setData($data);

            if ($form->isValid()) {
                $this->categoryService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return (new ErrorModel('Resource not found'))
            ->setStatus(404);
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\CategoryAndCount[]
     * @206 Success
     */
    public function assemblySummaryAction()
    {
        $assemblyId = $this->params('id');
        $categorySummary = $this->categoryService->fetchByAssembly($assemblyId);

        return (new CollectionModel($categorySummary))
            ->setStatus(206)
            ->setRange(0, count($categorySummary), count($categorySummary));
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function setCategoryService(Category $category)
    {
        $this->categoryService = $category;
        return $this;
    }
}
