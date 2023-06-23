<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\RewardAccumulator
 *
 * @property int $id
 * @property string|null $name
 * @property int $partner_id
 * @property string $frequency
 * @property int $value_type
 * @property int $type
 * @property int|null $currency_id
 * @property string|null $filter
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property-read \App\Partner $partner
 * @method static \Illuminate\Database\Eloquent\Builder|RewardAccumulator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RewardAccumulator newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RewardAccumulator onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RewardAccumulator query()
 * @method static \Illuminate\Database\Eloquent\Builder|RewardAccumulator withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RewardAccumulator withoutTrashed()
 * @mixin \Eloquent
 */
class RewardAccumulator extends Model
{
    use SoftDeletes;

    public $table      = 'reward_accumulator';
    public $timestamps = false;
    public $guarded = ['id'];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function caps(): HasMany
    {
        return $this->hasMany(RewardCap::class, 'reward_accumulator_id', 'id');
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(RewardTier::class, 'reward_accumulator_id', 'id');
    }
}
