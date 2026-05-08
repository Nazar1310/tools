<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property string $slug
 * @property string $name
 * @property string $title
 * @property string $desc
 * @property string $seo_title
 * @property string $seo_desc
 * @property int $category_id
 *
 * @property Category $category
 */
class Tool extends Model
{
    protected $table = "tools";
    protected $fillable = [
        'slug',
        'name',
        'title',
        'desc',
        'seo_title',
        'seo_desc',
        'category_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getRoute(): string
    {
        return route('tool', [$this->category->slug, $this->slug]);
    }
}
