<?php

namespace App\Http\Controllers\API\V1\Devotee;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Devotee\Devotee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DevoteeController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $devotees = $this->paginate(
            Devotee::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->when($request->query('search'), function ($q, $search) {
                    $q->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");
                    });
                })
                ->with('devoteeType')
                ->latest(),
            $request
        );

        return response()->json($devotees);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'devotee_type_id' => ['nullable', 'exists:devotee_types,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'sanskrit_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date'],
            'gotra' => ['nullable', 'string', 'max:100'],
            'rashi' => ['nullable', 'string', 'max:50'],
            'nakshatra' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email'],
            'mobile' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'is_member' => ['nullable', 'boolean'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['devotee_id'] = 'DEV-'.Str::upper(Str::random(8));
        $data['created_by'] = $request->user()->id;

        $devotee = Devotee::create($data);

        return response()->json($devotee, 201);
    }

    public function show(Request $request, Devotee $devotee): JsonResponse
    {
        $this->authorizeDevotee($request, $devotee);

        return response()->json($devotee->load(['family', 'documents', 'preferences', 'devoteeType']));
    }

    public function update(Request $request, Devotee $devotee): JsonResponse
    {
        $this->authorizeDevotee($request, $devotee);

        $data = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email'],
            'mobile' => ['sometimes', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'is_member' => ['nullable', 'boolean'],
        ]);

        $devotee->update($data);

        return response()->json($devotee);
    }

    public function destroy(Request $request, Devotee $devotee): JsonResponse
    {
        $this->authorizeDevotee($request, $devotee);
        $devotee->delete();

        return response()->json(null, 204);
    }

    private function authorizeDevotee(Request $request, Devotee $devotee): void
    {
        abort_unless($devotee->company_id === $this->companyId($request), 403);
    }
}
