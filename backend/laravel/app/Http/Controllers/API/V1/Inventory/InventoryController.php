<?php

namespace App\Http\Controllers\API\V1\Inventory;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Inventory\InventoryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $items = $this->paginate(
            InventoryItem::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->when($request->query('category_id'), fn ($q, $v) => $q->where('category_id', $v))
                ->when($request->boolean('low_stock'), fn ($q) => $q->whereColumn('quantity', '<=', 'min_quantity'))
                ->with('category'),
            $request
        );

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'category_id' => ['required', 'exists:inventory_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:20'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'min_quantity' => ['nullable', 'integer', 'min:0'],
            'max_quantity' => ['nullable', 'integer', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['item_code'] = 'ITM-'.Str::upper(Str::random(8));
        $data['total_value'] = ($data['quantity'] ?? 0) * ($data['unit_price'] ?? 0);
        $data['created_by'] = $request->user()->id;

        $item = InventoryItem::create($data);

        return response()->json($item, 201);
    }

    public function show(Request $request, InventoryItem $item): JsonResponse
    {
        abort_unless($item->company_id === $this->companyId($request), 403);

        return response()->json($item->load('category', 'movements'));
    }

    public function update(Request $request, InventoryItem $item): JsonResponse
    {
        abort_unless($item->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'min_quantity' => ['nullable', 'integer', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:available,low_stock,expired,disposed'],
        ]);

        $item->update($data);

        return response()->json($item);
    }
}
