<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowedIp extends Model
{

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'allowed_ips';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cname',
        'company',
        'ip',
    ];
}
