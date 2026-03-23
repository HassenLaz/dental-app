{{-- resources/views/livewire/patient-files.blade.php --}}
<div class="patient-files-wrapper">

    <style>
        .pf-input {
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
        .pf-input:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245,158,11,0.2);
        }
        .pf-input::placeholder { color: #6b7280; }

        .pf-label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9ca3af;
            margin-bottom: 0.3rem;
        }

        .pf-upload-zone {
            border: 2px dashed #374151;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            background: #111827;
        }
        .pf-upload-zone:hover {
            border-color: #f59e0b;
            background: rgba(245,158,11,0.03);
        }
        .pf-upload-zone.has-file {
            border-color: #22c55e;
            background: rgba(34,197,94,0.05);
        }

        .pf-save-btn {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.55rem 1.25rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.1s;
        }
        .pf-save-btn:hover   { opacity: 0.9; transform: translateY(-1px); }
        .pf-save-btn:active  { transform: translateY(0); }
        .pf-save-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        .pf-cancel-btn {
            padding: 0.55rem 1rem;
            font-size: 0.875rem;
            color: #9ca3af;
            border-radius: 0.5rem;
            border: 1px solid #374151;
            background: transparent;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }
        .pf-cancel-btn:hover { background: #374151; color: #f9fafb; }

        .pf-file-card {
            background: #1f2937;
            border: 1px solid #374151;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.875rem;
            transition: border-color 0.15s;
        }
        .pf-file-card:hover { border-color: #4b5563; }

        .pf-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }
        .pf-icon-pdf   { background: #450a0a; }
        .pf-icon-image { background: #1e3a5f; }
        .pf-icon-other { background: #1f2937; border: 1px solid #374151; }
    </style>

    <div class="bg-gray-900 rounded-2xl border border-gray-700 overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-white">Fichiers du patient</h2>
                <p class="text-xs text-gray-400 mt-0.5">PDFs, radiographies, ordonnances...</p>
            </div>
            @if(!$showForm)
            <button
                wire:click="$set('showForm', true)"
                class="flex items-center gap-1.5 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 text-xs font-semibold px-3 py-1.5 rounded-full transition-colors"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter un fichier
            </button>
            @endif
        </div>

        {{-- Upload Form --}}
        @if($showForm)
        <div class="px-6 py-5 border-b border-gray-700 bg-gray-800/50">
            <h3 class="text-sm font-semibold text-amber-400 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Nouveau fichier
            </h3>

            <div class="space-y-4">

                {{-- File name --}}
                <div>
                    <label class="pf-label">Nom du fichier *</label>
                    <input
                        type="text"
                        wire:model="name"
                        placeholder="ex : Radio panoramique, Ordonnance juin 2026..."
                        class="pf-input"
                    />
                    @error('name') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- File upload --}}
                <div>
                    <label class="pf-label">Fichier * <span class="normal-case text-gray-600 font-normal">(PDF, JPG, PNG, WEBP — max 10 Mo)</span></label>

                    <label class="pf-upload-zone {{ $file ? 'has-file' : '' }} block">
                        <input
                            type="file"
                            wire:model="file"
                            accept=".pdf,.jpg,.jpeg,.png,.gif,.webp"
                            class="sr-only"
                        />

                        @if($file)
                            <div class="flex items-center justify-center gap-3">
                                <svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-left">
                                    <p class="text-sm font-medium text-green-400">{{ $file->getClientOriginalName() }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ round($file->getSize() / 1024, 1) }} KB — cliquez pour changer</p>
                                </div>
                            </div>
                        @else
                            <div wire:loading.remove wire:target="file">
                                <svg class="w-8 h-8 text-gray-600 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <p class="text-sm text-gray-400">Glissez un fichier ici ou <span class="text-amber-400 font-medium">cliquez pour choisir</span></p>
                                <p class="text-xs text-gray-600 mt-1">PDF, JPG, PNG, WEBP jusqu'à 10 Mo</p>
                            </div>
                            <div wire:loading wire:target="file" class="text-sm text-gray-400">
                                Chargement...
                            </div>
                        @endif
                    </label>
                    @error('file') <span class="text-xs text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-2 pt-1">
                    <button
                        wire:click="save"
                        wire:loading.attr="disabled"
                        class="pf-save-btn"
                    >
                        <span wire:loading.remove wire:target="save">💾 Enregistrer</span>
                        <span wire:loading wire:target="save">Envoi en cours...</span>
                    </button>
                    <button wire:click="$set('showForm', false)" class="pf-cancel-btn">Annuler</button>
                </div>

            </div>
        </div>
        @endif

        {{-- File list --}}
        <div class="p-6">
            @if($files->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <p class="text-sm text-gray-500">Aucun fichier pour ce patient</p>
                    <p class="text-xs text-gray-600 mt-1">Ajoutez des radios, ordonnances, comptes rendus...</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($files as $f)
                    <div class="pf-file-card">

                        {{-- Icon --}}
                        <div class="pf-icon {{ $f->isPdf() ? 'pf-icon-pdf' : ($f->isImage() ? 'pf-icon-image' : 'pf-icon-other') }}">
                            @if($f->isPdf())
                                <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($f->isImage())
                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $f->name }}</p>
                            <div class="flex items-center gap-3 mt-0.5 flex-wrap">
                                <span class="text-xs text-gray-500 truncate max-w-[180px]">{{ $f->file_name }}</span>
                                <span class="text-xs text-gray-600">{{ $f->formattedSize() }}</span>
                                <span class="text-xs text-gray-600">{{ $f->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- View/Download --}}
                            <a
                                href="{{ $f->url() }}"
                                target="_blank"
                                class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
                                title="Ouvrir"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>

                            {{-- Download --}}
                            <a
                                href="{{ $f->url() }}"
                                download="{{ $f->file_name }}"
                                class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
                                title="Télécharger"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>

                            {{-- Delete --}}
                            @if($confirmDeleteId === $f->id)
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-red-400 font-medium">Supprimer ?</span>
                                    <button
                                        wire:click="delete({{ $f->id }})"
                                        class="p-1.5 text-red-400 hover:text-red-300 hover:bg-red-900/30 rounded-lg transition-colors"
                                        title="Confirmer"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="cancelDelete"
                                        class="p-1.5 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
                                        title="Annuler"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <button
                                    wire:click="confirmDelete({{ $f->id }})"
                                    class="p-1.5 text-gray-600 hover:text-red-400 hover:bg-red-900/20 rounded-lg transition-colors"
                                    title="Supprimer"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            @endif
                        </div>

                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
