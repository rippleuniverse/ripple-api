<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Module extends Model
{
    protected $fillable = [
        'module_no',
        'title',
        'description',
        'program_id',
        'id'
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
