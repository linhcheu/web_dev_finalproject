<?php

namespace App\Services\Api;

use Illuminate\Pagination\LengthAwarePaginator;

/** A paginator that also carries the extra keys the API sent (filters, totals...). */
class ApiPaginator extends LengthAwarePaginator
{
    public array $extra = [];
}
