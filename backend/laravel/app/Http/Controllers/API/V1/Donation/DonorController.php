<?php

namespace App\Http\Controllers\API\V1\Donation;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Donation\Donor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DonorController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $donors = $this->paginate(
            Donor::where('company_id', $this->companyId($request))
                ->when($request->query('search'), fn ($q, $s) => $q->where('full_name', 'like', "%{$s}%"))
                ->latest(),
            $request
        );

        return response()->json($donors);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'devotee_id' => ['nullable', 'exists:devotees,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'donor_type' => ['nullable', 'in:individual,corporate,trust,nri'],
            'pan_card' => ['nullable', 'string', 'max:50'],
            'tax_exempt' => ['nullable', 'boolean'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['donor_id'] = 'DNR-'.Str::upper(Str::random(8));

        $donor = Donor::create($data);

        return response()->json($donor, 201);
    }

    public function show(Request $request, Donor $donor): JsonResponse
    {
        abort_unless($donor->company_id === $this->companyId($request), 403);

        return response()->json($donor->load('donations'));
    }

    public function update(Request $request, Donor $donor): JsonResponse
    {
        abort_unless($donor->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'full_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $donor->update($data);

        return response()->json($donor);
    }
}
