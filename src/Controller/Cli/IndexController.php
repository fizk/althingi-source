<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\Assembly;
use Althingi\Utils\ConsoleResponse;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};

use Althingi\Service\EventService;

class IndexController
{
    use EventService;
    private Assembly $assemblyService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $options = [
            ['assembly',            []],
            ['cabinet',             []],
            ['category',            []],
            ['constituency',        []],
            ['committee',           []],
            ['party',               []],
            ['ministry',            []],
            ['inflation',           []],
            ['committee-sitting',   ['assembly_id', 'congressman_id', 'committee_id']],
            ['congressman',         ['assembly_id']],
            ['congressman-document',['assembly_id', 'congressman_id', 'issue_id']],
            ['issue',               ['assembly_id']],
            ['issue-category',      ['assembly_id']],
            ['document',            ['assembly_id', 'issue_id']],
            ['committee-document',  ['assembly_id', 'issue_id', 'document_id']],
            ['vote',                ['assembly_id', 'issue_id', 'document_id']],
            ['vote-item',           ['assembly_id', 'issue_id', 'document_id']],
            ['minister-sitting',    ['assembly_id']],
            ['plenary',             ['assembly_id']],
            ['plenary-agenda',      ['assembly_id']],
            ['president-sitting',   ['assembly_id']],
            ['session',             ['assembly_id', 'congressman_id']],
            ['speech',              ['assembly_id', 'issue_id']],
        ];

        usort($options, fn ($a, $b) => ($a[0] < $b[0]) ? -1 : 1);

        $result = array_reduce($options, function (mixed $carry, mixed $item) {
            $params = array_reduce($item[1], fn (mixed $c, mixed $i) => $c . ("\t--{$i} \n"));
            return $carry . ("\e[1;32mconsole:{$item[0]}\e[1;35m\n{$params}\e[0m");
        });
        return (new ConsoleResponse($result));
    }

    public function setAssemblyService(Assembly $assembly): self
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
