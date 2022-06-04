<?php

use Tests\Support\Utils;
use Illuminate\Http\Request;
use SamagTech\CoreLumen\Traits\WithValidation;
use SamagTech\CoreLumen\Core\BaseValidationRequest;
use SamagTech\CoreLumen\Exceptions\CoreException;
use Tests\TestCase;

class WithValidationTest extends TestCase {


    protected function setUp(): void {
        parent::setUp();

        $this->mock = $this->getMockForTrait(WithValidation::class);
    }

    /**
     * @test
     */
    public function validation_with_no_validation_rule () {
        $this->assertTrue(Utils::usePrivateMethod($this->mock, 'validation', new Request()));
    }

    /**
     * @test
     */
    public function validation_with_validation_rule () {

        $req = new Request(['name' => 'test', 'name2' => 'test2']);

        $firstValidation = new class($req) extends BaseValidationRequest {
            protected function rules() : array {
                return [
                    'name' => 'required'
                ];
            }

            protected function messages(): array
            {
                return [
                    'required' => 'The :attribute field is required.',
                ];
            }
        };

        $secondValidation = new class($req) extends BaseValidationRequest {

            protected function rules() : array {
                return [
                    'name2' => 'required'
                ];
            }

            protected function messages(): array
            {
                return [
                    'required' => 'The :attribute field is required.',
                ];
            }
        };


        $validations = [
            $firstValidation,
            $secondValidation,
        ];

        Utils::usePrivateProperty($this->mock, 'validations', $validations);

        $this->assertTrue(Utils::usePrivateMethod($this->mock,'validation', $req));

    }

    /**
     * @test
     */
    public function failed_validation_response () {

        $req = new Request(['name' => '']);


        $rule = new class($req) extends BaseValidationRequest {
            protected function rules() : array {
                return [
                    'name' => 'required'
                ];
            }

            protected function messages(): array
            {
                return [
                    'required' => 'The :attribute field is required.',
                ];
            }
        };

        Utils::usePrivateProperty($this->mock, 'validations', [$rule]);

        $this->assertFalse(Utils::usePrivateMethod($this->mock,'validation', $req));
        $this->assertEquals(['The name field is required.'],$this->mock->getValidationErrors() );
    }

    /**
     * @test
     */
    public function exception_with_validation_different_extends () {

        $req = new Request();

        $rule = new class ($req) {};


        $this->expectException(CoreException::class);
        Utils::usePrivateProperty($this->mock, 'validations', [$rule]);
        Utils::usePrivateMethod($this->mock,'validation', $req);
    }
}