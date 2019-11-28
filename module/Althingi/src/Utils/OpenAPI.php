<?php


namespace Althingi\Utils;

class OpenAPI
{
    private $object;

    /** @var string */
    private $host;

    /** @var string[]  */
    private $schema = ['http'];

    /** @var string */
    private $definition;

    public function transform($any)
    {
        $this->object = [
            'swagger' => "2.0",
            'info' => [
                "description" => "Loggjafarthing",
                "version" => "1.0.0",
                "title" => "Loggjafarthing API",
                "termsOfService" => "http://swagger.io/terms/",
            ],
            'schemes' => ['http'],
            'basePath' => '',
            'tags' => [],
            'paths' => [],
            'host' => $this->getHost(),//'loggjafarthing.einarvalur.co/api',
//            'host' => 'localhost/api',
        ];

        foreach ($any as $key => $value) {
            $this->addPath($this->openApiPath($key), $value);
        }
        ksort($this->object);
        return $this->object;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return OpenAPI
     */
    public function setHost(string $host): OpenAPI
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * @param string[] $schema
     * @return OpenAPI
     */
    public function setSchema(array $schema): OpenAPI
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefinition(): string
    {
        return $this->definition;
    }

    /**
     * @param string $definition
     * @return OpenAPI
     */
    public function setDefinition(string $definition): OpenAPI
    {
        $this->definition = $definition;
        return $this;
    }

    private function addPath(string $path, array $verbs)
    {
        $object = array_reduce($verbs, function ($carry, $item) use ($path) {
            $carry[strtolower($item['verb'])] = [
                'tags' => [],
                'summary' => '',
                'description' => $item['description'] ? : '',
                'operationId' => "{$item['class']}::{$item['method']}",
                'consumes' => array_merge([], $item['input'] ? ['application/x-www-form-urlencoded'] : []),
                'produces' => array_merge([], $item['output'] ? ['application/json'] : []),
                'parameters' => $this->getParams($item),
                'responses' => $this->buildResponse($item),
                'security' => []
            ];
            return $carry;
        }, []);

        $this->object['paths'][$path] = $object;
    }

    private function getParams($uri)
    {
        $params = array_map(function ($param) {
            return [
                'name' => $param,
                'in' => 'path',
                'required' => true,
                'type' => 'string',
            ];
        }, isset($uri['params']) ? $uri['params'] : []);

        $queries = array_map(function ($query) {
            return [
                'name' => $query,
                'in' => 'query',
                'type' => 'string',
            ];
        }, isset($uri['query']) ? $uri['query'] : []);

        return array_merge($params, $queries);
    }

    private function buildResponse($item)
    {
        $result = [];
        foreach ($item['status'] as $key => $value) {
            switch ($key) {
                case 200:
                case 206:
                    $result[$key] = [
                        'description' => $item['output'] ? : $value
                    ];
                    break;
                default:
                    $result[$key] = [
                        'description' => $value ?: ''
                    ];
                    break;
            }
        }
        return count($result) > 0 ? $result : (object)[];
    }

    private function openApiPath(string $path)
    {
        $path = str_replace(['[', ']'], ['', ''], $path);
        return preg_replace_callback('/(:)([a-z_]*)/', function ($match) {
            if (count($match) >= 3) {
                return "{{$match[2]}}";
            }
            return $match[0];
        }, $path);
    }
}
