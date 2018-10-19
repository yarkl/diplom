<?

require dirname(__FILE__)."/../app/db.connect/db_connect.php";
require "SQL.php";
require "RawModel.php";


global $SQLQCount;
$SQLQCount=0;

class Database {
	public $debugMode = false;
	private static $_instance = null;
	protected $dbh = null;
	public $connected = false;
	
	private function __construct(){
	}
	
	public function getDbh(){
		return $this->dbh; 
	}
	public static function get(){ 
		if(is_null(self::$_instance))
		self::$_instance = new self();

		return self::$_instance;
	}
	
	public function connect($host='', $db ='', $user='', $psw = '', $charset = 'utf8'){
		$this->dbh = null;
		$this->connected = false; 
		try{
			$this->dbh = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $psw);
		}catch (PDOException $e){
			echo 'Database connection error!';
			return false;
		}
		return $this->connected = true;  
	}
	public function sql(){
		call_user_func_array('sql', func_get_args());	
	}
	public function sql_value($value){
		return $this->dbh->quote($value);
	}
	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}

	public static function debug($debug = true){
		if (is_null($debug)) return self::get()->debugMode;
		return self::get()->debugMode = $debug;
	}

	public static function sql_name($name){
		//$name = str_replace(' ','', $name);
		$name = str_replace("'",'', $name);
		$name = str_replace('/','', $name);
		$name = str_replace('\\','', $name);
		$name = str_replace(';','', $name);
		$name = str_replace(',','', $name);
		return $name;
	}
	
	public static function sql_operator($name){
		//$name = str_replace(' ','', $name);
		$name = str_replace("'",'', $name);
		$name = str_replace('/','', $name);
		$name = str_replace('\\','', $name);
		$name = str_replace(';','', $name);
		$name = str_replace(',','', $name);
		return $name;
	}
}

class Recordset {
	protected $sth = null;
	protected $dbh = null;
	public $db = null;
	public $record = null;
	public $sql;
	public $sqlResult;
	public $params = null;
	public $names = null;
	
	public $isParams;
	public $isNames;
	
	// обратная совместимость
	public $recCount = 0;
	
	public $success;

	
	function __construct(){
		$this->db = Database::get();
		$this->dbh = Database::get()->getDbh();
	}
	public function sql(){
		$args = func_get_args();
		$numargs = func_num_args();
		if (isset($args[0])):
			$sql = $args[0];
		else:
			return false;
		endif;
		
		// плейсхолдеры
		if ($numargs > 1):
			if (is_array($args[1])):
				// Именные плейсхолдеры
				$params = $args[1];
				return $this->sqlNames($sql, $params);
			else:
				// безымянные плейсхолдеры
				$params = $args;
				array_shift($params);
				return $this->sqlParams($sql, $params);
			endif;		
		else:
		// без плейсхолдеров
			return $this->query($sql);
		endif;	
	}
	
	public function sqlParams($sql, $params){
		$this->isParams = true;
		$this->params = $params;
		$this->sql = $sql;
		
		$this->sth = $this->dbh->prepare($sql);
		$this->success = $this->sth->execute($params);
		$this->recCount = $this->sth->rowCount();
		
		return $this->success;		
	}	
	public function sqlNames($sql, $names){
		$this->isNames = true;	
		$this->names = $names;
		$this->sql = $sql;
		
		$this->sth = $this->dbh->prepare($sql);
		$this->success = $this->sth->execute($names);	
		$this->recCount = $this->sth->rowCount();
		
		return $this->success;
	}

	public function query($sql){
		$this->sql = $sql;

        $this->sth = $this->dbh->query($sql);
		if ($this->sth){ 
			$this->recCount = $this->sth->rowCount();
			return $this->success = true;
		}
		else 
			return $this->success = false;
	}	
	function nextRow($style = null){
        if (!$style) $style= PDO::FETCH_ASSOC;
		if ($this->sth):
			$this->record = $this->sth->fetch($style);
			return $this->record;
		endif;
		$this->record = null;
		return false; 
	}

