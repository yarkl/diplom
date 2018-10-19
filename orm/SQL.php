<?php

/**
 * Клас для побудови sql-запитів
 *
 * Class SQL
 */
class SQL{

	protected $table;
	protected $keyName = 'id';

	public $orderby = '';
	public $whereColumns = Array();
	public $whereOperators = Array();
	public $whereValues = Array();
	public $whereBools = Array();
	public $columns = '*';
	public $limit = '';

    public $groupBy = '';

    /**
     * join clauses
     * @var
     */
    protected $joins = [];

    /**
     * Model
     *
     * @var Model
     */
    protected $model;

    /**
     * Relation
     *
     * @var Relation
     */
    protected $relation = null;

    /**
     * distinct results
     *
     * @var bool
     */
    protected $distinct  = false;


	public static function create(){
		return new self();
	}

	public static function table($table, $key = null){
		$instance = self::create();
		$instance->setTable($table);
		if (! is_null($key)) $instance->setKeyName($key);
		return $instance;
	}

	public static function model(Model $model){
		$instance = self::create();
		$instance->setModel($model);
		return $instance;
	}

	public function setModel(Model $model){
		$this->model = $model;
		$this->setTable($model->getTable());
		$this->setKeyName($model->getKeyName());
        $this->select($model->getSelectColumns());

        return $this;
	}

    public function getModel(){
        return $this->model;
    }

	public function keyName(){
		return $this->keyName;
	} 

	public function setKeyName($key){
		$this->keyName = sql_name($key);
        return $this;
	}

	public function setTable($table){
		$this->table = sql_name($table);
	}

	public function formatResult( &$result ){
		if ($this->model):
			if (is_array($result))
				return $this->model->resultToModels ($result, $this->relation);
			if (is_object($result))
				return $this->model->resultToModel ($result, $this->relation);
		endif;

		return $result;		
	}

	public function get($columns = null){
        if ($columns) $this->columns = $columns;
		$result = $this->getRaw();
		return $this->formatResult ( $result );
	}

	public function getRaw(){
		$result = SQLI($this->processSelect(),$this->processParams());
		return $result;
	}	

	public function dget(){
		$result = DSQLI($this->processSelect(),$this->processParams());
		return $this->formatResult ( $result );
	}

	public function all(){
		$this->whereColumns = array();
		$result = SQLI($this->processSelect(), $this->processParams());
		return $this->formatResult($result);
	}

	public function dall(){
		$this->whereColumns = array();
		$result = DSQLI($this->processSelect(),$this->processParams());
		return $this->formatResult($result);
	}

	public function first($columns = null){
        if ($columns) $this->columns = $columns;
		$this->limit = '1';
		$arr = SQLi($this->processSelect(),$this->processParams());
		
		if (count($arr)==1):
			return $this->formatResult($arr[0]);
		endif;

		return false;
	}

	public function processSelect(){
		$sql = '';

        $distinct ='';
        if ($this->distinct) $distinct = ' DISTINCT ';

        if ($this->columns === '*') $this->columns = $this->table.'.*';

		if (isset($this->table)):
			$sql = ' SELECT ' . $distinct . $this->columns.' FROM '.$this->table.' ';
		endif;

        foreach ($this->joins as $join):
            if ($join->column2 === null):
                // using
                $sql .=
                    ' JOIN '
                    . $join->table
                    .' USING '
                    .'('
                        .$join->column1
                    .')';
            else:
                $sql .=
                    ' JOIN '
                    .$join->table
                    .' ON '
                    .$join->column1.' '
                    .$join->operator.' '
                    .$join->column2
                    .' ';
            endif;
        endforeach;

		$sql .= $this->processWhere();

        if($this->groupBy)
            $sql.=' GROUP BY '.$this->groupBy.' ';

		if($this->orderby)
			$sql.=' ORDER BY '.$this->orderby.' ';

		if ($this->limit)
			$sql.= ' LIMIT  ' . $this->limit . ' ';


		return $sql;
	}

	public function processWhere(){
		$sql = '';
		$i=0;

		foreach ($this->whereColumns as $col):
			if ($i == 0) $sql.=' WHERE ';
			else $sql.=' '.$this->whereBools[$i];

			$sql.=' '.$col.' '.$this->whereOperators[$i];

            if (! is_array($this->whereValues[$i]))
                $sql.=' :where'.$i.' ';
            else{
                // WHERE IN | NOT IN
                $sql.=' ( ';
                for ($j=0; $j<count($this->whereValues[$i]); $j++){
                    if ($j!=0) $sql.=', ';
                    $sql.=':where'.$i.'_'.$j.' ';
                }
                $sql.=' ) ';
            }
            $i++;
		endforeach;
		return $sql;
	}

