<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Cache;

class AppCaching
{

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        // If HTTP request caching enabled
        if(boolval(config('app.cache.http', false))){ 

            $key = md5($request->fullUrl());
            $ttl = config('app.cache.ttl');
            $tag = self::getCacheTag($request);

            if(!empty($content = Cache::tags($tag)->get($key))){
                return $this->getCachedResponse($content);
            }
            else {
                /**
                 * @var \Illuminate\Http\Response
                 */
                $response = $next($request);
                if(($response->isSuccessful() && !empty($response->getContent())) || ($response->isRedirection() && !$this->isPrivateCache($response))) {
                    $ttl = $this->getCacheExpiry($response) ?? $ttl;
                    Cache::tags($tag)->set($key, $this->getCacheContent($response, $ttl), $ttl);
                    $this->setCacheHeaders($response, $ttl);
                }
                return $response;
            }
        }
        else {
            /**
             * @var \Illuminate\Http\Response
             */
            $response = $next($request);
            if($response->isSuccessful()) {
                $this->setNoCacheHeaders($response);
            }
            return $response;
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function getCacheTag($request = null){
        $action = (app('router')->current() ? app('router')->current()->controller : null);
        return $action ? get_class($action) : ($request ?? app('request'))->segment; // fallback on segments if controller not present
    }

    /**
     * @param array $content
     * @return \Illuminate\Http\Response
     */
    private function getCachedResponse($content = []){
        $ttl = $content['ttl'] ?? null;
        $timestamp = $content['timestamp'] ?? null;
        $code = $content['code'] ?? 200;
        $headers = $content['headers'] ?? [];
        $body = (json_decode(@$content['body'], true) ?? @$content['body']) ?? $content;
        $ttl = $this->getRemainingTime($ttl, $timestamp);
        if($ttl > 0){
            $headers['Cache-Control'] =  "public, max-age $ttl";
            $headers['Expires'] =  now()->addSeconds($ttl)->toString();
        }
        return response($body, $code, array_merge($headers, $this->getCacheHitHeader()));
    }

    /**
     * @param integer $ttl
     * @param integer $timestamp
     * @return integer
     */
    private function getRemainingTime($ttl, $timestamp){
        if($ttl && $timestamp){
            $time = ($ttl - (now()->timestamp-$timestamp));
            return $time > 0 ? $time : $ttl;
        }
        return 0;
    }

    /**
     * @param \Illuminate\Http\Response $response
     * @param int $ttl
     * @return array
     */
    private function getCacheContent($response, $ttl){
        return [
            'code' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
            'body' => $response->getContent(),
            'ttl' => $ttl,
            'timestamp' => now()->timestamp
        ];
    }

    /**
     * @param \Illuminate\Http\Response $response
     * @param int $ttl
     * @return \Illuminate\Http\Response
     */
    private function setCacheHeaders(&$response, $ttl){
        $response->headers->set('X-Cache', 'Missed from '.config('app.name'), true);
        $response->headers->set('Cache-Control', 'public, max-age '.$ttl, true);
        return $response;
    }

    /**
     * @param \Illuminate\Http\Response $response
     * @param int $ttl
     * @return \Illuminate\Http\Response
     */
    private function setNoCacheHeaders(&$response){
        $response->headers->set('Cache-Control', 'no-cache', true);
        return $response;
    }

    /**
     * @return array
     */
    private function getCacheHitHeader(){
        return ['X-Cache' => 'Hit from '.config('app.name')];
    }

    /**
     * @param \Illuminate\Http\Response $response
     * @return bool
     */
    private function isPrivateCache($response){
        $header = $response->headers->get('Cache-Control');
        return $header ? preg_match("/private/", $header) : false;
    }


    /**
     * @param \Illuminate\Http\Response $response
     * @return int|null
     */
    private function getCacheExpiry($response){
        $ttl = 0;
        $cc_header = $response->headers->get('Cache-Control');
        $expiry_header = $response->headers->get('Expires');
        if(preg_match("/max-age\s*([0-9]*)/", $cc_header, $match)){
            $ttl = intval($match[1]);
        }
        else if(!empty($expiry_header)){
            $ttl = strtotime($expiry_header) - now()->timestamp;
        }
        return $ttl > 0 ? $ttl : null;
    }
}
