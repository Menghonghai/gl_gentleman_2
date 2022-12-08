<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;



class Products extends Model
{
    protected $table = 'tbl_product';
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
