<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorRequest;
use App\Http\Requests\GetAuthorsPostsRequest;
use App\Http\Resources\AuthorsResource;
use App\Models\Author;
use App\Repositories\AuthorRepository;
use App\Services\AuthorService;
use App\Services\PostService;
use Exception;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    public function __construct(
        protected PostService $postService,
        protected AuthorService $authorService,
        protected AuthorRepository $authorRepository)
    {
    }

    /**
     * Handle the request to retrieve authors and their posts.
     *
     * @param GetAuthorsPostsRequest $request The request object containing validated input parameters.
     *
     * @return AuthorsResource The JSON response with authors and their posts data.
     *
     * ### Parameters:
     * - **columns**: array|null
     *   An optional array specifying the list of requested columns. Each element must be a string.
     *
     * - **columns.\***: string
     *   Each column name in the array must be a string and is required if `columns` is provided.
     *
     * - **offset**: int|null
     *   An optional integer specifying the pagination offset. Must be greater than or equal to 0. Defaults to 0.
     *
     * - **limit**: int|null
     *   An optional integer specifying the number of records to retrieve. Must be between 1 and 50. Defaults to null.
     *
     * - **sort_column**: string|null
     *   An optional string specifying the column to sort the data by. Defaults to null.
     *
     * - **sort_direction**: string
     *   Direction of sorting, must be one of 'asc' or 'desc'. Defaults to 'asc'.
     *
     * - **filter_column**: string|null
     *   An optional string specifying the column to apply filtering on. Defaults to null.
     *
     * - **filter_value**: string|null
     *   An optional string specifying the value to filter by. Defaults to null.
     *
     * - **include_totals**: bool
     *   A boolean indicating whether to include totals in the response. Defaults to `false`.
     *
     * ### Returns:
     * A JSON response containing the requested authors and posts data, formatted according to the specified input.
     * @throws Exception
     */
    public function getAuthorsPosts(GetAuthorsPostsRequest $request): AuthorsResource
    {
        $columns = $request->input('columns', []);
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit');
        $sortColumn = $request->input('sort_column', 'first_name');
        $sortDirection = $request->input('sort_direction', 'asc');
        $filterColumn = $request->input('filter_column');
        $filterValue = $request->input('filter_value');
        $includeTotals = $request->input('include_totals', false);

        $data = $this->authorService->getAllAuthorsPosts(
            collect($columns),
            $offset,
            $limit,
            $sortColumn,
            $sortDirection,
            $filterColumn,
            $filterValue,
            $includeTotals
        );

        return AuthorsResource::make($data);
    }

    /**
     * @param AuthorRequest $request The request object containing validated input parameters.
     * @return JsonResponse
     */
    public function getAuthor(AuthorRequest $request): JsonResponse
    {
        $author = $this->authorRepository->getAuthorById($request->input('id'));

        return response()->json($author);
    }
}
