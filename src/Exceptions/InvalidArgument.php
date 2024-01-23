<?php

namespace Whitecube\Links\Exceptions;

use InvalidArgumentException;

class InvalidArgument extends InvalidArgumentException
{
    public array $arguments = [];

    /**
     * Create a new exception instance.
     */
    public static function for(): static
    {
        return new static(static::generateMessage([]));
    }

    /**
     * Generate a specific exception message.
     */
    public static function generateMessage(array $data): string
    {
        $subject = ($data['method'] ?? null)
            ? 'method '.$data['method']['classname'].'::'.$data['method']['function'].'()'
            : null;

        if($data['argument'] ?? null) {
            $subject = $subject ? 'argument "'.$data['argument'].'" of '.$subject : 'argument "'.$data['argument'].'"';
        }

        if($data['expected'] ?? null) {
            $last = (count($data['expected']) > 1) ? array_pop($data['expected']) : null;
            $expectation = implode(', ', $data['expected']) . ($last ? ' or ' . $last : '');
            $expected = 'expected value of type '.$expectation;
        } else {
            $expected = $subject ? 'was invoked with an invalid value' : 'invalid value';
        }

        if(array_key_exists('received', $data)) {
            $type = gettype($data['received']);

            $type = match ($type) {
                'object' => 'class instance "'.get_class($data['received']).'"',
                'array' => 'array "'.(strlen($value = json_encode($data['received'])) > 64 ? substr($value, 0, 64).'…' : $value).'"',
                'boolean' => 'bool '.($data['received'] ? 'TRUE' : 'FALSE'),
                'integer',
                'double',
                'string' => $type.' "'.(strlen($value = strval($data['received'])) > 64 ? substr($value, 0, 64).'…' : $value).'"',
                default => $type,
            };

            $complement = $subject ? ', got '.$type : ($data['expected'] ?? null ? ' but got '.$type : ' of type '.$type);
        } else {
            $complement = null;
        }

        return ucfirst(trim($subject.' '.$expected.$complement).'.');
    }

    /**
     * Define the method.
     */
    public function method($classname, $function): static
    {
        $arguments = array_merge($this->arguments, [
            'method' => ['classname' => $classname, 'function' => $function]
        ]);

        $instance = new static(static::generateMessage($arguments));
        $instance->arguments = $arguments;

        return $instance;
    }

    /**
     * Define the argument's name.
     */
    public function argument(string $argument): static
    {
        $arguments = array_merge($this->arguments, [
            'argument' => $argument
        ]);

        $instance = new static(static::generateMessage($arguments));
        $instance->arguments = $arguments;

        return $instance;
    }

    /**
     * Define the method's expected argument types
     */
    public function expected(string|array $types): static
    {
        if(is_string($types)) {
            $types = [$types];
        }

        $arguments = array_merge($this->arguments, [
            'expected' => $types,
        ]);

        $instance = new static(static::generateMessage($arguments));
        $instance->arguments = $arguments;

        return $instance;
    }

    /**
     * Define the method's actual received value.
     */
    public function received(mixed $value): static
    {
        $arguments = array_merge($this->arguments, [
            'received' => $value,
        ]);

        $instance = new static(static::generateMessage($arguments));
        $instance->arguments = $arguments;

        return $instance;
    }
}
