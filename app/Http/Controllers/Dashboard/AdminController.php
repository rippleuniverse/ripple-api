<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use App\Models\JobApplication;
use App\Models\OpenRole;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function overview(Request $request)
    {
        $programs = Program::count();
        $purchasedPrograms = InvoiceItem::where('product_type', 'program')->count();
        $totalUsers = User::count();
        $jobsListed = OpenRole::count();
        $jobApplications = JobApplication::count();
        $purchasedProgramsAmount = InvoiceItem::where('product_type', 'program')
            ->selectRaw('SUM(quantity * unit_price) as total')
            ->value('total') ?? 0;
        $purchasedEventsAmount = InvoiceItem::where('product_type', 'event')
            ->selectRaw('SUM(quantity * unit_price) as total')
            ->value('total') ?? 0;
    }
}
