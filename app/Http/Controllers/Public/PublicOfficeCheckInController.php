<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PublicOfficeCheckInController extends Controller
{
    public function index(): View
    {
        return view('public.office_checkin', [
            'immigrationCheckInUrl' => route('public.office-check-in.index'),
            'educationCheckInUrl' => (string) config('services.education_check_in.url'),
        ]);
    }
}
