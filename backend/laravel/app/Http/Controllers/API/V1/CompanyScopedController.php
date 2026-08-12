<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Shared helper for tenant-scoped resource controllers: every temple ERP
 * resource belongs to a company (tenant), so reads and writes are always
 * filtered/stamped by the authenticated user's company_id.
 */
abstract class CompanyScopedController extends Controller
{
    protected function companyId(Request $request): int
    {
        return $request->user()->company_id;
    }

    protected function paginate(\Illuminate\Database\Eloquent\Builder $query, Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $query->paginate((int) $request->integer('per_page', 20));
    }
}
