<?php
namespace Althingi\Utils;

use Laminas\Form\Form;
use  Laminas\Diactoros\Response\JsonResponse;

class ErrorFormResponse extends JsonResponse
{
    public function __construct(Form $form)
    {
        parent::__construct($this->extractForm($form), 400);
    }

    private function extractForm(Form $form): array
    {
        return array_map(function ($value, $key) {
            return [
                'field' => $key,
                'message' => array_values($value),
            ];
        }, $form->getMessages(), array_keys($form->getMessages()));

    }
}
