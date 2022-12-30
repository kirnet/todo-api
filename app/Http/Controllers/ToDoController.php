<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ToDoChangeStatusRequest;
use App\Http\Requests\TodoRequest;
use App\Http\Resources\ToDoCollection;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ToDoController extends Controller
{
    public function __construct(private readonly Request $request, private int $userId = 0)
    {
        if (!auth('sanctum')->user()) {
            return false;
        }
        $this->userId = auth('sanctum')->user()->id;
    }

    public function index()
    {
        return new ToDoCollection((new \App\Services\Todo($this->request, $this->userId))->list());
    }

    public function show($id): JsonResponse
    {
        return \response()->json((new \App\Services\Todo($this->request, $this->userId))->show($id));
    }

    public function store(TodoRequest $request): JsonResponse
    {
        return \response()->json((new \App\Services\Todo($request, $this->userId))->store());
    }

    public function update($id, TodoRequest $request): JsonResponse
    {
        $isUpdated = (new \App\Services\Todo($request, $this->userId))->update($id);
        $status = $isUpdated ? Response::HTTP_OK : Response::HTTP_NO_CONTENT;
        return \response()->json()->setStatusCode($status);
    }

    public function destroy($id): JsonResponse
    {
        $isDeleted = Todo::where(['id' => $id, 'user_id' => $this->userId])->delete();
        $status = $isDeleted ? Response::HTTP_OK : Response::HTTP_NO_CONTENT;
        return \response()->json()->setStatusCode($status);
    }

    public function changeStatus($id, ToDoChangeStatusRequest $request): JsonResponse
    {
        $isUpdated = (new \App\Services\Todo($request, $this->userId))->changeStatus($id);
        $status = $isUpdated ? Response::HTTP_OK : Response::HTTP_NO_CONTENT;
        return \response()->json()->setStatusCode($status);
    }
}
