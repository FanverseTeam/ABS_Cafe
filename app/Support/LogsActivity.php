<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait LogsActivity
{
    public function activityLabel(): string
    {
        return Str::of(class_basename(static::class))
            ->replace(['\\', '/'], ' ')
            ->replace('  ', ' ')
            ->trim()
            ->ucfirst();
    }

    public function activityModule(): string
    {
        return Str::of(class_basename(static::class))
            ->replace(['\\', '/'], ' ')
            ->replace('  ', ' ')
            ->trim()
            ->ucfirst();
    }
}