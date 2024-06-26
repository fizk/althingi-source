<?php

namespace Althingi\Utils;

use ReflectionClass;
use ReflectionMethod;
use zpt\anno\Annotations;

class RouteInspector
{
    public function run($conf)
    {
        $tree = $this->extractOptions(
            $this->flattenRoutes($conf['routes'], [], '')
        );
//        $tree = $this->flattenRoutes($conf['router']['routes'], [], '');
        ksort($tree);
        return $tree;
    }

    /**
     * Takes a tree-shaped router and flattens it into single row array.
     * The URI becomes the key and values are that the the route defines.
     *
     * @param array $route
     * @param array $carry
     * @param string $path
     * @return array
     */
    public function flattenRoutes(array $route, $carry = [], $path = ''): array
    {
        foreach ($route as $key => $value) {
            $carry = array_merge($carry, [
                $path . $value['options']['route'] => [
                    'options' => $value['options'],
                    'type' => $value['type'],
                ]
            ]);

            if (key_exists('child_routes', $value)) {
                $carry = array_merge(
                    $carry,
                    $this->flattenRoutes(
                        $value['child_routes'],
                        $carry,
                        $path . $value['options']['route']
                    )
                );
            }
        }
        return $carry;
    }

    public function extractOptions(array $routes): array
    {
        $map = [];
        foreach ($routes as $path => $value) {
            if (
                array_key_exists('action', $value['options']['defaults']) &&
                ! empty($value['options']['defaults']['action'])
            ) {
                $action = str_replace(' ', '', ucwords(str_replace('-', ' ', $value['options']['defaults']['action'])));
                try {
                    $reflectionClass = new ReflectionClass($value['options']['defaults']['controller']);
                    $method = $reflectionClass->getMethod($action . 'Action');
                    $map = array_merge($map, $this->pAction($method, $path));
                } catch (\ReflectionException $e) {
                    print_r($e);
                }
            } else {
                try {
                    $reflectionClass = new ReflectionClass($value['options']['defaults']['controller']);
                    $map = array_merge($map, $this->pController($reflectionClass, $path));
                } catch (\ReflectionException $e) {
                    print_r($e);
                }
            }
        }
        return $map;
    }

    public function extractAction(ReflectionMethod $method, $verb = 'GET')
    {
        $annotations = (new Annotations($method))->asArray();

        return [
            'method' => $method->getName(),
            'class' => $method->getDeclaringClass()->getName(),
            'verb' => $verb,
            'description' => $method->getDocComment(),
            'position' => [
                'file' => $method->getFileName(),
                'start' => $method->getStartLine(),
                'end' => $method->getEndLine(),
            ],
            'status' => $this->statusCodes($annotations),
            'query' => isset($annotations['query'])
                ? (is_array($annotations['query']) ? $annotations['query'] : [$annotations['query']])
                : [],
            'input' => isset($annotations['input']) ? $annotations['input'] : null,
            'output' => isset($annotations['output']) ? $annotations['output'] : null,
        ];
    }

