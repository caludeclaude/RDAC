<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportNumberSequence extends Model
{
    const CREATED_AT = null;           // this table has updated_at only

    protected $primaryKey = 'year';    // the key is the year, not "id"
    public $incrementing = false;      // we choose the year ourselves

    protected $fillable = ['year', 'last_number'];
}