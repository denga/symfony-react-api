<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OrderControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        /** @var EntityManagerInterface $em */
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $schemaTool = new SchemaTool($em);
        $schemaTool->updateSchema($em->getMetadataFactory()->getAllMetadata());
    }

    public function testPostOrdersWithValidBodyReturns201(): void
    {
        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'customerId' => 'customer-test-123',
                'items' => [
                    ['sku' => 'SKU-001', 'quantity' => 2, 'price_cents' => 500],
                ],
            ])
        );

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('orderId', $data);
        $this->assertArrayHasKey('orderUrl', $data);
        $this->assertStringContainsString('/api/orders/', $data['orderUrl']);
        $this->assertSame($data['orderId'], basename($data['orderUrl']));
    }

    public function testPostOrdersWithInvalidBodyReturns422(): void
    {
        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'customerId' => 'customer-test',
                'items' => [
                    ['sku' => 'SKU-001', 'quantity' => -1, 'price_cents' => 100],
                ],
            ])
        );

        $this->assertResponseStatusCodeSame(422);
    }

    public function testGetOrdersReturns200WithPaginationStructure(): void
    {
        $this->client->request('GET', '/api/orders?page=1&perPage=20');

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('meta', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('total', $data['meta']);
        $this->assertArrayHasKey('page', $data['meta']);
        $this->assertArrayHasKey('perPage', $data['meta']);
        $this->assertArrayHasKey('totalPages', $data['meta']);
        $this->assertIsArray($data['data']);
    }

    public function testGetOrderReturns200(): void
    {
        $this->client->request('POST', '/api/orders', [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'customerId' => 'customer-get-test',
                'items' => [['sku' => 'SKU-001', 'quantity' => 1, 'price_cents' => 100]],
            ]));
        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $orderId = $data['orderId'];

        $this->client->request('GET', '/api/orders/'.$orderId);

        $this->assertResponseStatusCodeSame(200);
        $getData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame($orderId, $getData['orderId']);
        $this->assertSame('customer-get-test', $getData['customerId']);
        $this->assertSame(100, $getData['totalCents']);
        $this->assertFalse($getData['paid']);
    }

    public function testGetOrderNotFoundReturns404(): void
    {
        $this->client->request('GET', '/api/orders/550e8400-e29b-41d4-a716-446655440000');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testPostOrdersWithBlankSkuReturns422(): void
    {
        $this->client->request(
            'POST',
            '/api/orders',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode([
                'customerId' => 'customer-test',
                'items' => [
                    ['sku' => '', 'quantity' => 1, 'price_cents' => 100],
                ],
            ])
        );

        $this->assertResponseStatusCodeSame(422);
    }
}
