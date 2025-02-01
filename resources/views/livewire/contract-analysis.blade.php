<div class="min-h-screen bg-gray-50 bg-opacity-90 rounded-xl">
    <div wire:loading.delay class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12">
                        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Procesando solicitud
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Por favor espere mientras se procesan los datos...
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <header class="bg-white shadow bg-opacity-80 rounded-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    Sistema de Consulta de Contratos
                </h1>
                @if($view === 'analysis')
                    <button
                        wire:click="$set('view', 'search')"
                        class="flex items-center text-indigo-600 hover:text-indigo-800"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Nueva Búsqueda
                    </button>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search View -->
        <div x-show="$wire.view === 'search'" class="bg-white rounded-lg shadow-lg p-6">
            <div class="max-w-xl mx-auto">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Consulta de Contratos</h2>

                <!-- Tipo de búsqueda -->
                <div class="flex gap-4 mb-6">
                    <button
                        type="button"
                        wire:click="$set('searchType', 'document')"
                        @class([
                            'px-4 py-2 rounded-full text-sm font-medium',
                            'bg-indigo-600 text-white' => $searchType === 'document',
                            'bg-gray-100 text-gray-700 hover:bg-gray-200' => $searchType !== 'document'
                        ])
                    >
                        Buscar por Documento
                    </button>
                    <button
                        type="button"
                        wire:click="$set('searchType', 'name')"
                        @class([
                            'px-4 py-2 rounded-full text-sm font-medium',
                            'bg-indigo-600 text-white' => $searchType === 'name',
                            'bg-gray-100 text-gray-700 hover:bg-gray-200' => $searchType !== 'name'
                        ])
                    >
                        Buscar por Nombre
                    </button>
                </div>

                <form wire:submit="searchContracts" class="space-y-4">
                    <div x-show="$wire.searchType === 'document'">
                        <label for="document" class="block text-sm font-medium text-gray-700">
                            Documento del Proveedor
                        </label>
                        <input
                            type="text"
                            wire:model="document"
                            id="document"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Ingrese el número de documento"
                        >
                        @error('document')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="$wire.searchType === 'name'">
                        <label for="searchName" class="block text-sm font-medium text-gray-700">
                            Nombre del Proveedor
                        </label>
                        <input
                            type="text"
                            wire:model="searchName"
                            id="searchName"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Ingrese el nombre del proveedor"
                        >
                        @error('searchName')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Buscar Contratos
                    </button>
                </form>

                @if($apiError)
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
                        <div class="flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            {{ $apiError }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Analysis View -->
        <div x-show="$wire.view === 'analysis'" x-cloak>
            <!-- Contractor Info -->
            @if($contractorName)
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $contractorName }}</h2>
                        <p class="text-sm text-gray-500">
                            @if($searchType === 'document')
                                Documento: {{ $document }}
                            @else
                                Búsqueda por nombre: {{ $searchName }}
                            @endif
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <p class="text-sm text-gray-500">Valor Total de Contratos</p>
                        <p class="text-xl font-bold text-indigo-600">
                            ${{ number_format($this->totalContractsValue, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

       <!-- Filters -->
       <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Nivel de Entidad -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nivel de Entidad
                </label>
                <select
                    wire:model.live="nivelEntidad"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="todos">Todos</option>
                    <option value="nacional">Nacional</option>
                    <option value="territorial">Territorial</option>
                </select>
            </div>

            <!-- Entidad -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Entidad
                </label>
                <select
                    wire:model.live="selectedEntity"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="todas">Todas</option>
                    @foreach($entities as $entity)
                        <option value="{{ $entity }}">{{ $entity }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha Inicio -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha Inicio
                </label>
                <input
                    type="date"
                    wire:model.live="filterStartDate"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    min="{{ $minDate }}"
                    max="{{ $maxDate }}"
                >
            </div>

            <!-- Fecha Fin -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha Fin
                </label>
                <input
                    type="date"
                    wire:model.live="filterEndDate"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    min="{{ $minDate }}"
                    max="{{ $maxDate }}"
                >
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="mt-6">
        <div class="flex flex-wrap gap-4">
            @foreach($this->visibleMonths as $month)
                <button
                    wire:click="selectMonth('{{ $month['date'] }}')"
                    wire:loading.attr="disabled"
                    @class([
                        'relative p-4 rounded-lg transition-all',
                        'bg-indigo-50 hover:bg-indigo-100 border border-indigo-200' => !($selectedMonth === $month['date']),
                        'bg-indigo-100 border-2 border-indigo-500 shadow-sm' => $selectedMonth === $month['date'],
                    ])
                >
                    <div class="text-center">
                        <span class="block text-base font-medium text-gray-900">
                            {{ Carbon\Carbon::parse($month['date'])->format('M Y') }}
                        </span>
                        <span class="block text-sm text-gray-500 mt-1">
                            {{ $month['count'] }} {{ Str::plural('contrato', $month['count']) }}
                        </span>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Contract Details -->
    @if($selectedMonth && $this->currentMonthContracts)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Contratos de {{ $this->selectedMonthLabel }}
            </h3>
            <div class="space-y-4">
                @foreach($this->currentMonthContracts as $contract)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Número de Contrato</p>
                                <p class="font-medium text-gray-900">{{ $contract['numero_del_contrato'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Valor</p>
                                <p class="font-medium text-gray-900">
                                    ${{ number_format(floatval($contract['valor_contrato'] ?? 0), 2, ',', '.') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Entidad</p>
                                <p class="font-medium text-gray-900">{{ $contract['nombre_de_la_entidad'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Nivel de Entidad</p>
                                <p class="font-medium text-gray-900">{{ $contract['nivel_entidad'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Estado</p>
                                <p class="font-medium text-gray-900">{{ $contract['estado_del_proceso'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tipo de Contrato</p>
                                <p class="font-medium text-gray-900">{{ $contract['tipo_de_contrato'] ?? 'N/A' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500">Objeto del Contrato</p>
                                <p class="font-medium text-gray-900">{{ $contract['objeto_del_proceso'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Fecha de Inicio</p>
                                <p class="font-medium text-gray-900">
                                    {{ isset($contract['fecha_inicio_ejecuci_n']) ? Carbon\Carbon::parse($contract['fecha_inicio_ejecuci_n'])->format('d/m/Y') : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Fecha de Fin</p>
                                <p class="font-medium text-gray-900">
                                    {{ isset($contract['fecha_fin_ejecuci_n']) ? Carbon\Carbon::parse($contract['fecha_fin_ejecuci_n'])->format('d/m/Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                        @if(isset($contract['url_contrato']))
                            <div class="mt-4 flex justify-end">

                                    href="{{ $contract['url_contrato'] }}"
                                    target="_blank"
                                    class="inline-flex items-center text-indigo-600 hover:text-indigo-800"
                                >
                                    Ver detalles en SECOP
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
</div>
</main>
</div>
