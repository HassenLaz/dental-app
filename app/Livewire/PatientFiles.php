<?php

// app/Livewire/PatientFiles.php

namespace App\Livewire;

use App\Models\PatientFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;

class PatientFiles extends Component
{
    use WithFileUploads;

    public int $patientId;

    // Upload form
    public ?string $name = null;
    public $file = null;

    // UI state
    public bool $showForm = false;
    public ?int $confirmDeleteId = null;

    public Collection $files;

    public function mount(int $patientId): void
    {
        $this->patientId = $patientId;
        $this->loadFiles();
    }

    public function loadFiles(): void
    {
        $this->files = PatientFile::where('patient_id', $this->patientId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function updatedFile(): void
    {
        // Auto-fill name from filename if empty
        if (empty($this->name) && $this->file) {
            $this->name = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240', // 10MB max
        ], [
            'file.mimes' => 'Seuls les fichiers PDF et images (JPG, PNG, GIF, WEBP) sont acceptés.',
            'file.max'   => 'Le fichier ne doit pas dépasser 10 Mo.',
            'name.required' => 'Veuillez donner un nom au fichier.',
        ]);

        $path = $this->file->store("patients/{$this->patientId}/files", 'public');

        PatientFile::create([
            'patient_id' => $this->patientId,
            'name'       => $this->name,
            'file_path'  => $path,
            'file_name'  => $this->file->getClientOriginalName(),
            'mime_type'  => $this->file->getMimeType(),
            'file_size'  => $this->file->getSize(),
        ]);

        $this->reset(['name', 'file', 'showForm']);
        $this->loadFiles();
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmDeleteId = null;
    }

    public function delete(int $id): void
    {
        $file = PatientFile::where('patient_id', $this->patientId)->findOrFail($id);
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
        $this->confirmDeleteId = null;
        $this->loadFiles();
    }

    public function render()
    {
        return view('livewire.patient-files');
    }
}
