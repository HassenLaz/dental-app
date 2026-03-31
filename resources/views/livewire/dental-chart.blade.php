{{-- resources/views/livewire/dental-chart.blade.php --}}
<div class="dental-chart-wrapper">

    <style>
        .dental-chart-wrapper {
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .tooth-g {
            cursor: pointer;
        }

        .tooth-g .tooth-rect {
            transition: transform 0.15s ease, filter 0.15s ease;
            transform-box: fill-box;
            transform-origin: center;
        }

        .tooth-g:hover .tooth-rect {
            transform: scale(1.08);
            filter: brightness(1.15);
        }

        .tooth-g.is-selected .tooth-rect {
            transform: scale(1.1);
            filter: brightness(1.2) drop-shadow(0 0 3px rgba(59, 130, 246, 0.6));
        }

        .dc-input {
            width: 100%;
            padding: .5rem .75rem;
            font-size: .875rem;
            border: 1px solid #374151;
            border-radius: .5rem;
            background: #1f2937;
            color: #f9fafb;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }

        .dc-input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, .2);
        }

        .dc-input::placeholder {
            color: #6b7280;
        }

        select.dc-input {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .75rem center;
            padding-right: 2.25rem;
        }

        .dc-label {
            display: block;
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #9ca3af;
            margin-bottom: .3rem;
        }

        .dc-save-btn {
            flex: 1;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            font-size: .875rem;
            font-weight: 600;
            padding: .6rem 1.25rem;
            border-radius: .5rem;
            border: none;
            cursor: pointer;
            transition: opacity .15s, transform .1s;
            letter-spacing: .02em;
        }

        .dc-save-btn:hover {
            opacity: .92;
            transform: translateY(-1px);
        }

        .dc-save-btn:active {
            transform: translateY(0);
        }

        .dc-save-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
            transform: none;
        }

        .dc-cancel-btn {
            padding: .6rem 1rem;
            font-size: .875rem;
            color: #9ca3af;
            border-radius: .5rem;
            border: 1px solid #374151;
            background: transparent;
            cursor: pointer;
            transition: background .15s, color .15s;
        }

        .dc-cancel-btn:hover {
            background: #374151;
            color: #f9fafb;
        }

        /* Status badges — same as plan manager */
        .status-badge {
            display: inline-block;
            font-size: .65rem;
            font-weight: 700;
            padding: .15rem .55rem;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .pay-paid {
            background: #052e16;
            color: #4ade80;
        }

        .pay-partial {
            background: #422006;
            color: #fbbf24;
        }

        .pay-pending {
            background: #450a0a;
            color: #f87171;
        }

        .dc-suggestion-item {
            width: 100%;
            text-align: left;
            padding: .5rem .75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            border: none;
            border-bottom: 1px solid #374151;
            background: transparent;
            cursor: pointer;
            transition: background .1s;
        }

        .dc-suggestion-item:hover {
            background: #374151;
        }

        .dc-suggestion-item:last-child {
            border-bottom: none;
        }
    </style>

    <div class="bg-gray-900 rounded-2xl border border-gray-700 overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-white">Schéma dentaire</h2>
                <p class="text-xs text-gray-400 mt-0.5">Cliquez sur une dent pour enregistrer un soin</p>
            </div>
            @if($selectedToothNumber)
            <div
                class="flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 text-amber-400 px-3 py-1.5 rounded-full text-xs font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Dent {{ $selectedToothNumber }} sélectionnée
            </div>
            @endif
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

                {{-- ── SVG Chart ──────────────────────────────── --}}
                <div>
                    <svg viewBox="0 0 500 580" xmlns="http://www.w3.org/2000/svg" class="w-full max-w-lg mx-auto">
                        <text x="120" y="18" text-anchor="middle" fill="#4b5563" font-size="8" font-weight="700"
                            letter-spacing="1">SUPÉRIEUR DROIT (1)</text>
                        <text x="380" y="18" text-anchor="middle" fill="#4b5563" font-size="8" font-weight="700"
                            letter-spacing="1">SUPÉRIEUR GAUCHE (2)</text>
                        <text x="120" y="568" text-anchor="middle" fill="#4b5563" font-size="8" font-weight="700"
                            letter-spacing="1">INFÉRIEUR DROIT (4)</text>
                        <text x="380" y="568" text-anchor="middle" fill="#4b5563" font-size="8" font-weight="700"
                            letter-spacing="1">INFÉRIEUR GAUCHE (3)</text>
                        <line x1="250" y1="22" x2="250" y2="558" stroke="#374151" stroke-width="1"
                            stroke-dasharray="4,3" />
                        <line x1="25" y1="290" x2="475" y2="290" stroke="#374151" stroke-width="1"
                            stroke-dasharray="4,3" />

                        @php
                        $upperRight = [
                        ['num'=>'11','x'=>228,'y'=>55,'type'=>'incisor'],['num'=>'12','x'=>206,'y'=>68,'type'=>'incisor'],
                        ['num'=>'13','x'=>182,'y'=>90,'type'=>'canine'],['num'=>'14','x'=>160,'y'=>118,'type'=>'premolar'],
                        ['num'=>'15','x'=>142,'y'=>150,'type'=>'premolar'],['num'=>'16','x'=>126,'y'=>190,'type'=>'molar'],
                        ['num'=>'17','x'=>113,'y'=>232,'type'=>'molar'],['num'=>'18','x'=>103,'y'=>274,'type'=>'molar'],
                        ];
                        $upperLeft = [
                        ['num'=>'21','x'=>272,'y'=>55,'type'=>'incisor'],['num'=>'22','x'=>294,'y'=>68,'type'=>'incisor'],
                        ['num'=>'23','x'=>318,'y'=>90,'type'=>'canine'],['num'=>'24','x'=>340,'y'=>118,'type'=>'premolar'],
                        ['num'=>'25','x'=>358,'y'=>150,'type'=>'premolar'],['num'=>'26','x'=>374,'y'=>190,'type'=>'molar'],
                        ['num'=>'27','x'=>387,'y'=>232,'type'=>'molar'],['num'=>'28','x'=>397,'y'=>274,'type'=>'molar'],
                        ];
                        $lowerLeft = [
                        ['num'=>'31','x'=>272,'y'=>525,'type'=>'incisor'],['num'=>'32','x'=>294,'y'=>512,'type'=>'incisor'],
                        ['num'=>'33','x'=>318,'y'=>490,'type'=>'canine'],['num'=>'34','x'=>340,'y'=>462,'type'=>'premolar'],
                        ['num'=>'35','x'=>358,'y'=>430,'type'=>'premolar'],['num'=>'36','x'=>374,'y'=>390,'type'=>'molar'],
                        ['num'=>'37','x'=>387,'y'=>348,'type'=>'molar'],['num'=>'38','x'=>397,'y'=>306,'type'=>'molar'],
                        ];
                        $lowerRight = [
                        ['num'=>'41','x'=>228,'y'=>525,'type'=>'incisor'],['num'=>'42','x'=>206,'y'=>512,'type'=>'incisor'],
                        ['num'=>'43','x'=>182,'y'=>490,'type'=>'canine'],['num'=>'44','x'=>160,'y'=>462,'type'=>'premolar'],
                        ['num'=>'45','x'=>142,'y'=>430,'type'=>'premolar'],['num'=>'46','x'=>126,'y'=>390,'type'=>'molar'],
                        ['num'=>'47','x'=>113,'y'=>348,'type'=>'molar'],['num'=>'48','x'=>103,'y'=>306,'type'=>'molar'],
                        ];
                        $colors = [
                        'incisor' => ['fill'=>'#1e3a5f','stroke'=>'#3b82f6'],
                        'canine' => ['fill'=>'#431407','stroke'=>'#f97316'],
                        'premolar' => ['fill'=>'#052e16','stroke'=>'#22c55e'],
                        'molar' => ['fill'=>'#450a0a','stroke'=>'#ef4444'],
                        ];
                        $sizes = [
                        'incisor' => ['w'=>22,'h'=>28,'rx'=>6],
                        'canine' => ['w'=>21,'h'=>30,'rx'=>6],
                        'premolar' => ['w'=>24,'h'=>26,'rx'=>6],
                        'molar' => ['w'=>28,'h'=>24,'rx'=>7],
                        ];
                        $allTeeth = array_merge($upperRight, $upperLeft, $lowerLeft, $lowerRight);
                        @endphp

                        @foreach($allTeeth as $tooth)
                        @php
                        $c = $colors[$tooth['type']];
                        $s = $sizes[$tooth['type']];
                        $hasTreatment = in_array($tooth['num'], $treatedTeeth);
                        $isSelected = $selectedToothNumber === $tooth['num'];
                        $fillColor = $hasTreatment ? '#422006' : $c['fill'];
                        $strokeColor = $hasTreatment ? '#f59e0b' : ($isSelected ? '#f59e0b' : $c['stroke']);
                        $strokeWidth = $isSelected ? '2.5' : '1.5';
                        $textColor = $isSelected ? '#fbbf24' : '#d1d5db';
                        @endphp
                        <g class="tooth-g {{ $isSelected ? 'is-selected' : '' }}"
                            wire:click="selectTooth('{{ $tooth['num'] }}')">
                            <rect class="tooth-rect" x="{{ $tooth['x'] - $s['w']/2 }}" y="{{ $tooth['y'] - $s['h']/2 }}"
                                width="{{ $s['w'] }}" height="{{ $s['h'] }}" rx="{{ $s['rx'] }}" fill="{{ $fillColor }}"
                                stroke="{{ $strokeColor }}" stroke-width="{{ $strokeWidth }}" />
                            @if($hasTreatment)
                            <circle cx="{{ $tooth['x'] + $s['w']/2 - 3 }}" cy="{{ $tooth['y'] - $s['h']/2 + 3 }}"
                                r="3.5" fill="#f59e0b" />
                            @endif
                            <text x="{{ $tooth['x'] }}" y="{{ $tooth['y'] + 4 }}" text-anchor="middle" font-size="9"
                                font-weight="{{ $isSelected ? '800' : '600' }}" fill="{{ $textColor }}"
                                style="pointer-events:none">{{ $tooth['num'] }}</text>
                        </g>
                        @endforeach
                    </svg>

                    {{-- Legend --}}
                    <div class="flex flex-wrap justify-center gap-4 mt-3">
                        <div class="flex items-center gap-1.5 text-xs text-gray-500"><span
                                class="w-3 h-3 rounded-sm inline-block"
                                style="background:#1e3a5f;border:1.5px solid #3b82f6"></span>Incisive</div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500"><span
                                class="w-3 h-3 rounded-sm inline-block"
                                style="background:#431407;border:1.5px solid #f97316"></span>Canine</div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500"><span
                                class="w-3 h-3 rounded-sm inline-block"
                                style="background:#052e16;border:1.5px solid #22c55e"></span>Prémolaire</div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500"><span
                                class="w-3 h-3 rounded-sm inline-block"
                                style="background:#450a0a;border:1.5px solid #ef4444"></span>Molaire</div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500"><span
                                class="w-3 h-3 rounded-sm inline-block"
                                style="background:#422006;border:1.5px solid #f59e0b"></span>Soin enregistré</div>
                    </div>
                </div>

                {{-- ── Right Panel ─────────────────────────────── --}}
                <div class="flex flex-col gap-5">

                    @if($selectedToothNumber)
                    {{-- Add treatment form --}}
                    <div class="bg-gray-800 border border-gray-600 rounded-xl p-6">
                        <h3 class="font-semibold text-amber-400 mb-5 flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Ajouter un soin — Dent {{ $selectedToothNumber }}
                        </h3>
                        <div class="space-y-4">

                            {{-- Name with autocomplete --}}
                            <div class="relative">
                                <label class="dc-label">Nom du soin *</label>
                                <input type="text" wire:model.live="form.name" wire:keydown.escape="hideSuggestions"
                                    placeholder="ex : Extraction, Détartrage, Couronne..." class="dc-input"
                                    autocomplete="off" />
                                @error('form.name') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                                @enderror

                                @if($showSuggestions && count($suggestions) > 0)
                                <div class="absolute z-50 w-full mt-1 rounded-lg border border-gray-600 overflow-hidden"
                                    style="background:#1f2937;box-shadow:0 8px 24px rgba(0,0,0,.4)">
                                    @foreach($suggestions as $act)
                                    <button type="button"
                                        wire:click="selectSuggestion('{{ addslashes($act['name']) }}', {{ $act['cost'] }})"
                                        class="dc-suggestion-item">
                                        <span style="font-size:.875rem;color:#f9fafb">{{ $act['name'] }}</span>
                                        <span
                                            style="font-size:.75rem;font-weight:600;color:#f59e0b;white-space:nowrap">{{
                                            number_format($act['cost'], 3) }} TND</span>
                                    </button>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <div>
                                <label class="dc-label">Description</label>
                                <textarea wire:model="form.description" rows="2" placeholder="Notes sur le soin..."
                                    class="dc-input resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="dc-label">Coût (TND) *</label>
                                    <input type="number" step="0.001" wire:model="form.cost" placeholder="0.000"
                                        class="dc-input" />
                                    @error('form.cost') <span class="text-xs text-red-400 mt-1 block">{{ $message
                                        }}</span> @enderror
                                </div>
                                <div>
                                    <label class="dc-label">Date *</label>
                                    <input type="date" wire:model="form.date" class="dc-input" />
                                    @error('form.date') <span class="text-xs text-red-400 mt-1 block">{{ $message
                                        }}</span> @enderror
                                </div>
                            </div>

                            <div class="flex gap-2 pt-1">
                                <button wire:click="saveTreatment" wire:loading.attr="disabled" class="dc-save-btn">
                                    <span wire:loading.remove wire:target="saveTreatment">Enregistrer le soin</span>
                                    <span wire:loading wire:target="saveTreatment">Enregistrement...</span>
                                </button>
                                <button wire:click="clearSelection" class="dc-cancel-btn">Annuler</button>
                            </div>
                        </div>
                    </div>

                    {{-- Treatment history for selected tooth --}}
                    @if($toothTreatments->count() > 0)
                    <div>
                        <h4
                            class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3 flex items-center gap-2 pb-4">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Historique — Dent {{ $selectedToothNumber }}
                        </h4>
                        <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                            @foreach($toothTreatments as $treatment)
                            @php $payStatus = $treatment->paymentStatus(); @endphp
                            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                                {{-- Treatment header row --}}
                                <div class="px-3 py-2.5 flex items-center gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="text-sm font-semibold text-white">{{ $treatment->name }}</p>
                                            <span class="status-badge pay-{{ $payStatus }}">
                                                {{ match($payStatus) { 'paid'=>'Payé','partial'=>'Partiel',default=>'En
                                                attente' } }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $treatment->date ? $treatment->date->format('d/m/Y') : '—' }}
                                            • {{ number_format($treatment->cost, 3) }} TND
                                            @if($treatment->remainingBalance() > 0)
                                            • <span class="text-red-400">{{
                                                number_format($treatment->remainingBalance(), 3) }} restant</span>
                                            @endif
                                        </p>
                                    </div>

                                </div>

                                {{-- Payment history for this treatment --}}
                                @if($treatment->payments->count() > 0)
                                <div class="border-t border-gray-700 px-3 py-2 space-y-1">
                                    @foreach($treatment->payments->sortByDesc('paid_at') as $payment)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="text-green-400 font-semibold">+ {{
                                                number_format($payment->amount, 3) }} TND</span>
                                            <span class="text-gray-500 capitalize">{{ $payment->payment_method }}</span>
                                            <span class="text-gray-600">{{
                                                \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y') }}</span>
                                            @if($payment->notes)
                                            <span class="text-gray-600 italic">{{ $payment->notes }}</span>
                                            @endif
                                        </div>
                                        <button wire:click="deletePayment({{ $payment->id }})"
                                            class="text-gray-700 hover:text-red-400 transition-colors ml-2">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @else
                    <div class="flex flex-col items-center justify-center py-14 text-center">
                        <p class="text-sm font-medium text-gray-500">Sélectionnez une dent</p>
                        <p class="text-xs text-gray-600 mt-1">pour ajouter ou consulter les soins</p>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- ── All Treatments Summary ──────────────────────────────── --}}
        @if($allTreatments->count() > 0)
        <div class="px-6 pb-6">
            <div class="border-t border-gray-700 pt-5">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Tous les soins</h3>
                <div class="space-y-2">
                    @foreach($allTreatments as $treatment)
                    @php $payStatus = $treatment->paymentStatus(); @endphp
                    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">
                        <div class="px-4 py-3 flex items-center gap-3">
                            {{-- Tooth badge --}}
                            @if($treatment->tooth_number)
                            <span
                                class="inline-flex items-center justify-center w-9 h-7 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold rounded flex-shrink-0">
                                {{ $treatment->tooth_number }}
                            </span>
                            @endif
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-semibold text-white">{{ $treatment->name }}</p>
                                    <span class="status-badge pay-{{ $payStatus }}">
                                        {{ match($payStatus) { 'paid'=>'Payé','partial'=>'Partiel',default=>'En attente'
                                        } }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $treatment->date ? $treatment->date->format('d/m/Y') : '—' }}
                                    • {{ number_format($treatment->cost, 3) }} TND
                                    @if($treatment->remainingBalance() > 0)
                                    • <span class="text-red-400">{{ number_format($treatment->remainingBalance(), 3) }}
                                        restant</span>
                                    @endif
                                </p>
                            </div>
                            {{-- + Paiement button --}}
                            <button wire:click="openPaymentModal({{ $treatment->id }})"
                                class="text-xs bg-green-900/40 hover:bg-green-900/60 text-green-400 px-2 py-1 rounded-lg transition-colors font-medium whitespace-nowrap flex-shrink-0">
                                + Paiement
                            </button>
                            @if($confirmDeleteTreatmentId === $treatment->id)
                            <div
                                class="flex items-center gap-1 bg-red-900/20 border border-red-800 rounded-lg px-2 py-1">
                                <span class="text-xs text-red-400 font-medium">Supprimer ?</span>
                                <button wire:click="deleteTreatment({{ $treatment->id }})"
                                    class="text-xs bg-red-600 hover:bg-red-700 text-white px-2 py-0.5 rounded font-semibold transition-colors">Oui</button>
                                <button wire:click="cancelDeleteTreatment"
                                    class="text-xs text-gray-400 hover:text-white px-1.5 py-0.5 rounded transition-colors">Non</button>
                            </div>
                            @else
                            <button wire:click="confirmDeleteTreatment({{ $treatment->id }})"
                                class="p-1.5 text-gray-600 hover:text-red-400 hover:bg-red-900/20 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            @endif
                        </div>

                        {{-- Payment rows --}}
                        @if($treatment->payments->count() > 0)
                        <div class="border-t border-gray-700 px-4 py-2 space-y-1">
                            @foreach($treatment->payments->sortByDesc('paid_at') as $payment)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 text-xs">
                                    <span class="text-green-400 font-semibold">+ {{ number_format($payment->amount, 3)
                                        }} TND</span>
                                    <span class="text-gray-500 capitalize">{{ $payment->payment_method }}</span>
                                    <span class="text-gray-600">{{
                                        \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y') }}</span>
                                    @if($payment->notes)
                                    <span class="text-gray-600 italic">{{ $payment->notes }}</span>
                                    @endif
                                </div>
                                <button wire:click="deletePayment({{ $payment->id }})"
                                    class="text-gray-700 hover:text-red-400 transition-colors">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach

                    {{-- Totals --}}
                    @php
                    $totalCost = $allTreatments->sum('cost');
                    $totalPaid = $allTreatments->sum(fn($t) => $t->totalPaid());
                    $totalBalance = $totalCost - $totalPaid;
                    @endphp
                    <div class="flex justify-between text-xs pt-3 border-t border-gray-700 mt-2 px-1">
                        <span class="text-gray-400">Total coût : <strong class="text-white">{{ number_format($totalCost,
                                3) }} TND</strong></span>
                        <span class="text-gray-400">Total payé : <strong class="text-white">{{ number_format($totalPaid,
                                3) }} TND</strong></span>
                        <span class="text-gray-400">Restant :
                            <strong class="{{ $totalBalance > 0 ? 'text-red-400' : 'text-green-400' }}">
                                {{ number_format($totalBalance, 3) }} TND
                            </strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- ── Payment Modal ────────────────────────────────────────────── --}}
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,.75)">
        <div class="bg-gray-800 border border-gray-600 rounded-2xl p-6 w-full max-w-md mx-4 shadow-2xl">
            <h3 class="text-sm font-semibold text-amber-400 mb-5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Enregistrer un paiement
            </h3>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="dc-label">Montant (TND) *</label>
                        <input type="number" step="0.001" wire:model="paymentForm.amount" class="dc-input"
                            placeholder="0.000" />
                        @error('paymentForm.amount') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="dc-label">Date *</label>
                        <input type="date" wire:model="paymentForm.paid_at" class="dc-input" />
                    </div>
                </div>
                <div>
                    <label class="dc-label">Méthode</label>
                    <select wire:model="paymentForm.payment_method" class="dc-input">
                        <option value="espèces">Espèces</option>
                        <option value="chèque">Chèque</option>
                        <option value="virement">Virement</option>
                        <option value="carte">Carte bancaire</option>
                    </select>
                </div>
                <div>
                    <label class="dc-label">Notes</label>
                    <input type="text" wire:model="paymentForm.notes" class="dc-input"
                        placeholder="Référence chèque, remarque..." />
                </div>
                <div class="flex gap-2 pt-1">
                    <button wire:click="savePayment" wire:loading.attr="disabled" class="dc-save-btn">
                        <span wire:loading.remove wire:target="savePayment">Enregistrer</span>
                        <span wire:loading wire:target="savePayment">...</span>
                    </button>
                    <button wire:click="closePaymentModal" class="dc-cancel-btn">Annuler</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>