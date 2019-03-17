<?php

namespace Althingi\Filter;

use Zend\Filter\Exception;
use Zend\Filter\FilterInterface;

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
     * @todo "Ekki útrætt á 148. þingi. (Beið fyrri umræðu.)"
     * @todo "Ekki útrætt á 148. þingi. (Var í nefnd eftir fyrri umræðu.)"
     * @todo these are no caught in the regex.
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

        if (1 === preg_match('/(Bíður\s|Beið\s)([0-9])(\.\sumræðu)/', $value, $matches) && 4 == count($matches)) {
            return sprintf('Bíður %s. umræðu', $matches[2]);
        }

        if (1 === preg_match('/(nefnd eftir\s)([0-9])(\.\sumræðu)/', $value, $matches) && 4 == count($matches)) {
            return sprintf('Í nefnd eftir %s. umræðu', $matches[2]);
        }

        if (1 === preg_match('/(.*)(\.$)/', $value, $matches) && 3 == count($matches)) {
            return $matches[1];
        }

        return $value;
    }
}
