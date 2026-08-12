<?php

namespace App\Http\Controllers\API\V1\Staff;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Staff\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $staff = $this->paginate(
            Staff::where('company_id', $this->companyId($request))
                ->when($request->query('temple_id'), fn ($q, $v) => $q->where('temple_id', $v))
                ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
                ->with('position'),
            $request
        );

        return response()->json($staff);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'position_id' => ['required', 'exists:staff_positions,id'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'mobile' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'employee_type' => ['nullable', 'in:permanent,contract,temporary,volunteer'],
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'joining_date' => ['nullable', 'date'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['staff_id'] = 'STF-'.Str::upper(Str::random(8));
        $data['created_by'] = $request->user()->id;

        $staff = Staff::create($data);

        return response()->json($staff, 201);
    }

    public function show(Request $request, Staff $staffMember): JsonResponse
    {
        abort_unless($staffMember->company_id === $this->companyId($request), 403);

        return response()->json($staffMember->load('position'));
    }

    public function update(Request $request, Staff $staffMember): JsonResponse
    {
        abort_unless($staffMember->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'status' => ['sometimes', 'in:active,inactive,on_leave,resigned,terminated'],
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
        ]);

        $staffMember->update($data);

        return response()->json($staffMember);
    }
}
