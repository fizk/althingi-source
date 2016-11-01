<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 22/03/2016
 * Time: 10:45 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Proponent as ProponentForm;
use Althingi\Lib\ServiceProponentAwareInterface;
use Althingi\Service\Proponent;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;

class ProponentController extends AbstractRestfulController implements
    ServiceProponentAwareInterface
{
    /** @var  \Althingi\Service\Proponent */
    private $proponentService;

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');
        $congressmanId = $this->params('congressman_id');

        $form = (new ProponentForm())
            ->setData(array_merge(
                $data,
                ['assembly_id' => $assemblyId,
                    'issue_id' => $issueId,
                    'document_id' => $documentId,
                    'congressman_id' => $congressmanId
                ]
            ));

        if ($form->isValid()) {
            $this->proponentService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }

    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');
        $congressmanId = $this->params('congressman_id');

        if (($assembly = $this->proponentService->get($assemblyId, $issueId, $documentId, $congressmanId)) != null) {
            $form = new ProponentForm();
            $form->bind($assembly);
            $form->setData($data);

            if ($form->isValid()) {
                $this->proponentService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205)
                    ->setOption('Access-Control-Allow-Origin', '*');
            }

            return (new ErrorModel($form))
                ->setStatus(400)
                ->setOption('Access-Control-Allow-Origin', '*');
        }

        return $this->notFoundAction();
    }

    /**
     * @param Proponent $proponent
     */
    public function setProponentService(Proponent $proponent)
    {
        $this->proponentService = $proponent;
    }
}
