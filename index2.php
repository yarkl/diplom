<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require dirname(__FILE__). '/vendor/autoload.php';
//var_dump(\App\Concept::all());


$link = new mysqli("127.0.0.1", "root", "root", "diplom");
$link->set_charset("utf8");
if (!$link) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
function sql (){
    return "
          SELECT t1.concept,t1.id,t2.found_id as pid FROM concepts t1
          LEFT JOIN CinC t2 on t1.id = t2.source_id 
          WHERE t2.found_id = ( SELECT MAX(t21.found_id) as pid FROM concepts t15
		  LEFT JOIN CinC t21 on t15.id = t21.source_id 
          WHERE t15.concept = t1.concept ORDER BY t15.id)
          ORDER BY t1.id;
		 
    ";

};
$query = $link->query(sql());

if(!$query){
    echo "Ошибка: Not correct query" . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
$oldArr = [];
while ($row = $query->fetch_assoc()) {
    $oldArr [] = $row;
}

mysqli_close($link);

$array = array();
$labels = [];
array_push($labels,['id' => 2,'label'=> 'Позвоночник']);
$array = array();

function recursive($data, $pid = 2, $level = 0){
    global $array;
    global $labels;



    foreach ($data as $row)   {
        if ($row['pid'] == $pid)   {

            $_row['from']    = $pid;
            $_row['to']    = $row['id'];
            //$_row['value']    = $row['searchConceptsCalled'];
            $array[] = $_row;
            $_label['id'] = (int)$row['id'];
            $_label['label'] = $row['concept'];
            $_label['group']    = $pid;
           //$_label['cid']    = $pid;
            $_label['value']    = count($data);
            //$_label['color'] = random_color();
            $labels [] = $_label;


            recursive($data, $row['id'], $level + 1);
        }
    }
}
recursive($oldArr);
//echo json_encode($labels,JSON_UNESCAPED_UNICODE);
//echo json_encode($array);
sort($array);

echo json_encode(['labels' => $labels,'nodes' => $array],JSON_UNESCAPED_UNICODE);
