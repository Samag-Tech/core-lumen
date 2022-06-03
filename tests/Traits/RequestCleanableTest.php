<?php


use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use \SamagTech\CoreLumen\Traits\RequestCleanable;
use SamagTech\CoreLumen\Exceptions\CoreException;

class RequestCleanableTest extends TestCase {


    protected function setUp(): void {
        $this->mock = $this->getMockForTrait(RequestCleanable::class);
    }

    /**
     * @test
     */
    public function is_only_used_fields_is_empty () {

        $this->expectException(CoreException::class);

        $ref = new ReflectionMethod(get_class($this->mock), 'cleanRequest');

        $ref->setAccessible(true);
        $ref->invoke($this->mock, new Request(['name' => 'test']));

    }

    /**
     * @test
     */
    public function is_only_used_fields_is_not_empty () {

        $ref = new ReflectionProperty(get_class($this->mock), 'onlyUsedFields');
        $ref->setAccessible(true);
        $ref->setValue($this->mock, ['key', 'key1']);

        $refMethod = new ReflectionMethod(get_class($this->mock), 'cleanRequest');
        $refMethod->setAccessible(true);

        $this->assertEquals(['key' => 'val', 'key1' => 'val'], $refMethod->invoke($this->mock, new Request(['key' => 'val', 'key1' => 'val', 'key2' => 'val'])));
    }

}