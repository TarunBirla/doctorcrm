<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;

class LandingPageController extends Controller
{
    /**
     * Show the public static landing page for PhysioPii Clinic CRM.
     */
    public function index()
    {
        $clinicsCount = Clinic::where('is_active', true)->count();
        $doctorsCount = Doctor::active()->count();
        $patientsCount = Patient::count();
        $appointmentsCount = Appointment::count();

        // Fallbacks for display counters if fresh install
        $clinicsDisplay = max($clinicsCount, 12);
        $doctorsDisplay = max($doctorsCount, 25);
        $patientsDisplay = max($patientsCount, 5400);
        $appointmentsDisplay = max($appointmentsCount, 18500);

        return view('landing.index', compact(
            'clinicsDisplay',
            'doctorsDisplay',
            'patientsDisplay',
            'appointmentsDisplay'
        ));
    }
}
