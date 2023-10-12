<?php

namespace App\Http\Controllers;

use App\Http\Services\TodoService;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    protected $todoService;

    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
        $this->middleware('jwt.verify');
    }

    public function index()
    {
        return $this->todoService->getTodoList();
    }

    public function store(Request $request)
    {
        return $this->todoService->createTodo($request);
    }

    public function show($id)
    {
        return $this->todoService->getTodo($id);
    }

    public function update(Request $request, $id)
    {
        return $this->todoService->updateTodo($request, $id);
    }

    public function destroy($id)
    {
        return $this->todoService->deleteTodo($id);
    }
}
