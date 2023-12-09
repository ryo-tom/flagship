<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcquisitionSourceStoreRequest;
use App\Models\AcquisitionSource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AcquisitionSourceController extends Controller
{
    public function index(): Response
    {
        $acquisitionSources = AcquisitionSource::orderByDisplayOrder()->get();

        return Inertia::render('AcquisitionSource/Index', [
            'acquisitionSources' => $acquisitionSources,
        ]);
    }

    public function store(AcquisitionSourceStoreRequest $request): RedirectResponse
    {
        $acquisitionSource = AcquisitionSource::create([
            'name'          => $request->input('name'),
            'display_order' => $request->input('display_order'),
        ]);

        return back()->with('message', "ID:{$acquisitionSource->id} 獲得元を追加しました。");
    }
}
