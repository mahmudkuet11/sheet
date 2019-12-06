<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\Sheet\Middleware;


class MiddlewareManager {
    public function passThrough(array $middlewares, $row) {
        if(! $row) return null;
        
        foreach ($middlewares as $middleware){
            if($middleware instanceof \Closure){
                $row = $middleware($row);
            }else{
                $row = $middleware->handle($row);
            }
        }
        
        return $row;
    }
}