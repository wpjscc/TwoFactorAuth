<?php

declare(strict_types=1);

namespace RobThree\Auth\Providers\Qr;

use Exception;

use function React\Async\await;

use React\Http\Browser;
use React\Socket\Connector;

abstract class BaseHTTPQRCodeProvider implements IQRCodeProvider
{
    protected bool $verifyssl = true;

    protected function getContent(string $url): string
    {
        $connector = new Connector(array(
            'timeout' => 10.0,
            'tls' => array(
                'verify_peer' => $this->verifyssl,
                'verify_peer_name' => $this->verifyssl,
            ),
        ));
        $browser = new Browser($connector);

        try {
            $response = await($browser->withTimeout(10)->get($url, array(
                'User-Agent' => 'TwoFactorAuth',
            )));
        } catch (Exception $ex) {
            throw new QRException($ex->getMessage());
        }

        return (string) $response->getBody();
    }
}
