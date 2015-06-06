<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 2/06/15
 * Time: 7:31 AM
 */

namespace Althingi\Controller;

use Zend\Stdlib\RequestInterface;
use Althingi\Form\Assembly;
use Althingi\View\Model\ErrorModel;
use Althingi\View\Model\EmptyModel;
use Althingi\View\Model\ItemModel;
use Althingi\View\Model\CollectionModel;

class LoggjafarthingController extends AbstractRestfulController
{
    const PER_PAGE = 25;

    /**
     * Return list of Assemblies.
     *
     * @return \Althingi\View\Model\CollectionModel
     */
    public function getList()
    {
        /** @var  $assemblyService \Althingi\Service\Assembly */
        $assemblyService = $this->getServiceLocator()
            ->get('Althingi\Service\Assembly');

        $count = $assemblyService->count();
        $range = $this->getRange($this->getRequest(), $count);
        $assemblies = $assemblyService->fetchAll($range['from'], $range['to']);

        return (new CollectionModel($assemblies))
            ->setStatus(206)
            ->setRange($range['from'], $range['to'], $count);
    }

    /**
     * Create new Resource Assembly.
     *
     * @param  int $id
     * @param  array $data
     * @return \Althingi\View\Model\ItemModel
     */
    public function put($id, $data)
    {
        /** @var $assemblyService \Althingi\Service\Assembly */
        $sm = $this->getServiceLocator();

        $form = new Assembly();
        $form->bindValues(array_merge($data, ['assembly_id' => (int)$this->params('id', null)]));

        if ($form->isValid()) {
            $object = $form->getObject();
            $assemblyService = $sm->get('Althingi\Service\Assembly');
            $assemblyService->create($object);
            return (new ItemModel($object))
                ->setStatus(201);

        } else {
            return (new ErrorModel($form))
                ->setStatus(400);

        }
    }

    /**
     * @return \Althingi\View\Model\EmptyModel
     */
    public function optionsList()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS'])
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * @return \Althingi\View\Model\EmptyModel
     */
    public function options()
    {
        return (new EmptyModel())
            ->setStatus(200)
            ->setAllow(['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'])
            ->setOption('Access-Control-Allow-Origin', '*');
    }

    /**
     * Get on Assembly.
     *
     * @param int $id
     * @return \Althingi\View\Model\ErrorModel|\Althingi\View\Model\ItemModel
     */
    public function get($id)
    {
        /** @var  $assemblyService \Althingi\Service\Assembly */
        $assemblyService = $this->getServiceLocator()
            ->get('Althingi\Service\Assembly');
        if (($resource = $assemblyService->get($id))) {
            return (new ItemModel($resource));
        }

        return $this->notFoundAction();
    }

    /**
     * Update on Assembly
     *
     * @param int $id
     * @param array $data
     * @return \Althingi\View\Model\ErrorModel|\Althingi\View\Model\EmptyModel
     */
    public function patch($id, $data)
    {
        $sm = $this->getServiceLocator();
        /** @var $assemblyService \Althingi\Service\Assembly */
        $assemblyService = $sm->get('Althingi\Service\Assembly');

        if (($assembly = $assemblyService->get($id)) != null) {
            $form = new Assembly();
            $form->setObject($assembly);
            $form->setData($data);

            if ($form->isValid()) {
                $assemblyService->update($form->getObject());
                return (new EmptyModel())
                    ->setStatus(204);
            } else {
                return (new ErrorModel($form))
                    ->setStatus(401);
            }
        }

        return $this->notFoundAction();
    }

    /**
     * Split up Range HTTP header or return default.
     *
     * @param RequestInterface $request
     * @param int $count
     * @return object
     * @todo if range is something like 'items hundur-vei'
     */
    private function getRange(RequestInterface $request, $count = 0)
    {
        /** @var $range \Zend\Http\Header\Range */
        if ($range = $request->getHeader('Range')) {
            $match = [];
            preg_match('/([0-9]*)-([0-9]*)/', $range->getFieldValue(), $match);
            if (count($match) == 3) {
                $from = (int) $match[1];
                $to = (int) $match[2];

                //NEGATIVE RANGE
                if ($to - $from < 0) {
                    return [
                        'from' => 0,
                        'to' => 0
                    ];
                //OUT OF RANGE
                } elseif ($to > $count) {
                    //BOTH OUT OF RANGE
                    if ($from > $count) {
                        return [
                            'from' => 0,
                            'to' => 0
                        ];
                    }
                    //LOWER BOUND IN RANGE
                    return [
                        'from' => $from,
                        'to' => $count
                    ];
                //RANGE BIGGER
                } elseif ($to - $from > self::PER_PAGE) {
                    return [
                        'from' => $from,
                        'to' => $from + self::PER_PAGE
                    ];
                }

                return [
                    'from' => $from,
                    'to' => $to
                ];
            }
        }

        return [
            'from' => 0,
            'to' => (self::PER_PAGE > $count) ? $count : self::PER_PAGE
        ];
    }
}
