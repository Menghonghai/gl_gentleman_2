<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;



class Staff extends Model
{
    protected $table = 'tblstaffs';
    protected $primaryKey = 'staff_id';
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
