<?php

namespace App\Aws;

use Exception;
use Aws\S3\S3Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;

class S3Manager extends AbstractController
{
    /**
     * @var S3Client $s3
     */
    private $s3;

    /**
     * @var string $bucket
     */
    private $bucket;

    /**
     * @var string $prefix
     */
    private $prefix;

    /**
     * S3Manager constructor.
     * @param S3Client $s3
     * @param $bucket
     * @param $prefix
     */
    public function __construct(S3Client $s3, $bucket, $prefix)
    {
        $this->s3 = $s3;
        $this->bucket = $bucket;
        $this->prefix = $prefix;
    }

    /**
     * @param File
     *
     * @return array
     *
     * @throws Exception
     */
    public function uploadPicture($image, $extension)
    {
        try {
            $key = $this->prefix . '/' .  md5(uniqid()) . '.' . $extension;

            $result = $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'Body' => $image,
                'ACL' => 'public-read-write',
                'ContentType' => $extension,
            ]);

            return [
                'url' => $result->get('ObjectURL'),
                'key' => $key
            ];
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param string $key
     *
     * @throws Exception
     */
    public function deletePicture(string $key)
    {
        if (!$key) {
            throw new \Exception('Picture not found.');
        }

        try {
            $this->s3->deleteMatchingObjects($this->bucket, $key);
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
