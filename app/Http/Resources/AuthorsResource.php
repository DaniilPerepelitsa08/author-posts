<?php

namespace App\Http\Resources;

use App\DTO\AuthorsResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AuthorsResponse
 */
class AuthorsResource extends JsonResource
{
    public function toArray(Request $request)
    {
        if ($this->totalAuthorsCount !== null) {
            $this->additional([
                "total_authors_count" => $this->totalAuthorsCount,
                "filtered_authors_count" => $this->filteredAuthorsCount
            ]);
        }

        return $this->data->toArray();
    }
}
