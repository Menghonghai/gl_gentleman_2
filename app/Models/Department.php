<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;



class Department extends Model
{
    protected $table = 'tbl_department';
    protected $primaryKey = 'department_id';
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
