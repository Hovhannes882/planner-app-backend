<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(["user_id", "aspect_id", "name", "description", "day", "priority", "remind_at", "start_at", "end_at"])]
class Task extends Model
{
    public const string PRIORITY_HIGH = "high";
    public const string PRIORITY_MEDIUM = "medium";
    public const string PRIORITY_LOW = "low";
}
