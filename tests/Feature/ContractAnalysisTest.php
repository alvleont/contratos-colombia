<?php

namespace Tests\Feature;

use Tests\TestCase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use App\Livewire\ContractAnalysis;
use Illuminate\Support\Facades\Http;

class ContractAnalysisTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    #[Test]
    public function it_initializes_with_search_form()
    {
        Livewire::test(ContractAnalysis::class)
            ->assertSet('view', 'search')
            ->assertSee('Documento del Proveedor');
    }

    #[Test]
    public function it_loads_contract_data_and_switches_to_analysis_view()
    {
        Http::fake([
            '*' => Http::response([
                [
                    'numero_de_contrato' => 'CT-2022-001',
                    'fecha_de_firma' => '2022-10-15',
                    'valor_del_contrato' => 1000000
                ]
            ])
        ]);

        Livewire::test(ContractAnalysis::class)
            ->set('document', '98560689')
            ->call('searchContracts')
            ->assertSet('view', 'analysis')
            ->assertSee('línea de tiempo')
            ->assertSee('CT-2022-001');
    }

    #[Test]
    public function it_processes_contract_data_into_timeline_format()
    {
        $mockData = [
            [
                'numero_de_contrato' => 'CT-2022-001',
                'fecha_de_firma' => '2022-10-15',
                'valor_del_contrato' => 1000000
            ],
            [
                'numero_de_contrato' => 'CT-2022-002',
                'fecha_de_firma' => '2022-11-01',
                'valor_del_contrato' => 2000000
            ]
        ];

        Http::fake(['*' => Http::response($mockData)]);

        $component = Livewire::test(ContractAnalysis::class)
            ->set('document', '98560689')
            ->call('searchContracts');

        $timelineData = $component->get('timelineData');

        $this->assertArrayHasKey('2022-10', $timelineData);
        $this->assertArrayHasKey('2022-11', $timelineData);
    }
}
