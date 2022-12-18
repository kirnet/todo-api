<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Todo;
use App\Notifications\TelegramNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private readonly Todo $toDo)
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (config('services.telegram-bot-api.notify')) {
            $this->toDo->notifyNow(new TelegramNotification());
        }
    }
}
