<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ToDoStatus;
use App\Models\Categories;
use App\Models\Todo as Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class Todo
{
    public function __construct(private readonly Request $request, private readonly int $userId)
    {
    }

    public function list(): LengthAwarePaginator
    {
        $page = $this->request->get('page', 0);
        $perPage = $this->request->get('perPage', 10);
        $sortModel = $this->request->get('sortModel', []);
        /** @var Builder $todos */
        $todos = Model::where('user_id', $this->userId);
        foreach ($sortModel as $sort) {
            $todos->orderBy($sort['field'], $sort['sort']);
        }
        match ($this->request->get('status')) {
            ToDoStatus::Done->value => $todos->whereNotNull('done'),
            ToDoStatus::Process->value => $todos->whereNotNull('started')->whereNull('done'),
            default => $todos->whereNull(['done', 'started'])
        };
        return $todos->paginate(perPage: $perPage, page: $page);
    }

    public function show($id)
    {
        return Model::where(['id' => $id, 'user_id' => $this->userId])->first();
    }

    public function update($id): bool|int
    {
        $data = $this->request->validated();
        $todo = Model::where(['id' => $id, 'user_id' => $this->userId])->first();
        if (!$todo) {
            abort(Response::HTTP_NO_CONTENT);
        }
        return $todo->update($data);
    }

    public function changeStatus($id): JsonResponse|bool
    {
        $data = $this->request->validated();
        $todo = Model::where(['id' => $id, 'user_id' => $this->userId])->first();
        if (!$todo) {
            abort(Response::HTTP_NO_CONTENT);
        }
        match ($data['status']) {
            ToDoStatus::Process->value => $todo->started = now(),
            ToDoStatus::Done->value => $todo->done = now(),
        };
        return $todo->save();
    }

    public function store(): \App\Models\Todo
    {
        $data = $this->request->validated();
        if (!empty($data['schedule_start'])) {
            $data['schedule_start'] = Carbon::createFromFormat('Y-m-d H:i', $data['schedule_start']);
        }
        $data['user_id'] = $this->userId;
        $data['category_id'] ??= (Categories::first()?->id ?? null);

        return Model::create($data);
    }
}
