<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

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
     */
    public function put($id, $data)
    {
        $form = new SuperCategoryForm();
        $form->bindValues(array_merge($data, ['super_category_id' => $id]));
        if ($form->isValid()) {
            $this->superCategoryService->create($form->getObject());
            return (new EmptyModel())
                ->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

//    /**
//     * Update one Party
//     *
//     * @param int $id
//     * @param array $data
//     * @return \Rend\View\Model\ModelInterface
//     */
//    public function patch($id, $data)
//    {
//        if (($party = $this->partyService->get($id)) != null) {
//            $form = new PartyForm();
//            $form->bind($party);
//            $form->setData($data);
//
//            if ($form->isValid()) {
//                $this->partyService->update($form->getData());
//                return (new EmptyModel())
//                    ->setStatus(204);
//            }
//
//            return (new ErrorModel($form))
//                ->setStatus(400);
//        }
//
//        return $this->notFoundAction();
//    }

    /**
     * @param SuperCategory $superCategory
     */
    public function setSuperCategoryService(SuperCategory $superCategory)
    {
        $this->superCategoryService = $superCategory;
    }
}
