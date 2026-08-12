<?php

namespace App\Http\Controllers\API\V1\Seva;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Seva\SevaService;
use App\Models\Seva\SevaSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlotController extends CompanyScopedController
{
    public function index(Request $request, SevaService $seva): JsonResponse
    {
        abort_unless($seva->company_id === $this->companyId($request), 403);

        $slots = $seva->slots()
            ->when($request->query('date'), fn ($q, $date) => $q->where('slot_date', $date))
            ->where('is_available', true)
            ->orderBy('slot_date')
            ->orderBy('start_time')
            ->get();

        return response()->json($slots);
    }

    public function store(Request $request, SevaService $seva): JsonResponse
    {
        abort_unless($seva->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'slot_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ]);

        $data['seva_id'] = $seva->id;
        $slot = SevaSlot::create($data);

        return response()->json($slot, 201);
    }

    public function destroy(Request $request, SevaService $seva, SevaSlot $slot): JsonResponse
    {
        abort_unless($seva->company_id === $this->companyId($request), 403);
        abort_unless($slot->seva_id === $seva->id, 404);
        abort_if($slot->booked_count > 0, 422, 'Cannot delete a slot with existing bookings');
        $slot->delete();

        return response()->json(null, 204);
    }
}
