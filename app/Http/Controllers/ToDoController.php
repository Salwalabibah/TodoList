<?php

namespace App\Http\Controllers;

use App\Exports\ToDoExport;
use App\Models\ToDo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ToDoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $to_do = ToDo::all();
        return response()->json($to_do);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|max:255',
            'assignee' => 'nullable',
            'due_date' => 'required|date|after_or_equal:today',
            'time_tracked' => 'nullable',
            'status' => 'nullable',
            'priority' => 'required'
        ]);

        $to_do = ToDo::create([
            'title' => $validate['title'],
            'assignee' => $validate['assignee'],
            'due_date' => $validate['due_date'],
            'time_tracked' => $validate['time_tracked'],
            'status' => $validate['status'],
            'priority' => $validate['priority'],
        ]);

        return response()->json([
            'message' => 'To Do added successfully',
            'data' => $to_do
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function export(Request $request)
    {
        $filters = [
            'title' => $request->has('title') ? $request->title : '',
            'assignee' => $request->has('assignee') ? explode(',', $request->assignee) : [],
            'status' => $request->has('status') ? explode(',', $request->status) : [],
            'priority' => $request->has('priority') ? explode(',', $request->priority) : [],
            'start_date' => $request->has('start') ? $request->start : null,
            'end_date' => $request->has('end') ? $request->end : null,
            'min_time' => $request->has('min') ? $request->min : null,
            'max_time' => $request->has('max') ? $request->max : null,
        ];

        return Excel::download(new ToDoExport($filters), 'todo.xlsx');
    }

}

