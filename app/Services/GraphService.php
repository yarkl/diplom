<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 08.11.18
 * Time: 13:35
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;

class GraphService
{
    private $array = [];
    private $labels = [];
    private $readyArr = [];

    public function __construct()
    {
        $this->array  = $this->getRows();
    }

    private function getRows(){
        return DB::select("SELECT t1.concept,t1.id,t2.found_id as pid,t3.concept as pcon FROM concepts t1
          LEFT JOIN CinC t2 on t1.id = t2.source_id 
          LEFT JOIN concepts t3 on t3.id = t2.found_id
          WHERE t2.found_id = ( SELECT MAX(t21.found_id) as pid FROM concepts t15
		  LEFT JOIN CinC t21 on t15.id = t21.source_id 
          WHERE t15.concept = t1.concept ORDER BY t15.id)
          ORDER BY t1.id;");
    }

    public function processArray($pid,$data = NULL,$level = 0){
        $arr = array_filter($this->array,function ($item) use ($pid){ return  $item->pid == $pid; });
        foreach ($arr as $row)   {
            $id = (int) $row->id;
//            $this->removeParentTo($pid,$id);
            $_row['from']  = $pid;
            $_row['to']    = $id;
            $_label['id'] = $id;
            $_label['label'] = $row->concept;
            $_label['url'] = url("/show/{$id}");
            $_label['group']    = $pid;
            $_label['value']    = count($arr);
            array_push($this->labels,$_label);
            unset($_label);
            $_label['id'] = $pid;
            $_label['label'] = $row->pcon;
            $_label['url'] = url("/show/{$pid}");
            $_label['group']    = $pid;
            $_label['value']    = count($arr);
            array_push($this->labels,$_label);
            array_push($this->readyArr,$_row);

            $this->processArray( $id, $this->array,$level + 1);
        }
        return $this;
    }

    public function decode(){
        return json_encode(['nodes' => $this->getLabels(),'edges' => $this->getReadyArr()],JSON_UNESCAPED_UNICODE);
    }

    public function getLabels(){
        return $this->labels;
    }

    public function getReadyArr(){
        return $this->readyArr;
    }
}