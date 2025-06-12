<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $primaryKey = 'isbn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'isbn',
        'book_title',
        'book_author',
        'book_publisher',
        'book_year',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class, 'isbn', 'isbn');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'isbn', 'isbn');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'isbn', 'isbn');
    }

    public function wishedBy()
    {
        return $this->hasMany(Whishlist::class, 'isbn', 'isbn');
    }

    public function userlists()
    {
        return $this->belongsToMany(Userlist::class, 'userlist_book', 'isbn', 'userlist_id');
    }
}
