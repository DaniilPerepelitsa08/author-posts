<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @method Builder byName(string $filtername)
 * @method Builder byGender(string $gender)
 */
class Author extends Model
{
    use HasFactory;
    protected $appends = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['gender', 'name', 'first_name', 'last_name'];

    /**
     * Get the posts associated with the author.
     *
     * @return HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Accessor for the author's full name.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function setNameAttribute(string $name): void
    {
        // TODO improve first/last name detection
        $this->first_name = trim(Str::before($name, " "));
        $this->last_name = trim(Str::after($name, " "));
    }

    public function scopeByName(Builder $query, $nameFilter): Builder
    {
        return $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$nameFilter}%"]);
    }

    public function scopeByGender(Builder $query, $filterValue): Builder
    {
        return $query->where('gender', $filterValue);
    }
}
