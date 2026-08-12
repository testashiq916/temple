<?php

namespace App\Http\Controllers\API\V1\Crowd;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Crowd\DarshanQueue;
use App\Models\Crowd\QueueEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QueueController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $queues = DarshanQueue::where('company_id', $this->companyId($request))
            ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
            ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
            ->latest('start_time')
            ->get();

        return response()->json($queues);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'queue_type' => ['nullable', 'in:general,special,vip,divyang'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['queue_id'] = 'QUEUE-'.Str::upper(Str::random(8));
        $data['start_time'] = now();
        $data['status'] = 'active';

        $queue = DarshanQueue::create($data);

        return response()->json($queue, 201);
    }

    public function show(Request $request, DarshanQueue $queue): JsonResponse
    {
        abort_unless($queue->company_id === $this->companyId($request), 403);

        return response()->json($queue->load('entries'));
    }

    public function addEntry(Request $request, DarshanQueue $queue): JsonResponse
    {
        abort_unless($queue->company_id === $this->companyId($request), 403);
        abort_unless($queue->status === 'active', 422, 'Queue is not active');

        $data = $request->validate(['devotee_id' => ['nullable', 'exists:devotees,id']]);

        $entry = QueueEntry::create([
            'queue_id' => $queue->id,
            'devotee_id' => $data['devotee_id'] ?? null,
            'queue_number' => (string) ($queue->total_devotees + 1),
            'entry_time' => now(),
            'status' => 'waiting',
            'qr_code' => 'QR-'.Str::upper(Str::random(10)),
        ]);

        $queue->increment('total_devotees');

        return response()->json($entry, 201);
    }

    public function completeEntry(Request $request, DarshanQueue $queue, QueueEntry $entry): JsonResponse
    {
        abort_unless($queue->company_id === $this->companyId($request), 403);
        abort_unless($entry->queue_id === $queue->id, 404);

        $entry->update([
            'status' => 'completed',
            'exit_time' => now(),
            'darshan_duration' => now()->diffInMinutes($entry->entry_time),
        ]);

        $queue->increment('current_position');

        return response()->json($entry);
    }

    public function close(Request $request, DarshanQueue $queue): JsonResponse
    {
        abort_unless($queue->company_id === $this->companyId($request), 403);
        $queue->update(['status' => 'closed']);

        return response()->json($queue);
    }
}
