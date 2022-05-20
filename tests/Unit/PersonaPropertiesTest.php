<?php

declare(strict_types=1);

namespace PhpCfdi\CsfScraper\Tests\Unit;

use DateTimeImmutable;
use PhpCfdi\CsfScraper\Persona;
use PhpCfdi\CsfScraper\Tests\TestCase;
use PhpCfdi\CsfScraper\Tests\Unit\Traits\PersonaTrait;

class PersonaPropertiesTest extends TestCase
{
    use PersonaTrait;

    private Persona $person;

    public function setUp(): void
    {
        parent::setUp();
        $this->person = new Persona();
    }
    /**
     *
     * @return array<int, array<int, string>>
     */
    private function datePropertiesProvider(): array
    {
        return [
            ['FechaInicioOperaciones', '12-01-2017'],
            ['FechaUltimoCambioSituacion', '12-01-2018'],
        ];
    }
    /**
     *
     * @return array<int, array<int, string>>
     */
    private function stringPropertiesProvider(): array
    {
        return [
            ['SituacionContribuyente', 'ACTIVO'],
            ['EntidadFederativa', 'Puebla'],
            ['MunicipioDelegacion', 'Municipio'],
            ['Colonia', 'Mi Colonia'],
            ['TipoVialidad', 'Vialidad'],
            ['NombreVialidad', 'Nombre Calle'],
            ['NumeroExterior', '1'],
            ['NumeroInterior', '2'],
            ['CodigoPostal', '72000'],
            ['CorreoElectronico', 'correo@example.com'],
            ['Al', 'Puebla 1'],
        ];
    }
    /**
     * @dataProvider stringPropertiesProvider
     */
    public function test_set_and_get_string_properties(string $complementFunction, string $value): void
    {
        $result = $this->setAndGetProperty($complementFunction, $value);
        $this->assertSame($value, $result);
    }

    /**
     * @dataProvider datePropertiesProvider
     */
    public function test_set_and_get_date_properties(string $complementFunction, string $value): void
    {
        $result = $this->setAndGetProperty($complementFunction, $value);
        $this->assertEquals(DateTimeImmutable::createFromFormat('d-m-Y', $value), $result);
    }
    /**
     *
     * test to check if new property not contained in Persona is found is assigned and value can be retrieved
     * its goal is not to be used but to prevent unexpected exceptions
     * */
    public function test_assign_dynamic_property(): void
    {
        $persona = new Persona();
        /** @phpstan-ignore-next-line */
        $persona->dynamic = 'dynamic';
        $this->assertSame('dynamic', $persona->dynamic);
    }
}
