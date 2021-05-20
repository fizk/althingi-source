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
        return (new ConsoleResponse(
            "\nCommands:" . "\n\n" .
            "\e[1;32mconsole:assembly\e[0m\n" .
            "\e[1;32mconsole:cabinet\e[0m\n" .
            "\e[1;32mconsole:committee-sitting \e[1;35m\n\t--assembly_id \n\t--congressman_id \n\t--committee_id\e[0m\n" .
            "\e[1;32mconsole:congressman \e[1;35m\n\t--assembly_id\e[0m\n" .
            "\e[1;32mconsole:congressman-document \e[1;35m\n\t--assembly_id \n\t--congressman_id \n\t--issue_id\e[0m\n" .
            "\e[1;32mconsole:session \e[1;35m\n\t--assembly_id \n\t--congressman_id\e[0m\n"
        ));
    }

    public function setAssemblyService(Assembly $assembly): self
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
