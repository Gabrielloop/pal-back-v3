<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
     protected $primaryKey = 'isbn';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'isbn',
        'reading_content',
        'is_started',
        'is_reading',
        'is_finished',
        'is_abandoned',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'is_started' => 'boolean',
        'is_finished' => 'boolean',
        'is_abandoned' => 'boolean',
        'is_reading' => 'boolean',
        'started_at' => 'datetime:Y-m-d',
        'finished_at' => 'datetime:Y-m-d',
    ];

    protected $dates = ['started_at', 'finished_at'];

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    public function book()
        { 
        return $this->belongsTo(Book::class, 'isbn', 'isbn');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
