<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Constituency as ConstituencyForm;
use Althingi\Lib\ServiceConstituencyAwareInterface;
use Althingi\Service\Constituency;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\Helper\Http\Range;

class ConstituencyController extends AbstractRestfulController implements
    ServiceConstituencyAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Constituency */
    private $constituencyService;

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $form = new ConstituencyForm();
        $form->setData(array_merge($data, ['constituency_id' => $id]));
        if ($form->isValid()) {
            $this->constituencyService->create($form->getObject());
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
        if (($constituency = $this->constituencyService->get($id)) != null) {
            $form = new ConstituencyForm();
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
     */
    public function setConstituencyService(Constituency $constituency)
    {
        $this->constituencyService = $constituency;
    }
}
