<?php
namespace Orm;
abstract class Model {

	protected  	$table;
	protected  	$keyName = 'id';
	protected 	$originalKeyValue;
	public 		$keyAutoinc = true;
    /**
     * @var SQL
     */
	protected  	$builder;

	protected   $attributes = [];
	protected   $exists = false;

    /**
     * columns for select
     * selects only this
     * if null selects all *
     *
     * @var array
     */
	protected $columns;

    /**
     * Збережені пов'язані моделі
     *
     * @var array
     */
    protected   $related = [];

    /**
     * Relation which been used to create(!) current model
     * Note: to use related models retrieved by this model we use $this->related[ relation => models ]
     *
     * ? Do we really need it?
     *
     * @var
     */
    protected $relation;

	public function __construct($attributes = [], $exists = false, Relation $relation = null ){
		$this->getTable();
		$this->attributes 	= (array) $attributes;
		$this->exists 		= $exists;
		if ($this->exists):
			$this->originalKeyValue = $this->attributes [ $this->getKeyName() ];
		endif;

        if ($relation)
            $this->relation = $relation;

        // columns with table name prefix
        if ($this->columns)
            $this->setColumns($this->columns);

        $this->onCreate();
	}

    /**
     * OnCreate Event of Model - for overriding
     */
     public function onCreate(){}

	public function getBuilder(){
		if (! $this->builder) return $this->newBuilder();
		return $this->builder;
	}

	public function newBuilder(){
		$this->builder = SQL::model($this);
		return $this->builder;
	}

	public function getTable(){
		if (isset($this->table)) {
            return $this->table;
        } else {
	        $class = $this->getClassName();
	        $this->table = $this->toSnake($class).'s';
	        return $this->table;
        }
	}

