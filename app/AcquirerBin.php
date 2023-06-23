<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\AcquirerBin
 *
 * @property int $id
 * @property string $acquirer_bin
 * @property int $partner_id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static Builder|AcquirerBin filterByPartner(int $partnerId)
 * @method static Builder|AcquirerBin filterBySearchPhrase(string $searchPhrase)
 * @method static Builder|AcquirerBin newModelQuery()
 * @method static Builder|AcquirerBin newQuery()
 * @method static Builder|AcquirerBin onlyTrashed()
 * @method static Builder|AcquirerBin query()
 * @method static Builder|AcquirerBin withTrashed()
 * @method static Builder|AcquirerBin withoutTrashed()
 * @mixin \Eloquent
 */
class AcquirerBin extends Model
{
    use SoftDeletes;

    protected $table = 'acquirer_bin';
    protected $fillable = ['id', 'acquirer_bin', 'description', 'partner_id'];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * @param int|array $partnerId
     */
    public function scopeFilterByPartner(Builder $q, $partnerId): void
    {
        $partnerIds = is_array($partnerId) ? $partnerId : [$partnerId];
        $q->whereIn('acquirer_bin.partner_id', $partnerIds);
    }

    public function scopeFilterBySearchPhrase(Builder $q, string $searchPhrase): void
    {
        $q->where(function ($q) use ($searchPhrase) {
            $q->whereLike('acquirer_bin', $searchPhrase)
                ->orWhereLike('description', $searchPhrase);
        });
    }
}