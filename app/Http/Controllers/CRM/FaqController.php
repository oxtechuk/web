<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->orderBy('id', 'desc')->get();
        return view('crm.settings.faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new FAQ.
     */
    public function create()
    {
        return view('crm.settings.faqs.create');
    }

    /**
     * Store a newly created FAQ in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|array',
            'question.ar' => 'required|string',
            'question.en' => 'required|string',
            'answer' => 'required|array',
            'answer.ar' => 'required|string',
            'answer.en' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->only(['question', 'answer', 'sort_order']);
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order') ?? 0;

        Faq::create($data);

        return redirect()->route('crm.settings.faqs.index')->with('success', __('تم إضافة السؤال الشائع بنجاح'));
    }

    /**
     * Show the form for editing the specified FAQ.
     */
    public function edit(string $id)
    {
        $faq = Faq::findOrFail($id);
        return view('crm.settings.faqs.edit', compact('faq'));
    }

    /**
     * Update the specified FAQ in database.
     */
    public function update(Request $request, string $id)
    {
        $faq = Faq::findOrFail($id);

        $request->validate([
            'question' => 'required|array',
            'question.ar' => 'required|string',
            'question.en' => 'required|string',
            'answer' => 'required|array',
            'answer.ar' => 'required|string',
            'answer.en' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->only(['question', 'answer', 'sort_order']);
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->input('sort_order') ?? 0;

        $faq->update($data);

        return redirect()->route('crm.settings.faqs.index')->with('success', __('تم تعديل السؤال الشائع بنجاح'));
    }

    /**
     * Remove the specified FAQ from database.
     */
    public function destroy(string $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return back()->with('success', __('تم حذف السؤال الشائع بنجاح'));
    }
}
