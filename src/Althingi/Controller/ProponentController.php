<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 22/03/2016
 * Time: 10:45 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Proponent;
use Rend\Controller\AbstractRestfulController;
use Rend\View\Model\EmptyModel;
use Rend\View\Model\ErrorModel;

class ProponentController extends AbstractRestfulController
{
    public function put($id, $data)
    {
        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');
        $documentId = $this->params('document_id');
        $congressmanId = $this->params('congressman_id');

        $proponentService = $this->getServiceLocator()
            ->get('Althingi\Service\Proponent');

        $form = (new Proponent())
            ->setData(array_merge(
                $data,
                ['assembly_id' => $assemblyId,
                    'issue_id' => $issueId,
                    'document_id' => $documentId,
                    'congressman_id' => $congressmanId
                ]
            ));

        if ($form->isValid()) {
            $proponentService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }

        return (new ErrorModel($form))->setStatus(400);
    }
}
