<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;



class News extends Model
{
    protected $table = 'tblnews_events';
    protected $primaryKey = 'news_id';
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
