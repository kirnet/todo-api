<?php

declare(strict_types=1);

namespace App\Enums;

enum ToDoStatus: string {
    case Done = 'done';
    case Process = 'process';
}
