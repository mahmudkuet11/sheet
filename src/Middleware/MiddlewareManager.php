<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\Sheet\Middleware;


class MiddlewareManager {
    public function passThrough(array $middlewares, $row) {
        foreach ($middlewares as $middleware){
            if($middleware instanceof \Closure){
                if($row){
                    $row = $middleware($row);
                }
            }else{
                if($row){
                    $row = $middleware->handle($row);
                }
            }
        }
        
        return $row;
    }
}