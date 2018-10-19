<?php
/**
 * Class Relation
 * Потрібен для опису відношення для Моделей
 */
abstract class Relation {

    protected $builder;
    protected $parent;
    protected $related;

    public function __construct( SQL $builder, Model $parent){
        $this->builder = $builder;
        $this->parent = $parent;
        $this->related = $this->builder->getModel();
        $this->builder->setRelation($this);
        $this->addConstraints();
    }

    abstract public function getResults();

    abstract public function addConstraints();

    public function __call($method, $parameters){

        $result = call_user_func_array([$this->builder, $method], $parameters);

        if ($result === $this->builder) {
            return $this;
        }

        return $result;
    }

    public function getParent(){
        return $this->parent;
    }

} 