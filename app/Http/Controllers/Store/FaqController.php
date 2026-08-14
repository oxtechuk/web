<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->orderBy('id', 'asc')->get();
        return view('store.faq', compact('faqs'));
    }
}
