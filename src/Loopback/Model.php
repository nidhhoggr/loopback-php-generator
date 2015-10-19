<?php
namespace LoopbackPHP\Loopback;

/**
 * Class Model
 * Loopback model reader
 * @package LoopbackPHP\Loopback
 */
class Model
{

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
    }
}