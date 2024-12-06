# Author & Posts API

This Laravel-based project provides an API for managing authors and their posts. It supports advanced filtering, sorting, pagination, and statistical calculations, making it easy to retrieve and display detailed author-related data.

## Features

- Manage **Authors** and their **Posts**.
- Flexible API for retrieving author data with the following capabilities:
    - Filter authors by gender, name, or any other column.
    - Sort results by any column, in ascending or descending order.
    - Paginate results using offset and limit.
- Advanced data calculations:
    - Total count of posts by an author.
    - Posts published within the last month.
    - Average ratings of posts (all-time and last month).
    - Details of the latest published post.
- Extendable and modular backend architecture.

## Requirements

- **PHP**: >= 8.2
- **Laravel**: >= 11
- **Database**: MySQL, PostgreSQL, or SQLite
- **Composer**

## Installation

Clone the repository:

1. ```git clone <repository-url>```
   
2. ```cd <repository-folder>```

3. ```composer install```

4. ```php artisan key:generate```

5. ```php artisan migrate```

6. ```php artisan db:seed```

7. ```php artisan serve```


API Endpoints
GET /api/get-authors-posts
GET /api/author/{id}
Retrieve a list of authors with optional filters, sorting, offset and limit.

Request Parameters:

| Parameter        | Type      | Description                                                              |
|------------------|-----------|--------------------------------------------------------------------------|
| `columns`        | `array`   | List of columns to include in the response.                              |
| `offset`         | `integer` | Number of records to skip (default: 0).                                  |
| `limit`          | `integer` | Number of records to return (default: 10, max: 50).                      |
| `sort_column`    | `string`  | Column to sort by (default: `first_name`).                               |
| `sort_direction` | `string`  | Sorting direction: `asc` or `desc` (default: `asc`).                     |
| `filter_column`  | `string`  | Column to filter by (e.g., `gender`).                                    |
| `filter_value`   | `string`  | Value to filter by.                                                      |
| `include_totals` | `boolean` | Whether to include total counts in the response.                         |


### Example Response

The following is an example of a response returned by the API when requesting the authors list:

```json
{
  "data": [
    {
      "name": "John Doe",
      "gender": "male",
      "total_posts_count": 15,
      "last_month_posts_count": 3,
      "average_rating": 8.2,
      "average_rating_last_month": 9.0,
      "latest_post": {
        "title": "Understanding APIs",
        "content": "APIs are a way for..."
      }
    }
  ],
  "total_authors_count": 100,
  "filtered_authors_count": 50
}
```

## Project Structure

### Key Directories
- `app/Models`: Contains the Author and Post models with relationships.
- `app/Services`: The `AuthorService`, which encapsulates business logic.
- `app/Repositories`: Manages database query logic, separating it from the service layer.
- `app/Resources`: Response Formatting.
- `app/Enums`: - **PostStatusesEnum**: This enum defines the statuses for posts. It includes two statuses:
    - `PRIVATE` (1): The post is private.
    - `PUBLIC` (0): The post is public.

  The enum provides several methods to interact with the status values:
    - `names()`: Returns an array of all case names in the enum.
    - `values()`: Returns an array of all case values in the enum.
    - `array()`: Returns an associative array where the keys are the case values and the values are the case names.
- `routes/api.php`: Defines API routes for the application.

### Key Classes
- `AuthorService`: Implements filtering, sorting, and pagination logic for the API.
- `AuthorRepository`: Contains reusable database query methods.
- `Author`: The Eloquent model representing authors.
- `Post`: The Eloquent model representing posts.

### Extensibility
- Easily add new columns or calculations in the `formAuthorsDataForResponse` method of `AuthorService`.
- Add new filters or sorting options by extending the query methods in `AuthorRepository`.
