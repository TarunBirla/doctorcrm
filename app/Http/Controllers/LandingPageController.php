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
        $clinics = Clinic::where('is_active', true)->get();
        $doctors = Doctor::active()->get();
        return view('landing.index', compact('clinics', 'doctors'));
    }

    public function about()
    {
        $clinics = Clinic::where('is_active', true)->get();
        return view('landing.about', compact('clinics'));
    }

    public function services()
    {
        $clinics = Clinic::where('is_active', true)->get();
        return view('landing.services', compact('clinics'));
    }

    public function testimonials()
    {
        return view('landing.testimonials');
    }

    public function gallery()
    {
        return view('landing.gallery');
    }

    public function contact()
    {
        $clinics = Clinic::where('is_active', true)->get();
        return view('landing.contact', compact('clinics'));
    }

    public function enquiry()
    {
        $clinics = Clinic::where('is_active', true)->get();
        return view('landing.enquiry', compact('clinics'));
    }
}
