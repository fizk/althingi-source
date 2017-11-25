<?php

namespace Althingi\StatusNormalizer;

use Althingi\Model\Status;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use Finite\Loader\ArrayLoader;

class StatusNormalizerL
{

    /** @var  \Finite\StateMachine\StateMachine */
    private $stateMachine;

    /** @var array */
    private $states = [
        'class'  => 'MyDocument',
        'states' => [
            'breytingartillaga'             => ['type' => 'normal',     'properties' => []], //
            'framhaldsnefndarálit'          => ['type' => 'normal',     'properties' => []], //
            'frávísunartilllaga'            => ['type' => 'normal',     'properties' => []], //
            'frhnál. með brtt.'             => ['type' => 'normal',     'properties' => []], //
            'frhnál. með frávt.'            => ['type' => 'normal',     'properties' => []], //
            'frumvarp'                      => ['type' => 'initial',    'properties' => []], //
            'frumvarp eftir 2. umræðu'      => ['type' => 'normal',     'properties' => []], //
            'frumvarp nefndar'              => ['type' => 'initial',    'properties' => []], //
            'frv. (afgr. frá deild)'        => ['type' => 'normal',     'properties' => []], //
            'lög (m.áo.br.)'                => ['type' => 'final',      'properties' => []], //
            'lög (samhlj.)'                 => ['type' => 'final',      'properties' => []], //
            'lög í heild'                   => ['type' => 'final',      'properties' => []], //
            'nál. með brtt.'                => ['type' => 'normal',     'properties' => []], //
            'nál. með frávt.'               => ['type' => 'normal',     'properties' => []], //
            'nál. með rökst.'               => ['type' => 'normal',     'properties' => []], //
            'nál. með þáltil.'              => ['type' => 'normal',     'properties' => []], //
            'nefndarálit'                   => ['type' => 'normal',     'properties' => []],
            'rökstudd dagskrá'              => ['type' => 'normal',     'properties' => []],
            'stjórnarfrumvarp'              => ['type' => 'initial',    'properties' => []],

            '1. umræða'                     => ['type' => 'normal',    'properties' => []],
            '2. umræða'                     => ['type' => 'normal',    'properties' => []],
            '3. umræða'                     => ['type' => 'normal',    'properties' => []],
            'í nefnd'                       => ['type' => 'normal',    'properties' => []],
        ],
        'transitions' => [
            'breytingartillaga' => [
                'from' => ['nefndarálit'],
                'to' => 'breytingartillaga'
            ],
            'framhaldsnefndarálit' => [
                'from' => ['nefndarálit', 'nál. með brtt.'],
                'to' => 'framhaldsnefndarálit'
            ],
            'frávísunartilllaga' => [
                'from' => [],
                'to' => 'frávísunartilllaga'
            ],
            'frhnál. með brtt.' => [
                'from' => ['nefndarálit', 'nál. með brtt'],
                'to' => 'frhnál. með brtt.'
            ],
            'frhnál. með frávt.' => [
                'from' => [],
                'to' => 'frhnál. með frávt.'
            ],
            'frumvarp eftir 2. umræðu' => [
                'from' => ['2. umræða'],
                'to' => 'frumvarp eftir 2. umræðu'
            ],
            'frv. (afgr. frá deild)' => [
                'from' => [],
                'to' => 'frv. (afgr. frá deild)'
            ],
            'lög (m.áo.br.)' => [
                'from' => ['3. umræða'],
                'to' => 'lög (m.áo.br.)'
            ],
            'lög (samhlj.)' => [
                'from' => ['3. umræða'],
                'to' => 'lög (samhlj.)'
            ],
            'lög í heild' => [
                'from' => ['3. umræða'],
                'to' => 'lög í heild'
            ],
            'nál. með brtt.' => [
                'from' => ['1. umræða', '2. umræða', 'frumvarp eftir 2. umræðu'],
                'to' => 'nál. með brtt.'
            ],
            'nál. með frávt.' => ['from' => [
                '1. umræða', 'í nefnd'],
                'to' => 'nál. með frávt.'
            ],
            'nál. með rökst.' => [
                'from' => [],
                'to' => 'nál. með rökst.'
            ],
            'nál. með þáltil.' => [
                'from' => [],
                'to' => 'nál. með þáltil.'
            ],
            'nefndarálit' => [
                'from' => ['1. umræða', '2. umræða', 'frumvarp eftir 2. umræðu'],
                'to' => 'nefndarálit'
            ],
            'rökstudd dagskrá' => [
                'from' => [],
                'to' => 'rökstudd dagskrá'
            ],
            '1. umræða' => [
                'from' => ['frumvarp', 'frumvarp nefndar', 'stjórnarfrumvarp'],
                'to' => '1. umræða'
            ],
            '2. umræða' => [
                'from' => [
                    'breytingartillaga',
                    'nál. með brtt.',
                    'nál. með frávt.',
                    '1. umræða',
                    'frhnál. með brtt.',
                    'í nefnd'
                ],
                'to' => '2. umræða'
            ],
            '3. umræða' => [
                'from' => ['frumvarp eftir 2. umræðu', 'frhnál. með brtt.', '2. umræða', 'breytingartillaga'],
                'to' => '3. umræða'
            ],
            'í nefnd' => [
                'from' => ['1. umræða', '2. umræða'],
                'to' => 'í nefnd'
            ],
        ]
    ];

    public function __construct()
    {
        $document = new class implements StatefulInterface
        {
            private $state;
            public function getFiniteState()
            {
                return $this->state;
            }
            public function setFiniteState($state)
            {
                $this->state = $state;
            }
        };
        $this->stateMachine = new StateMachine;
        $loader = new ArrayLoader($this->states);
        $loader->load($this->stateMachine);
        $this->stateMachine->setObject($document);
        $this->stateMachine->initialize();
    }

    /**
     * @param array $data
     * @return \Althingi\Model\Status[]
     */
    public function __invoke(array $data): array
    {
        $first = $data[0];
        $rest = array_slice($data, 1);


        $result = array_filter($rest, function (Status $status) {
            if ($this->stateMachine->can($status->getTitle())) {
                $this->stateMachine->apply($status->getTitle());
                return true;
            } else {
//                echo "Can't got from {$stateMachine->getCurrentState()} to {$status->getTitle()}\n";
                return false;
            }
        });

        return array_merge([$first], $result);
    }
}