	public function processParams(){
		$params = Array();
		$i=0;
		foreach ($this->whereValues as $value) {
            if (is_array(($value))):
                $j = 0;
                foreach ($value as $in):
                    $params['where'.$i.'_'.$j] = $in;
                    $j++;
                endforeach;
            else:
			    $params['where'.$i] = $value;
            endif;
			$i++;
		}
        //dprint($params);

        return $params;
	}

	public function processUpdateParams($attributes){
		$params = $this->processParams();
		foreach ($attributes as $key => $value) {
			$params[sql_name($key)] = $value;
		}
		return $params;
	}

	public function update($attributes){
		// Where має бути встановлено
		// захист від випадкового перезапису усієї таблиці
		if (!$this->whereColumns) return false;
		//dprint ($this->processUpdate($attributes));
		return DDL (
			$this->processUpdate($attributes), 
			$this->processUpdateParams($attributes)
		);	
	}

	public function processUpdate($attributes){
		$sql = ' UPDATE '. $this->table. ' ';

		if ($attributes) $sql.=' SET ';
		else return false;

		$comma = '';
		foreach ($attributes as $key => $value):
			$sql .= $comma. sql_name($key).'=:'.sql_name($key).' ';	
			$comma = ",\n ";
		endforeach;	

		$sql.=$this->processWhere();	

		return $sql;
	}

	public function orderby($orderby){
        if ($orderby){
            $this->orderby = $orderby;
        }
		return $this;
	}

	public function where($column, $operator = null, $value = null, $boolean = 'and'){

		// Скорочений виклик:
		// Перший аргумент - поле
		// Другий аргумент - значення
		if (func_num_args() == 2):
			$value = $operator;
			$operator = '=';
		endif;

        if (strpos($column, '.') === false) $column = $this->table.'.'.$column;

		$this->whereColumns		[] 		= sql_name($column);
		$this->whereValues 		[] 		= $value;
		$this->whereOperators 	[]	 	= sql_operator ($operator);
		$this->whereBools 		[] 		= sql_operator ($boolean);

		return $this;
	}

    public function orWhere($column, $operator = null, $value = null){
        // Скорочений виклик:
        // Перший аргумент - поле
        // Другий аргумент - значення
        if (func_num_args() == 2):
            $value = $operator;
            $operator = '=';
        endif;
        return $this->where($column, $operator, $value, 'or');
    }

	public function limit($limit){
		$this->limit = $limit;
		return $this;
	}


	public function find($value, $columns = null){
		if ($columns) $this->columns = $columns;
		$column = $this->keyName();

		if (is_array($value)):
			foreach ($value as $val) {
				$this->where ($column, '=', $val, 'or');
			}
			return $this->get();
		endif;

		$this->limit('1');
		return $this->where($column,'=', $value)->first();
	}

	public function count(){
		$this->columns = ' count(*) as cnt';
		$r = $this->getRaw();
		return $r[0]->cnt; 
	}

	public function __call($method, $parameters){
		if ($this->isDynamicWhere($method)):
			return $this->dynamicWhere($method, $parameters);
		endif;
		throw new Exception('Undefined SQL-method: '.$method );
	}

	public function isDynamicWhere($method){
		$length = strlen('where');
     	return (substr($method, 0, $length) === 'where');
	}
	
	public function dynamicWhere($method, $parameters){
		$length = strlen('where');
		$column = lcfirst (substr($method, $length));
		array_unshift($parameters, $column); 
		return call_user_func_array( [$this, 'where'], $parameters);
	}

    public function select($columns){
	    if (is_array($columns)):
            foreach ($columns as &$column):
                if (strpos($column, '.') === false):
                    if ($this->table)
                        $column = $this->table.'.'.$column;
                endif;
            endforeach;
            $columns = implode(',', $columns);
        endif;
        $this->columns = $columns;
        return $this;
    }

