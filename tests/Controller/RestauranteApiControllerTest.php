<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RestauranteApiControllerTest extends WebTestCase
{
    public function testGetRestaurantes(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/restaurantes', [], [], [
            'HTTP_X-API-KEY' => '1234'
        ]);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('application/json', $client->getResponse()->headers->get('content-type'));
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testCreateRestaurante(): void
    {
        $client = static::createClient();
        $payload = [
            'nombre' => 'Test Restaurante',
            'direccion' => 'Calle Test 123',
            'telefono' => '123456789'
        ];
        $client->request('POST', '/api/restaurantes', [], [], [
            'HTTP_X-API-KEY' => '1234',
            'CONTENT_TYPE' => 'application/json'
        ], json_encode($payload));

        $this->assertSame(201, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertSame('Test Restaurante', $data['nombre']);
        $this->assertSame('Calle Test 123', $data['direccion']);
        $this->assertSame('123456789', $data['telefono']);

        // Guardar el ID para los siguientes tests
        self::$restauranteId = $data['id'];
    }

    public function testUpdateRestaurante(): void
    {
        $client = static::createClient();
        $id = self::$restauranteId ?? null;
        $this->assertNotNull($id, 'ID del restaurante no disponible');
        $payload = [
            'nombre' => 'Restaurante Actualizado',
            'direccion' => 'Nueva Direccion 456',
            'telefono' => '987654321'
        ];
        $client->request('PUT', "/api/restaurantes/{$id}", [], [], [
            'HTTP_X-API-KEY' => '1234',
            'CONTENT_TYPE' => 'application/json'
        ], json_encode($payload));

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Restaurante actualizado', $data['status']);
    }

    public function testDeleteRestaurante(): void
    {
        $client = static::createClient();
        $id = self::$restauranteId ?? null;
        $this->assertNotNull($id, 'ID del restaurante no disponible');
        $client->request('DELETE', "/api/restaurantes/{$id}", [], [], [
            'HTTP_X-API-KEY' => '1234'
        ]);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Restaurante eliminado', $data['status']);
    }

    // Variable estática para compartir el ID entre tests
    private static $restauranteId;
}
