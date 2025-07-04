<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_dark_mode',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_dark_mode' => 'boolean',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function userlists()
    {
        return $this->hasMany(Userlist::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function favoriteBooks()
    {
        return $this->belongsToMany(Book::class, 'favorites', 'user_id', 'isbn', 'id', 'isbn');
    }

    public function notedBooks()
    {
        return $this->belongsToMany(Book::class, 'notes', 'user_id', 'isbn', 'id', 'isbn');
    }

    public function wishedBooks()
    {
        return $this->belongsToMany(Book::class, 'wishlists', 'user_id', 'isbn', 'id', 'isbn');
    }

    public function listedBooks()
    {
        return $this->hasManyThrough(Book::class, UserlistBook::class, 'userlist_id', 'isbn', 'id', 'isbn');
    }
}