    public function getClassName($class = null){
        if (is_null($class)) $class = $this;

        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

	public function toSnake($input) {
	  	return  ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $input)), '_');
	}

	public function getKeyName(){
		return $this->keyName;
	}

    public function getQualifiedKeyName(){
        return $this->getTable().'.'.$this->getKeyName();
    }

	public function setKeyName($key){
		$this->key = $keyName;
	}

	public function originalKeyValue(){
		return $this->originalKeyValue;
	}

    public function getKeyValue(){
        return $this->attributes[$this->getKeyName()];
    }

    public function getQualifiedColumn($name){
        return $this->getTable().'.'.$name;
    }

	/**
	* Міст до sql-білдера в нестатичному контексті
	* @return екземпляр SQL (білдер запитів)
	*/
	public function __call(string $name , array $arguments ){
        $builder = $this -> getBuilder();
		$result = call_user_func_array( [$builder, $name] , $arguments);
		if ($result instanceof SQL) return $this;
		return $result;
	}

	/**
	* Міст до sql-білдера в статичному контексті
	* Створюється новий білдер
	* @return екземпляр SQL (білдер запитів)
	*/
	public static function __callStatic($name , $arguments ){
		$instance = new static();
		$builder = $instance -> newBuilder();
		$result = call_user_func_array( [$builder, $name] , $arguments);
        if ($result instanceof SQL) return $instance;
        return $result;
	}

	/**
	* Доступ до атрибутів
	*/
	public  function __get($name){
		if (array_key_exists ($name, $this->attributes))
			return $this->attributes[$name];

        if (method_exists($this, $name)) {
            return $this->getRelated($name);
        }

		throw new Exception('Undefined Model property: '.$name );
	}

    /**
     * Повертає пов'язані моделі
     *
     * @param $key
     * @return mixed
     */
    public function getRelated($key){
        // Related already loaded
        if ($this->relatedLoaded($key)) return $this->related[$key];

        // run SQL
        $this->related[$key] = $this->$key()->getResults();
        return $this->related[$key];
    }

    /**
     * Перевіряє, чи вже виконувався запит на отримання моделей відповідно до певного відношення.
     * (Чи є в масиві відношень такий ключ)
     * @return bool
     */
    public function relatedLoaded($key){
        return array_key_exists($key, $this->related);
    }
	/**
	* Установка атрибута
	*/
	public function __set($name, $value){
		$this->attributes[$name] = $value;
	}

    public static function resultToModels ( $items, Relation $relation = null ) {
    	$instance = new static(); 
    	$items = array_map ( function ($item) use ($instance, $relation) {
    		return $instance->newInstance( $item , true, $relation );
    	}, $items );
    	return $items;
    }

    /**
     * Результат SQL запита перетворити на масив Моделей
     */
    public function resultToModel($object, Relation $relation = null ){
    	return $this->newInstance($object , true, $relation);
    }

    /**
     * Новий екземпляр Моделі
     */
    public function newInstance( $attributes = [] , $exists = false, Relation $relation = null  ){
    	return  new static( $attributes, $exists, $relation );
    }


    /**
    * save updates
    */
    public function save(){   	
    	if ($this->exists):
    		$sql = $this->newBuilder();
    		$sql->where($this->getKeyName(), '=', $this->originalKeyValue());
    		return $sql->update($this->attributes);
    	else:
            $sql = $this->newBuilder();
            return $sql->insert($this->attributes);
        endif;
    }

    /**
     * Встановлення та обробка запитів 1-до-багатьох
     *
     * @param $modelName
     * @param null $foreignKey
     * @param null $relatedKey
     * @return mixed
     */
    public function hasMany( $modelName , $foreignKey = null, $relatedKey = null ){
        $model = new $modelName;
        return new HasMany($model->getBuilder(), $this , $foreignKey , $relatedKey);
    }

    /**
     * Default foreign Key Name
     */
    public function getDefaultForeignKey()
    {
        return $this->toSnake($this->getClassName()).'_id';
    }

    /**
     * Встановлення та обробка запитів 1-до-1
     *
     * @param $modelName
     * @param null $foreignKey
     * @param null $relatedKey
     * @return mixed
     */
    public function hasOne( $modelName , $foreignKey = null, $relatedKey = null ){
        $model = new $modelName;
        return new HasOne($model->getBuilder(), $this , $foreignKey , $relatedKey);

    }

    /**
     * Children relation
     *
     * @param null $parentKey
     * @return hasChildren
     */
    public function hasChildren( $parentKey = null){
        $class = get_class($this);
        $model = new $class;

        return new HasChildren($model->getBuilder(), $this, $parentKey );
    }

    /**
     * Parent relation
     *
     * @param null $parentKey
     * @return hasParent
     */
    public function hasParent($parentKey = null){
        $class = get_class($this);
        $model = new $class;

        return new HasParent($model->getBuilder(), $this, $parentKey );
    }

    /**
     * Get Default Parent field name for Hierarchy relations
     *
     * @return string
     */
    public function getDefaultParentKey(){
        return 'parent';
    }

    /**
     * Many-to-Many relation
     *
     * @param $modelName
     * @param null $table
     * @param null $foreignKey
     * @param null $otherKey
     * @return BelongsToMany
     */
    public function belongsToMany ($modelName, $table = null, $foreignKey = null, $otherKey = null){
        $model      = new $modelName;
        $table      = $table ?:         $this->getDefaultCrossTable($modelName);
        $foreignKey = $foreignKey ?:    $this ->getDefaultForeignKey();
        $otherKey   = $otherKey ?:      $model->getDefaultForeignKey();

        return new BelongsToMany($model->getBuilder(), $this, $table, $foreignKey, $otherKey);
    }

    /**
     * get default name of cross table
     * example: model1_model2
     * @param $modelName
     * @return string
     */
    public function getDefaultCrossTable($modelName){
        $one = $this->toSnake( $this->getClassName());
        $two = $this->toSnake( $this->getClassName($modelName) );
        $names = [$one, $two];
        sort($names);
        return implode('_', $names);
    }
    /**
     * for saving link to origin relation in related models
     *
     * @param $relation
     */
    public function setRelation(Relation $relation){
        $this->relation = $relation;
    }

    /**
     * has custom relation
     *
     * Метод, що викликає hasCustom повинен реалізувати SQL-запит
     * Наприклад
     *   return hasCustom('someTable')->where('someField', '>', 10);
     *
     * @param $modelName
     * @return HasCustom
     */
    public function hasCustom($modelName){
        $model      = new $modelName;
        return new HasCustom($model->getBuilder(), $this);
    }

    /**
     * Get relation which been used to create current model
     *
     * @return Relation
     */
    public function getRelation(){
        return $this->relation;
    }

    /**
     * attributes
     *
     * @return array
     */
    public function getAttributes(){
        return $this->attributes;
    }

    /**
     * get attribute
     *
     * @param $name
     * @return mixed
     */
    public function getAttribute($name){
        return $this->attributes[$name];
    }

    /**
     * set columns
     *
     * @param $cols array
     * @return self
     */
    public function setColumns(array $cols){
        foreach ($cols as &$col) {
            if (strpos($col, $this->table.'.') === false)
                $col = $this->table.'.'.$col;
        }
        $this->columns = $cols;
        return $this;
    }

    /**
     * get columns array
     *
     * @return array
     */
    public function getColumns(){
        return $this->columns;
    }

    /**
     * get columns for select
     *
     * @return string
     */
    public function getSelectColumns(){
        if ($this->columns)
            return implode (',', $this->columns);

        return $this->table.'.*';
    }

    /**
     * set attributes
     *
     * @param $attributes
     */
    public function setAttributes($attributes){
        $this->attributes = $attributes;
    }

}


