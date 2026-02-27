<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class OrderControllerTest extends WebTestCase
{
    private KernelBrowser $kernelBrowser;

    public function testPostOrdersWithValidBodyReturns201(): void
    {
        $body = json_encode([
            'customerId' => 'customer-test-123',
            'items' => [
                [
                    'sku' => 'SKU-001',
                    'quantity' => 2,
                    'price_cents' => 500,
                ],
            ],
        ]);
        $this->assertNotFalse($body);
        $this->kernelBrowser->request(
            Request::METHOD_POST,
            '/api/orders',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $body
        );

        $this->assertResponseStatusCodeSame(201);
        $content = $this->kernelBrowser->getResponse()->getContent();
        $this->assertNotFalse($content);
        $data = json_decode((string) $content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('orderId', $data);
        $this->assertArrayHasKey('orderUrl', $data);
        $this->assertIsString($data['orderUrl']);
        $this->assertStringContainsString('/api/orders/', $data['orderUrl']);
        $this->assertIsString($data['orderId']);
        $this->assertSame($data['orderId'], basename($data['orderUrl']));
    }

    public function testPostOrdersWithInvalidBodyReturns422(): void
    {
        $body = json_encode([
            'customerId' => 'customer-test',
            'items' => [
                [
                    'sku' => 'SKU-001',
                    'quantity' => -1,
                    'price_cents' => 100,
                ],
            ],
        ]);
        $this->assertNotFalse($body);
        $this->kernelBrowser->request(
            Request::METHOD_POST,
            '/api/orders',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $body
        );

        $this->assertResponseStatusCodeSame(422);
    }

    public function testGetOrdersReturns200WithPaginationStructure(): void
    {
        $this->kernelBrowser->request(Request::METHOD_GET, '/api/orders?page=1&perPage=20');

        $this->assertResponseStatusCodeSame(200);
        $content = $this->kernelBrowser->getResponse()->getContent();
        $this->assertNotFalse($content);
        $data = json_decode((string) $content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('meta', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['meta']);
        $this->assertArrayHasKey('total', $data['meta']);
        $this->assertArrayHasKey('page', $data['meta']);
        $this->assertArrayHasKey('perPage', $data['meta']);
        $this->assertArrayHasKey('totalPages', $data['meta']);
        $this->assertIsArray($data['data']);
    }

    public function testGetOrderReturns200(): void
    {
        $body = json_encode([
            'customerId' => 'customer-get-test',
            'items' => [[
                'sku' => 'SKU-001',
                'quantity' => 1,
                'price_cents' => 100,
            ]],
        ]);
        $this->assertNotFalse($body);
        $this->kernelBrowser->request(Request::METHOD_POST, '/api/orders', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $body);
        $this->assertResponseStatusCodeSame(201);
        $content = $this->kernelBrowser->getResponse()->getContent();
        $this->assertNotFalse($content);
        $data = json_decode((string) $content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('orderId', $data);
        $this->assertIsString($data['orderId']);
        $orderId = $data['orderId'];

        $this->kernelBrowser->request(Request::METHOD_GET, '/api/orders/'.$orderId);

        $this->assertResponseStatusCodeSame(200);
        $getContent = $this->kernelBrowser->getResponse()->getContent();
        $this->assertNotFalse($getContent);
        $getData = json_decode((string) $getContent, true);
        $this->assertIsArray($getData);
        $this->assertSame($orderId, $getData['orderId']);
        $this->assertSame('customer-get-test', $getData['customerId']);
        $this->assertSame(100, $getData['totalCents']);
        $this->assertFalse($getData['paid']);
    }

    public function testGetOrderNotFoundReturns404(): void
    {
        $this->kernelBrowser->request(Request::METHOD_GET, '/api/orders/550e8400-e29b-41d4-a716-446655440000');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testPostOrdersWithBlankSkuReturns422(): void
    {
        $body = json_encode([
            'customerId' => 'customer-test',
            'items' => [
                [
                    'sku' => '',
                    'quantity' => 1,
                    'price_cents' => 100,
                ],
            ],
        ]);
        $this->assertNotFalse($body);
        $this->kernelBrowser->request(
            Request::METHOD_POST,
            '/api/orders',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $body
        );

        $this->assertResponseStatusCodeSame(422);
    }

    protected function setUp(): void
    {
        $this->kernelBrowser = self::createClient();
        /** @var EntityManagerInterface $em */
        $em = $this->kernelBrowser->getContainer()->get('doctrine.orm.entity_manager');
        $schemaTool = new SchemaTool($em);
        $schemaTool->updateSchema($em->getMetadataFactory()->getAllMetadata());
    }
}
