<?php

namespace Althingi\Router\Http;

use Psr\Http\Message\RequestInterface;
use Laminas\Stdlib\ArrayUtils;
use Althingi\Router\Exception;
use Traversable;

/**
 * Segment route.
 */
class Segment implements RouteInterface
{
    protected static array $cacheEncode = [];

    /**
     * Map of allowed special chars in path segments.
     *
     * http://tools.ietf.org/html/rfc3986#appendix-A
     * segement      = *pchar
     * pchar         = unreserved / pct-encoded / sub-delims / ":" / "@"
     * unreserved    = ALPHA / DIGIT / "-" / "." / "_" / "~"
     * sub-delims    = "!" / "$" / "&" / "'" / "(" / ")"
     *               / "*" / "+" / "," / ";" / "="
     */
    protected static array $urlencodeCorrectionMap = [
        '%21' => "!", // sub-delims
        '%24' => "$", // sub-delims
        '%26' => "&", // sub-delims
        '%27' => "'", // sub-delims
        '%28' => "(", // sub-delims
        '%29' => ")", // sub-delims
        '%2A' => "*", // sub-delims
        '%2B' => "+", // sub-delims
        '%2C' => ",", // sub-delims
        '%3A' => ":", // pchar
        '%3B' => ";", // sub-delims
        '%3D' => "=", // sub-delims
        '%40' => "@", // pchar
    ];

    protected array $parts;
    protected string $regex;
    protected array $paramMap = [];
    protected array $defaults;
    protected array $assembledParams = [];

    public function __construct(string $route, array $constraints = [], array $defaults = [])
    {
        $this->defaults = $defaults;
        $this->parts    = $this->parseRouteDefinition($route);
        $this->regex    = $this->buildRegex($this->parts, $constraints);
    }

