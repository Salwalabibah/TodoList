<?php

namespace App\Http\Controllers;

use App\Exports\ToDoExport;
use App\Models\ToDo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'title' => 'required|string|max:255',
            'assignee' => 'nullable|string',
            'due_date' => 'required|date|after_or_equal:today',
            'time_tracked' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,open,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
        ]);

        $to_do = ToDo::create([
            'title' => $validate['title'],
            'assignee' => $validate['assignee'],
            'due_date' => $validate['due_date'],
            'time_tracked' => $validate['time_tracked'] ?? 0,
            'status' => $validate['status'] ?? 'pending',
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
            'assignee' => $request->has('assignee') ? $this->explodeParam($request->assignee) : [],
            'status' => $request->has('status') ? $this->explodeParam($request->status) : [],
            'priority' => $request->has('priority') ? $this->explodeParam($request->priority) : [],
            'start_date' => $request->has('start') ? $request->start : null,
            'end_date' => $request->has('end') ? $request->end : null,
            'min_time' => $request->has('min') ? $request->min : null,
            'max_time' => $request->has('max') ? $request->max : null,
        ];

        return Excel::download(new ToDoExport($filters), 'todo.xlsx');
    }

    public function chart(Request $request)
    {
        $validTypes = ['status', 'priority', 'assignee'];

        $type = $request->type ?? null;
        if (!$type || !in_array($type, $validTypes)) {
            return response()->json(['error' => 'Invalid type parameter.']);
        }

        $data = [];

        if ($type !== 'assignee') {
            $to_do = $this->getTypeSummary($type);

            if ($type === 'status') {
                $data = [
                    '{$type}_summary' => [
                        'pending' => $to_do->where('status', 'pending')->first()->total ?? 0,
                        'open' => $to_do->where('status', 'open')->first()->total ?? 0,
                        'in_progress' => $to_do->where('status', 'in_progress')->first()->total ?? 0,
                        'completed' => $to_do->where('status', 'completed')->first()->total ?? 0,
                    ]
                ];
            } else if ($type === 'priority') {
                $data = [
                    '{$type}_summary' => [
                        'low' => $to_do->where('priority', 'low')->first()->total ?? 0,
                        'medium' => $to_do->where('priority', 'medium')->first()->total ?? 0,
                        'high' => $to_do->where('priority', 'high')->first()->total ?? 0,
                    ]
                ];
            }
        } else {
            $data['{$type}_summary'] = ToDo::select([
                'assignee',
                DB::raw('COUNT(*) as total_todos'),
                DB::raw('COUNT(CASE WHEN status = "pending" THEN 1 END) as total_pending_todos'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN time_tracked END) as total_timetracked_completed_todos')
            ])
                ->groupBy('assignee')
                ->get()
                ->mapWithKeys(fn($row) => [
                    $row->assignee => [
                        'total_todos' => (int) $row->total_todos,
                        'total_pending_todos' => (int) $row->total_pending_todos,
                        'total_timetracked_completed_todos' => (float) $row->total_timetracked_completed_todos,
                    ]
                ])
                ->toArray();
        }

        return response()->json($data);
    }

    private function explodeParam(?string $param)
    {
        return $param ? array_map('trim', explode(',', $param)) : [];
    }

    private function getTypeSummary(string $type)
    {
        return ToDo::select([$type, DB::raw('COUNT(*) as total')])
            ->groupBy($type)
            ->get();
    }
}

