<?php

namespace Goodwong\LaravelSubscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    /**
     * table name
     */
    protected $table = 'subscriptions';

    /**
     * fillable fields
     */
    protected $fillable = [
        'user_id',
        'type',
        'level',
        'comment',
        'start_at',
        'end_at',
    ];
    
    /**
     * date
     */
    protected $dates = [
        'start_at',
        'end_at',
        'deleted_at',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('available', function (Builder $builder) {
            $now = date('Y-m-d H:i:s');
            $builder->where('start_at', '<', $now)->where('end_at', '>', $now);
        });
    }
}
