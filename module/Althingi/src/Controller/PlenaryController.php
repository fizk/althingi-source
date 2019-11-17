<?php

namespace Althingi\Controller;

use Althingi\Form;
use Althingi\Service\Plenary;
use Althingi\Injector\ServicePlenaryAwareInterface;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\ErrorModel;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\CollectionModel;
use Rend\Helper\Http\Range;
use Rend\View\Model\ItemModel;

class PlenaryController extends AbstractRestfulController implements
    ServicePlenaryAwareInterface
{
    use Range;

    /** @var \Althingi\Service\Plenary */
    private $plenaryService;

    /**
     * @param mixed $id
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Plenary
     * @200 Success
     * @404 Resource not found
     */
    public function get($id)
    {
        $assemblyId = $this->params('id');
        $plenaryId = $this->params('plenary_id');

        $plenary = $this->plenaryService->get($assemblyId, $plenaryId);

        return $plenary
            ? (new ItemModel($plenary))->setStatus(200)
            : (new ErrorModel('Resource Not Found'))->setStatus(404);
    }

    /**
     * @return \Rend\View\Model\ModelInterface
     * @output \Althingi\Model\Plenary[]
     * @206 Success
     */
    public function getList()
    {
        $assemblyId = $this->params('id', null);
        $count = $this->plenaryService->countByAssembly($assemblyId);
        $range = $this->getRange($this->getRequest(), $count);

        $plenaries = $this->plenaryService->fetchByAssembly(
            $assemblyId,
            $range->getFrom(),
            $count
            //($range->getFrom()-$range->getTo())
        );
        return (new CollectionModel($plenaries))
            ->setStatus(206)
            ->setRange($range->getFrom(), $range->getFrom() + count($plenaries), $count);
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Plenary
     * @201 Created
     * @205 Updated
     * @400 Invalid input
     */
    public function put($id, $data)
    {
        $form = (new Form\Plenary())
            ->setData(
                array_merge(
                    $data,
                    ['assembly_id' => $this->params('id'), 'plenary_id' => $id]
                )
            );

        if ($form->isValid()) {
            $affectedRows = $this->plenaryService->save($form->getObject());
            return (new EmptyModel())->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    /**
     * @param $id
     * @param $data
     * @return \Rend\View\Model\ModelInterface
     * @input \Althingi\Form\Plenary
     * @205 Updated
     * @400 Invalid input
     */
    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $plenaryId = $this->params('plenary_id');

        if (($assembly = $this->plenaryService->get($assemblyId, $plenaryId)) != null) {
            $form = new Form\Plenary();
            $form->bind($assembly);
            $form->setData($data);

            if ($form->isValid()) {
                $this->plenaryService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param Plenary $plenary
     * @return $this;
     */
    public function setPlenaryService(Plenary $plenary)
    {
        $this->plenaryService = $plenary;
        return $this;
    }
}
