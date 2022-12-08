<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;



class Currencyexchange extends Model
{
    protected $table = 'tblcurrency_exchange';
    protected $primaryKey = 'exchange_id';
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
