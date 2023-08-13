<?php

declare(strict_types=1);

namespace Core\Testing\GraphQL\QueryBuilder;

use Core\Enums\BaseEnum;
use Core\Testing\GraphQL\Scalar\Scalar;
use Core\ValueObjects\AbstractValueObject;
use Illuminate\Http\UploadedFile;
use JsonException;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @see \Tests\Unit\Testing\GraphQL\QueryBuilder\QueryBuilderTest
 * to find some usage examples
 */
class GraphQLQuery
{
    protected const QUERY_TYPES
        = [
            'query'    => 'query',
            'mutation' => 'mutation',
            'upload'   => 'upload',
        ];

    protected string $queryType = self::QUERY_TYPES['query'];

    protected array $args;
    protected array $select;
    protected string $name;
    protected string $query;

    protected bool $hasFiles = false;
    protected array $map = [];
    protected array|UploadedFile|null $files = [];

    protected array $fileVariableNames;

    protected function __construct()
    {
    }

    public static function query(string $name): self
    {
        $self = new self();
        $self->name($name);

        $self->queryType = self::QUERY_TYPES['query'];

        return $self;
    }

    protected function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public static function mutation(string $name): self
    {
        $self = new self();
        $self->name($name);

        $self->queryType = self::QUERY_TYPES['mutation'];

        return $self;
    }

    public static function upload(string $name): self
    {
        $self = new self();
        $self->name($name);

        $self->queryType = self::QUERY_TYPES['upload'];

        return $self;
    }

    public function args(array $args = []): self
    {
        $this->args = $args;

        return $this;
    }

    public function select(array $select = []): self
    {
        $this->select = $select;

        return $this;
    }

    /**
     * @throws JsonException
     */
    public function make(): array
    {
        $this->buildQuery();

        return match ($this->queryType) {
            self::QUERY_TYPES['mutation'] => $this->getMutation(),
            self::QUERY_TYPES['upload'] => $this->getUploadMutation(),
            default => $this->getQuery(),
        };
    }

    protected function buildQuery(): void
    {
        if (!empty($this->query)) {
            return;
        }

        $this->query = '{' . $this->name . $this->convertToGQLArgs()
            . $this->convertToGQLSelect() . '}';

    }

    protected function convertToGQLArgs(): string
    {
        if (empty($this->args) || count($this->args) === 0) {
            return '';
        }

        $argsString = '(';

        foreach ($this->args as $key => $value) {
            $argsString .= $this->resolveValue($key, $value);
        }

        $argsString = trim($argsString, ', ');

        $argsString .= ')';

        return $argsString;
    }

    protected function resolveValue(int|string $key, mixed $value): string
    {
        if ($this->isScalar($value)) {
            return $this->resolveScalarValue($key, $value);
        }

        if (
            $value instanceof UploadedFile
            || $this->arrayIsListOfFiles($value)
        ) {
            return $this->resolveFiles($key, $value);
        }

        $object = $this->resolveArrayValue($value);

        if (is_string($key) && array_is_list($value)) {
            return $key . ': [' . $this->normalizeObject($object) . '], ';
        }

        if (is_string($key)) {
            return $key . ': ' . $object;
        }

        return $object;
    }

    protected function isScalar(mixed $value): bool
    {
        return is_scalar($value)
            || is_null($value)
            || $value instanceof Scalar
            || $value instanceof BaseEnum
            || $value instanceof AbstractValueObject;
    }

    protected function resolveScalarValue(int|string $key, mixed $value): string
    {
        return $key . ': ' . $this->toGraphQLValue($value) . ', ';
    }

    protected function toGraphQLValue(mixed $var): string|float|int
    {
        if ($var instanceof BaseEnum) {
            return $var->value;
        }

        if ($var instanceof Scalar) {
            return (string)$var;
        }

        if (is_int($var) || is_float($var)) {
            return $var;
        }

        if (is_bool($var)) {
            return $var ? 'true' : 'false';
        }

        if (is_null($var)) {
            return 'null';
        }

        return '"' . $var . '"';
    }

