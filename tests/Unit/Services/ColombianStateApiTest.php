<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use PHPUnit\Metadata\Test;
use App\Services\ColombianStateApi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ColombianStateApiTest extends TestCase
{
    private ColombianStateApi $api;

    protected function setUp(): void
    {
        parent::setUp();
        $this->api = new ColombianStateApi();
    }

    #[Test]
    public function it_fetches_contracts_by_supplier_document()
    {
        Http::fake([
            '*' => Http::response([
                ['documento_proveedor' => '123456789', 'valor_del_contrato' => 1000000]
            ], 200)
        ]);

        $result = $this->api->getContractsBySupplier('123456789');

        $this->assertNotEmpty($result);
        $this->assertEquals('123456789', $result[0]['documento_proveedor']);
    }

    #[Test]
    public function it_caches_api_responses()
    {
        Cache::spy();

        Http::fake([
            '*' => Http::response([
                ['documento_proveedor' => '123456789']
            ], 200)
        ]);

        $this->api->getContractsBySupplier('123456789');

        Cache::shouldHaveReceived('remember')->once();
    }
}
