<?php

namespace App\DTO;

use Illuminate\Support\Collection;

class AuthorsResponse
{
    public function __construct(
        public readonly Collection $data,
        public readonly ?int $totalAuthorsCount,
        public readonly ?int $filteredAuthorsCount,
    )
    {
    }
}
