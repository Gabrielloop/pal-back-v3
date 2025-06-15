<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserlistBook extends Model
{
    protected $table = 'userlist_book';

    protected $fillable = ['userlist_id', 'isbn'];

    public $timestamps = true;

    public function userlist()
    {
        return $this->belongsTo(Userlist::class, 'userlist_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'isbn', 'isbn');
    }
}
