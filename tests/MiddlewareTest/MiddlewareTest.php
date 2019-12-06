<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\Sheet\Tests\MiddlewareTest;


use Mahmud\Sheet\Middleware\TrimMiddleware;
use Mahmud\Sheet\SheetReader;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase {
    /**
     * @test
     */
    public function it_can_trim_data() {
        $data = [];
        SheetReader::makeFromCsv(__DIR__ . "/../dummy/files/test1.csv")
            ->columns(['id', 'name', 'age'])
            ->ignoreRow(0)
            ->applyMiddleware(new TrimMiddleware())
            ->onEachRow(function ($row, $index) use (&$data) {
                $data[] = $row;
            })->read();
        
        $this->assertEquals([
            'id'   => 2,
            'name' => 'Raju',
            'age'  => "",
        ], $data[1]);
    }
}