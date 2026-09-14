<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CulturalCategory extends Model
{
    use HasFactory;

    protected $table = 'mizan3g_cultural_categories';

    protected $fillable = ['code', 'name', 'description', 'framework', 'sort_order', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(CulturalSubcategory::class, 'category_id');
    }

    public function terms(): HasMany
    {
        return $this->hasMany(CulturalTerm::class, 'category_id');
    }
}