    function nextObj(){
        return $this->nextRow(PDO::FETCH_OBJ);
    }
	function next(){
		return $this->nextRow();
	}
	function all(){
		return $this->sth->fetchAll(PDO::FETCH_OBJ);
	}
	public function sqlResult(){
		$sql = $this->sql;
		if ($this->isNames):
			foreach ($this->names as $key => $value):
				$sql = str_replace (':'.$key, $this->dbh->quote($value), $sql);
			endforeach;
			$this->sqlResult = $sql;
			return $this->sqlResult;
		endif;
		
		if ($this->isParams):
			//dprint ($this->params);
			$found = true;
			$num = 0;
			while ($found):
				$pos = strpos($sql, '?');
				if ($pos!== false):
					$str1 = substr($sql,0, $pos);
					$str2 = substr($sql,$pos+1);
					if (!$str2) $str2='';
					$sql = 
						  $str1 
						. $this->dbh->quote($this->params[$num]) 
						. $str2;
						
					$num++;
					if ($num>=count($this->params)) break; 
				endif;
			endwhile;
			$this->sqlResult = $sql;
			return $this->sqlResult;
		endif;
		
		return $this->sql;	
	}
	public function dsql(){

        echo
			'<pre style="border:1px solid #ccc; padding:15px; margin:10px; background:#eee; color:blue;">'
			.'<b>SQL text:</b><br><br>'
			.$this->sqlResult()
			.'<br><br><i>Затронутых записей:</i> <b>'
			.$this->recCount
			.'</b>'
			.'</pre>';
	}

	function sql_value($value){
		return $this->dbh->quote($value);
	}

}

function sql(){
	incSQLQCount();

    $r = new Recordset();
	call_user_func_array(array($r,'sql'), func_get_args());
	if (Database::get()->debugMode) $r->dsql();
	return $r;
}
function ddl(){

    incSQLQCount();
	$r = new Recordset();
	$res = call_user_func_array(array($r,'sql'), func_get_args());
	if (Database::get()->debugMode) $r->dsql();
	return $res;
}
function dsql(){
	incSQLQCount();
	$r = new Recordset();
	call_user_func_array(array($r,'sql'), func_get_args());
	$r->dsql();
	return $r;
}

function sqli(){
	incSQLQCount();

    $r = new Recordset();
	call_user_func_array(array($r,'sql'), func_get_args());
	if (Database::get()->debugMode) $r->dsql();

	return $r->all();
}
function dsqli(){
	incSQLQCount();

    $r = new Recordset();
	call_user_func_array(array($r,'sql'), func_get_args());
	$r->dsql();
	
	return $r->all();
}
function query(){
	call_user_func_array('sql', func_get_args());
}

function sql_value($value){
	return Database::get()->sql_value($value);
}

function sql_name($name){
	return Database::sql_name($name);
}

function sql_operator($name){
	return Database::sql_operator($name);
}

function last_insert_id(){
	return Database::get()->lastInsertId();	
}

function incSQLQCount(){
	global $SQLQCount;
	$SQLQCount++;
}
function printSQLQCount($text=''){
	if (isset($GLOBALS['isLOCALHOST'])){
	global $SQLQCount;
	print "<div> SQL Count $text = ".$SQLQCount."</div>";
	}
}

function getSQLCount(){
    global $SQLQCount;
    return $SQLQCount;
}


function debugPrint($text, $caption = ''){
	print '<pre style="border:1px solid #ccc; padding:10px; margin:10px; background:#eee; color:blue;">';
    if ($caption)
        print "<p><strong>$caption:</strong></p>";
    print_r($text);
    print '</pre>';
}
function dprint($var, $caption = ''){
    debugPrint($var, $caption);
}

function sqldebug($debug = true){
	return Database::debug($debug);
}

function getMemoryUsage(){
    return round(memory_get_peak_usage (true) / (1024 * 1024), 2) . ' Mb' ;
}

Database::get()->connect(HostName,DBName,UserName,Psw, DB_CHARSET);
$DB = Database::get();

Database::get()->debugMode = false;

//dprint($DB);


//$r = dsql("select * from views ");
//$r = dsql('select * from views where view=?','main');
//dprint ($r->next());



require dirname(__FILE__)."/../app/db.connect/db_init.php";
