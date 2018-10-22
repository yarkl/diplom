<?php
namespace Orm;
class HasMany extends Relation{

    protected $foreignKey;
    protected $localKey;


    public function __construct(SQL $builder, Model $parent, $foreignKey = null, $localKey = null){

        $this->foreignKey = $foreignKey ?: $parent->getDefaultForeignKey();
        $this->localKey = $localKey ?: $parent->getKeyName();

        parent::__construct($builder, $parent);
    }

    public function getResults(){
        return $this->builder->get();
    }

    public function addConstraints(){
        $this->builder->where( $this->related->getTable().'.'.$this->foreignKey, $this->getLocalKeyValue());
    }

    public function getLocalKeyValue(){
        return $this->parent->{$this->localKey};
    }
} 