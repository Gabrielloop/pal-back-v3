<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Note;
use App\Models\Favorite;
use App\Models\Wishlist;
use App\Models\Userlist;
use App\Models\User;

class Book extends Model
{
    protected $primaryKey = 'isbn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $appends = [
        'is_favorite',
        'is_wished',
        'content_comment',
        'content_note',
        'content_note_total',
        'userlists',
    ];

    protected $fillable = [
        'isbn',
        'book_title',
        'book_author',
        'book_publisher',
        'book_year',
    ];

    // Accessors

    public function getIsWishedAttribute()
    {
        $user = Auth::user();
        if (!$user) return false;

        return $this->wishlists()->where('user_id', $user->id)->exists();
    }

    public function getUserlistsAttribute()
    {
        $user = Auth::user();
        if (!$user) return [];

        return $this->userlists()
            ->where('user_id', $user->id)
            ->get(['userlists.userlist_id', 'userlist_name', 'userlist_description', 'userlist_type']);
    }

    public function getIsFavoriteAttribute()
    {
        $user = Auth::user();
        if (!$user) return false;

        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function getContentCommentAttribute()
    {
        $user = Auth::user();
        if (!$user) return null;

        return $this->comments()->where('user_id', $user->id)->value('comment_content');
    }

    public function getContentNoteAttribute()
    {
        $user = Auth::user();
        if (!$user) return null;

        return $this->notes()->where('user_id', $user->id)->value('note_content');
    }

    public function getContentNoteTotalAttribute()
    {
        return $this->notes()->avg('note_content');
    }

    // Relations

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

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'isbn', 'isbn');
    }

    public function userlists()
    {
        return $this->belongsToMany(Userlist::class, 'userlist_book', 'isbn', 'userlist_id', 'isbn', 'userlist_id');
    }
}
