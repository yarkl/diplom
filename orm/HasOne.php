<?php

/**
 * Class HasOne
 *
 * Описує зв'язок 1 до 1
 */
class HasOne extends HasMany{

    /**
     * Повертає один запис
     */
    public function getResults(){
        return $this->builder->first();
    }

    /**
     *  Задання зв'язку для запиту
     */
    public function addConstraints(){
        $this->builder->where($this->foreignKey, $this->getLocalKeyValue())->limit(1);
    }

} 