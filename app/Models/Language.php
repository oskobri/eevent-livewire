<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Language extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $guarded = [];

    public array $translatable = ['name'];

    public $timestamps = false;

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }
}
