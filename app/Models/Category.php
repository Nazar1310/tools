<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property string $slug
 * @property string $name
 * @property string $title
 * @property string $desc
 * @property string $seo_title
 * @property string $seo_desc
 *
 * @property Tool[] $calculators
 */
class Category extends Model
{
    protected $table = "categories";
    protected $fillable = [
        'slug',
        'name',
        'title',
        'desc',
        'seo_title',
        'seo_desc',
    ];

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }

    public function getRoute(): string
    {
        return route('category', $this->slug);
    }
}
