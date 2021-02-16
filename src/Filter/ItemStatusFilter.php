<?php

namespace Althingi\Filter;

use Laminas\Filter\Exception;
use Laminas\Filter\FilterInterface;

class ItemStatusFilter implements FilterInterface
{
    /**
     * Returns the result of filtering $value
     *
     * Bíður 1. umræðu.
     * Bíður 2. umræðu
     * Bíður 3. umræðu.
     * Ekki útrætt á 130. þingi. (Beið 1. umræðu.)
     * Ekki útrætt á 130. þingi. (Var í nefnd eftir 1. umræðu.)
     * Ekki útrætt á 130. þingi. Bíður 2. umræðu
     * Ekki útrætt á 131. þingi. (Beið 1. umræðu.)
     * Ekki útrætt á 131. þingi. (Var í nefnd eftir 1. umræðu.)
     * Ekki útrætt á 131. þingi. Bíður 2. umræðu
     * Ekki útrætt á 135. þingi. (Var í nefnd eftir 1. umræðu.)
     * Í nefnd eftir 1. umræðu.
     * Samþykkt sem lög frá Alþingi.
     * Vísað til ríkisstjórnar.
     *
     * @param  mixed $value
     * @throws Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        if (empty($value)) {
            return null;
        }

        $matches = [];

        if (1 === preg_match('/(Bíður\s|Beið\s)([0-9]\.|fyrri|síðari)(\sumræðu)/', $value, $matches)
            && 4 == count($matches)) {
            return sprintf('Bíður %s umræðu', $this->wordToNumber($matches[2]));
        }

        if (1 === preg_match('/(nefnd eftir\s)([0-9]\.|fyrri|síðari)(\sumræðu)/', $value, $matches)
            && 4 == count($matches)) {
            return sprintf('Í nefnd eftir %s umræðu', $this->wordToNumber($matches[2]));
        }

        if (1 === preg_match('/(.*)(\.$)/', $value, $matches) && 3 == count($matches)) {
            return $matches[1];
        }

        return $value;
    }

    private function wordToNumber(string $word)
    {
        switch (strtolower($word)) {
            case 'fyrri':
                return '1.';
            case 'síðari':
                return '2.';
            default:
                return $word;
        }
    }
}
