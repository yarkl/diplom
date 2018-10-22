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
            SELECT t3.id as pid,t3.concept as parent_concept,t1.id,t1.concept,t1.searchConceptsCalled FROM concepts t1
            INNER JOIN CinC t2 on t1.id = t2.source_id 
            INNER JOIN concepts t3 on t2.found_id = t3.id
            WHERE t3.id = $id;
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


$array = array();
$labels = [];
function recursive($data, $pid = 2, $level = 0){
    global $array,$labels;
    foreach ($data as $row)   {
        if ($row['pid'] == $pid)   {
            $_row['from']    = (int)$pid;
            $_row['to']    = (int)$row['id'];
            $array[] = $_row; //

            $_label['id'] = (int)$row['pid'];
            $_label['label'] = $row['parent_concept'];
            $labels [] = $_label;
            unset($_label);
            $_label['id'] = (int)$row['id'];
            $_label['label'] = $row['concept'];
            $_label['group']    = $pid;
            $_label['value']    = $row['searchConceptsCalled'];

            $labels [] = $_label;
            $array[] = $_row; //

            $link = new mysqli("127.0.0.1", "root", "root", "diplom");
            $link->set_charset("utf8");
            $query = $link->query(sql($_row['to']));

            if(!$query){
                echo "Ошибка: Not correct query" . PHP_EOL;
                echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
                echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
                exit;
            }
            $data = [];
            while ($column = $query->fetch_assoc()) {
                $data [] = $column;
            }
            mysqli_close($link);

            recursive($data, $_row['to'], $level + 1);

        }
    }
}
recursive($oldArr);
//echo json_encode($labels,JSON_UNESCAPED_UNICODE);
//echo json_encode($array);
sort($array);

echo json_encode(['labels' => $labels,'nodes' => $array],JSON_UNESCAPED_UNICODE);

//echo "Соединение с MySQL установлено!" . PHP_EOL;
//echo "Информация о сервере: " . mysqli_get_host_info($link) . PHP_EOL;