    protected function arrayIsListOfFiles(array $value): bool
    {
        if (empty($value)) {
            return false;
        }

        foreach ($value as $k => $v) {
            if (!is_int($k)) {
                return false;
            }

            if (!$v instanceof UploadedFile) {
                return false;
            }
        }

        return true;
    }

    protected function resolveFiles(
        string $key,
        array|UploadedFile $value
    ): string {
        $this->hasFiles = true;

        $fileVariableKey = $key;
        $fileVariableName = '$' . $fileVariableKey;

        if (is_array($value)) {
            $this->map[] = sprintf('"%s": ["variables.%s"]', $key, $key);
            $this->fileVariableNames[$fileVariableName] = [$key];
        } else {
            $this->map[] = sprintf('"%s": ["variables.%s"]', $key, $key);
            $this->fileVariableNames[$fileVariableName] = $key;
        }

        $this->files[$fileVariableKey] = $value;

        if (is_array($value)) {
            $fileVariable = "$fileVariableName";
        } else {
            $fileVariable = $fileVariableName;
        }

        return sprintf('%s: %s, ', $key, $fileVariable);
    }

    protected function resolveArrayValue(array $value): string
    {
        $object = '';

        if ($this->arrayIsListOfScalars($value)) {
            return $this->implodeScalars($value);
        }

        foreach ($value as $k => $v) {
            $object .= $this->resolveValue($k, $v);
        }

        $object = trim($object, ', ');

        return '{' . $object . '}, ';
    }

    protected function arrayIsListOfScalars(array $value): bool
    {
        foreach ($value as $k => $v) {
            if (!is_int($k)) {
                return false;
            }

            if (
                !is_scalar($v)
                && !$v instanceof Scalar
                && !$v instanceof BaseEnum
            ) {
                return false;
            }
        }

        return true;
    }

    protected function implodeScalars(array $value): string
    {
        $wrapper = static function ($v) {
            if (is_string($v)) {
                return '"' . $v . '"';
            }

            return $v;
        };

        return implode(', ', array_map($wrapper, $value));
    }

    protected function normalizeObject(string $object): string
    {
        $object = trim($object, ', ');

        return str_replace(['{{', '}}'], ['{', '}'], $object);
    }

    protected function convertToGQLSelect(): string
    {
        if (empty($this->select) || count($this->select) === 0) {
            return '';
        }

        return '{' . $this->implodeRecursive(' ', $this->select) . '}';
    }

    protected function implodeRecursive(string $glue, array $parameters): string
    {
        $output = '';

        foreach ($parameters as $key => $parameter) {
            if (is_array($parameter)) {
                $output .= $key . ' {' . $this->implodeRecursive(
                        $glue,
                        $parameter
                    ) . '} ';
            } else {
                $output .= $parameter . $glue;
            }
        }

        return trim($output, $glue);
    }

    protected function getMutation(): array
    {
        $mutation = 'mutation ';

        if ($this->hasFiles) {
            $mutation .= '(';
            foreach ($this->fileVariableNames as $key => $name) {
                if(is_array($name)) {
                    $mutation .= sprintf('%s: [Upload!]! ', $key);
                } else {
                    $mutation .= sprintf('%s: Upload! ', $key);
                }
            }
            $mutation = trim($mutation);
            $mutation .= ') ';
        }

        return ['query' => $mutation . $this->getQueryString()];
    }

    public function getQueryString(): string
    {
        $this->buildQuery();

        return $this->query;
    }

    /**
     * @throws JsonException
     */
    protected function getUploadMutation(): array
    {
        return [
                'operations' => json_encode(
                    $this->getMutation(),
                    JSON_THROW_ON_ERROR
                ),
                'map'        => '{ ' . implode(', ', $this->map) . ' }',
            ] + $this->files;
    }

    protected function getQuery(): array
    {
        return ['query' => 'query ' . $this->getQueryString()];
    }

    public function dd(): void
    {
        $this->dump();

        exit(1);
    }

    public function dump(): self
    {
        $this->buildQuery();

        VarDumper::dump($this);

        return $this;
    }
}
