<?php

namespace App\Http\Controllers;

use App\Models\Learner;
use App\Models\Stipend;
use Illuminate\Http\Request;

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
                $query->select('id', 'name','email');
            }
        ])->select('id', 'amount', 'status', 'month', 'learner_id','receipt_path','updated_at');

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('learner', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        $stipend = $query->orderBy('updated_at', 'desc')->paginate(10);
        $learner = Learner::get(['name', 'id']);
        return response()->json(['stipends' => $stipend, 'learners' => $learner]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $stipend = Stipend::create($request->all());

        //increase learner balance
        $learner = Learner::Find($stipend->learner_id);
        $learner->save();

        return response()->json([$stipend,'message'=>'stipend created successfully.']);
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
        return response()->json($stipend);
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
        $stipend->update($request->all());
        return response()->json([$stipend, 'message'=>'stipend updated successfully.','type'=>'success']);
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
        $stipend->delete();
        return response()->json(['message' => 'stipend deleted', 'type'=>'message']);
    }

    public function uploadReceipt(Request $request, $id)
    {
        $request->validate([
            'receipt' => 'required|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $stipend = Stipend::findOrFail($id);

        if ($request->hasFile('receipt')) {
            $originalName = $request->file('receipt')->getClientOriginalName();
            $filename = time() . '_' . $stipend->learner->name .'_'. $originalName;
            $filePath = $request->file('receipt')->storeAs('receipts', $filename, 'public');

            $stipend->receipt_path = $filePath;
            $stipend->status = 'Paid'; // Fixed update syntax
            $stipend->save();
        }

        return response()->json(['message'=> 'Receipt uploaded successfully.','type'=>'success']);
    }
}
