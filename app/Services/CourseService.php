<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 26.11.18
 * Time: 16:22
 */

namespace App\Services;


use App\Traits\Decode;
use Illuminate\Support\Facades\DB;

class CourseService
{
    use Decode;

    private $array;

    private $labels = [];

    private $readyArr= [];



    public function __construct()
    {
        $this->getRows();
    }

    public function getRows($root = "laravel")
    {
        $this->array = [];
        $this->array =  DB::connection('mysql2')
            ->select(
                "select t1.id,t2.id as pid,t1.view,t2.view as parentView,
                t1.shortCaptionEn,t1.captionEn,t2.shortCaptionEn as pShortCaptionEn,t2.captionEn as pCaptionEn  from views t1 
                inner join views t2 on t1.parentView = t2.view where t2.view = '$root'"
            );
        $parent = DB::connection('mysql2')
            ->select(
                "select id,captionEn,shortCaptionEn from views WHERE view = '$root'"
            );
        $this->parent($parent);

    }

    public function parent(array $data){
        $_label['id'] = $data[0]->id;
        if(!empty($data[0]->shortCaptionEn)){
            $_label['label'] = $data[0]->shortCaptionEn;
        }else{
            $_label['label'] = $data[0]->captionEn;
        }
        $_label['group']    = $data[0]->id;
        $_label['value']    = 10000;
        array_push($this->labels,$_label);
    }

    public function processArray($data = NULL,$level = 0){
        $data = !is_null($data) ? $data : $this->array;
        foreach ($data as $item){
            $id = (int) $item->id;
            $pid = $item->pid;
            $_row['from']  = $pid;
            $_row['to']    = $id;
            $_row['arrows']  = 'to';
            array_push($this->readyArr,$_row);
            $_label['show']    = $item->view;
            $_label['url'] = url("/course-show/{$item->view}");
            $_label['url2'] = 'http://semantic-portal.net/laravel';
            $_label['id'] = $id;
            if(!empty($item->shortCaptionEn)){
                $_label['label'] = $item->shortCaptionEn;
            }else{
                $_label['label'] = $item->captionEn;
            }
            $_label['group']    = $pid;
            $_label['show']    = $item->view;
            $_label['value']    = count($data);
            array_push($this->labels,$_label);
            unset($_label);
            $this->processArray($this->getRows($item->view),$level + 1);
        }
        return $this;
    }

    public function clear(){
        $this->readyArr = [];
        $this->labels = [];
        return $this;
    }


    public function processNodesWithoutChild(){
        $from = array_column($this->readyArr,'from');
        $to = array_column($this->readyArr,'to');
        $withoutChild = count($this->labels) == 1 ? array_column($this->labels,'id') : array_diff($to,$from);
        foreach ($withoutChild as $item){
            $view = DB::connection('mysql2')->select("select view from views where id = $item");
            $concepts = DB::connection('mysql2')
                ->select(
                    "
                            select  DISTINCT(thesis),concept_id from thesises t1 inner join concepts t2 on t1.concept_id = t2.id 
                            WHERE t1.view = '{$view[0]->view}' AND  thesis != \"\"
                        "
                );
            foreach ($concepts as $it){
                $_row['from']  = $item;
                $_row['to']    = $it->concept_id;
                $_row['arrows']  = 'to';
                array_push($this->readyArr,$_row);
                $_label['id'] = $it->concept_id;
                $_label['label'] = $it->thesis;
                $_label['group']    = $item;
                $_label['value']    = count($concepts);
                array_push($this->labels,$_label);
            }
        }
        return $this;
    }


}