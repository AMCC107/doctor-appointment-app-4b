<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\PrescriptionItem;
use Livewire\Component;

class ConsultationManager extends Component
{
    public int $appointmentId;
    public ?Appointment $appointment = null;

    public string $diagnosis = '';
    public string $treatment = '';
    public string $notes = '';

    /** @var array<int, array{medication: string, dose: string, frequency_duration: string}> */
    public array $medications = [];

    public bool $showHistoryModal = false;
    public bool $showPastConsultationsModal = false;

    public function mount(int $appointmentId): void
    {
        $this->appointmentId = $appointmentId;
        $this->appointment = Appointment::with(['patient.user', 'patient.bloodType', 'doctor.user'])
            ->findOrFail($appointmentId);

        $this->diagnosis = $this->appointment->diagnosis ?? '';
        $this->treatment = $this->appointment->treatment ?? '';
        $this->notes = $this->appointment->notes ?? '';

        foreach ($this->appointment->prescriptionItems as $item) {
            $this->medications[] = [
                'id' => $item->id,
                'medication' => $item->medication,
                'dose' => $item->dose,
                'frequency_duration' => $item->frequency_duration ?? '',
            ];
        }
        if (empty($this->medications)) {
            $this->medications[] = ['id' => 0, 'medication' => '', 'dose' => '', 'frequency_duration' => ''];
        }
    }

    public function openHistoryModal(): void
    {
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal(): void
    {
        $this->showHistoryModal = false;
    }

    public function openPastConsultationsModal(): void
    {
        $this->showPastConsultationsModal = true;
    }

    public function closePastConsultationsModal(): void
    {
        $this->showPastConsultationsModal = false;
    }

    public function getPastConsultationsProperty()
    {
        if (!$this->appointment?->patient_id) {
            return collect();
        }
        return Appointment::with('doctor.user')
            ->where('patient_id', $this->appointment->patient_id)
            ->where('id', '!=', $this->appointmentId)
            ->whereNotNull('diagnosis')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();
    }

    public function addMedication(): void
    {
        $this->medications[] = ['id' => 0, 'medication' => '', 'dose' => '', 'frequency_duration' => ''];
    }

    public function removeMedication(int $index): void
    {
        unset($this->medications[$index]);
        $this->medications = array_values($this->medications);
        if (empty($this->medications)) {
            $this->medications[] = ['id' => 0, 'medication' => '', 'dose' => '', 'frequency_duration' => ''];
        }
    }

    public function saveConsultation(): void
    {
        $this->appointment->update([
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'notes' => $this->notes,
        ]);

        $this->appointment->prescriptionItems()->delete();

        foreach ($this->medications as $m) {
            $med = trim($m['medication'] ?? '');
            $dose = trim($m['dose'] ?? '');
            if ($med !== '' || $dose !== '') {
                $this->appointment->prescriptionItems()->create([
                    'medication' => $med ?: '-',
                    'dose' => $dose ?: '-',
                    'frequency_duration' => $m['frequency_duration'] ?? null,
                ]);
            }
        }

        session()->flash('consultation_saved', true);
        $this->dispatch('consultation-saved');
    }

    public function render()
    {
        return view('livewire.admin.consultation-manager');
    }
}
