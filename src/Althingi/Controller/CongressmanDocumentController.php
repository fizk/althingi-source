<?php

namespace Althingi\Controller;

use Althingi\Form\CongressmanDocument as CongressmanDocumentForm;
use Althingi\Lib\ServiceProponentAwareInterface;
use Althingi\Service\CongressmanDocument;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;

class CongressmanDocumentController extends AbstractRestfulController implements
    ServiceProponentAwareInterface
{
    /** @var  \Althingi\Service\CongressmanDocument */
    private $congressmanDocumentService;

    /**
     * @param mixed $id
     * @param mixed $data
     * @return \Rend\View\Model\ModelInterface
     * @input Althingi\Form\CongressmanDocument
     */
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');
        $congressmanId = $this->params('congressman_id');

        $form = (new CongressmanDocumentForm())
            ->setData(array_merge(
                $data,
                [
                    'assembly_id' => $assemblyId,
                    'issue_id' => $issueId,
                    'document_id' => $documentId,
                    'congressman_id' => $congressmanId
                ]
            ));

        if ($form->isValid()) {
            $affectedRows = $this->congressmanDocumentService->save($form->getObject());
            return (new EmptyModel())
                ->setStatus($affectedRows === 1 ? 201 : 205);
        }

        return (new ErrorModel($form))
            ->setStatus(400);
    }

    /**
     * @param int $id
     * @param array $data
     * @return \Rend\View\Model\ModelInterface
     * @input Althingi\Form\CongressmanDocument
     */
    public function patch($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');
        $congressmanId = $this->params('congressman_id');

        if (($proponent = $this->congressmanDocumentService
                ->get($assemblyId, $issueId, $documentId, $congressmanId)) != null) {
            $form = new CongressmanDocumentForm();
            $form->bind($proponent);
            $form->setData($data);

            if ($form->isValid()) {
                $this->congressmanDocumentService->update($form->getData());
                return (new EmptyModel())
                    ->setStatus(205);
            }

            return (new ErrorModel($form))
                ->setStatus(400);
        }

        return $this->notFoundAction();
    }

    /**
     * @param CongressmanDocument $congressmanDocument
     */
    public function setCongressmanDocumentService(CongressmanDocument $congressmanDocument)
    {
        $this->congressmanDocumentService = $congressmanDocument;
    }
}
