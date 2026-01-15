<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanySwitchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Company $company)
    {
        $user = $request->user();

        // Check if user has access to this company
        if (!$user->hasAccessTo($company->id)) {
            abort(403, 'You do not have access to this company.');
        }

        // Switch the company
        $user->switchCompany($company->id);

        return redirect()->back()->with('success', 'Company switched successfully.');
    }
}
