<?php

declare(strict_types=1);

namespace PhpCfdi\CsfScraper;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class Scraper
{
    private ClientInterface $client;
    public static string $url = 'https://siat.sat.gob.mx/app/qr/faces/pages/mobile/validadorqr.jsf?D1=10&D2=1&D3=%s_%s';

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     *
     * @return array<int|string, array<int|string, mixed>|string>
     *
     * @throws RuntimeException
     */
    public function data(string $rfc, string $idCIF): array
    {
        $isFisica = 13 === strlen($rfc);
        try {
            $uri = sprintf(self::$url, $idCIF, $rfc);

            $html = $this->client->request('GET', $uri)
                ->getBody()
                ->getContents();
            return (new DataExtractor($html))->extract($isFisica);
        } catch (GuzzleException $exception) {
            throw new \RuntimeException('The request has failed', 0, $exception);
        }
    }
}
