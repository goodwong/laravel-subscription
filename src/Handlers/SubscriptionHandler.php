<?php

namespace Goodwong\LaravelSubscription\Handlers;

use Goodwong\LaravelSubscription\Entities\Subscription;

class SubscriptionHandler
{
    /**
     * subscribe
     * 
     * 如果订阅的内容(相同type)已经存在，会先删除，并且加上comment
     * 
     * @param  integer  $user_id
     * @param  string  $level
     * @param  integer  $days
     * @param  array  $config [ $type, $comment, $start_at, ]
     * @return Subscription
     */
    public function subscribe($user_id, $level, $days, $config = [])
    {
        $type = data_get($config, 'type');
        $comment = data_get($config, 'comment');
        $now = time();
        $start_at = data_get($config, 'start_at', date('Y-m-d H:i:s', $now));
        $end_at = date('Y-m-d H:i:s', strtotime($start_at) + $days * 86400);
        $subscription = Subscription::create(
            compact('user_id', 'type', 'level', 'comment', 'start_at', 'end_at')
        );

        $exist = Subscription::where('user_id', $user_id)->where('type', $type)->first();
        if ($exist) {
            $exist->update([
                'comment' => "{$exist->comment} replace with new subscription #{$subscription->id}",
            ]);
            $exist->delete();
        }

        return $subscription;
    }
}