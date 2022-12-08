<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;



class Mainproducts extends Model
{
    protected $table = 'tbl_main_product';
    protected $primaryKey = 'id';
    public $timestamps = false;
    /*protected $fillable = array(
        'name',
        'artist',
        'price'
    );*/

    public function scopeGettable()
    {
        return $this->table;
    }
}
