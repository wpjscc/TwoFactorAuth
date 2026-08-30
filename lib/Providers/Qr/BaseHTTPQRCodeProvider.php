<?php

declare(strict_types=1);

namespace RobThree\Auth\Providers\Qr;

use Exception;
use Psr\Http\Message\ResponseInterface;

use function React\Async\await;

use React\Http\Browser;
use React\Socket\Connector;

abstract class BaseHTTPQRCodeProvider implements IQRCodeProvider
{
    protected bool $verifyssl = true;

    protected function getContent(string $url): string
    {
        $connector = new Connector(array(
            'timeout' => 10,
            'tls' => array(
                'verify_peer' => $this->verifyssl,
                'verify_peer_name' => $this->verifyssl,
            ),
        ));

        $browser = (new Browser($connector))
            ->withTimeout(10)
            ->withHeader('User-Agent', 'TwoFactorAuth');

        try {
            /** @var ResponseInterface $response */
            $response = await($browser->get($url));
        } catch (Exception $e) {
            throw new QRException($e->getMessage());
        }

        return (string) $response->getBody();
    }
}
