<?php namespace Tests\Core;

use Illuminate\Http\Resources\Json\JsonResource;
use SamagTech\CoreLumen\Core\BaseRepository;
use SamagTech\CoreLumen\Core\BaseService;
use SamagTech\CoreLumen\Exceptions\ResourceNotFoundException;
use Tests\Support\Utils;
use Tests\TestCase;

class BaseServiceTest extends TestCase {

    /**
     * @test
     */
    public function show_success_one_resource() {

        $repository = $this->getMockBuilder(BaseRepository::class)
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

        $repository = $this->getMockBuilder(BaseRepository::class)
            ->addMethods(['find'])
            ->getMock();

        $repository->method('find')->willReturn(null);

        $mock = $this->getMockForAbstractClass(BaseService::class, [$repository]);

        $mock->show(1);
    }

    /**
     * @test
     */
    public function delete_fail_with_resource_not_found() {

        $this->expectException(ResourceNotFoundException::class);

        $repository = $this->getMockBuilder(BaseRepository::class)
            ->addMethods(['find'])
            ->getMock();

        $repository->method('find')->willReturn(null);

        $mock = $this->getMockForAbstractClass(BaseService::class, [$repository]);

        $mock->delete(1);
    }

    /**
     * @test
     */
    public function delete_with_success() {

        $repository = $this->getMockBuilder(BaseRepository::class)
            ->addMethods(['find'])
            ->onlyMethods(['delete'])
            ->getMock();

        $repository->method('find')->willReturn($this->returnSelf());

        $repository->method('delete')->willReturn(true);

        $mock = $this->getMockForAbstractClass(BaseService::class, [$repository]);

        $this->assertTrue($mock->delete(1));
    }

    /**
     * @test
     */
    public function delete_with_fail() {

        $repository = $this->getMockBuilder(BaseRepository::class)
            ->addMethods(['find'])
            ->onlyMethods(['delete'])
            ->getMock();

        $repository->method('find')->willReturn($this->returnSelf());

        $repository->method('delete')->willReturn(false);

        $mock = $this->getMockForAbstractClass(BaseService::class, [$repository]);

        $this->assertFalse($mock->delete(1));
    }


}