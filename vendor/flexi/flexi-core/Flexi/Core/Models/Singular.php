<?php

namespace Flexi\Core\Models;

abstract class Singular
{
    public $slug;
    public $name;
    public $singularName;

    protected $args;
    protected $model = null;

    public abstract function setModel($model);
    protected abstract function setLabels($args);
    protected abstract function setArgs($args, $labels);

    public function getArgs()
    {
        return $this->args;
    }

    public function getModel()
    {
        return $this->model ?? null;
    }
}