<?php


use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use \SamagTech\CoreLumen\Traits\RequestCleanable;
use SamagTech\CoreLumen\Exceptions\CoreException;
use Tests\Support\Utils;

class RequestCleanableTest extends TestCase {


    protected function setUp(): void {
        $this->mock = $this->getMockForTrait(RequestCleanable::class);
    }

    /**
     * @test
     */
    public function only_used_fields_is_empty () {

        $this->expectException(CoreException::class);

        Utils::usePrivateMethod($this->mock, 'cleanRequest', new Request(['name' => 'test']));
    }

    /**
     * @test
     */
    public function is_only_used_fields_is_not_empty () {

        Utils::usePrivateProperty($this->mock,'onlyUsedFields',  ['key', 'key1'] );

        $request = new Request(['key' => 'val', 'key1' => 'val', 'key2' => 'val']);

        $this->assertEquals(
            ['key' => 'val', 'key1' => 'val'],
            Utils::usePrivateMethod($this->mock, 'cleanRequest', $request)
        );
    }

    /**
     * @test
     */
    public function empty_clean_request_with_no_match_keys() {

        Utils::usePrivateProperty($this->mock,'onlyUsedFields',  ['key', 'key1'] );

        $this->assertEmpty(Utils::usePrivateMethod($this->mock, 'cleanRequest', new Request()));

        Utils::usePrivateProperty($this->mock,'onlyUsedFields',  ['key', 'key1'] );

        $this->assertEmpty(Utils::usePrivateMethod($this->mock, 'cleanRequest', new Request(['key3'])));
    }
}