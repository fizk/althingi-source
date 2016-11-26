<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Category as CategoryForm;
use Althingi\Lib\ServiceCategoryAwareInterface;
use Althingi\Service\Category;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\Helper\Http\Range;

class CategoryController extends AbstractRestfulController implements
    ServiceCategoryAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Category */
    private $categoryService;

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $superCategoryId = $this->params('super_category_id');
        $categoryId = $this->params('category_id');

        $form = new CategoryForm();
        $form->bindValues(array_merge($data, ['super_category_id' => $superCategoryId, 'category_id' => $categoryId]));
        if ($form->isValid()) {
            $this->categoryService->create($form->getObject());
            return (new EmptyModel())
                ->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    public function patch($id, $data)
    {
        if (($assembly = $this->categoryService->get($id)) != null) {
            $form = new CategoryForm();
            $form->bind($assembly);
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
     * @param Category $category
     */
    public function setCategoryService(Category $category)
    {
        $this->categoryService = $category;
    }
}
