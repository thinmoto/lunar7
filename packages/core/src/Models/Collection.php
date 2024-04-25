<?php

namespace Lunar\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;
use Lunar\Base\BaseModel;
use Lunar\Base\Casts\AsAttributeData;
use Lunar\Base\Traits\HasChannels;
use Lunar\Base\Traits\HasCustomerGroups;
use Lunar\Base\Traits\HasMacros;
use Lunar\Base\Traits\HasMedia;
use Lunar\Base\Traits\HasTranslations;
use Lunar\Base\Traits\HasUrls;
use Lunar\Base\Traits\Searchable;
use Lunar\Database\Factories\CollectionFactory;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;

/**
 * @property int $id
 * @property int $collection_group_id
 * @property-read  int $_lft
 * @property-read  int $_rgt
 * @property ?int $parent_id
 * @property string $type
 * @property ?array $attribute_data
 * @property string $sort
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
class Collection extends BaseModel implements SpatieHasMedia
{
    use HasChannels,
        HasCustomerGroups,
        HasFactory,
        HasMacros,
        HasMedia,
        HasTranslations,
        HasUrls,
        NodeTrait,
        Searchable {
            NodeTrait::usesSoftDelete insteadof Searchable;
        }

    /**
     * Define which attributes should be cast.
     *
     * @var array
     */
    protected $casts = [
        'attribute_data' => AsAttributeData::class,
    ];

    protected $guarded = [];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): CollectionFactory
    {
        return CollectionFactory::new();
    }

    /**
     * Return the group relationship.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(CollectionGroup::class, 'collection_group_id');
    }

    public function scopeInGroup(Builder $builder, int $id): Builder
    {
        return $builder->where('collection_group_id', $id);
    }

    /**
     * Return the products relationship.
     */
    public function products(): BelongsToMany
    {
        $prefix = config('lunar.database.table_prefix');

        return $this->belongsToMany(
            Product::class,
            "{$prefix}collection_product"
        )->withPivot([
            'position',
        ])->withTimestamps()->orderByPivot('position');
    }

    /**
     * Get the translated name of ancestor collections.
     */
    public function getBreadcrumbAttribute(): \Illuminate\Support\Collection
    {
        return $this->ancestors->map(function ($ancestor) {
            return $ancestor->translateAttribute('name');
        });
    }

    /**
     * Return the customer groups relationship.
     */
    public function customerGroups(): BelongsToMany
    {
        $prefix = config('lunar.database.table_prefix');

        return $this->belongsToMany(
            CustomerGroup::class,
            "{$prefix}collection_customer_group"
        )->withPivot([
            'visible',
            'enabled',
            'starts_at',
            'ends_at',
        ])->withTimestamps();
    }

	public function childs(): HasMany
	{
		return $this->hasMany(\Lunar\Models\Collection::class, 'parent_id', 'id');
	}

	public function getChildsTree(): \Illuminate\Support\Collection
	{
		$out = collect();
		$out->prepend($this, $this->id);

		foreach($this->childs as $child)
			foreach($child->getChildsTree() as $ch)
				$out->prepend($ch, $ch->id);

		return $out->reverse();
	}
}