    public function join($table, $column1, $operator = null, $column2 = null){
        // JOIN USING
        if (func_num_args() == 2):
            $this->joins[] = (Object)[
                'table'    => sql_name($table)
                ,'column1'  => sql_name($column1)
                ,'operator' => null
                ,'column2'  => null
            ];
            return $this;
        endif;

        if (func_num_args() == 3):
            $column2    = $operator;
            $operator   = '=';
        endif;

        // standart JOIN
        if (strpos($column1, '.') === false )
            $column1 = $this->table.'.'.$column1;
        if (strpos($column2, '.') === false)
            $column2 = $table.'.'.$column2;

        $this->joins[] = (Object) [
             'table'    => sql_name($table)
            ,'column1'  => sql_name($column1)
            ,'operator' => sql_operator($operator)
            ,'column2'  => sql_name($column2)
        ];
        return $this;
    }

    /**
     * process insert
     *
     * @param $attributes
     * @return bool|mixed
     */
    public function insert($attributes){

        if (! $this->isArrayOfAssoc($attributes))
            $attributes = [$attributes];

        $columns = [];
        $values = [];
        $sqlValues = '';
        $i = 0;
        foreach ($attributes as $record){
            $i++;

            if (! $columns){
                $columns =  array_keys($record);
            }else
                // check if input arrays is of the same structure
                if ($columns !== array_keys($record)){
                    return false;
                }
            if ($i>1) $sqlValues.=' , ';
            $sqlValues.=' ( ';
            $first = true;
            foreach ($record as $key => $value){
                $values[$key.$i] = $value;

                if (! $first){
                    $sqlValues.=' , ';
                }else $first = false;

                $sqlValues.= ':'.$key.$i;
            }
            $sqlValues.=' ) ';
        }

        $sql = 'INSERT into '
            .$this->table
            .' ('
            .implode(',', $columns)
            .') '
            . ' VALUES '
            .$sqlValues
            .$this->processWhere()
        ;
        return DDL($sql, $values);
    }

    /**
     * Delete records
     *
     * @param null $id
     * @return bool|mixed
     */
    public function delete($id = NULL){
        if ($id) $this->where($this->keyName, $id);

        // do not allow delete without where
        if (! $this->whereColumns) return false;

        $sql = 'DELETE from '
            .$this->table
            .$this->processWhere();

        return DDL($sql, $this->processParams());
    }

    public function setRelation(Relation $relation){
        $this->relation = $relation;
    }

    /**
     * check if array is array of associative arrays - many attribute arrays
     *
     * @param $arr
     * @return bool
     */
    public function isArrayOfAssoc($arr) {
        foreach ($arr as $key => $value) {
            if (is_string($key))    return false;
            if (is_int($key))       return true;
        }
        return false;
    }

    /**
     * pluck:   get array of one column values
     *          or [custom key => column] array
     *
     * @param $column
     * @param null $key
     * @return array
     */
    public function pluck($column, $key = null){
        $results = $this->get(is_null($key) ? $column : $column.', '. $key);
        //auth_print($results);
        return $this->pluckResult($results, $column, $key);
    }

    /**
     * prepare pluck results
     *
     * @param $results
     * @param $column
     * @param null $key
     * @return array
     */
    public function pluckResult($results, $column, $key = null){
        $plucked    = [];
        $column     = $this->getPlainColumn($column);
        $key        = $this->getPlainColumn($key);
        //dprint($results);
        foreach ($results as $result):
            if (is_null($key))
                $plucked []              = $result->$column;
            else
                $plucked [$result->$key] = $result->$column;
        endforeach;

        //auth_print($plucked);

        return $plucked;
    }

    /**
     * get plain column name (no table name)
     *
     * @param $column
     * @return mixed
     */
    public function getPlainColumn($column){
        if (strpos($column, '.')!== false)
            list(,$column) = explode('.', $column);
        return $column;
    }

    /**
     * WHERE in
     *
     * @param $column
     * @param $values
     * @param string $bool
     * @return $this
     */
    public function whereIn($column, $values, $bool = 'and'){
        return $this->where($column, 'in', $values, $bool);
    }

    /**
     * WHERE NOT IN
     *
     * @param $column
     * @param $values
     * @param string $bool
     * @return $this
     */
    public function whereNotIn($column, $values, $bool = 'and'){
        // если пустой массив
        if (! $values) return $this;
        //если массив не пустой
        return $this->where($column, 'NOT IN', $values, $bool);
    }

    /**
     * distinct
     *
     * @return $this
     */
    public function distinct(){
        $this->distinct = true;
        return $this;
    }

    /**
     * groupBy
     *
     * @param $groupBy
     * @return SQL
     */
    public function groupBy($groupBy){
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * truncate table
     *
     * @return mixed
     */
    public function truncate(){
        return
            DDL('TRUNCATE TABLE ' . $this->table);
    }



}


