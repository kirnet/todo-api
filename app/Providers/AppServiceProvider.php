<?php

namespace App\Providers;

use App\Models\Todo;
use App\Observers\ToDoObserver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Todo::observe(ToDoObserver::class);

        DB::listen(function ($query) {
            try {
                $sql = sprintf(
                    str_replace('?', "'%s'", $query->sql),
                     ...$query->bindings

                );

            } catch(\Throwable $e) {
                dd($e->getMessage());
            }

            File::append(
                storage_path('/logs/query.log'),
                '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $sql . PHP_EOL . $query->time . PHP_EOL . PHP_EOL
            );
        });
    }
}
