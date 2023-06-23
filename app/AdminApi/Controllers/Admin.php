<?php

namespace App\AdminApi\Controllers;

use App\AdminApi\Controllers\Traits\AccumulatorTrait;
use App\AdminApi\Controllers\Traits\AcquirerBinTrait;
use App\AdminApi\Controllers\Traits\TopEarnersTrait;
use App\AdminApi\Helpers\Auth\CurrentAdmin;
use App\AdminApi\Policies\BaseAccessPolicy;
use App\Infrastructure\Bus\DispatchesServiceRequests;
use App\Infrastructure\Validation\ValidateRequestTrait;
use App\Rules\FileUpload\Traits\FileImportRulesTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Response as DownloadResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller;

class Admin extends Controller
{
    use DispatchesServiceRequests;
    use ValidateRequestTrait;
    use FileImportRulesTrait;
    use AcquirerBinTrait;
    use TopEarnersTrait;
    use AccumulatorTrait;

    protected function authorize(string ...$permission): void
    {
        abort_if(
            !CurrentAdmin::can(...$permission),
            403,
            'You need  ' . implode(',', $permission) . ' to access this resource.'
        );
    }

    protected function checkAccessPolicy(BaseAccessPolicy $accessPolicy): void
    {
        $isSuperAdmin = CurrentAdmin::canManageAllPartners();

        $allowed = $accessPolicy->setCanManageAllPartners($isSuperAdmin)
            ->unless(
                $isSuperAdmin,
                fn(BaseAccessPolicy $p) => $p->setManagedPartnerIDs(CurrentAdmin::managedPartnerIds())
            )
            ->check(CurrentAdmin::admin());

        abort_if(!$allowed, 403);
    }

    protected function response200($response): Response
    {
        if ($response instanceof JsonResource) {
            return $response->response()->setStatusCode(Response::HTTP_OK);
        }

        return response($response, Response::HTTP_OK);
    }

    // Entity created
    protected function response201($response): Response
    {
        if ($response instanceof JsonResource) {
            return $response->response()->setStatusCode(Response::HTTP_CREATED);
        }

        return response($response, Response::HTTP_CREATED);
    }

    // Entity updated
    protected function response202($response): Response
    {
        if ($response instanceof JsonResource) {
            return $response->response()->setStatusCode(Response::HTTP_ACCEPTED);
        }

        return response($response, Response::HTTP_ACCEPTED);
    }

    // Entity deleted
    protected function response204(): Response
    {
        return response([], Response::HTTP_NO_CONTENT);
    }

    // Logic exception
    protected function response418(string $message): Response
    {
        return response(['message' => $message], Response::HTTP_I_AM_A_TEAPOT);
    }

    protected function responseCsv($fileName): BinaryFileResponse
    {
        return DownloadResponse::download($fileName, basename($fileName));
    }

    protected function validateBasic(array $data, array $rules, array $messages = [], array $customAttributes = [], \Closure $callBack = null)
    {
        $validatorFactory = Validator::getFacadeRoot();

        if ($callBack) {
            $callBack($validatorFactory);
        }

        $validator = $validatorFactory->make($data, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
