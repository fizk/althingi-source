<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Party as PartyForm;
use Althingi\Lib\ServicePartyAwareInterface;
use Althingi\Service\Party;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\Helper\Http\Range;

class PartyController extends AbstractRestfulController implements
    ServicePartyAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Party */
    private $partyService;

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $form = new PartyForm();
        $form->bindValues(array_merge($data, ['party_id' => $id]));
        if ($form->isValid()) {
            $this->partyService->create($form->getObject());
            return (new EmptyModel())
                ->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * Update one Party
     *
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
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
                    ->setStatus(204);
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
