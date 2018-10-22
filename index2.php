<?php
header("Access-Control-Allow-Origin: X-Requested-With");
header("Access-Control-Allow-Headers: X-Requested-With");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$link = new mysqli("127.0.0.1", "root", "root", "diplom");
$link->set_charset("utf8");
if (!$link) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
function sql ($id){
    return "
         SELECT t1.id,t1.concept,t2.found_id as pid,t1.searchConceptsCalled  FROM concepts t1
		 LEFT JOIN CinC t2 on t1.id = t2.source_id;
		 
    ";

};
$query = $link->query(sql(2));

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

function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}

random_color();


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
            $_label['value']    = $row['searchConceptsCalled'];
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
