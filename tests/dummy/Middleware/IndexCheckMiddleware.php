<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\Sheet\Tests\dummy\Middleware;


class IndexCheckMiddleware {
    private $callback;
    
    public function __construct($callback) {
        $this->callback = $callback;
    }
    
    public function handle($row, $index) {
        call_user_func($this->callback, $index);
        return $row;
    }
}