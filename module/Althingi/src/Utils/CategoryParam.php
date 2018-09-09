<?php
namespace Althingi\Utils;

trait CategoryParam
{
    public function getCategoriesFromQuery()
    {
        $categories = $this->params()->fromQuery('category', null);
        if (! $categories) {
            return ['A'];
        }
        $results = [];
        preg_match_all('/([aA]|[bB])/', $categories, $results);
        return array_map(function ($item) {
            return strtoupper($item);
        }, $results[0]);
    }

    public function getCategoryFromQuery()
    {
        $categories = $this->params()->fromQuery('category', null);
        if (! $categories) {
            return 'A';
        }
        $results = [];
        preg_match_all('/([aA]|[bB])/', $categories, $results);

        $filteredResult = array_filter($results[0], function ($item) {
            return $item === 'a' || $item === 'A' || $item === 'b' || $item === 'B';
        });
        $formattedResult = array_map(function ($item) {
            return strtoupper($item);
        }, $filteredResult);

        return array_shift($formattedResult);
    }
}
