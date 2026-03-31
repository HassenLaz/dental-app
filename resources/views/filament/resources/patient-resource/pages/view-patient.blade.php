{{-- resources/views/filament/resources/patient-resource/pages/view-patient.blade.php --}}

<x-filament-panels::page>
    {{-- Standard Filament infolist (patient details) --}}
    @if ($this->hasInfolist())
    {{ $this->getInfolist('infolist') }}
    @else
    {{ $this->getForm('form') }}
    @endif

    {{-- Dental Chart --}}
    <div class="mt-6">
        @livewire('dental-chart', ['patientId' => $record->id], key('dental-chart-' . $record->id))
    </div>
    <div class="mt-6">
        @livewire('treatment-plan-manager', ['patientId' => $record->id], key('plans-' . $record->id))
    </div>
    <div class="mt-6">
        @livewire('patient-files', ['patientId' => $record->id], key('patient-files-' . $record->id))
    </div>
    {{-- Flash message --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
        class="fixed bottom-6 right-6 z-50 bg-green-600 text-white text-sm font-medium px-4 py-3 rounded-xl shadow-lg flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ session('success') }}
    </div>
    @endif
</x-filament-panels::page>