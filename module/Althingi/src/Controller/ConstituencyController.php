<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServiceConstituencyAwareInterface;
use Althingi\Service\Constituency;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\Helper\Http\Range;
use Rend\View\Model\ItemModel;

class ConstituencyController extends AbstractRestfulController implements
    ServiceConstituencyAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Constituency */
    private $constituencyService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Constituency
     */
    public function get($id)
    {
        $constituency = $this->constituencyService->get($id);
        return $constituency
            ? new ItemModel($constituency)
            : $this->notFoundAction();
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input Althingi\Form\Constituency
     */
    public function put($id, $data)
    {
        $form = new Form\Constituency();
        $form->setData(array_merge($data, ['constituency_id' => $id]));
        if ($form->isValid()) {
            $affectedRows = $this->constituencyService->save($form->getObject());
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
     * @input Althingi\Form\Constituency
     */
    public function patch($id, $data)
    {
        if (($constituency = $this->constituencyService->get($id)) != null) {
            $form = new Form\Constituency();
            $form->bind($constituency);
            $form->setData($data);

            if ($form->isValid()) {
                $this->constituencyService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param Constituency $constituency
     * @return $this;
     */
    public function setConstituencyService(Constituency $constituency)
    {
        $this->constituencyService = $constituency;
        return $this;
    }
}
