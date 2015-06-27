<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Althingi\Form\Speech;
use Althingi\View\Model\CollectionModel;
use Althingi\View\Model\ErrorModel;
use Althingi\View\Model\EmptyModel;
use Althingi\View\Model\ItemModel;

class SpeechController extends AbstractRestfulController
{
    use Range;

    public function put($id, $data)
    {
        /** @var  $speechService \Althingi\Service\Speech */
        $speechService = $this->getServiceLocator()
            ->get('Althingi\Service\Speech');

        $assemblyId = $this->params('id');
        $issueId = $this->params('issue_id');

        $form = new Speech();
        $form->setData(array_merge(
            $data,
            ['speech_id' => $id, 'issue_id' => $issueId, 'assembly_id' => $assemblyId]
        ));

        if ($form->isValid()) {
            $speechService->create($form->getObject());
            return (new EmptyModel())->setStatus(201);
        }
        return (new ErrorModel($form))->setStatus(400);
    }
}
