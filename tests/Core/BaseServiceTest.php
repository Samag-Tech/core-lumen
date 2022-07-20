<?php namespace Tests\Core;

use Tests\TestCase;
use Tests\Support\Utils;
use SamagTech\CoreLumen\Models\Log;
use SamagTech\CoreLumen\Contracts\Logger;
use SamagTech\CoreLumen\Core\BaseService;
use SamagTech\CoreLumen\Handlers\DBLogger;
use SamagTech\CoreLumen\Core\BaseRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use SamagTech\CoreLumen\Exceptions\ResourceNotFoundException;

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

        $logger = $this->getMockForAbstractClass(Logger::class);

        $mock = $this->getMockForAbstractClass(BaseService::class, [$repository, $logger]);

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

        $logger = $this->getMockForAbstractClass(Logger::class);

        $mock = $this->getMockForAbstractClass(BaseService::class, [$repository, $logger]);

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

        $logger = $this->getMockForAbstractClass(Logger::class);

        app()->instance(Logger::class, $logger);

        $mock = $this->getMockForAbstractClass(BaseService::class, [$repository, $logger]);

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

        $logger = $this->getMockForAbstractClass(Logger::class);

        $logger->expects($this->once())->method('write');

        $mock = $this->getMockForAbstractClass(BaseService::class, [$repository, $logger]);

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

        $logger = $this->getMockForAbstractClass(Logger::class);

        $mock = $this->getMockForAbstractClass(BaseService::class, [$repository, $logger]);

        $this->assertFalse($mock->delete(1));
    }


}