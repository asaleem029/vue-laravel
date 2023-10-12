<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Todo;
use Exception;

class TodoService
{
    public function getTodoList()
    {
        try {
            $user_id = Auth::user()->id;
            $todos = Todo::where('user_id', $user_id)->get();
            return response()->json([
                'status' => 'success',
                'todos' => $todos,
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function createTodo($data)
    {
        try {
            // Validate request
            $validator = Validator::make($data->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            // Create new todo
            $todo = Todo::create([
                'title' => $data->title,
                'description' => $data->description,
                'user_id' => Auth::user()->id
            ]);

            if ($todo) {
                // Return response
                return response()->json([
                    'status' => 'success',
                    'message' => 'Todo created successfully',
                    'todo' => $todo,
                ]);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateTodo($data, $id)
    {
        try {
            // Validate request
            $validator = Validator::make($data->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            // Update todo data
            $todo = Todo::find($id);
            $todo->title = $data->title;
            $todo->description = $data->description;
            $todo->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Todo updated successfully',
                'todo' => $todo,
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getTodo($id)
    {
        try {
            $todo = Todo::find($id);
            return response()->json([
                'status' => 'success',
                'todo' => $todo,
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function deleteTodo($id)
    {
        try {
            $todo = Todo::find($id);
            $todo->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Todo deleted successfully',
                'todo' => $todo,
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
