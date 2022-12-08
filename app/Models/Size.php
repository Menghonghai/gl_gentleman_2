<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;



class Size extends Model
{
    protected $table = 'tblsizes';
    protected $primaryKey = 'size_id';
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
