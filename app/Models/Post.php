<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['author_id', 'title', 'content', 'published_at', 'is_private', 'rating'];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
