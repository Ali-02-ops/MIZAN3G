<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CulturalSubcategory extends Model
{
    use HasFactory;

    protected $table = 'mizan3g_cultural_subcategories';

    protected $fillable = ['category_id', 'code', 'name', 'description', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CulturalCategory::class);
    }

    public function terms(): HasMany
    {
        return $this->hasMany(CulturalTerm::class, 'subcategory_id');
    }
}
