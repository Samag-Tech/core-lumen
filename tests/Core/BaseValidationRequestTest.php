<?php namespace Tests\Core;

use Tests\TestCase;
use Illuminate\Http\Request;
use SamagTech\CoreLumen\Core\BaseValidationRequest;

class BaseValidationRequestTest extends TestCase {

    /**
     * @test
     */
    public function request_is_valid() {

        $request = new Request(['key' => 'value']);

        $validationRequest = new class ($request) extends BaseValidationRequest {

            protected function rules() : array {
                return [
                    'key'   => 'required'
                ];
            }

            protected function messages() : array {
                return [
                    'required'   => 'The :attribute'
                ];
            }
        };

        $this->assertTrue($validationRequest->isValid());
        $this->assertEmpty($validationRequest->getErrors());
    }

    /**
     * @test
     */
    public function request_is_not_valid() {

        $request = new Request(['key' => '']);

        $validationRequest = new class ($request) extends BaseValidationRequest {

            protected function rules() : array {
                return [
                    'key'   => 'required'
                ];
            }

            protected function messages() : array {
                return [
                    'required'   => 'The :attribute'
                ];
            }
        };

        $this->assertFalse($validationRequest->isValid());
        $this->assertNotEmpty($validationRequest->getErrors());
        $this->assertEquals(['The key'], $validationRequest->getErrors());
    }
}