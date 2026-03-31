{{-- resources/views/livewire/treatment-plan-manager.blade.php --}}
<div class="treatment-plan-wrapper">
    <style>
        .tp-input {
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

        .tp-input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, .2);
        }

        .tp-input::placeholder {
            color: #6b7280;
        }

        select.tp-input {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .75rem center;
            padding-right: 2.25rem;
        }

        .tp-label {
            display: block;
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #9ca3af;
            margin-bottom: .3rem;
        }

        .tp-btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            font-size: .875rem;
            font-weight: 600;
            padding: .55rem 1.25rem;
            border-radius: .5rem;
            border: none;
            cursor: pointer;
            transition: opacity .15s, transform .1s;
        }

        .tp-btn-primary:hover {
            opacity: .9;
            transform: translateY(-1px);
        }

        .tp-btn-secondary {
            padding: .55rem 1rem;
            font-size: .875rem;
            color: #9ca3af;
            border-radius: .5rem;
            border: 1px solid #374151;
            background: transparent;
            cursor: pointer;
            transition: background .15s, color .15s;
        }

        .tp-btn-secondary:hover {
            background: #374151;
            color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            font-size: .65rem;
            font-weight: 700;
            padding: .15rem .55rem;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .s-draft {
            background: #1f2937;
            color: #9ca3af;
            border: 1px solid #374151;
        }

        .s-approved {
            background: #1e3a5f;
            color: #60a5fa;
        }

        .s-in_progress {
            background: #422006;
            color: #fbbf24;
        }

        .s-completed {
            background: #052e16;
            color: #4ade80;
        }

        .s-planned {
            background: #1f2937;
            color: #9ca3af;
            border: 1px solid #374151;
        }

        .s-cancelled {
            background: #450a0a;
            color: #f87171;
        }

        .pay-pending {
            background: #450a0a;
            color: #f87171;
        }

        .pay-partial {
            background: #422006;
            color: #fbbf24;
        }

        .pay-paid {
            background: #052e16;
            color: #4ade80;
        }

        .tp-suggestion-item {
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

        .tp-suggestion-item:hover {
            background: #374151;
        }

        .tp-suggestion-item:last-child {
            border-bottom: none;
        }
    </style>

    <div class="bg-gray-900 rounded-2xl border border-gray-700 overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-white">Plans de traitement</h2>
                <p class="text-xs text-gray-400 mt-0.5">Regroupez plusieurs actes avec suivi des paiements</p>
            </div>
            @if(!$showPlanForm)
            <button wire:click="$set('showPlanForm', true)"
                class="flex items-center gap-1.5 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 text-xs font-semibold px-3 py-1.5 rounded-full transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau plan
            </button>
            @endif
        </div>

        {{-- ── Create Form ─────────────────────────────────────────── --}}
        @if($showPlanForm)
        <div class="px-6 py-5 border-b border-gray-700 bg-gray-800/50">
            <h3 class="text-sm font-semibold text-amber-400 mb-5">Créer un plan de traitement</h3>
            <div class="space-y-4">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="tp-label">Titre du plan</label>
                        <input type="text" wire:model="planForm.title" class="tp-input"
                            placeholder="ex : Réhabilitation complète" />
                    </div>
                    <div>
                        <label class="tp-label">Date *</label>
                        <input type="date" wire:model="planForm.created_date" class="tp-input" />
                    </div>
                </div>

                <div>
                    <label class="tp-label">Coût total estimé (TND) *</label>
                    <input type="number" step="0.001" wire:model="planForm.total_cost" class="tp-input"
                        placeholder="0.000" />
                    @error('planForm.total_cost') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="tp-label">Notes</label>
                    <textarea wire:model="planForm.notes" rows="2" class="tp-input resize-none"
                        placeholder="Observations..."></textarea>
                </div>

                {{-- Plan Items --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="tp-label mb-0">Actes du plan</label>
                        <button type="button" wire:click="addItem"
                            class="text-xs text-amber-400 hover:text-amber-300 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Ajouter un acte
                        </button>
                    </div>

                    <div class="space-y-2">
                        @foreach($planItems as $i => $item)
                        <div class="flex gap-2 items-start bg-gray-900 border border-gray-700 rounded-lg p-3">

                            {{-- Tooth number --}}
                            <div style="width:70px;flex-shrink:0">
                                <input type="text" wire:model="planItems.{{ $i }}.tooth_number" placeholder="Dent"
                                    class="tp-input text-center" maxlength="2" />
                            </div>

                            {{-- Act name with autocomplete --}}
                            <div class="flex-1 relative">
                                <input type="text" wire:model.live="planItems.{{ $i }}.name"
                                    wire:keydown.escape="hideSuggestions" placeholder="Nom de l'acte *" class="tp-input"
                                    autocomplete="off" />
                                @error("planItems.{$i}.name")
                                <span class="text-xs text-red-400 mt-0.5 block">{{ $message }}</span>
                                @enderror

                                {{-- Suggestions dropdown --}}
                                @if($showSuggestions && $focusedItemIndex === $i && count($suggestions) > 0)
                                <div class="absolute z-50 w-full mt-1 rounded-lg border border-gray-600 overflow-hidden"
                                    style="background:#1f2937;box-shadow:0 8px 24px rgba(0,0,0,.5)">
                                    @foreach($suggestions as $act)
                                    <button type="button"
                                        wire:click="selectItemSuggestion('{{ addslashes($act['name']) }}', {{ $i }})"
                                        class="tp-suggestion-item">
                                        <span style="font-size:.875rem;color:#f9fafb">{{ $act['name'] }}</span>
                                        @if(isset($act['cost']))
                                        <span style="font-size:.75rem;font-weight:600;color:#f59e0b;white-space:nowrap">
                                            {{ number_format($act['cost'], 3) }} TND
                                        </span>
                                        @endif
                                    </button>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            {{-- Description --}}
                            <div class="flex-1">
                                <input type="text" wire:model="planItems.{{ $i }}.description"
                                    placeholder="Description (optionnel)" class="tp-input" />
                            </div>

                            {{-- Remove --}}
                            @if(count($planItems) > 1)
                            <button type="button" wire:click="removeItem({{ $i }})"
                                class="text-gray-600 hover:text-red-400 transition-colors mt-2 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-2 pt-1">
                    <button wire:click="savePlan" wire:loading.attr="disabled" class="tp-btn-primary">
                        <span wire:loading.remove wire:target="savePlan">💾 Enregistrer le plan</span>
                        <span wire:loading wire:target="savePlan">Enregistrement...</span>
                    </button>
                    <button wire:click="$set('showPlanForm', false)" class="tp-btn-secondary">Annuler</button>
                </div>
            </div>
        </div>
        @endif

        {{-- ── Plans List ───────────────────────────────────────────── --}}
        <div class="p-6">
            @if($plans->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <p class="text-sm text-gray-500">Aucun plan de traitement</p>
            </div>
            @else
            <div class="space-y-4">
                @foreach($plans as $plan)
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden">

                    {{-- Plan Header --}}
                    <div class="px-4 py-3 flex items-center gap-3">
                        {{-- Expand toggle --}}
                        <button wire:click="toggleExpand({{ $plan->id }})" class="flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-400 transition-transform {{ $expandedPlanId === $plan->id ? 'rotate-90' : '' }}"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0 cursor-pointer" wire:click="toggleExpand({{ $plan->id }})">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-semibold text-white">{{ $plan->title }}</p>
                                <span class="status-badge s-{{ $plan->status }}">
                                    {{ match($plan->status) {
                                    'draft'=>'Brouillon','approved'=>'Approuvé','in_progress'=>'En
                                    cours','completed'=>'Terminé',default=>$plan->status } }}
                                </span>
                                <span class="status-badge pay-{{ $plan->payment_status }}">
                                    {{ match($plan->payment_status) { 'paid'=>'Payé','partial'=>'Partiel',default=>'En
                                    attente' } }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $plan->created_date->format('d/m/Y') }} •
                                {{ $plan->items->count() }} acte(s) •
                                {{ number_format($plan->total_cost, 3) }} TND
                                @if($plan->remainingBalance() > 0)
                                • <span class="text-red-400">{{ number_format($plan->remainingBalance(), 3) }}
                                    restant</span>
                                @endif
                            </p>
                        </div>

                        {{-- Action buttons --}}
                        <div class="flex items-center gap-1 flex-shrink-0">
                            {{-- Add payment --}}
                            <button wire:click="openPaymentForm({{ $plan->id }})"
                                class="text-xs bg-green-900/40 hover:bg-green-900/60 text-green-400 px-2 py-1 rounded-lg transition-colors font-medium">
                                + Paiement
                            </button>

                            {{-- Edit --}}
                            <button wire:click="openEditForm({{ $plan->id }})"
                                class="p-1.5 text-gray-400 hover:text-amber-400 hover:bg-amber-500/10 rounded-lg transition-colors"
                                title="Modifier">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>

                            {{-- Delete with inline confirmation --}}
                            @if($confirmDeletePlanId === $plan->id)
                            <div
                                class="flex items-center gap-1.5 bg-red-900/20 border border-red-800 rounded-lg px-2 py-1">
                                <span class="text-xs text-red-400 font-medium">Supprimer ?</span>
                                <button wire:click="deletePlan({{ $plan->id }})"
                                    class="text-xs bg-red-600 hover:bg-red-700 text-white px-2 py-0.5 rounded font-semibold transition-colors">
                                    <span class="status-badge pay-pending">Oui</span>
                                </button>
                                <button wire:click="cancelDeletePlan"
                                    class="text-xs text-gray-400 hover:text-white px-1.5 py-0.5 rounded transition-colors">
                                    <span class="status-badge s-approved">Non</span>
                                </button>
                            </div>
                            @else
                            <button wire:click="confirmDeletePlan({{ $plan->id }})"
                                class="p-1.5 text-gray-600 hover:text-red-400 hover:bg-red-900/20 rounded-lg transition-colors"
                                title="Supprimer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>

                    {{-- Expanded Content --}}
                    @if($expandedPlanId === $plan->id)
                    <div class="border-t border-gray-700 px-4 py-4 space-y-5">

                        {{-- Plan status selector --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="tp-label mb-0 mr-1">Statut :</span>
                            @foreach(['draft'=>'Brouillon','approved'=>'Approuvé','in_progress'=>'En
                            cours','completed'=>'Terminé'] as $val => $label)
                            <button wire:click="updatePlanStatus({{ $plan->id }}, '{{ $val }}')"
                                class="text-xs px-2.5 py-1 rounded-full border transition-colors {{ $plan->status === $val ? 'bg-amber-500/20 border-amber-500/50 text-amber-400' : 'border-gray-600 text-gray-500 hover:border-gray-400 hover:text-gray-300' }}">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>

                        {{-- Items --}}
                        <div>
                            <p class="tp-label mb-2">Actes prévus</p>
                            <div class="space-y-1.5">
                                @foreach($plan->items as $item)
                                <div class="flex items-center gap-3 bg-gray-900 rounded-lg px-3 py-2.5">
                                    @if($item->tooth_number)
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-6 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold rounded flex-shrink-0">
                                        {{ $item->tooth_number }}
                                    </span>
                                    @endif
                                    <span class="flex-1 text-sm text-gray-200">{{ $item->name }}</span>
                                    @if($item->description)
                                    <span class="text-xs text-gray-500 truncate max-w-xs hidden sm:block">{{
                                        $item->description }}</span>
                                    @endif
                                    <div class="flex items-center gap-1 flex-shrink-0 flex-wrap justify-end">
                                        @foreach(['planned'=>'Planifié','in_progress'=>'En
                                        cours','completed'=>'Fait','cancelled'=>'Annulé'] as $val => $label)
                                        <button wire:click="updateItemStatus({{ $item->id }}, '{{ $val }}')"
                                            class="text-xs px-2 py-0.5 rounded-full border transition-colors
                                        {{ $item->status === $val
                                            ? 'bg-amber-500/20 border-amber-500/50 text-amber-400'
                                            : 'border-gray-700 text-gray-600 hover:text-gray-300 hover:border-gray-500' }}">
                                            {{ $label }}
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Payment History --}}
                        <div>
                            <p class="tp-label mb-2">Historique des paiements</p>
                            @if($plan->payments->isEmpty())
                            <p class="text-xs text-gray-600 italic">Aucun paiement enregistré.</p>
                            @else
                            <div class="space-y-1.5">
                                @foreach($plan->payments->sortByDesc('paid_at') as $payment)
                                <div class="flex items-center justify-between bg-gray-900 rounded-lg px-3 py-2">
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <span class="text-sm font-semibold text-green-400">+ {{
                                            number_format($payment->amount, 3) }} TND</span>
                                        <span class="text-xs text-gray-400 capitalize">{{ $payment->payment_method
                                            }}</span>
                                        <span class="text-xs text-gray-500">{{
                                            \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y') }}</span>
                                        @if($payment->notes)
                                        <span class="text-xs text-gray-600 italic">{{ $payment->notes }}</span>
                                        @endif
                                    </div>
                                    <button wire:click="deletePayment({{ $payment->id }})"
                                        class="text-gray-700 hover:text-red-400 transition-colors flex-shrink-0 ml-2">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between text-xs mt-3 pt-3 border-t border-gray-700">
                                <span class="text-gray-400">Total payé : <strong class="text-white">{{
                                        number_format($plan->amount_paid, 3) }} TND</strong></span>
                                <span class="text-gray-400">Restant : <strong
                                        class="{{ $plan->remainingBalance() > 0 ? 'text-red-400' : 'text-green-400' }}">{{
                                        number_format($plan->remainingBalance(), 3) }} TND</strong></span>
                            </div>
                            @endif
                        </div>

                    </div>
                    @endif

                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>

    {{-- ── Edit Modal ─────────────────────────────────────────────────── --}}
    @if($showEditForm)
    <div class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,.75)">
        <div class="bg-gray-800 border border-gray-600 rounded-2xl p-6 w-full max-w-lg mx-4 shadow-2xl">
            <h3 class="text-sm font-semibold text-amber-400 mb-5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Modifier le plan
            </h3>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="tp-label">Titre</label>
                        <input type="text" wire:model="editForm.title" class="tp-input" />
                        @error('editForm.title') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="tp-label">Date</label>
                        <input type="date" wire:model="editForm.created_date" class="tp-input" />
                    </div>
                </div>
                <div>
                    <label class="tp-label">Coût total estimé (TND) *</label>
                    <input type="number" step="0.001" wire:model="editForm.total_cost" class="tp-input"
                        placeholder="0.000" />
                    @error('editForm.total_cost') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="tp-label">Statut</label>
                    <select wire:model="editForm.status" class="tp-input">
                        <option value="draft">Brouillon</option>
                        <option value="approved">Approuvé</option>
                        <option value="in_progress">En cours</option>
                        <option value="completed">Terminé</option>
                    </select>
                </div>
                <div>
                    <label class="tp-label">Notes</label>
                    <textarea wire:model="editForm.notes" rows="2" class="tp-input resize-none"
                        placeholder="Observations..."></textarea>
                </div>
                <div class="flex gap-2 pt-1">
                    <button wire:click="saveEdit" wire:loading.attr="disabled" class="tp-btn-primary">
                        <span wire:loading.remove wire:target="saveEdit">💾 Enregistrer</span>
                        <span wire:loading wire:target="saveEdit">Enregistrement...</span>
                    </button>
                    <button wire:click="cancelEdit" class="tp-btn-secondary">Annuler</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Payment Modal ──────────────────────────────────────────────── --}}
    @if($showPaymentForm)
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
                        <label class="tp-label">Montant (TND) *</label>
                        <input type="number" step="0.001" wire:model="paymentForm.amount" class="tp-input"
                            placeholder="0.000" />
                        @error('paymentForm.amount') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="tp-label">Date *</label>
                        <input type="date" wire:model="paymentForm.paid_at" class="tp-input" />
                    </div>
                </div>
                <div>
                    <label class="tp-label">Méthode de paiement</label>
                    <select wire:model="paymentForm.payment_method" class="tp-input">
                        <option value="espèces">Espèces</option>
                        <option value="chèque">Chèque</option>
                        <option value="virement">Virement</option>
                        <option value="carte">Carte bancaire</option>
                    </select>
                </div>
                <div>
                    <label class="tp-label">Notes</label>
                    <input type="text" wire:model="paymentForm.notes" class="tp-input"
                        placeholder="Référence chèque, remarque..." />
                </div>
                <div class="flex gap-2 pt-1">
                    <button wire:click="savePayment" wire:loading.attr="disabled" class="tp-btn-primary">
                        <span wire:loading.remove wire:target="savePayment">Enregistrer</span>
                        <span wire:loading wire:target="savePayment">...</span>
                    </button>
                    <button wire:click="$set('showPaymentForm', false)" class="tp-btn-secondary">Annuler</button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>