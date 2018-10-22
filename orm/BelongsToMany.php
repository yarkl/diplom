<?php
namespace Orm;

/**
 * Class BelongsToMany
 */
class BelongsToMany extends Relation {

    /**
     * cross table
     *
     * @var string
     */
    protected $table;

    protected $foreignKey;

    protected $otherKey;

    public function __construct(SQL $builder, Model $parent, $table, $foreignKey, $otherKey)
    {
        $this->table = $table;
        $this->otherKey = $otherKey;
        $this->foreignKey = $foreignKey;

        parent::__construct($builder, $parent);
    }

    public function addConstraints(){
        $this->builder
            ->select(
                $this->related->getQualifiedColumn('*')
            )
            ->join(
                $this->table
                , $this->related->getQualifiedKeyName()
                , '='
                , $this->table.'.'.$this->otherKey
            )
            ->where( $this->table.'.'.$this->foreignKey , $this->parent->getKeyValue() );
    }

    public function getResults(){
        return $this->builder->get();
    }

} 