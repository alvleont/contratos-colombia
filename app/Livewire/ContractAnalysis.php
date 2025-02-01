<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;
use App\Services\ColombianStateApi;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ContractAnalysis extends Component
{
    #[Rule('required_without:searchName|numeric|min:5', message: 'El documento debe tener al menos 5 dígitos numéricos')]
    public string $document = '';

    #[Rule('required_without:document|min:3', message: 'El nombre debe tener al menos 3 caracteres')]
    public string $searchName = '';

    public string $searchType = 'document';
    public string $view = 'search';
    public array $contracts = [];
    public array $timelineData = [];
    public string $currentPeriod;
    public ?string $selectedMonth = null;
    public ?string $apiError = null;
    public string $nivelEntidad = 'todos';
    public ?string $contractorName = null;
    public ?string $minDate = null;
    public ?string $maxDate = null;
    public ?string $filterStartDate = null;
    public ?string $filterEndDate = null;
    public array $entities = [];
    public string $selectedEntity = 'todas';

    public bool $isLoading = false;

    public function mount()
    {
        $this->currentPeriod = now()->startOfYear()->format('Y-m-d');
    }

    public function searchContracts()
    {
        $this->isLoading = true;
        $this->validate();

        try {
            $api = new ColombianStateApi();

            $this->contracts = match($this->searchType) {
                'document' => $api->getContractsBySupplier($this->document),
                'name' => $api->getContractsByName($this->searchName),
                default => throw new \Exception('Tipo de búsqueda inválido')
            };

            if (empty($this->contracts)) {
                $this->apiError = 'No se encontraron contratos';
                $this->isLoading = false;
                return;
            }

            $this->contractorName = $this->contracts[0]['nom_raz_social_contratista'] ?? null;
            $this->entities = collect($this->contracts)
                ->pluck('nombre_de_la_entidad')
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            $this->processTimelineData();
            $this->calculateDateLimits();
            $this->view = 'analysis';
        } catch (\Exception $e) {
            \Log::error('Error en ContractAnalysis: ' . $e->getMessage());
            $this->apiError = 'Error al consultar los contratos: ' . $e->getMessage();
        }
        $this->isLoading = false;
    }

    private function calculateDateLimits()
    {
        $dates = collect($this->contracts)
            ->map(fn ($contract) => Carbon::parse($contract['fecha_de_firma_del_contrato']))
            ->sort();

        $this->minDate = $dates->first()?->startOfYear()->format('Y-m-d');
        $this->maxDate = min(
            now()->format('Y-m-d'),
            $dates->last()?->endOfYear()->format('Y-m-d')
        );

        // Ajustar el período actual si está fuera de los límites
        $currentDate = Carbon::parse($this->currentPeriod);
        if ($currentDate->lt($this->minDate)) {
            $this->currentPeriod = $this->minDate;
        } elseif ($currentDate->gt($this->maxDate)) {
            $this->currentPeriod = $this->maxDate;
        }
    }

    public function previousPeriod()
    {
        $newDate = Carbon::parse($this->currentPeriod)->subYear()->format('Y-m-d');
        if ($newDate >= $this->minDate) {
            $this->currentPeriod = $newDate;
        }
    }

    public function nextPeriod()
    {
        $newDate = Carbon::parse($this->currentPeriod)->addYear()->format('Y-m-d');
        if ($newDate <= $this->maxDate) {
            $this->currentPeriod = $newDate;
        }
    }

    public function setNivelEntidad($nivel)
    {
        $this->nivelEntidad = $nivel;
        $this->processTimelineData();
    }

    public function setEntity($entity)
    {
        $this->selectedEntity = $entity;
        $this->processTimelineData();
    }

    public function selectMonth(string $yearMonth)
    {
        $this->selectedMonth = $yearMonth;
    }

    public function getVisibleMonthsProperty()
    {
        return collect($this->timelineData)
            ->map(function ($contracts, $yearMonth) {
                $date = Carbon::parse($yearMonth);
                return [
                    'date' => $yearMonth,
                    'label' => $date->format('M Y'),
                    'count' => count($contracts)
                ];
            })
            ->sortBy('date')
            ->values()
            ->toArray();
    }

    public function getCurrentPeriodLabelProperty()
    {
        return Carbon::parse($this->currentPeriod)->format('F Y');
    }

    public function getSelectedMonthLabelProperty()
    {
        return $this->selectedMonth ?
            Carbon::parse($this->selectedMonth)->format('F Y') :
            null;
    }

    public function getCurrentMonthContractsProperty()
    {
        if (!$this->selectedMonth) {
            return [];
        }

        return $this->timelineData[$this->selectedMonth] ?? [];
    }

    public function getTotalContractsValueProperty()
    {
        return collect($this->timelineData)
            ->flatMap(fn($contracts) => $contracts)
            ->sum(fn($contract) => floatval($contract['valor_contrato'] ?? 0));
    }

    private function processTimelineData()
    {
        $filteredContracts = collect($this->contracts);

        // Aplicar filtros de nivel de entidad
        if ($this->nivelEntidad !== 'todos') {
            $filteredContracts = $filteredContracts->filter(fn($contract) =>
                strtolower($contract['nivel_entidad']) === strtolower($this->nivelEntidad)
            );
        }

        // Aplicar filtro de entidad específica
        if ($this->selectedEntity !== 'todas') {
            $filteredContracts = $filteredContracts->filter(fn($contract) =>
                $contract['nombre_de_la_entidad'] === $this->selectedEntity
            );
        }

        // Aplicar filtros de fecha
        if ($this->filterStartDate) {
            $startDate = Carbon::parse($this->filterStartDate)->startOfDay();
            $filteredContracts = $filteredContracts->filter(fn($contract) =>
                Carbon::parse($contract['fecha_de_firma_del_contrato'])->gte($startDate)
            );
        }

        if ($this->filterEndDate) {
            $endDate = Carbon::parse($this->filterEndDate)->endOfDay();
            $filteredContracts = $filteredContracts->filter(fn($contract) =>
                Carbon::parse($contract['fecha_de_firma_del_contrato'])->lte($endDate)
            );
        }

        $this->timelineData = $filteredContracts
            ->groupBy(fn ($contract) =>
                Carbon::parse($contract['fecha_de_firma_del_contrato'])->format('Y-m')
            )
            ->toArray();
    }

    public function updatedNivelEntidad()
    {
        $this->processTimelineData();
    }

    public function updatedSelectedEntity()
    {
        $this->processTimelineData();
    }

    public function updatedFilterStartDate()
    {
        $this->processTimelineData();
    }

    public function updatedFilterEndDate()
    {
        $this->processTimelineData();
    }

    public function render()
    {
        return view('livewire.contract-analysis');
    }
}
