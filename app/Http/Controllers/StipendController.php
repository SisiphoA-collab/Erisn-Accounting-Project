<?php

namespace App\Http\Controllers;

use App\Models\Learner;
use App\Models\Stipend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class StipendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Stipend::with([
            'learner' => function ($query) {
                $query->select('id', 'name', 'email');
            }
        ])->select('id', 'amount', 'status', 'month', 'learner_id', 'receipt_path', 'updated_at');

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('learner', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $stipends = $query->orderBy('updated_at', 'desc')->paginate(10);

        // Calculate counts of unique learners with Paid and Pending stipends
        $paidCount = Stipend::where('status', 'Paid')->distinct('learner_id')->count('learner_id');
        $pendingCount = Stipend::where('status', 'Pending')->distinct('learner_id')->count('learner_id');

        // Special handling for 'Paid' status to include learner details and totals
        $paidLearners = [];
        $totalPaidAmount = 0;
        if ($request->filled('status') && $request->status === 'Paid') {
            $paidLearners = Stipend::where('status', 'Paid')
                ->select('learner_id')
                ->with('learner')
                ->groupBy('learner_id')
                ->get()
                ->map(function ($stipend) {
                    return [
                        'learner_id' => $stipend->learner_id,
                        'learner_name' => $stipend->learner->name,
                        'total_amount' => Stipend::where('learner_id', $stipend->learner_id)
                            ->where('status', 'Paid')
                            ->sum('amount'),
                    ];
                });

            $totalPaidAmount = Stipend::where('status', 'Paid')->sum('amount');
        }

        $learners = Learner::get(['name', 'id']);

        return response()->json([
            'stipends' => $stipends->items(),
            'links' => $stipends->links()->toHtml(),
            'learners' => $learners,
            'stats' => [
                'paid_learners' => $paidCount,
                'pending_learners' => $pendingCount,
            ],
            'paid_learners_data' => $paidLearners,
            'total_paid_amount' => $totalPaidAmount,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'learner_id' => 'required|exists:learners,id',
            'amount' => 'required|numeric',
            'status' => 'required|in:Draft,Pending,Paid',
            'month' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'type' => 'error'
            ], 422);
        }

        $stipend = Stipend::create($request->only(['learner_id', 'amount', 'status', 'month', 'receipt_path']));

        // Increase learner balance (assuming a 'balance' column exists in learners table)
        $learner = Learner::find($stipend->learner_id);
        if ($learner && $stipend->status === 'Paid') {
            $learner->balance = ($learner->balance ?? 0) + $stipend->amount;
            $learner->save();
        }

        return response()->json([
            'stipend' => $stipend,
            'message' => 'Stipend created successfully.',
            'type' => 'success',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $stipend = Stipend::with('learner')->findOrFail($id);
        return response()->json([
            'stipend' => $stipend,
            'message' => 'Stipend retrieved successfully.',
            'type' => 'success',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $stipend = Stipend::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'learner_id' => 'required|exists:learners,id',
            'amount' => 'required|numeric',
            'status' => 'required|in:Draft,Pending,Paid',
            'month' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'type' => 'error'
            ], 422);
        }

        $oldStatus = $stipend->status;
        $stipend->update($request->only(['learner_id', 'amount', 'status', 'month']));

        // Update learner balance if status changes to Paid
        $learner = Learner::find($stipend->learner_id);
        if ($learner && $stipend->status === 'Paid' && $oldStatus !== 'Paid') {
            $learner->balance = ($learner->balance ?? 0) + $stipend->amount;
            $learner->save();
        } elseif ($learner && $oldStatus === 'Paid' && $stipend->status !== 'Paid') {
            $learner->balance = max(0, ($learner->balance ?? 0) - $stipend->amount);
            $learner->save();
        }

        return response()->json([
            'stipend' => $stipend,
            'message' => 'Stipend updated successfully.',
            'type' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stipend = Stipend::findOrFail($id);

        // Adjust learner balance if stipend was Paid
        $learner = Learner::find($stipend->learner_id);
        if ($learner && $stipend->status === 'Paid') {
            $learner->balance = max(0, ($learner->balance ?? 0) - $stipend->amount);
            $learner->save();
        }

        $stipend->delete();
        return response()->json([
            'message' => 'Stipend deleted',
            'type' => 'success',
        ]);
    }

    /**
     * Upload a receipt for the specified stipend.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadReceipt(Request $request, $id)
    {
        $request->validate([
            'receipt' => 'required|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $stipend = Stipend::findOrFail($id);

        if ($request->hasFile('receipt')) {
            // Delete old receipt if it exists
            if ($stipend->receipt_path) {
                Storage::disk('public')->delete($stipend->receipt_path);
            }

            $originalName = $request->file('receipt')->getClientOriginalName();
            $filename = time() . '_' . $stipend->learner->name . '_' . $originalName;
            $filePath = $request->file('receipt')->storeAs('receipts', $filename, 'public');

            $stipend->receipt_path = $filePath;
            $stipend->status = 'Paid'; // Set status to Paid when receipt is uploaded
            $stipend->save();

            // Update learner balance
            $learner = Learner::find($stipend->learner_id);
            if ($learner) {
                $learner->balance = ($learner->balance ?? 0) + $stipend->amount;
                $learner->save();
            }
        }

        return response()->json([
            'message' => 'Receipt uploaded successfully.',
            'type' => 'success',
        ]);
    }

    /**
     * Import stipends from a CSV file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'type' => 'error'
            ], 422);
        }

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getPathname()));

            // Expected headers
            $expectedHeaders = ['learner_id', 'amount', 'status', 'month'];
            $header = array_shift($csvData); // Remove header row

            // Validate headers
            if ($header !== $expectedHeaders) {
                return response()->json([
                    'message' => 'Invalid CSV format. Expected headers: ' . implode(', ', $expectedHeaders),
                    'type' => 'error'
                ], 422);
            }

            // Process each row
            foreach ($csvData as $row) {
                if (count($row) !== count($expectedHeaders)) {
                    continue; // Skip invalid rows
                }

                // Validate learner_id exists
                $learner = Learner::find($row[0]);
                if (!$learner) {
                    continue; // Skip rows with invalid learner_id
                }

                // Validate status
                if (!in_array($row[2], ['Pending', 'Paid'])) {
                    continue; // Skip rows with invalid status
                }

                $stipend = Stipend::create([
                    'learner_id' => $row[0],
                    'amount' => $row[1],
                    'status' => $row[2],
                    'month' => $row[3],
                    'receipt_path' => null,
                ]);

                // Update learner balance if status is Paid
                if ($stipend->status === 'Paid') {
                    $learner->balance = ($learner->balance ?? 0) + $stipend->amount;
                    $learner->save();
                }
            }

            return response()->json([
                'message' => 'Stipends imported successfully.',
                'type' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error importing CSV: ' . $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
}