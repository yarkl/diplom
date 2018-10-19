<?php

/**
 * Class HasCustom
 * Для кастомних відношень.
 * Сам запит повинен прописуватись у методі моделі,
 * що викликає в return $this->hasCustom(..)
 *
 * Наприклад:
 * return
 *      $this->hasCustom('MyRelatedTable', $this)
 *      ->where('someKey', $this->someField);
 */
class HasCustom extends Relation
{
    public function __construct(SQL $builder, Model $parent)
    {
        parent::__construct($builder, $parent);
    }

    public function getResults()
    {
        return $this->builder->get();
    }

    public function addConstraints()
    {
        // nothing to do
    }
}