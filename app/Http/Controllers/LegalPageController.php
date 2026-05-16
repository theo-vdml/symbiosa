<?php

namespace App\Http\Controllers;

use App\Models\LegalPage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LegalPageController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $page = LegalPage::where('slug', $slug)->firstOrFail();

        $version = null;

        if ($request->has('version')) {
            $version = $page->versions()
                ->where('version_number', $request->query('version'))
                ->first();
        } elseif ($request->has('date')) {
            $version = $page->versions()
                ->where('created_at', '<=', $request->query('date'))
                ->orderByDesc('version_number')
                ->first();
        }

        // Fallback to latest version if no specific version requested or found
        if (!$version) {
            $version = $page->latestVersion;
        }

        if (!$version) {
            abort(404, "Aucune version disponible pour cette page.");
        }

        return Inertia::render('Legal/Show', [
            'page' => $page,
            'version' => $version,
        ]);
    }
}
