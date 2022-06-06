<?php namespace Tests\Core;

use Illuminate\Http\Resources\Json\JsonResource;
use SamagTech\CoreLumen\Core\BaseModel;
use SamagTech\CoreLumen\Core\BaseService;
use SamagTech\CoreLumen\Exceptions\ResourceNotFoundException;
use Tests\Support\Utils;
use Tests\TestCase;

class BaseServiceTest extends TestCase {

    /**
     * @test
     */
    public function show_success_one_resource() {

        $repository = $this->getMockBuilder(BaseModel::class)
            ->addMethods(['find'])
            ->getMock();

        $repository->method('find')->willReturnSelf();

        $repository->id = 1;

        $mock = $this->getMockForAbstractClass(BaseService::class, [$repository]);

        Utils::usePrivateProperty($mock, 'jsonResource', JsonResource::class);

        $this->assertInstanceOf(JsonResource::class, $mock->show(1));
    }

    /**
     * @test
     */
    public function show_fail_one_resource() {

        $this->expectException(ResourceNotFoundException::class);

        $repository = $this->getMockBuilder(BaseModel::class)
            ->addMethods(['find'])
            ->getMock();

        $repository->method('find')->willReturn(null);

        $mock = $this->getMockForAbstractClass(BaseService::class, [$repository]);

        $mock->show(1);
    }
}