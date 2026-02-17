<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\BloodType;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.patients.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.patients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $bloodTypes = BloodType::all();

        $initialTab = 'datos_personales';
        $errors = session()->get('errors');
        if ($errors) {
            $bag = $errors->getBag('default');
            $antecedentesFields = ['allergies', 'chronic_conditions', 'surgical_history', 'family_history'];
            $infoFields = ['blood_type_id', 'observations'];
            $contactoFields = ['emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship'];
            if ($bag->hasAny($antecedentesFields)) {
                $initialTab = 'antecedentes';
            } elseif ($bag->hasAny($infoFields)) {
                $initialTab = 'informacion_general';
            } elseif ($bag->hasAny($contactoFields)) {
                $initialTab = 'contacto_emergencia';
            }
        }

        return view('admin.patients.edit', compact('patient', 'bloodTypes', 'initialTab'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'blood_type_id' => 'nullable|exists:blood_types,id',
            'allergies' => 'nullable|string|min:3|max:255',
            'chronic_conditions' => 'nullable|string|min:3|max:255',
            'surgical_history' => 'nullable|string|min:3|max:255',
            'family_history' => 'nullable|string|min:3|max:255',
            'observations' => 'nullable|string|min:3|max:255',
            'emergency_contact_name' => 'nullable|string|min:3|max:50',
            'emergency_contact_phone' => ['nullable', 'string', 'min:10', 'max:12'],
            'emergency_contact_relationship' => 'nullable|string|min:3|max:50',
        ]);

        $patient->update($data);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Paciente actualizado',
            'text' => 'El paciente se actualizó correctamente',
        ]);

        return redirect()->route('admin.patients.edit', $patient);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        //
    }
}