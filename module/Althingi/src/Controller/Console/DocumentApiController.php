<?php

namespace Althingi\Controller\Console;

use Zend\Mvc\Controller\AbstractActionController;
use ReflectionClass;
use ReflectionMethod;
use zpt\anno\Annotations;

class DocumentApiController extends AbstractActionController
{
    public function indexAction()
    {
        $conf = require __DIR__ . '/../../../../config/module.config.php';
//        print_r($conf['router']['routes']);

        $tree = $this->processRoute($conf['router']['routes'], [], '');

        echo json_encode($tree);
    }

    private function processRoute($routes, $deep, $path)
    {

        foreach ($routes as $key => $value) {
            if (key_exists('action', $value['options']['defaults'])) {
                $result = $this->processAction(
                    $value['options']['defaults']['controller'],
                    $value['options']['defaults']['action'],
                    $path . $value['options']['route']
                );
                $deep = array_merge($deep, $result);
            } else {
                $result = $this->processRestController(
                    $value['options']['defaults']['controller'],
                    $path . $value['options']['route']
                );
                $deep = array_merge($deep, $result);
            }

            if (key_exists('child_routes', $value)) {
                $result = $this->processRoute($value['child_routes'], $deep, $path . $value['options']['route']);
                $deep = array_merge($deep, $result);
            }
        }

        return $deep;
    }

    private function processAction($controller, $action, $path)
    {
        $action = str_replace(' ', '', ucwords(str_replace('-', ' ', $action)));
        $reflectionClass = new ReflectionClass($controller);
        $action = $reflectionClass->getMethod($action . 'Action');
        $annotations = (new Annotations($action))->asArray();
        $result = [];
        if (key_exists('output', $annotations)) {
            $res = $this->processOutput($path, 'get', $annotations['output']);
            foreach ($res as $key => $value) {
                if (!key_exists($key, $result)) {
                    $result[$key] = [];
                }
                $result[$key][] = $value;
            }
        }
        return $result;
    }

    private function processRestController($controller, $path)
    {
        $reflectionClass = new ReflectionClass($controller);
        $reflectionMethods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);


        $result = [];

        foreach ($reflectionMethods as $item) {
            if ($item->getDeclaringClass()->getName() == $controller) {
                $annotations = (new Annotations($item))->asArray();

                if (key_exists('output', $annotations)) {
                    $res = $this->processOutput($path, $item->getName(), $annotations['output']);
                    foreach ($res as $key => $value) {
                        if (!key_exists($key, $result)) {
                            $result[$key] = [];
                        }
                        $result[$key][] = $value;
                    }
                }

                if (key_exists('input', $annotations)) {
                    $res = $this->processInput($path, $item->getName(), $annotations['input']);
                    foreach ($res as $key => $value) {
                        if (!key_exists($key, $result)) {
                            $result[$key] = [];
                        }
                        $result[$key][] = $value;
                    }
                }
            }
        }

        return $result;
    }

    private function processOutput($path, $action, $output)
    {
        switch ($action) {
            case 'getList':
                $res = [];
                preg_match_all('/\[\/:[a-z_]*\]/', $path, $res);
                $noLast = str_replace($res[0][count($res[0]) - 1], '', $path);
                $key = str_replace(['[', ']'], ['', ''], $noLast);

                return [
                    $key => [
                        'verb' => 'GET',
                        'props' => $output
                    ]
                ];
                break;
            default:
                $key = str_replace(['[', ']'], ['', ''], $path);
                return [
                    $key => [
                        'verb' => 'GET',
                        'props' => $output
                    ]
                ];
                break;
        }
    }

    private function processInput($path, $action, $input)
    {
        switch ($action) {
            case 'put':
                $key = str_replace(['[', ']'], ['', ''], $path);
                return [
                    $key => [
                        'verb' => 'PUT',
                        'props' => $input
                    ]
                ];
                break;
            case 'putList':
                $res = [];
                preg_match_all('/\[\/:[a-z_]*\]/', $path, $res);
                $noLast = str_replace($res[0][count($res[0]) - 1], '', $path);
                $key = str_replace(['[', ']'], ['', ''], $noLast);

                return [
                    $key => [
                        'verb' => 'PUT',
                        'props' => $input
                    ]
                ];
                break;
            case 'patch':
                $key = str_replace(['[', ']'], ['', ''], $path);
                return [
                    $key => [
                        'verb' => 'PATCH',
                        'props' => $input
                    ]
                ];
                break;
            case 'patchList':
                $res = [];
                preg_match_all('/\[\/:[a-z_]*\]/', $path, $res);
                $noLast = str_replace($res[0][count($res[0]) - 1], '', $path);
                $key = str_replace(['[', ']'], ['', ''], $noLast);

                return [
                    $key => [
                        'verb' => 'PATCH',
                        'props' => $input
                    ]
                ];
                break;
            case 'post':
                $key = str_replace(['[', ']'], ['', ''], $path);
                return [
                    $key => [
                        'verb' => 'POST',
                        'props' => $input
                    ]
                ];
                break;
            case 'postList':
                $res = [];
                preg_match_all('/\[\/:[a-z_]*\]/', $path, $res);
                $noLast = str_replace($res[0][count($res[0]) - 1], '', $path);
                $key = str_replace(['[', ']'], ['', ''], $noLast);

                return [
                    $key => [
                        'verb' => 'POST',
                        'props' => $input
                    ]
                ];
                break;
        }
    }
}
