<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    public $timestamps = true;

    protected $fillable = ['user_id', 'isbn'];

    public function book()
    {
        return $this->belongsTo(Book::class, 'isbn', 'isbn');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
