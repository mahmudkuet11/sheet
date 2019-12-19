<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\Sheet\Tests\MiddlewareTest;


use Mahmud\Sheet\Middleware\MiddlewareManager;
use PHPUnit\Framework\TestCase;

class MiddlewareManagerTest extends TestCase {
    /**
     * @test
     */
    public function data_can_be_passed_through_the_middleware() {
        $middleware = \Mockery::mock('TestMiddleware')
            ->shouldReceive('handle')
            ->once()
            ->andReturn([
                'foo' => 'foo1',
                'bar' => 'bar1'
            ])->getMock();
        
        $manager = new MiddlewareManager();
        $data = $manager->passThrough([$middleware], [
            'foo' => 'bar',
            'bar' => 'baz'
        ], 0);
        
        $this->assertEquals([
            'foo' => 'foo1',
            'bar' => 'bar1'
        ], $data);
    }
    
    /**
     * @test
     */
    public function closure_can_be_passed_as_middleware() {
        $middleware = function ($row) {
            return [
                'foo' => 'foo1',
                'bar' => 'bar1'
            ];
        };
        $manager = new MiddlewareManager();
        $data = $manager->passThrough([$middleware], [
            'foo' => 'bar',
            'bar' => 'baz'
        ], 0);
        
        $this->assertEquals([
            'foo' => 'foo1',
            'bar' => 'bar1'
        ], $data);
    }
    
    protected function tearDown() {
        \Mockery::close();
        parent::tearDown();
    }
}