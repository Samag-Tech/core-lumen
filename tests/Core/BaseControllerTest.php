<?php namespace Tests\Core;

use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use SamagTech\CoreLumen\Contracts\Logger;
use Tests\Support\Utils;
use Tests\Support\DummyService;
use SamagTech\CoreLumen\Models\ServiceKey;
use SamagTech\CoreLumen\Core\BaseController;
use Tests\Support\DummyServiceSuffix;

class BaseControllerTest extends TestCase {

    // function setUp(): void
    // {
    //     $logger = $this->getMockForAbstractClass(Logger::class, mockedMethods: ['setUser']);

    //     app()->instance(Logger::class, $logger);
    // }

    /**
     * @test
     */
    public function make_default_service () {

        $serviceKey = $this->getMockBuilder(ServiceKey::class)
            ->addMethods(['find'])
            ->getMock();

        $uuid = Uuid::uuid4();

        $serviceKey->method('find')->willReturn($this->returnValue((object) [
            'id'        => $uuid,
            'suffix'    => 'Foo'
        ]));


        // $logger = $this->getMockForAbstractClass(Logger::class);
        $logger = $this->getMockForAbstractClass(Logger::class, mockedMethods: ['setUser']);

        app()->instance(Logger::class, $logger);

        $baseController = new class($serviceKey, $logger) extends BaseController {

            protected string $model = 'Tests\Support\DummyModel';

            protected string $defaultService = 'Tests\Support\DummyService';
        };

        $this->assertInstanceOf(DummyService::class, $baseController->makeService($serviceKey));
        $this->assertInstanceOf(DummyService::class, $baseController->makeService($serviceKey, $uuid));
    }

    /**
     * @test
     *
     */
    public function make_another_service () {

        $serviceKey = $this->getMockBuilder(ServiceKey::class)
            ->addMethods(['find'])
            ->getMock();

        $uuid = Uuid::uuid4();
        $serviceKey->method('find')->willReturn($this->returnValue((object) [
            'id'        => $uuid,
            'suffix'    => 'Suffix'
        ]));


        $logger = $this->getMockForAbstractClass(Logger::class, mockedMethods: ['setUser']);

        app()->instance(Logger::class, $logger);

        $baseController = new class($serviceKey, $logger) extends BaseController {

            protected string $model = 'Tests\Support\DummyModel';

            protected string $defaultService = 'Tests\Support\DummyService';
        };

        $this->assertInstanceOf(DummyService::class, $baseController->makeService($serviceKey));
        $this->assertInstanceOf(DummyServiceSuffix::class, $baseController->makeService($serviceKey, $uuid));
    }
}