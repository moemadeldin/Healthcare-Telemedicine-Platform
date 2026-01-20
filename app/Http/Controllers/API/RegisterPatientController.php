<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Actions\Patient\CreatePatientAction;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Utilities\APIResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final readonly class RegisterPatientController
{
    use APIResponses;

    public function __invoke(StorePatientRequest $request, CreatePatientAction $action): JsonResponse
    {
        /** @var array{first_name: string, last_name: string, email: string, password: string} $validated */
        $validated = $request->validated();

        return $this->success(
            $action->execute($validated),
            'Patient Created Successfuly, Check your mail for verification.',
            Response::HTTP_CREATED
        );
    }
}
