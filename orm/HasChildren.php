<?php
namespace Orm;
/**
 * Children relation
 *
 * Class HasChildren
 */
class HasChildren extends Relation {

    protected $parentKey;

    public function __construct(SQL $builder, Model $parent , $parentKey = null){

        $this->builder = $builder;
        $this->parent = $parent;
        $this->parentKey = $parentKey ? $parentKey : $parent->getDefaultParentKey();

        parent::__construct($builder, $parent);
    }

    public function getResults(){
        return $this->builder->get();
    }

    public function addConstraints(){
        $this->builder->where(
            $this->related->getTable().'.'.$this->parentKey
            , $this->parent->getKeyValue()
        );
    }
}