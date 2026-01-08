<?php
namespace App\GraphQL\Mutations\Concerns;

use App\Http\Requests\UploadImageRequest;
use App\Services\ImageService;

trait ImageMutations
{
    public function uploadImage($rootValue, array $args): string
    {
        $this->requireUser();

        $payload = $args;
        $payload['target'] = strtolower((string) ($args['target'] ?? ''));
        $data = $this->validatedInput($payload, new UploadImageRequest());

        /** @var ImageService $service */
        $service = app(ImageService::class);

        return $service->uploadImage($this->requireUser(), $data);
    }
}
