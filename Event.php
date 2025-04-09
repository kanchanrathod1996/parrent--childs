<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Event extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_name',
        'acc',
        'home_address',
        'email',
        'phone',
        'location',
        'gender',
        'age',
        'child_name'
    ];
    public function children()
    {
        return $this->hasMany(Children::class); 
    }

   
    
}