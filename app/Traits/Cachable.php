<?php
namespace App\Traits;

use App\Http\Middleware\AppCaching;
use Closure;
use Illuminate\Support\Facades\Cache;

trait Cachable {

    /**
     * On Boot - subscribe to model event
     *
     * @return void
     */
    protected static function bootCachable()
    {
        static::created(function(){
            self::flushHttpCache();
            return true;
        });
        static::updated(function($model){
            if(is_array($model->primaryKey)){
                $keys = array_map(function($key) use($model){
                    return $model->{$key};
                }, $model->primaryKey);
                self::flushItemCache(self::flatKey($keys));
            }
            else {
                self::flushItemCache($model->{$model->primaryKey});
            }
            self::flushHttpCache();
            return true;
        });
        static::deleted(function($model){
            if(is_array($model->primaryKey)){
                $keys = array_map(function($key) use($model){
                    return $model->{$key};
                }, $model->primaryKey);
                self::flushItemCache(self::flatKey($keys));
            }
            else {
                self::flushItemCache($model->{$model->primaryKey});
            }
            self::flushHttpCache();
            return true;
        });
    }

    /**
     * Flatten key if array
     *
     * @param mixed $key
     * @return void
     */
    private static function flatKey($key){
        if(is_array($key)){
            return md5(json_encode($key));
        }
        return md5($key);
    }

    /**
     * Get the value of cacheEnabled - If model caching enabled
     */
    public static function getCacheEnabled(){
        return boolval(config('app.cache.model', false));
    }

    /**
     * Get the value of cacheKey
     */
    public static function getCacheKey(){
        return self::class;
    }

    /**
     * Get the value of cacheTTL
     */
    public static function getCacheTTL(){
        return config('app.cache.ttl');
    }

    /**
     * Get Cache tag for this model
     * @return string
     */
    public static function getCacheTag(){
        return self::class;
    }

    /**
     * Get Cache tag for a single item of this model
     * @param string $tag Item Id or Special tag
     * @return string
     */
    public static function getItemCacheTag($tag){
        return self::class.':'.$tag;
    }

    /**
     * Get list of Cache tags for a single item of this model
     * @param mixed $tag Item Id(s) or Special tag(s)
     * @return array
     */
    public static function getItemCacheTags($tag){
        if(is_array($tag)){
            return array_merge([self::getCacheTag()], $tag);
        }
        return [self::getCacheTag(), self::getItemCacheTag($tag)];
    }

    /**
     * Get item (s) if in cache else execute closure and cache result
     *
     * @param array $tags
     * @param string $key
     * @param Closure $callback Returns data to be cached
     * @param Closure $condition Receives value of $callback as parameter. Allow Cache if this function is not available or evaluates to true
     * @return mixed
     */
    protected static function remember($tags, $key, Closure $callback, Closure $condition = null){
        $value = Cache::tags($tags)->get($key);
        if (!is_null($value)) {
            if((is_null($condition) || $condition($value))){
                return $value;
            }
            else {
                Cache::tags($tags)->delete($key);
            }
        }

        $value = $callback();
        if(!is_null($value) && (is_null($condition) || $condition($value))){
            Cache::tags($tags)->put($key, $value, self::getCacheTTL());
        }
        return $value;
    }

    /**
     * Get single item if in cache else execute closure and cache result
     *
     * @param mixed $key e.g Item Id(s) or Special Tag(s)
     * @param Closure $callback Returns data to be cached
     * @param Closure $condition Optional - Receives value of $callback as parameter. Allow Cache if this function is not available or evaluates to true
     * @return static
     */
    public static function rememberItem($key, Closure $callback, Closure $condition = null){
        if(self::getCacheEnabled()){
            return self::remember(self::getItemCacheTags($key), self::getCacheKey().':'.self::flatKey($key), $callback, $condition);
        }
        else {
            return $callback();
        }
    }

    /**
     * Flush all caches for this model
     * @return void
     */
    public static function flushCache(){
        if(self::getCacheEnabled()){ // If gloabal caching enabled
            Cache::tags(self::getCacheTag())->flush();
        }
    }

    /**
     * Flush a single item cache for this model
     * @param string $tag Item Id or Special tag
     * @return void
     */
    public static function flushItemCache($tag){
        if(self::getCacheEnabled()){ // If gloabal caching enabled
            Cache::tags(self::getItemCacheTag($tag))->flush();
        }
    }

    /**
     * Flush cache for this current request segment
     * @return void
     */
    public static function flushHttpCache(){
        if(self::getCacheEnabled()){ // If gloabal caching enabled
            Cache::tags(AppCaching::getCacheTag())->flush();
        }
    }
}
