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
}