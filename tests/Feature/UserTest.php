<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Categories;
use App\Models\Todo;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Categories as SeedCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApiTest extends TestCase
{
//    use RefreshDatabase;

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

        $this->user = Sanctum::actingAs(User::factory()->create(), ['*']);
    }

    public function testCreateUser(): void
    {
        $this->assertModelExists($this->user);
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
                    'schedule_start' => Carbon::getTestNow(),
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
        $todo = Todo::first();
        $response = $this->patchJson(route('startDone', $todo->id), ['started' => true]);
        $response->assertOk();
    }

    public function testToDoDone()
    {
        $todo = Todo::first();
        $response = $this->patchJson(route('startDone', $todo->id), ['done' => true]);
        $response->assertOk();
    }

    public function testTodoDelete()
    {
        $todo = Todo::first();
        $response = $this->delete(route('todoDelete', $todo->id));
        $response->assertOk();
    }


    public static function tearDownAfterClass(): void
    {
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
