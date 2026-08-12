<?php

namespace App\Http\Controllers\API\V1\Staff;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Staff\Volunteer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VolunteerController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $volunteers = $this->paginate(
            Volunteer::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v)),
            $request
        );

        return response()->json($volunteers);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'devotee_id' => ['required', 'exists:devotees,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'mobile' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'skills' => ['nullable', 'array'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['volunteer_id'] = 'VOL-'.Str::upper(Str::random(8));
        $data['join_date'] = now()->toDateString();
        $data['created_by'] = $request->user()->id;

        $volunteer = Volunteer::create($data);

        return response()->json($volunteer, 201);
    }

    public function update(Request $request, Volunteer $volunteer): JsonResponse
    {
        abort_unless($volunteer->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'status' => ['sometimes', 'in:active,inactive,suspended'],
            'total_hours' => ['nullable', 'integer', 'min:0'],
        ]);

        $volunteer->update($data);

        return response()->json($volunteer);
    }
}
