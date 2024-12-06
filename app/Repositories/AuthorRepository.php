<?php

namespace App\Repositories;

use App\Enums\PostStatusesEnum;
use App\Models\Author;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuthorRepository
{
    public function getAuthorById(int $id): Author
    {
        return Author::find($id);
    }

    /**
     * Get a query builder for authors with their public posts.
     *
     * This method retrieves a query for authors, including their posts that are marked as public.
     *
     * @return Builder The query builder instance with the necessary relationships and filters applied.
     */
    public function getAllAuthorsPostsQuery(): Builder
    {
        return Author::with(['posts' => function ($query) {
            $query->where('is_private', PostStatusesEnum::PUBLIC->value);
        }]);
    }

    /**
     * Filter the query by a specific column and value.
     *
     * This method applies a filter to the query using a "like" operator on the specified column.
     *
     * @param Builder $query The query builder instance.
     * @param string $filterColumn The name of the column to filter.
     * @param string $filterValue The value to filter the column by.
     *
     * @return Builder The query builder instance with the column filter applied.
     */
    public function filterQueryByColumn(Builder $query, string $filterColumn, string $filterValue): Builder
    {
        return $query->where($filterColumn, 'like', "%$filterValue%");
    }

    /**
     * Sort the query by a specific column and direction.
     *
     * This method applies sorting to the query based on the specified column and direction.
     *
     * @param Builder $query The query builder instance.
     * @param string|null $sortColumn The column to sort by.
     * @param string $sortDirection The direction of sorting, either 'asc' or 'desc'.
     */
    public function sortQueryByColumn(Builder $query, ?string $sortColumn, string $sortDirection = 'asc'): void
    {
        if (!empty($sortColumn)) {
            $sortColumn = match ($sortColumn) {
                'name' => DB::raw("CONCAT(first_name, ' ', last_name)"),
                default => $sortColumn,
            };

            $query->orderBy($sortColumn, $sortDirection);
        }
    }

    /**
     * Validate and retrieve the columns for the query.
     *
     * @param Collection $columns The requested columns.
     */
    public function selectColumns(Builder $query, Collection $columns): void
    {
        if ($columns->isEmpty()) {
            $validColumns = collect(['*']);
        } else {
            // will silently skip all not existing columns
            $validColumns = $columns->intersect($this->getExistingColumns());

            if ($columns->contains('name')) {
                $validColumns = $validColumns
                    ->filter(fn ($val) => $val !== 'name')
                    ->push(
                        DB::raw("CONCAT(first_name, ' ', last_name) AS name"),
                        'first_name',
                        'last_name'
                    );
            }

            if (!$validColumns->contains('id')) {
                $validColumns->push('id');
            }
        }

        $query->select($validColumns->toArray());
    }

    /**
     * Retrieve the existing columns in the authors table.
     *
     * @return Collection The collection of column names.
     */
    public function getExistingColumns(): Collection
    {
        //possibly replace with hardcoded columns names?
        return Cache::remember(
            'author_columns',
            CarbonInterval::minutes(10),
            function () {
                $table = (new Author())->getTable();
                return collect(Schema::getColumnListing($table));
            }
        );
    }
}
