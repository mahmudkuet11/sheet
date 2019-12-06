<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmudkuet11@gmail.com>.
 */

namespace Mahmud\Sheet\Tests;

use Mahmud\Sheet\SheetReader;
use PHPUnit\Framework\TestCase;

class SheetReaderTest extends TestCase {
    /**
     * @test
     */
    public function it_can_read_csv_file() {
        $data = [];
        SheetReader::makeFromCsv(__DIR__ . "/dummy/files/test1.csv")
            ->columns(['id', 'name', 'age'])
            ->onEachRow(function ($row, $index) use (&$data) {
                if ($index === 1) {
                    $data = $row;
                }
            })->read();
        $this->assertEquals([
            'id'   => 1,
            'name' => 'Mahmud',
            'age'  => 26
        ], $data);
    }
    
    /**
     * @test
     */
    public function middleware_can_be_applied_to_row() {
        $data = [];
        SheetReader::makeFromCsv(__DIR__ . "/dummy/files/test1.csv")
            ->columns(['id', 'name', 'age'])
            ->ignoreRow(0)
            ->applyMiddleware(function ($row) {
                $row['url'] = 'https://mahmud.live';
                
                return $row;
            })
            ->onEachRow(function ($row, $index) use (&$data) {
                $data[] = $row;
            })->read();
        
        $this->assertEquals([
            'id'   => 1,
            'name' => 'Mahmud',
            'age'  => 26,
            'url'  => 'https://mahmud.live'
        ], $data[0]);
    }
}