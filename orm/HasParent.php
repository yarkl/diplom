<?php
namespace Orm;
/**
 * Class HasParent
 */
class HasParent extends HasChildren {

    const PARENT_ROOT = 'default';

    public function addConstraints(){
        $this->builder->where(
            $this->related->getQualifiedKeyName()
            , $this->parent->{$this->parentKey});
    }

    public function getResults(){
        if ($this->parent->{$this->parentKey} === self::PARENT_ROOT)
            return false;
        return $this->builder->first();
    }
} 