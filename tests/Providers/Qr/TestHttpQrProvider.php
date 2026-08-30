<?php

declare(strict_types=1);

namespace Tests\Providers\Qr;

use RobThree\Auth\Providers\Qr\BaseHTTPQRCodeProvider;

class TestHttpQrProvider extends BaseHTTPQRCodeProvider
{
    public function __construct(protected bool $verifyssl = true)
    {
    }

    public function getMimeType(): string
    {
        return 'text/plain';
    }

    public function getQRCodeImage(string $qrText, int $size): string
    {
        return $this->getContent($qrText);
    }

    public function fetch(string $url): string
    {
        return $this->getContent($url);
    }
}
