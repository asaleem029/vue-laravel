<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use Exception;

class TodoController extends Controller
{
    public function __construct()
    {
        // Apply middleware 
        $this->middleware('jwt.verify');
    }

    public function index()
    {
        $todos = Todo::all();
        return response()->json([
            'status' => 'success',
            'todos' => $todos,
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:255',
            ]);

            // Create new todo
            $todo = Todo::create([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            // Return response
            return response()->json([
                'status' => 'success',
                'message' => 'Todo created successfully',
                'todo' => $todo,
            ]);
        } catch (Exception $e) {
            // Return exception
            return response()->json([
                'status' => 'error',
                'message' => $e,
            ]);
        }
    }

    public function show($id)
    {
        $todo = Todo::find($id);
        return response()->json([
            'status' => 'success',
            'todo' => $todo,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        // Update todo data
        $todo = Todo::find($id);
        $todo->title = $request->title;
        $todo->description = $request->description;
        $todo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo updated successfully',
            'todo' => $todo,
        ]);
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);
        $todo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted successfully',
            'todo' => $todo,
        ]);
    }
}
