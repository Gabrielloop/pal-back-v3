<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Userlist extends Model
{
    protected $primaryKey = 'userlist_id';

    protected $fillable = [
        'user_id',
        'userlist_name',
        'userlist_description',
        'userlist_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'userlist_book', 'userlist_id', 'isbn');
    }

}
