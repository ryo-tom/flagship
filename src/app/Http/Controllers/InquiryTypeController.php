<?php

namespace App\Http\Controllers;

use App\Http\Requests\InquiryTypeStoreRequest;
use App\Models\InquiryType;
use Inertia\Inertia;
use Inertia\Response;

class InquiryTypeController extends Controller
{
    public function index(): Response
    {
        $inquiryTypes = InquiryType::all();

        return Inertia::render('InquiryType/Index', [
            'inquiryTypes' => $inquiryTypes,
        ]);
    }

    public function store(InquiryTypeStoreRequest $request)
    {
        $inquiryType = InquiryType::create([
            'name'          => $request->input('name'),
            'custom_label'  => $request->input('custom_label'),
        ]);

        return back()->with('message', "ID:{$inquiryType->id} 問い合わせ区分を追加しました。");
    }
}
