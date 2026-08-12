<?php

namespace App\Http\Controllers\API\V1\Inventory;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockMovementController extends CompanyScopedController
{
    public function index(Request $request, InventoryItem $item): JsonResponse
    {
        abort_unless($item->company_id === $this->companyId($request), 403);

        return response()->json($item->movements()->latest('movement_date')->get());
    }

    public function store(Request $request, InventoryItem $item): JsonResponse
    {
        abort_unless($item->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'movement_type' => ['required', 'in:purchase,issue,return,transfer,dispose'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'from_location' => ['nullable', 'string', 'max:255'],
            'to_location' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        $movement = DB::transaction(function () use ($data, $item, $request) {
            $unitPrice = $data['unit_price'] ?? $item->unit_price;

            $movement = StockMovement::create([
                'company_id' => $item->company_id,
                'temple_id' => $item->temple_id,
                'item_id' => $item->id,
                'movement_id' => 'MOV-'.Str::upper(Str::random(8)),
                'movement_type' => $data['movement_type'],
                'quantity' => $data['quantity'],
                'unit_price' => $unitPrice,
                'total_amount' => $data['quantity'] * $unitPrice,
                'movement_date' => now()->toDateString(),
                'from_location' => $data['from_location'] ?? null,
                'to_location' => $data['to_location'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'created_by' => $request->user()->id,
            ]);

            $delta = in_array($data['movement_type'], ['purchase', 'return'], true) ? $data['quantity'] : -$data['quantity'];
            $item->quantity = max(0, $item->quantity + $delta);
            $item->total_value = $item->quantity * $item->unit_price;
            if ($item->quantity <= $item->min_quantity) {
                $item->status = 'low_stock';
            }
            $item->save();

            return $movement;
        });

        return response()->json($movement, 201);
    }
}
