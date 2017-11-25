<?php

namespace Althingi\Controller;

use Althingi\Form\SuperCategory as SuperCategoryForm;
use Althingi\Lib\ServiceSuperCategoryAwareInterface;
use Althingi\Service\SuperCategory;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\Helper\Http\Range;

class SuperCategoryController extends AbstractRestfulController implements
    ServiceSuperCategoryAwareInterface
{
    use Range;

    /** @var \Althingi\Service\SuperCategory */
    private $superCategoryService;

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\SuperCategory
     */
    public function put($id, $data)
    {
        $form = new SuperCategoryForm();
        $form->bindValues(array_merge($data, ['super_category_id' => $id]));
        if ($form->isValid()) {
            $affectedRows = $this->superCategoryService->save($form->getObject());
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * Update one Party
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\SuperCategory
     */
    public function patch($id, $data)
    {
        if (($superCategory = $this->superCategoryService->get($id)) != null) {
            $form = new SuperCategoryForm();
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

        return $this->notFoundAction();
    }

    /**
     * @param SuperCategory $superCategory
     */
    public function setSuperCategoryService(SuperCategory $superCategory)
    {
        $this->superCategoryService = $superCategory;
    }
}
