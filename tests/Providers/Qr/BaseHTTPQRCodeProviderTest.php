<?php

declare(strict_types=1);

namespace Tests\Providers\Qr;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use RobThree\Auth\Providers\Qr\QRException;

class BaseHTTPQRCodeProviderTest extends TestCase
{
    public function testGetContentReturnsResponseBody(): void
    {
        $userAgent = '';
        $socket = new SocketServer('127.0.0.1:0');
        $server = new HttpServer(function (ServerRequestInterface $request) use (&$userAgent) {
            $userAgent = $request->getHeaderLine('User-Agent');

            return Response::plaintext('qr-image-bytes');
        });
        $server->listen($socket);

        $address = str_replace('tcp://', 'http://', (string) $socket->getAddress());
        $provider = new TestHttpQrProvider();

        $this->assertSame('qr-image-bytes', $provider->fetch($address));
        $this->assertSame('TwoFactorAuth', $userAgent);

        $socket->close();
    }

    public function testGetContentThrowsQrExceptionOnFailure(): void
    {
        $provider = new TestHttpQrProvider();

        $this->expectException(QRException::class);
        $provider->fetch('http://127.0.0.1:1/');
    }
}
