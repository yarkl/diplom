<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 01.11.18
 * Time: 12:49
 */

namespace App;


class Recursive
{
    private $array;
    private $readyArr = [];
    private $labels = [];
    private $parent_id;

    public function __construct(array $array)
    {
        try{
            $this->array = $array;
        }catch (\Exception $e){
            throw $e;
        }
    }



    public function processArray($pid,$data = NULL,$level = 0){
        $arr = array_filter($this->array,function ($item) use ($pid){ return $item['f'] == $pid; });
        if($level == 2){
            return;
        }
        foreach ($arr as $row)   {
            $id = (int) $row['t'];
//            $this->removeParentTo($pid,$id);
            $_row['from']  = $pid;
            $_row['to']    = $id;
            $_label['id'] = $id;
            $_label['label'] = $row['concept'];
            $_label['group']    = $pid;
            $_label['value']    = count($arr);
            array_push($this->labels,$_label);
            array_push($this->readyArr,$_row);

            $this->processArray( $id, $this->array,$level + 1);
        }

    }

    public function removeParentTo(){
        foreach ($this->readyArr as $key => $item){
           $arr = array_filter($this->readyArr,function ($data,$key) use($item){
               if($data['from'] < $item['from'] && $data['to'] == $item['to']){
                   unset($this->readyArr[$key]);
               }
           },ARRAY_FILTER_USE_BOTH);
        }
    }

    public function getArr(){
        sort($this->readyArr);
        return $this->readyArr;
    }
    public function getLabels(){
        array_push($this->labels,['id' => 2,'label'=> 'Позвоночник']);
        return $this->labels;
    }


    public function setPid($id){
        $this->parent_id = $id;
    }

    public function __get(string $property)
    {
        $name = ucfirst($property);
        $method = "get{$name}";
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

    public function __set(string $property, string $value)
    {
        $name = ucfirst($property);
        $method = "set{$name}";
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
    }
}