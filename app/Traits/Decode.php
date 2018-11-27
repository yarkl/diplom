<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 26.11.18
 * Time: 17:21
 */

namespace App\Traits;


trait Decode
{
    public function decode(){
        return json_encode(
            [
                'nodes' => $this->getLabels(),
                'edges' => $this->getReadyArr()
            ],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function getLabels(){
        return $this->labels;
    }


    public function getReadyArr(){
        return $this->readyArr;
    }
}