<?php
namespace LoopbackPHP\Loopback;

/**
 * Class Model
 * Loopback model reader
 * @package LoopbackPHP\Loopback
 */
class Model
{

    const TYPE_MAPPING = [
        'number' => 'number',
        'string' => 'string',
        'object' => 'array',
        'array' => 'array',
        'date' => '\DateTime',
        'null' => 'null'
    ];

    /**
     * @var array
     */
    public $properties;

    /**
     * @var array
     */
    public $model;

    public function __construct($filepath)
    {
        if (!file_exists($filepath)) {
            throw new \InvalidArgumentException("Model file doesn't exists: " . $filepath);
        }

        $this->model = json_decode(file_get_contents($filepath), true);
        $this->properties = $this->model['properties'];
        foreach ($this->properties as $property => &$propertyDef) {
            $propertyDef['type'] = self::getTypeMapping($propertyDef['type']);
        }
    }

    /**
     * @param mixed $type
     * @return string
     */
    public static function getTypeMapping($type)
    {
        if (is_string($type)) {
            $mapping = self::TYPE_MAPPING;
            return isset($mapping[$type]) ? $mapping[$type] : $type;
        } else {
            if (is_array($type)) {
                return 'array';
            } else {
                return $type;
            }
        }
    }
}