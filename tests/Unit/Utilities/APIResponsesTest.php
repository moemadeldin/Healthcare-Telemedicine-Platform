<?php

declare(strict_types=1);

namespace Tests\Unit\Utilities;

use App\Utilities\APIResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class APIResponsesTest extends TestCase
{
    private object $traitObject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->traitObject = new class
        {
            use APIResponses;

            public function successResponse(array|object $data, string $message, int $code = Response::HTTP_OK): JsonResponse
            {
                return $this->success($data, $message, $code);
            }

            public function failResponse(string $message, int $code = Response::HTTP_BAD_REQUEST): JsonResponse
            {
                return $this->fail($message, $code);
            }

            public function noContentResponse(): Response
            {
                return $this->noContent();
            }
        };
    }

    #[Test]
    public function success_returns_json_response_with_correct_structure(): void
    {
        $data = ['user' => 'John Doe'];
        $message = 'Operation successful';

        $response = $this->traitObject->successResponse($data, $message);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode((string) $response->getContent(), true);

        $this->assertEquals('Success', $content['status']);
        $this->assertEquals($message, $content['message']);
        $this->assertEquals($data, $content['data']);
    }

    #[Test]
    public function success_accepts_custom_status_code(): void
    {
        $data = ['id' => 1];
        $message = 'Resource created';
        $code = Response::HTTP_CREATED;

        $response = $this->traitObject->successResponse($data, $message, $code);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    #[Test]
    public function success_works_with_array_data(): void
    {
        $data = ['name' => 'Test', 'email' => 'test@example.com'];
        $message = 'Success';

        $response = $this->traitObject->successResponse($data, $message);

        $content = json_decode((string) $response->getContent(), true);

        $this->assertIsArray($content['data']);
        $this->assertEquals($data, $content['data']);
    }

    #[Test]
    public function success_works_with_object_data(): void
    {
        $data = (object) ['name' => 'Test', 'email' => 'test@example.com'];
        $message = 'Success';

        $response = $this->traitObject->successResponse($data, $message);

        $content = json_decode((string) $response->getContent());

        $this->assertIsObject($content->data);
        $this->assertEquals('Test', $content->data->name);
    }

    #[Test]
    public function success_returns_200_by_default(): void
    {
        $response = $this->traitObject->successResponse(['data' => 'test'], 'Success');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    #[Test]
    public function fail_returns_json_response_with_correct_structure(): void
    {
        $message = 'Operation failed';

        $response = $this->traitObject->failResponse($message);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $content = json_decode((string) $response->getContent(), true);

        $this->assertEquals('Failed', $content['status']);
        $this->assertEquals($message, $content['message']);
        $this->assertArrayNotHasKey('data', $content);
    }

    #[Test]
    public function fail_accepts_custom_status_code(): void
    {
        $message = 'Not found';
        $code = Response::HTTP_NOT_FOUND;

        $response = $this->traitObject->failResponse($message, $code);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    #[Test]
    public function fail_returns_400_by_default(): void
    {
        $response = $this->traitObject->failResponse('Error');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    #[Test]
    public function fail_handles_empty_message(): void
    {
        $response = $this->traitObject->failResponse('');

        $content = json_decode((string) $response->getContent(), true);

        $this->assertEquals('', $content['message']);
    }

    #[Test]
    public function no_content_returns_response_with_204_status(): void
    {
        $response = $this->traitObject->noContentResponse();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    #[Test]
    public function no_content_returns_empty_body(): void
    {
        $response = $this->traitObject->noContentResponse();

        $this->assertEmpty($response->getContent());
    }
}
