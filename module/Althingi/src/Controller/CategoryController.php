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

    public function get($id)
    {
        return (new ItemModel($this->categoryService->get($id)))->setStatus(200);
    }

    public function getList()
    {
        $id = $this->params('super_category_id');
        return (new CollectionModel(
            $this->categoryService->fetch($id)
        ))->setStatus(206);
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Category
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

        return $this->notFoundAction();
    }

    /**
     * @return CollectionModel
     * @output \Althingi\Model\CategoryAndCount[]
     */
    public function assemblySummaryAction()
    {
        $assemblyId = $this->params('id');
        $categorySummary = $this->categoryService->fetchByAssembly($assemblyId);

        return (new CollectionModel($categorySummary))
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
