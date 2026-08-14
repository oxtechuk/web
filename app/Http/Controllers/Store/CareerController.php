<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Mail\JobApplicationReceived;
use App\Models\JobApplication;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CareerController extends Controller
{
    public function create()
    {
        return view('store.careers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'job_title' => 'required|string|max:255',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:10240', // Max 10MB
            'cover_letter' => 'nullable|string|max:3000',
            'g-recaptcha-response' => [new \App\Rules\Recaptcha],
        ]);

        // Upload resume file
        $resumePath = $request->file('resume')->store('resumes', 'public');

        // Create job application
        $application = JobApplication::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'job_title' => $request->job_title,
            'resume_path' => $resumePath,
            'cover_letter' => $request->cover_letter,
            'status' => 'new',
        ]);

        // Fetch HR email or fallback to contact email
        $hrEmail = Setting::where('key', 'hr_email')->first()?->value
            ?? Setting::where('key', 'contact_email')->first()?->value
            ?? 'hr@grmotors.com';

        // Send Email
        try {
            Mail::to($hrEmail)->send(new JobApplicationReceived($application));
        } catch (\Exception $e) {
            // Log or ignore email failure to not block application submission
            logger()->error('Failed to send job application email: '.$e->getMessage());
        }

        return back()->with('success', __('تم تقديم طلبك بنجاح! سنتواصل معك في حال مطابقة مؤهلاتك لمتطلبات الوظيفة.'));
    }
}
