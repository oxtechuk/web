<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = JobApplication::latest();

        // Search by name or phone
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(20)->withQueryString();
        $statuses = JobApplication::STATUSES;

        return view('crm.job-applications.index', compact('applications', 'statuses'));
    }

    public function show(JobApplication $jobApplication)
    {
        $statuses = JobApplication::STATUSES;
        return view('crm.job-applications.show', compact('jobApplication', 'statuses'));
    }

    public function updateStatus(Request $request, JobApplication $jobApplication)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(JobApplication::STATUSES)),
        ]);

        $jobApplication->update([
            'status' => $request->status,
        ]);

        return back()->with('success', __('تم تحديث حالة الطلب بنجاح.'));
    }

    public function destroy(JobApplication $jobApplication)
    {
        // Delete CV file if exists
        if ($jobApplication->resume_path && Storage::disk('public')->exists($jobApplication->resume_path)) {
            Storage::disk('public')->delete($jobApplication->resume_path);
        }

        $jobApplication->delete();

        return redirect()->route('crm.job-applications.index')->with('success', __('تم حذف طلب التوظيف بنجاح.'));
    }
}
