<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $primaryKey = 'isbn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'isbn',
        'user_id',
        'comment_content',
    ];

    public $timestamps = true;

    public function book()
    {
        return $this->belongsTo(Book::class, 'isbn', 'isbn');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
