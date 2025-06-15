<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'isbn',
        'reading_content',
        'is_started',
        'is_reading',
        'is_finished',
        'is_abandoned',
    ];

    protected $casts = [
        'is_started' => 'boolean',
        'is_finished' => 'boolean',
        'is_abandoned' => 'boolean',
        'is_reading' => 'boolean',
    ];

    public function book()
        { 
        return $this->belongsTo(Book::class, 'isbn', 'isbn');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
