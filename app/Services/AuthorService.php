<?php

namespace App\Services;

use App\DTO\AuthorsResponse;
use App\Models\Author;
use App\Repositories\AuthorRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class AuthorService
 *
 * A service class for managing authors and their posts, including filtering, sorting,
 * and formatting data for JSON responses.
 */
class AuthorService
{
    /**
     * Constructor.
     *
     * @param AuthorRepository $authorRepository The repository for handling author-related queries.
     */
    public function __construct(protected AuthorRepository $authorRepository)
    {
    }

    /**
     * Retrieve all authors and their posts with applied filters, sorting, and pagination.
     *
     * @param Collection $columns The requested columns for the query.
     * @param int|null $offset The offset for pagination.
     * @param int|null $limit The limit for pagination.
     * @param string|null $sortColumn The column to sort by.
     * @param string $sortDirection The sorting direction ('asc' or 'desc').
     * @param string|null $filterColumn The column to apply filtering on.
     * @param string|null $filterValue The value to filter the specified column by.
     * @param bool $includeTotals Whether to include total counts in the response.
     *
     * @return AuthorsResponse authors and their posts data.
     * @throws Exception
     */
    public function getAllAuthorsPosts(Collection $columns,
                                       ?int       $offset,
                                       ?int       $limit,
                                       ?string    $sortColumn,
                                       string     $sortDirection,
                                       ?string    $filterColumn,
                                       ?string    $filterValue,
                                       bool       $includeTotals): AuthorsResponse
    {
        $query = $this->authorRepository->getAllAuthorsPostsQuery();

        $this->authorRepository->sortQueryByColumn($query, $sortColumn, $sortDirection);

        $this->filterAuthorsPostsQuery($query, $filterColumn, $filterValue);

        $this->authorRepository->selectColumns($query, $columns);

        return $this->formAuthorsResponse($query, $includeTotals, $offset, $limit, $columns);
    }

    /**
     * Paginate a collection based on offset and limit.
     *
     * @param Builder $query The collection of data.
     * @param int $offset The offset for pagination.
     * @param int $limit The number of items to retrieve.
     */
    public function getOffsetData(Builder $query, int $offset, int $limit): void
    {
        $query->skip($offset)->take($limit);
    }

    /**
     * Apply filters to the authors' posts query based on column and name.
     *
     * @param Builder $query The query builder instance.
     * @param string|null $filterColumn The column to apply filtering on.
     * @param string|null $filterValue The value to filter the column by.
     * @throws Exception
     */
    public function filterAuthorsPostsQuery(Builder $query,
                                            ?string $filterColumn,
                                            ?string $filterValue)
    {
        if (empty($filterColumn)) {
            return $query;
        } else {
            if (!$this->authorRepository->getExistingColumns()->contains($filterColumn) && $filterColumn !== 'name') {
                throw new Exception('Filter column "' . $filterColumn . '" not found');
            }

            match ($filterColumn) {
                'gender' => $query->byGender($filterValue),
                'name' => $query->byName($filterValue),
                default => $this->authorRepository->filterQueryByColumn($query, $filterColumn, $filterValue)
            };
        }
    }

    /**
     * Generate the full name of an author.
     *
     * @param Author $author The author model instance.
     *
     * @return string The full name of the author.
     */
    public function getAuthorFullName(Author $author): string
    {
        return $author->first_name . ' ' . $author->last_name;
    }

    /**
     * Prepare authors' data for the JSON response.
     *
     * @param Collection $data The authors' data.
     * @param Collection $initialColumns The initially requested columns.
     *
     * @return Collection The transformed data for the response.
     */
    public function formAuthorsDataForResponse(Collection $data, Collection $initialColumns): Collection
    {
        return $data->map(function ($author) use ($initialColumns) {
            $author->name ??= $this->getAuthorFullName($author);
            $author->total_posts_count = $this->getAuthorsPostsCount($author->posts);
            $author->last_month_posts_count = $this->getAuthorsPostsCount($author->posts, true);
            $author->latest_post = null;
            $author->average_rating = null;
            $author->average_rating_last_month = null;

            if ($author->posts->count() > 0) {
                $author->latest_post = [
                    'title' => $author->posts->sortByDesc('published_at')->first()->title,
                    'content' => Str::limit($author->posts->sortByDesc('published_at')->first()->content, 100),
                ];
                $author->average_rating = $author->posts->avg('rating');
                $author->average_rating_last_month = $this->getLastMonthPosts($author->posts)->avg('rating');
            }

            if (!$initialColumns->contains('first_name')) {
                $author->makeHidden('first_name');
            }

            if (!$initialColumns->contains('last_name')) {
                $author->makeHidden('last_name');
            }

            $author->makeHidden('posts');

            return $author;
        });
    }

    /**
     * Format the final response for authors' data.
     *
     * @param Builder $query The authors' data.
     * @param bool $includeTotals Whether to include total counts.
     * @param int $offset The offset for pagination.
     * @param int $limit The limit for pagination.
     * @param Collection $initialColumns The initially requested columns.
     *
     * @return AuthorsResponse response.
     */
    public function formAuthorsResponse(Builder    $query,
                                        bool       $includeTotals,
                                        int        $offset,
                                        int        $limit,
                                        Collection $initialColumns): AuthorsResponse
    {
        if ($includeTotals) {
            $totalAuthorsCount = Author::count();
            $filteredAuthorsCount = $query->count();
        }

        $this->getOffsetData($query, $offset, $limit);

        return new AuthorsResponse(
            $this->formAuthorsDataForResponse($query->get(), $initialColumns),
            $totalAuthorsCount ?? null,
            $filteredAuthorsCount ?? null,
        );
    }

    /**
     * Count the number of posts for an author, optionally filtering for the last month.
     *
     * @param Collection $posts The collection of posts.
     * @param bool $postsForLastMonth Whether to filter for posts in the last month.
     *
     * @return int The count of posts.
     */
    public function getAuthorsPostsCount(Collection $posts, bool $postsForLastMonth = false): int
    {
        if ($postsForLastMonth) {
            return $this->getLastMonthPosts($posts)->count();
        }

        return $posts->count();
    }

    /**
     * Retrieve posts published in the last month.
     *
     * @param Collection $posts The collection of posts.
     *
     * @return Collection The filtered posts from the last month.
     */
    public function getLastMonthPosts(Collection $posts): Collection
    {
        return $posts->filter(function ($post) {
            return $post->published_at !== null
                && Carbon::parse($post->published_at)->toDateString() >= Carbon::now()->subMonth()->toDateString();
        });
    }
}
