<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Injector\ServicePartyAwareInterface;
use Althingi\Service\Party;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\Helper\Http\Range;
use Rend\View\Model\ItemModel;

class PartyController extends AbstractRestfulController implements
    ServicePartyAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Party */
    private $partyService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Party
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $party = $this->partyService->get($id);
        return $party
            ? (new ItemModel($party))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Party
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put($id, $data)
    {
        $form = new Form\Party();
        $form->bindValues(array_merge($data, ['party_id' => $id]));
        if ($form->isValid()) {
            $affectedRow = $this->partyService->save($form->getObject());
            return (new EmptyModel())
                ->setStatus($affectedRow === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * Update one Party
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Party
     * @205 Updated
     * @400 Invalid input
     * @404 Resource not found
     */
    public function patch($id, $data)
    {
        if (($party = $this->partyService->get($id)) != null) {
            $form = new Form\Party();
            $form->bind($party);
            $form->setData($data);

            if ($form->isValid()) {
                $this->partyService->update($form->getData());
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
     * @param Party $party
     * @return $this;
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
        return $this;
    }
}
