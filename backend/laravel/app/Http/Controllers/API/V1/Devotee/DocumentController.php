<?php

namespace App\Http\Controllers\API\V1\Devotee;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Devotee\Devotee;
use App\Models\Devotee\DevoteeDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends CompanyScopedController
{
    public function index(Request $request, Devotee $devotee): JsonResponse
    {
        abort_unless($devotee->company_id === $this->companyId($request), 403);

        return response()->json($devotee->documents);
    }

    public function store(Request $request, Devotee $devotee): JsonResponse
    {
        abort_unless($devotee->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'document_type' => ['required', 'string', 'max:50'],
            'document_name' => ['required', 'string', 'max:255'],
            'document_path' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'issue_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
        ]);

        $data['devotee_id'] = $devotee->id;
        $document = DevoteeDocument::create($data);

        return response()->json($document, 201);
    }

    public function destroy(Request $request, Devotee $devotee, DevoteeDocument $document): JsonResponse
    {
        abort_unless($devotee->company_id === $this->companyId($request), 403);
        abort_unless($document->devotee_id === $devotee->id, 404);
        $document->delete();

        return response()->json(null, 204);
    }
}
