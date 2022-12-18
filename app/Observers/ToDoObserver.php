<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\NotifyQueue;
use App\Models\ToDo;
use App\Notifications\TelegramNotification;
use NotificationChannels\Telegram\TelegramMessage;


class ToDoObserver
{
    /**
     * Handle the ToDo "created" event.
     *
     * @param ToDo $toDo
     *
     * @return void
     */
    public function created(ToDo $toDo): void
    {
//        $toDo->notify(new TelegramNotification());
        if ($toDo->schedule_start) {
            NotifyQueue::dispatch($toDo)->delay($toDo->schedule_start);
        }
    }

    /**
     * Handle the ToDo "updated" event.
     *
     * @param ToDo $toDo
     *
     * @return void
     */
    public function updated(ToDo $toDo)
    {
        //
    }

    /**
     * Handle the ToDo "deleted" event.
     *
     * @param ToDo  $toDo
     *
     * @return void
     */
    public function deleted(ToDo $toDo)
    {
        //
    }

    /**
     * Handle the ToDo "restored" event.
     *
     * @param  ToDo  $toDo
     *
     * @return void
     */
    public function restored(ToDo $toDo)
    {
        //
    }

    /**
     * Handle the ToDo "force deleted" event.
     *
     * @param  ToDo  $toDo
     *
     * @return void
     */
    public function forceDeleted(ToDo $toDo)
    {
        //
    }
}
