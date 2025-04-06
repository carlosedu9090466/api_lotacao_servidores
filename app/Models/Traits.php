<?php

namespace App\Models;
use Aws\S3\S3Client;

use Illuminate\Database\Eloquent\Model;

class Traits extends Model
{
    public function generatePresignedUrl(string $path, int $expiryMinutes = 5): string
    {
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region'),
            'endpoint' => config('filesystems.disks.s3.url'),
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
            'use_path_style_endpoint' => true,
        ]);

        $command = $s3->getCommand('GetObject', [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $path
        ]);

        return (string) $s3->createPresignedRequest($command, "+{$expiryMinutes} minutes")->getUri();
    }
}
