<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ToDoStatus;
use App\Models\Categories;
use App\Models\Todo;
use App\Models\User;
use Database\Seeders\Categories as SeedCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private Object|null $user = null;

    public static function setUpBeforeClass(): void
    {

    }

    public function setUp(): void
    {
        parent::setUp();

        if (!Categories::first()) {
            $this->seed(SeedCategory::class);
        }
        $user = User::first();
        $this->user = $user ? Sanctum::actingAs($user, ['*']) : Sanctum::actingAs(User::factory()->create(), ['*']);
    }

    public function testCreateUser(): void
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);
    }

    public function testLogin()
    {
        $this->assertTrue($this->isAuthenticated());

//        Sanctum::actingAs($this->user, ['*']);
//        $response = $this->post(route('login'), [
//            'email' => $this->user->email,
//            'password' => 'password',
//        ]);
//
//        $response->assertStatus(Response::HTTP_OK)
//            ->assertJsonStructure(['token'])
//        ;
    }

    public function testCreateToDo()
    {
        $response = $this->postJson(
                route('createTodo'),
                [
                    'name' => 'testTodo',
                    'description' => 'Test description',
                    'schedule_start' => date('Y-m-d H:i', time() + 10000),
                    'category_id' => Categories::first()->id
                ]
            )
        ;

        $response->assertJsonStructure([
            'name',
            'description',
            'schedule_start',
            'user_id',
            'category_id',
            'updated_at',
            'created_at',
            'id'
        ]);

    }

    public function testToDoStarted()
    {
        $this->testCreateToDo();
        $todo = Todo::first();
        $response = $this->patchJson(route('changeStatus', $todo->id), ['status' => ToDoStatus::Process->value]);
        $response->assertOk();
    }

    public function testToDoDone()
    {
        $this->testCreateToDo();
        $todo = Todo::first();
        $response = $this->patchJson(route('changeStatus', $todo->id), ['status' => ToDoStatus::Done->value]);
        $response->assertOk();
    }

    public function testTodoDelete()
    {
        $this->testCreateToDo();
        $todo = Todo::first();
        $response = $this->delete(route('todoDelete', $todo->id));
        $response->assertOk();
    }


    public static function tearDownAfterClass(): void
    {
//        dump(env('DB_CONNECTION'));
//        (new self())->setUp();
//        try {
//
//            DB::table('todo')->truncate();
//            DB::table('users')->truncate();
//            DB::table('categories')->truncate();
//        } catch(\Exception $e) {
//            dd($e);
//        }
    }
}
