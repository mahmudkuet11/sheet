<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmudkuet11@gmail.com>.
 */

namespace Mahmud\Sheet\Tests;

use Mahmud\Sheet\SheetReader;
use Mahmud\Sheet\Tests\dummy\Middleware\IndexCheckMiddleware;
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
    
    /**
     * @test
     */
    public function callback_wont_be_called_if_middleware_returns_null() {
        $data = [];
        SheetReader::makeFromCsv(__DIR__ . "/dummy/files/test1.csv")
            ->columns(['id', 'name', 'age'])
            ->ignoreRow(0)
            ->applyMiddleware(function ($row) {
                if($row['id'] == 2){
                    return null;
                }
                return $row;
            })
            ->onEachRow(function ($row, $index) use (&$data) {
                $data[] = $row;
            })->read();
    
        $this->assertEquals(1, count($data));
    }
    
    /**
     * @test
     */
    public function middleware_handler_will_receive_current_index_as_second_argument() {
        $indexes1 = [];
        $indexes2 = [];
        SheetReader::makeFromCsv(__DIR__ . "/dummy/files/test1.csv")
            ->columns(['id', 'name', 'age'])
            ->ignoreRow(0)
            ->applyMiddleware([
                function ($row, $index) use (&$indexes1) {
                    if(isset($index)){
                        $indexes1[] = $index;
                    }
                    return $row;
                },
                new IndexCheckMiddleware(function($index) use (&$indexes2){
                    if(isset($index)){
                        $indexes2[] = $index;
                    }
                })
            ])
            ->onEachRow(function ($row, $index) use (&$data) {
                $data[] = $row;
            })->read();
        
        $this->assertTrue(count($indexes1) > 0);
        $this->assertTrue(count($indexes2) > 0);
    }
    
    /**
     * @test
     */
    public function apply_middleware_can_be_called_multiple_times() {
        $isChanged = true;
        SheetReader::makeFromCsv(__DIR__ . "/dummy/files/test1.csv")
            ->columns(['id', 'name', 'age'])
            ->ignoreRow(0)
            ->applyMiddleware(function($row){
                $row['name'] = "TEST";
                
                return $row;
            })
            ->applyMiddleware(function($row){
                $row['age'] = "TEST";
    
                return $row;
            })
            ->onEachRow(function ($row, $index) use (&$isChanged) {
                if($row['name'] !== 'TEST' || $row['age'] !== "TEST"){
                    $isChanged = false;
                }
            })->read();
        
        $this->assertTrue($isChanged);
    }
    
    /**
     * @test
     */
    public function total_rows_can_be_counted() {
        $count = SheetReader::makeFromCsv(__DIR__ . "/dummy/files/test1.csv")->totalRows();
        $this->assertEquals(3, $count);
    }
}