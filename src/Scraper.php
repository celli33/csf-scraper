<?php

declare(strict_types=1);

namespace PhpCfdi\CsfScraper;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

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
     * @return array <string, string>
     * @throws RuntimeException
     */
    public function data(string $rfc, string $idCIF): array
    {
        try {
            $uri = sprintf(self::$url, $idCIF, $rfc);

            $html = $this->client->request('GET', $uri)
                ->getBody()
                ->getContents();
            file_put_contents(__DIR__ . '/../tests/_files/scrap.html', $html);
            return $this->extractData($html);
        } catch (GuzzleException $exception) {
            throw new \RuntimeException('The request has failed', 0, $exception);
        }
    }

    /**
     *
     * @return array<string, string>
     * @throws RuntimeException
     */
    private function extractData(string $html): array
    {
        $html = $this->clearHtml($html);

        $crawler = new Crawler($html);
        $elements = $crawler->filter('td[role="gridcell"]');

        $values = [];

        $elements->each(function (Crawler $elem, int $index) use (&$values): void {
            if (0 === $elem->filter('span')->count()) {
                $keyName = $this->getKeyNameByIndex($index);

                if (null !== $keyName) {
                    $values[$keyName] = trim($elem->text());
                }
            }
        });

        return $values;
    }

    private function clearHtml(string $html): string
    {
        $html = str_replace('<?xml version="1.0" encoding="UTF-8" ?>', '', $html);
        return str_replace('<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />', '', $html);
    }

    private function getKeyNameByIndex(int $index): ?string
    {
        return match ($index) {
            2 => 'razon_social',
            4 => 'regimen_de_capital',
            6 => 'fecha_constitucion',
            8 => 'fecha_inicio_operaciones',
            10 => 'situacion_contribuyente',
            12 => 'fecha_ultimo_cambio_situacion',
            17 => 'entidad_federativa',
            19 => 'municipio_delegacion',
            21 => 'colonia',
            23 => 'tipo_vialidad',
            25 => 'nombre_vialidad',
            27 => 'numero_exterior',
            29 => 'numero_interior',
            31 => 'codigo_postal',
            33 => 'correo_electronico',
            35 => 'al',
            40 => 'regimen',
            42 => 'fecha_alta',
            default => null
        };
    }
}
