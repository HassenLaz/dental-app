{{-- resources/views/livewire/dental-chart.blade.php --}}
<div class="dental-chart-wrapper">

    <style>
        .dental-chart-wrapper {
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* Smooth, contained scale — no jumping */
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

        /* Form inputs */
        .dc-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid #374151;
            border-radius: 0.5rem;
            background: #1f2937;
            color: #f9fafb;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .dc-input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
        }

        .dc-input::placeholder {
            color: #6b7280;
        }

        .dc-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9ca3af;
            margin-bottom: 0.3rem;
        }

        .dc-save-btn {
            flex: 1;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.1s;
            letter-spacing: 0.02em;
        }

        .dc-save-btn:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .dc-save-btn:active {
            transform: translateY(0);
        }

        .dc-save-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .dc-cancel-btn {
            padding: 0.6rem 1rem;
            font-size: 0.875rem;
            color: #9ca3af;
            border-radius: 0.5rem;
            border: 1px solid #374151;
            background: transparent;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }

        .dc-cancel-btn:hover {
            background: #374151;
            color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            text-transform: capitalize;
        }

        .status-paid {
            background: #052e16;
            color: #4ade80;
        }

        .status-partial {
            background: #422006;
            color: #fbbf24;
        }

        .status-pending {
            background: #450a0a;
            color: #f87171;
        }

        select.dc-input {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            padding-right: 2.25rem;
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

                {{-- SVG Chart --}}
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
                        ['num'=>'11','x'=>228,'y'=>55,'type'=>'incisor'],
                        ['num'=>'12','x'=>206,'y'=>68,'type'=>'incisor'],
                        ['num'=>'13','x'=>182,'y'=>90,'type'=>'canine'],
                        ['num'=>'14','x'=>160,'y'=>118,'type'=>'premolar'],
                        ['num'=>'15','x'=>142,'y'=>150,'type'=>'premolar'],
                        ['num'=>'16','x'=>126,'y'=>190,'type'=>'molar'],
                        ['num'=>'17','x'=>113,'y'=>232,'type'=>'molar'],
                        ['num'=>'18','x'=>103,'y'=>274,'type'=>'molar'],
                        ];
                        $upperLeft = [
                        ['num'=>'21','x'=>272,'y'=>55,'type'=>'incisor'],
                        ['num'=>'22','x'=>294,'y'=>68,'type'=>'incisor'],
                        ['num'=>'23','x'=>318,'y'=>90,'type'=>'canine'],
                        ['num'=>'24','x'=>340,'y'=>118,'type'=>'premolar'],
                        ['num'=>'25','x'=>358,'y'=>150,'type'=>'premolar'],
                        ['num'=>'26','x'=>374,'y'=>190,'type'=>'molar'],
                        ['num'=>'27','x'=>387,'y'=>232,'type'=>'molar'],
                        ['num'=>'28','x'=>397,'y'=>274,'type'=>'molar'],
                        ];
                        $lowerLeft = [
                        ['num'=>'31','x'=>272,'y'=>525,'type'=>'incisor'],
                        ['num'=>'32','x'=>294,'y'=>512,'type'=>'incisor'],
                        ['num'=>'33','x'=>318,'y'=>490,'type'=>'canine'],
                        ['num'=>'34','x'=>340,'y'=>462,'type'=>'premolar'],
                        ['num'=>'35','x'=>358,'y'=>430,'type'=>'premolar'],
                        ['num'=>'36','x'=>374,'y'=>390,'type'=>'molar'],
                        ['num'=>'37','x'=>387,'y'=>348,'type'=>'molar'],
                        ['num'=>'38','x'=>397,'y'=>306,'type'=>'molar'],
                        ];
                        $lowerRight = [
                        ['num'=>'41','x'=>228,'y'=>525,'type'=>'incisor'],
                        ['num'=>'42','x'=>206,'y'=>512,'type'=>'incisor'],
                        ['num'=>'43','x'=>182,'y'=>490,'type'=>'canine'],
                        ['num'=>'44','x'=>160,'y'=>462,'type'=>'premolar'],
                        ['num'=>'45','x'=>142,'y'=>430,'type'=>'premolar'],
                        ['num'=>'46','x'=>126,'y'=>390,'type'=>'molar'],
                        ['num'=>'47','x'=>113,'y'=>348,'type'=>'molar'],
                        ['num'=>'48','x'=>103,'y'=>306,'type'=>'molar'],
                        ];

                        $colors = [
                        'incisor' => ['fill'=>'#1e3a5f','stroke'=>'#3b82f6'],
                        'canine' => ['fill'=>'#431407','stroke'=>'#f97316'],
                        'premolar' => ['fill'=>'#052e16','stroke'=>'#22c55e'],
                        'molar' => ['fill'=>'#450a0a','stroke'=>'#ef4444'],
                        ];

                        $sizes = [
                        'incisor'  => ['w'=>22,'h'=>28,'rx'=>6],
                        'canine'   => ['w'=>21,'h'=>30,'rx'=>6],
                        'premolar' => ['w'=>24,'h'=>26,'rx'=>6],
                        'molar'    => ['w'=>28,'h'=>24,'rx'=>7],
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
                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            <span class="w-3 h-3 rounded-sm inline-block"
                                style="background:#1e3a5f;border:1.5px solid #3b82f6"></span> Incisive
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            <span class="w-3 h-3 rounded-sm inline-block"
                                style="background:#431407;border:1.5px solid #f97316"></span> Canine
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            <span class="w-3 h-3 rounded-sm inline-block"
                                style="background:#052e16;border:1.5px solid #22c55e"></span> Prémolaire
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            <span class="w-3 h-3 rounded-sm inline-block"
                                style="background:#450a0a;border:1.5px solid #ef4444"></span> Molaire
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-500">
                            <span class="w-3 h-3 rounded-sm inline-block"
                                style="background:#422006;border:1.5px solid #f59e0b"></span> Soin enregistré
                        </div>
                    </div>
                </div>

                {{-- Right panel --}}
                <div class="flex flex-col gap-5">

                    @if($selectedToothNumber)
                    <div class="bg-gray-800 border border-gray-600 rounded-xl p-6">
                        <h3 class="font-semibold text-amber-400 mb-5 flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Ajouter un soin — Dent {{ $selectedToothNumber }}
                        </h3>

                        <div class="space-y-4">

                            <div>
                                <label class="dc-label">Nom du soin *</label>
                                <input type="text" wire:model="form.name"
                                    placeholder="ex : Extraction, Détartrage, Couronne..." class="dc-input" />
                                @error('form.name') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                                @enderror
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
                                    <label class="dc-label">Montant payé (TND)</label>
                                    <input type="number" step="0.001" wire:model="form.amount_paid" placeholder="0.000"
                                        class="dc-input" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="dc-label">Date *</label>
                                    <input type="date" wire:model="form.date" class="dc-input" />
                                    @error('form.date') <span class="text-xs text-red-400 mt-1 block">{{ $message
                                        }}</span> @enderror
                                </div>
                                <div>
                                    <label class="dc-label">Statut paiement</label>
                                    <select wire:model="form.payment_status" class="dc-input">
                                        <option value="pending">En attente</option>
                                        <option value="partial">Partiel</option>
                                        <option value="paid">Payé</option>
                                    </select>
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

                    @if($toothTreatments->count() > 0)
                    <div>
                        <h4
                            class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3 flex items-center gap-2 p-6">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Historique — Dent {{ $selectedToothNumber }}
                        </h4>
                        <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                            @foreach($toothTreatments as $treatment)
                            <div class="bg-gray-800 border border-gray-700 rounded-lg p-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-white truncate">{{ $treatment->name }}</p>
                                        @if($treatment->description)
                                        <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $treatment->description
                                            }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-1">{{
                                            \Carbon\Carbon::parse($treatment->date)->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-sm font-semibold text-white">{{ number_format($treatment->cost,
                                            3) }} TND</p>
                                        <span class="status-badge status-{{ $treatment->payment_status }} mt-1">
                                            {{ match($treatment->payment_status) { 'paid' => 'Payé', 'partial' =>
                                            'Partiel', default => 'En attente' } }}
                                        </span>
                                    </div>
                                </div>
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

        {{-- All Treatments Summary --}}
        @if($allTreatments->count() > 0)
        <div class="px-6 pb-6">
            <div class="border-t border-gray-700 pt-5 p-5">
                <h1 class="font-semibold uppercase tracking-wide mb-4 mt-6">Tous les soins</h1>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                                <th class="pb-2 pr-4">Dent</th>
                                <th class="pb-2 pr-4">Soin</th>
                                <th class="pb-2 pr-4">Date</th>
                                <th class="pb-2 pr-4">Coût</th>
                                <th class="pb-2 pr-4">Payé</th>
                                <th class="pb-2">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @foreach($allTreatments as $treatment)
                            <tr class="text-gray-300">
                                <td class="py-2 pr-4">
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-6 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold rounded">
                                        {{ $treatment->tooth_number ?? '—' }}
                                    </span>
                                </td>
                                <td class="py-2 pr-4 font-medium text-white">{{ $treatment->name }}</td>
                                <td class="py-2 pr-4 text-gray-500">{{
                                    \Carbon\Carbon::parse($treatment->date)->format('d/m/Y') }}</td>
                                <td class="py-2 pr-4 font-semibold text-white">{{ number_format($treatment->cost, 3) }}
                                </td>
                                <td class="py-2 pr-4 text-gray-400">{{ number_format($treatment->amount_paid, 3) }}</td>
                                <td class="py-2">
                                    <span class="status-badge status-{{ $treatment->payment_status }}">
                                        {{ match($treatment->payment_status) { 'paid' => 'Payé', 'partial' => 'Partiel',
                                        default => 'En attente' } }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="text-xs font-semibold text-gray-300 border-t border-gray-700">
                                <td colspan="3" class="pt-3 pr-4 text-right text-gray-500">Total :</td>
                                <td class="pt-3 pr-4 text-white">{{ number_format($allTreatments->sum('cost'), 3) }} TND
                                </td>
                                <td class="pt-3 pr-4 text-gray-400">{{ number_format($allTreatments->sum('amount_paid'),
                                    3) }} TND</td>
                                <td class="pt-3">
                                    @php $balance = $allTreatments->sum('cost') - $allTreatments->sum('amount_paid');
                                    @endphp
                                    @if($balance > 0)
                                    <span class="text-red-400 font-semibold">{{ number_format($balance, 3) }}
                                        restant</span>
                                    @else
                                    <span class="text-green-400 font-semibold">Soldé</span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>