<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainToIgnore extends Model
{
    protected $guarded = ['id'];

    protected $table = 'domains_to_ignore';

    protected $fillable = ['id', 'domain'];

}
