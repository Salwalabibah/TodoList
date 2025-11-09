<?php

namespace App\Http\Controllers;

use App\Models\ToDo;
use Illuminate\Http\Request;

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
    public function excel_generate(Request $request)
    {
        
    }

}
