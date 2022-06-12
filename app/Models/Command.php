<?php

namespace App\Models;

use App\Traits\ {
	UUids
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Command extends Model
{

    use HasFactory, UUids;

    protected $fillable = [
    	'command',
    	'description',
    	'script_type',
    	'command_type',
    	'environtment'
    ];

}
