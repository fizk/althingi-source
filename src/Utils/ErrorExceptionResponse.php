<?php

namespace Althingi\Utils;

use  Laminas\Diactoros\Response\JsonResponse;
use Throwable;

class ErrorExceptionResponse extends JsonResponse
{
    public function __construct(Throwable $exception)
    {
        parent::__construct($this->extractException($exception), 500);
    }

    private function extractException(\Throwable $exception): array
    {
        $messages = [];
        while ($exception) {
            $messages[] = [
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace()
            ];
            $exception = $exception->getPrevious();
        }
        return $messages;
    }
}
