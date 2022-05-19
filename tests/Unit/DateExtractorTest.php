<?php

declare(strict_types=1);

namespace PhpCfdi\CsfScraper\Tests\Unit;

use PhpCfdi\CsfScraper\DataExtractor;
use PhpCfdi\CsfScraper\Tests\TestCase;

class DateExtractorTest extends TestCase
{
    public function test_scrap_from_idcif_and_rfc_by_moral(): void
    {
        $html = $this->fileContents('scrap_moral.html');
        $extractor = new DataExtractor($html);

        $expectedData = [
            'razon_social' => 'Mi razón social',
            'regimen_de_capital' => 'SA DE CV',
            'fecha_constitucion' => '21-02-2019',
            'fecha_inicio_operaciones' => '21-02-2019',
            'situacion_contribuyente' => 'ACTIVO',
            'fecha_ultimo_cambio_situacion' => '21-02-2019',
            'entidad_federativa' => 'CIUDAD DE MEXICO',
            'municipio_delegacion' => 'CUAUHTEMOC',
            'colonia' => 'CUAUHTEMOC',
            'tipo_vialidad' => 'Tipo vialidad',
            'nombre_vialidad' => 'PASEO DE LA REFORMA',
            'numero_exterior' => '143',
            'numero_interior' => 'Piso 69',
            'codigo_postal' => '72055',
            'correo_electronico' => 'example@example.com',
            'al' => 'CIUDAD DE MEXICO 2',
            'regimenes' => [
                [
                    'regimen' => 'Régimen General de Ley Personas Morales',
                    'fecha_alta' => '21-02-2019',
                ],
            ],
        ];

        $data = $extractor->extract(false);

        $this->assertSame($expectedData, $data);
    }

    public function test_scrap_from_idcif_and_rfc_by_fisica(): void
    {
        $html = $this->fileContents('scrap_fisica.html');
        $extractor = new DataExtractor($html);

        $expectedData = [
            'curp' => 'CURP',
            'nombre' => 'JUAN',
            'apellido_paterno' => 'PEREZ',
            'apellido_materno' => 'PEREZ',
            'fecha_nacimiento' => '01-05-1973',
            'fecha_inicio_operaciones' => '03-11-2004',
            'situacion_contribuyente' => 'ACTIVO',
            'fecha_ultimo_cambio_situacion' => '03-11-2004',
            'entidad_federativa' => 'CIUDAD DE MEXICO',
            'municipio_delegacion' => 'IZTAPALAPA',
            'colonia' => 'MI COLONIA',
            'tipo_vialidad' => 'CALLE',
            'nombre_vialidad' => 'BENITO JUAREZ',
            'numero_exterior' => '183',
            'numero_interior' => '',
            'codigo_postal' => '72000',
            'correo_electronico' => '',
            'al' => 'CIUDAD DE MEXICO 3',
            'regimenes' => [
                [
                    'regimen' => 'Régimen de Incorporación Fiscal',
                    'fecha_alta' => '01-01-2014',
                ],
            ],
        ];

        $data = $extractor->extract(true);

        $this->assertSame($expectedData, $data);
    }

    public function test_scrap_from_idcif_and_rfc_multiple_regimen(): void
    {
        $html = $this->fileContents('scrap_regimenes.html');
        $extractor = new DataExtractor($html);

        $expectedData = [
            'curp' => 'CURP',
            'nombre' => 'JUAN',
            'apellido_paterno' => 'PEREZ',
            'apellido_materno' => 'PEREZ',
            'fecha_nacimiento' => '21-07-1995',
            'fecha_inicio_operaciones' => '01-01-2018',
            'situacion_contribuyente' => 'ACTIVO',
            'fecha_ultimo_cambio_situacion' => '16-08-2018',
            'entidad_federativa' => 'QUERETARO',
            'municipio_delegacion' => 'MUNICIPIO',
            'colonia' => 'MI COLONIA',
            'tipo_vialidad' => 'CALZADA (CALZ.)',
            'nombre_vialidad' => 'DEL BOSQUE',
            'numero_exterior' => '19',
            'numero_interior' => '',
            'codigo_postal' => '72000',
            'correo_electronico' => 'example@example.com',
            'al' => 'QUERETARO 1',
            'regimenes' => [
                [
                    'regimen' => 'Régimen de Sueldos y Salarios e Ingresos Asimilados a Salarios',
                    'fecha_alta' => '01-01-2018',
                ],
                [
                    'regimen' => 'Régimen Simplificado de Confianza',
                    'fecha_alta' => '09-02-2022',
                ],
            ],
        ];

        $data = $extractor->extract(true);

        $this->assertSame($expectedData, $data);
    }

    public function test_return_empty_when_not_found(): void
    {
        $html = $this->fileContents('error.html');
        $extractor = new DataExtractor($html);

        $expectedData = [];

        $data = $extractor->extract(false);

        $this->assertSame($expectedData, $data);
    }
}