    public static function factory(/*array|Traversable*/$options = []): static
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (! is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable set of options',
                __METHOD__
            ));
        }

        if (! isset($options['route'])) {
            throw new Exception\InvalidArgumentException('Missing "route" in options array');
        }

        if (! isset($options['constraints'])) {
            $options['constraints'] = [];
        }

        if (! isset($options['defaults'])) {
            $options['defaults'] = [];
        }

        return new static($options['route'], $options['constraints'], $options['defaults']);
    }

    protected function parseRouteDefinition(string $def): array
    {
        $currentPos = 0;
        $length     = strlen($def);
        $parts      = [];
        $levelParts = [&$parts];
        $level      = 0;

        while ($currentPos < $length) {
            preg_match('(\G(?P<literal>[^:{\[\]]*)(?P<token>[:{\[\]]|$))', $def, $matches, 0, $currentPos);

            $currentPos += strlen($matches[0]);

            if (! empty($matches['literal'])) {
                $levelParts[$level][] = ['literal', $matches['literal']];
            }

            if ($matches['token'] === ':') {
                if (
                    ! preg_match(
                        '(\G(?P<name>[^:/{\[\]]+)(?:{(?P<delimiters>[^}]+)})?:?)',
                        $def,
                        $matches,
                        0,
                        $currentPos
                    )
                ) {
                    throw new Exception\RuntimeException('Found empty parameter name');
                }

                $levelParts[$level][] = [
                    'parameter',
                    $matches['name'],
                    isset($matches['delimiters']) ? $matches['delimiters'] : null
                ];

                $currentPos += strlen($matches[0]);
            } elseif ($matches['token'] === '{') {
                if (! preg_match('(\G(?P<literal>[^}]+)\})', $def, $matches, 0, $currentPos)) {
                    throw new Exception\RuntimeException('Translated literal missing closing bracket');
                }

                $currentPos += strlen($matches[0]);

                $levelParts[$level][] = ['translated-literal', $matches['literal']];
            } elseif ($matches['token'] === '[') {
                $levelParts[$level][] = ['optional', []];
                $levelParts[$level + 1] = &$levelParts[$level][count($levelParts[$level]) - 1][1];

                $level++;
            } elseif ($matches['token'] === ']') {
                unset($levelParts[$level]);
                $level--;

                if ($level < 0) {
                    throw new Exception\RuntimeException('Found closing bracket without matching opening bracket');
                }
            } else {
                break;
            }
        }

        if ($level > 0) {
            throw new Exception\RuntimeException('Found unbalanced brackets');
        }

        return $parts;
    }

    protected function buildRegex(array $parts, array $constraints, int &$groupIndex = 1): string
    {
        $regex = '';

        foreach ($parts as $part) {
            switch ($part[0]) {
                case 'literal':
                    $regex .= preg_quote($part[1]);
                    break;

                case 'parameter':
                    $groupName = '?P<param' . $groupIndex . '>';

                    if (isset($constraints[$part[1]])) {
                        $regex .= '(' . $groupName . $constraints[$part[1]] . ')';
                    } elseif ($part[2] === null) {
                        $regex .= '(' . $groupName . '[^/]+)';
                    } else {
                        $regex .= '(' . $groupName . '[^' . $part[2] . ']+)';
                    }

                    $this->paramMap['param' . $groupIndex++] = $part[1];
                    break;

                case 'optional':
                    $regex .= '(?:' . $this->buildRegex($part[1], $constraints, $groupIndex) . ')?';
                    break;

                // case 'translated-literal':
                //     $regex .= '#' . $part[1] . '#';
                //     $this->translationKeys[] = $part[1];
                //     break;
            }
        }

        return $regex;
    }

    protected function buildPath(
        array $parts,
        array $mergedParams,
        bool $isOptional,
        bool $hasChild,
        array $options
    ): string {
        $path      = '';
        $skip      = true;
        $skippable = false;

        foreach ($parts as $part) {
            switch ($part[0]) {
                case 'literal':
                    $path .= $part[1];
                    break;

                case 'parameter':
                    $skippable = true;

                    if (! isset($mergedParams[$part[1]])) {
                        if (! $isOptional || $hasChild) {
                            throw new Exception\InvalidArgumentException(sprintf('Missing parameter "%s"', $part[1]));
                        }

                        return '';
                    } elseif (
                        ! $isOptional
                        || $hasChild
                        || ! isset($this->defaults[$part[1]])
                        || $this->defaults[$part[1]] !== $mergedParams[$part[1]]
                    ) {
                        $skip = false;
                    }

                    $path .= $this->encode($mergedParams[$part[1]]);

                    $this->assembledParams[] = $part[1];
                    break;

                case 'optional':
                    $skippable    = true;
                    $optionalPart = $this->buildPath($part[1], $mergedParams, true, $hasChild, $options);

                    if ($optionalPart !== '') {
                        $path .= $optionalPart;
                        $skip  = false;
                    }
                    break;
            }
        }

        if ($isOptional && $skippable && $skip) {
            return '';
        }

        return $path;
    }

    public function match(
        RequestInterface $request, /*string|null*/
        $pathOffset = null,
        array $options = []
    )/*: RouteMatch|null*/ {

        $uri  = $request->getUri();
        $path = $uri->getPath();

        $regex = $this->regex;

        if ($pathOffset !== null) {
            $result = preg_match('(\G' . $regex . ')', $path, $matches, 0/*null*/, $pathOffset);
        } else {
            $result = preg_match('(^' . $regex . '$)', $path, $matches);
        }

        if (! $result) {
            return;
        }

        $matchedLength = strlen($matches[0]);
        $params        = [];

        foreach ($this->paramMap as $index => $name) {
            if (isset($matches[$index]) && $matches[$index] !== '') {
                $params[$name] = $this->decode($matches[$index]);
            }
        }

        return new RouteMatch(array_merge($this->defaults, $params), $matchedLength);
    }

    public function assemble(array $params = [], array $options = []) /*: mixed*/
    {
        $this->assembledParams = [];

        return $this->buildPath(
            $this->parts,
            array_merge($this->defaults, $params),
            false,
            (isset($options['has_child']) ? $options['has_child'] : false),
            $options
        );
    }

    public function getAssembledParams(): array
    {
        return $this->assembledParams;
    }

    protected function encode(string $value): string
    {
        $key = (string) $value;
        if (! isset(static::$cacheEncode[$key])) {
            static::$cacheEncode[$key] = rawurlencode($value);
            static::$cacheEncode[$key] = strtr(static::$cacheEncode[$key], static::$urlencodeCorrectionMap);
        }
        return static::$cacheEncode[$key];
    }

    protected function decode(string $value): string
    {
        return rawurldecode($value);
    }
}
