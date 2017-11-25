<?php

namespace Althingi\Controller;

use Althingi\Form\Party as PartyForm;
use Althingi\Lib\ServicePartyAwareInterface;
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
     */
    public function get($id)
    {
        $party = $this->partyService->get($id);
        return $party
            ? new ItemModel($party)
            : $this->notFoundAction();
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Party
     */
    public function put($id, $data)
    {
        $form = new PartyForm();
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
     */
    public function patch($id, $data)
    {
        if (($party = $this->partyService->get($id)) != null) {
            $form = new PartyForm();
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

        return $this->notFoundAction();
    }

    /**
     * @param Party $party
     */
    public function setPartyService(Party $party)
    {
        $this->partyService = $party;
    }
}
