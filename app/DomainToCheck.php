<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainToCheck extends Model
{
    protected $guarded = ['id'];

    protected $table = 'domains_to_check';

    protected $fillable = ['domain','status'];
}
