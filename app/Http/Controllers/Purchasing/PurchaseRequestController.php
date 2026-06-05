<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Purchasing\PurchaseRequest;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $purchaseRequests = PurchaseRequest::with('requestedBy')->paginate(10);
        return view('purchasing.requests.index', compact('purchaseRequests'));
    }

    public function create()
    {
        return view('purchasing.requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'branch_id' => 'required|exists:branches,id',
            'request_number' => 'required|string|max:50',
            'request_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $validated['requested_by'] = auth()->id();
        $validated['status'] = 'draft';

        PurchaseRequest::create($validated);

        return redirect()->route('purchasing.requests.index')
            ->with('success', 'Purchase request created successfully.');
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load('items', 'requestedBy');
        return view('purchasing.requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        return view('purchasing.requests.edit', compact('purchaseRequest'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'request_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $purchaseRequest->update($validated);

        return redirect()->route('purchasing.requests.index')
            ->with('success', 'Purchase request updated successfully.');
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->delete();
        return redirect()->route('purchasing.requests.index')
            ->with('success', 'Purchase request deleted successfully.');
    }

    public function submit(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->update(['status' => 'submitted']);
        return redirect()->route('purchasing.requests.index')
            ->with('success', 'Purchase request submitted.');
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);
        return redirect()->route('purchasing.requests.index')
            ->with('success', 'Purchase request approved.');
    }

    public function reject(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->update(['status' => 'rejected']);
        return redirect()->route('purchasing.requests.index')
            ->with('success', 'Purchase request rejected.');
    }
}
