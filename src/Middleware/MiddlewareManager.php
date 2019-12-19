<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\Sheet\Middleware;


class MiddlewareManager {
    public function passThrough(array $middlewares, $row, $index) {
        foreach ($middlewares as $middleware){
            if($middleware instanceof \Closure){
                if($row){
                    $row = $middleware($row, $index);
                }
            }else{
                if($row){
                    $row = $middleware->handle($row, $index);
                }
            }
        }
        
        return $row;
    }
}