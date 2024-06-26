<?php

namespace Althingi\Utils;

use Althingi\Form\Form;
use Library\Form\Form as LForm;
use Laminas\Diactoros\Response\JsonResponse;

class ErrorFormResponse extends JsonResponse
{
    public function __construct(Form|LForm $form)
    {
        parent::__construct($this->extractForm($form), 400);
    }

    private function extractForm(Form|LForm $form): array
    {
        return [
            'form' => method_exists($form, 'getData') ? $form->getData() : null,
            'messages' => array_map(function ($value, $key) {
                return [
                    'field' => $key,
                    'message' => array_values($value),
                ];
            }, $form->getMessages(), array_keys($form->getMessages()))
        ];
    }
}
