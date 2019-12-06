<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\Sheet\Middleware;


class TrimMiddleware {
    public function handle($row) {
        foreach ($row as $key => $val) {
            $row[$key] = $val ? trim($val) : $val;
        }
        
        return $row;
    }
}