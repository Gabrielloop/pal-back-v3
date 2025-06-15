<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{

    protected $primaryKey = 'isbn';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = ['user_id', 'isbn', 'reading_content'];

    public function book()
    {
        return $this->belongsTo(Book::class, 'isbn', 'isbn');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