    public function pController(ReflectionClass $controller, string $path): array
    {
        $res = [];
        preg_match_all('/\[\/:[a-z_]*\]/', $path, $res);
        $pathStrippedLastId = count($res[0]) > 0
            ? str_replace($res[0][count($res[0]) - 1], '', $path)
            : $path;

        $result = [];
        $methods = $controller->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->getName() == $controller->getName()) {
                switch ($method->getName()) {
                    case 'get':
                        if (! array_key_exists($path, $result)) {
                            $result[$path] = [];
                        }
                        $result[$path][] = array_merge(
                            $this->extractAction($method, 'GET'),
                            $this->extractParamsFromUri($path)
                        );
                        break;
                    case 'getList':
                        if (! array_key_exists($pathStrippedLastId, $result)) {
                            $result[$pathStrippedLastId] = [];
                        }
                        $result[$pathStrippedLastId][] = array_merge(
                            $this->extractAction($method, 'GET'),
                            $this->extractParamsFromUri($pathStrippedLastId)
                        );
                        break;
                    case 'post':
                        if (! array_key_exists($path, $result)) {
                            $result[$path] = [];
                        }
                        $result[$path][] = array_merge(
                            $this->extractAction($method, 'POST'),
                            $this->extractParamsFromUri($path)
                        );
                        break;
                    case 'postList':
                        if (! array_key_exists($pathStrippedLastId, $result)) {
                            $result[$pathStrippedLastId] = [];
                        }
                        $result[$pathStrippedLastId][] = array_merge(
                            $this->extractAction($method, 'POST'),
                            $this->extractParamsFromUri($pathStrippedLastId)
                        );
                        break;
                    case 'put':
                        if (! array_key_exists($path, $result)) {
                            $result[$path] = [];
                        }
                        $result[$path][] = array_merge(
                            $this->extractAction($method, 'PUT'),
                            $this->extractParamsFromUri($path)
                        );
                        break;
                    case 'putList':
                        if (! array_key_exists($pathStrippedLastId, $result)) {
                            $result[$pathStrippedLastId] = [];
                        }
                        $result[$pathStrippedLastId][] = array_merge(
                            $this->extractAction($method, 'PUT'),
                            $this->extractParamsFromUri($pathStrippedLastId)
                        );
                        break;
                    case 'patch':
                        if (! array_key_exists($path, $result)) {
                            $result[$path] = [];
                        }
                        $result[$path][] = array_merge(
                            $this->extractAction($method, 'PATCH'),
                            $this->extractParamsFromUri($path)
                        );
                        break;
                    case 'patchList':
                        if (! array_key_exists($pathStrippedLastId, $result)) {
                            $result[$pathStrippedLastId] = [];
                        }
                        $result[$pathStrippedLastId][] = array_merge(
                            $this->extractAction($method, 'PATCH'),
                            $this->extractParamsFromUri($pathStrippedLastId)
                        );
                        break;
                    case 'options':
                        if (! array_key_exists($path, $result)) {
                            $result[$path] = [];
                        }
                        $result[$path][] = array_merge(
                            $this->extractAction($method, 'OPTIONS'),
                            $this->extractParamsFromUri($path)
                        );
                        break;
                    case 'optionsList':
                        if (! array_key_exists($pathStrippedLastId, $result)) {
                            $result[$pathStrippedLastId] = [];
                        }
                        $result[$pathStrippedLastId][] = array_merge(
                            $this->extractAction($method, 'OPTIONS'),
                            $this->extractParamsFromUri($pathStrippedLastId)
                        );
                        break;
                    case 'head':
                        if (! array_key_exists($path, $result)) {
                            $result[$path] = [];
                        }
                        $result[$path][] = array_merge(
                            $this->extractAction($method, 'HEAD'),
                            $this->extractParamsFromUri($path)
                        );
                        break;
                    case 'headList':
                        if (! array_key_exists($pathStrippedLastId, $result)) {
                            $result[$pathStrippedLastId] = [];
                        }
                        $result[$pathStrippedLastId][] = array_merge(
                            $this->extractAction($method, 'HEAD'),
                            $this->extractParamsFromUri($pathStrippedLastId)
                        );
                        break;
                }
            }
        }
        return $result;
    }

    public function pAction(ReflectionMethod $method, string $path): array
    {
        return [
            $path => [
                    array_merge(
                        $this->extractAction($method),
                        $this->extractParamsFromUri($path)
                    )
                ]
            ,
        ];
    }


    public function extractParamsFromUri($uri)
    {
        $match = [];
        preg_match_all('/:[a-z_]*/', $uri, $match);

        if (empty($match[0])) {
            return ['params' => []];
        }

        $result = array_map(function ($item) {
            return str_replace(':', '', $item);
        }, $match[0]);

        return ['params' => $result];
    }

    private function statusCodes($annotation)
    {
        return array_filter($annotation, function ($key) {
            return is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);
    }
}
