<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 22.10.18
 * Time: 8:35
 */

namespace App;


use Orm\Model;

class Concept extends Model
{
    protected $table = 'Concept';

    public function getCinC(){
        return $this->hasMany(Cinc::class,'source_id');
    }
}