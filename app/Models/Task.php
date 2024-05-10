<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory,SoftDeletes;

    protected $appends = ['statusColor'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'due_date',
        'status',
        'todo_id',
    ];

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }

    protected function getStatusColorAttribute(): ?string
    {
        $dueDate = Carbon::parse($this->due_date);

        if ($dueDate->gte(today()->addDays(3))) {
            return 'green';
        }

        if ($dueDate->lte(now()->addHours(3))) {
            return 'red';
        }

        if ($dueDate->lt(now()->addHours(24))) {
            return 'yellow';
        }

        return null;
    }
}
