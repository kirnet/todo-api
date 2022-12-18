<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ToDoStatus;
use App\Http\Requests\ToDoChangeStatusRequest;
use App\Http\Requests\TodoRequest;
use App\Models\Categories;
use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ToDoController extends Controller
{
    private int $userId = 0;

    const STATUS_DONE = 'DONE';
    const STATUS_PROCESS = 'PROCESS';

    public function __construct(public Request $request)
    {
        if (!auth('sanctum')->user()) {
            return false;
        }
        $this->userId = auth('sanctum')->user()->id;
    }

    public function index()
    {
        $result = Todo::where('user_id', $this->userId);
        match ($this->request->get('status')) {
            ToDoStatus::Done->value => $result->whereNotNull('done'),
            ToDoStatus::Process->value => $result->whereNotNull('started')->whereNull('done'),
            default => $result->whereNull(['done', 'started'])
        };
        return $result->get();
    }

    public function show($id)
    {
        return Todo::where(['id' => $id, 'user_id' => $this->userId])->first();
    }

    public function store(TodoRequest $request)
    {
        $data = $request->validated();
        if (!empty($data['schedule_start'])) {
            $data['schedule_start'] = Carbon::createFromFormat('m/d/Y h:i A', $data['schedule_start']);
            //Event::dispatch(new Scheduled());
        }
        $data['user_id'] = $this->userId;
        $data['category_id'] ??= (Categories::first()?->id ?? null);

        return Todo::create($data);
    }

    public function update($id, TodoRequest $request): JsonResponse
    {
        $data = $request->validated();
        /** @var Todo $todo */
        $todo = Todo::findOrFail($id);
        if ($this->userId !== $todo->user_id) {
            return \response()->json('Forbidden')->setStatusCode(Response::HTTP_FORBIDDEN);
        }
        $status = Response::HTTP_NO_CONTENT;
        if ($todo->update($data)) {
            $status = Response::HTTP_OK;
        }
        return \response()->json()->setStatusCode($status);
    }

    public function delete($id): JsonResponse
    {
        $todo = Todo::where(['id' => $id, 'user_id' => $this->userId])->first();
        $status = Response::HTTP_NO_CONTENT;
        if ($todo && $todo->delete()) {
            $status = Response::HTTP_OK;
        }
        return \response()->json()->setStatusCode($status);
    }

    public function changeStatus($id, ToDoChangeStatusRequest $request)
    {
        $request->validated();
        $todo = Todo::where(['id' => $id, 'user_id' => $this->userId])->first();
        if (!$todo) {
            return \response()->json('Todo not found')->setStatusCode(Response::HTTP_NO_CONTENT);
        }
        match ($request->get('status')) {
            ToDoStatus::Process->value => $todo->started = now(),
            ToDoStatus::Done->value => $todo->done = now(),
        };

        return $todo->save();
    }
}
