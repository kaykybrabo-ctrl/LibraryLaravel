<?php
namespace App\Services;

use App\Exceptions\ClientException;
use App\Models\User;
use Illuminate\Support\Facades\Http;

/**
 * Domain service responsible for handling image uploads.
 */
class ImageService
{
    /**
     * Upload an image for the given user using validated UploadImageRequest data.
     *
     * @param array<string,mixed> $data
     */
    public function uploadImage(User $user, array $data): string
    {
        $target = (string) $data['target'];
        $filename = (string) $data['filename'];
        $fileData = (string) $data['fileData'];

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if ($ext === '' || !in_array($ext, $allowed, true)) {
            throw new ClientException(__('errors.invalid_image_file'), 'invalid_image_file');
        }

        $base64 = $fileData;
        $mime = null;
        if (str_starts_with($fileData, 'data:image/')) {
            $commaPos = strpos($fileData, ',');
            if ($commaPos === false) {
                throw new ClientException(__('errors.invalid_image_data'), 'invalid_image_data');
            }
            $header = substr($fileData, 0, $commaPos);
            $base64 = substr($fileData, $commaPos + 1);
            if (preg_match('#^data:(image/[^;]+);base64$#', $header, $m)) {
                $mime = $m[1];
            }
        }

        if ($mime === null) {
            $extForMime = $ext === 'jpg' ? 'jpeg' : $ext;
            $mime = 'image/' . $extForMime;
        }

        $binary = base64_decode($base64, true);
        if ($binary === false) {
            throw new ClientException(__('errors.invalid_image_data'), 'invalid_image_data');
        }

        $folder = $target === 'book' ? 'pedbook/books' : 'pedbook/profiles';
        $nameOnly = pathinfo($filename, PATHINFO_FILENAME);
        if ($nameOnly === '') {
            $nameOnly = 'image';
        }
        $safeName = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $nameOnly);
        if ($safeName === '' || $safeName === '_') {
            $safeName = 'image';
        }
        $uploadName = $safeName . '.' . $ext;

        $resp = Http::attach('file', $binary, $uploadName)
            ->post(
                'https://api.cloudinary.com/v1_1/ddfgsoh5g/image/upload',
                [
                    'upload_preset' => 'pedbook_unsigned',
                    'folder' => $folder,
                ]
            );

        if (!$resp->ok()) {
            throw new ClientException(__('errors.cloudinary_upload_failed'), 'cloudinary_upload_failed');
        }

        $json = $resp->json();
        $publicId = is_array($json) ? ($json['public_id'] ?? null) : null;
        if (!$publicId) {
            throw new ClientException(__('errors.cloudinary_invalid_response'), 'cloudinary_invalid_response');
        }

        return (string) $publicId;
    }
}
