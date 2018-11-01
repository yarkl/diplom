<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require dirname(__FILE__). '/vendor/autoload.php';
require 'dd.php';
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
         SELECT t1.concept as p_concept,t3.concept,t2.from_id as f, t2.to_id as t FROM CtoC t2
         inner join concepts t1 on t2.from_id = t1.id
         inner join concepts t3 on t2.to_id = t3.id
         order by from_id;
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



$rec = new \App\Recursive($oldArr);
$rec->processArray(2);
$rec->removeParentTo();

//echo json_encode($labels,JSON_UNESCAPED_UNICODE);
//echo json_encode($array);
//sort($rec->arr);

echo json_encode(['labels' => $rec->labels,'nodes' => $rec->arr],JSON_UNESCAPED_UNICODE);
