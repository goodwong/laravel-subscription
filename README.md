# Laravel 5 Subscription

订阅模块，用于有时间限制的服务订阅


## 安装

1. 通过composer安装
    ```shell
    composer require goodwong/laravel-subscription
    ```

4. 打开config/app.php，在providers数组里注册服务：
    ```php
    Goodwong\LaravelSubscription\SubscriptionServiceProvider::class,
    ```

5. 创建数据库
    ```shell
    php artisan migrate
    ```


## 操作

1. 为用户添加订阅服务（在相同type下，若用户已经有效订阅，则会自动删除旧的订阅）
    ```php
    $handler = app('Goodwong\LaravelSubscription\Handlers\SubscriptionHandler');
    $subscription = $handler->subscribe($user_id, $level = 'basic', $days = 30, $config = [
        'type' => 'plan',
        'start_at' => '2017-05-05 08:00:59',
        'comment' => '',
    ]);
    ```
2. 查询订阅
    ```php
    // 有global scope限定start_at/end_at
    Goodwong\LaravelSubscription\Entities\Subscription::where('user_id', $user_id)->first();
    // 查询所有订阅（包含已经归档的）
    Goodwong\LaravelSubscription\Entities\Subscription::withoutGlobalScopes()->withTrashed()->get();
    ```

3. 与User结合
    ```php
    <?php
    
    namespace App\User\Entities;
    
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    
    class User extends Authenticatable
    {
        use Notifiable;

        /**
         * membership
         */
        public function plan()
        {
            return $this->hasOne('Goodwong\LaravelSubscription\Entities\Subscription')
                // ->where('type', 'plan')
                ->orderBy('id', 'desc')
                ;
        }
    
        /**
         * The "booting" method of the model.
         *
         * @return void
         */
        protected static function boot()
        {
            parent::boot();
    
            static::addGlobalScope('plan', function (Builder $builder) {
                $builder->with('plan');
            });
        }
    }
    ```

