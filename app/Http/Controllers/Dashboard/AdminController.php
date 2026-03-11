<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Event;
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
        $events = Event::count();
        $purchasedEvents = InvoiceItem::where('product_type', 'event')->count();
        $purchasedPrograms = InvoiceItem::where('product_type', 'program')->count();
        $totalUsers = User::count();
        $jobsListed = OpenRole::count();
        $jobApplications = JobApplication::count();

        $data = [
            'programs' => $programs,
            'purchased_programs' => $purchasedPrograms,
            'total_users' => $totalUsers,
            'jobs_listed' => $jobsListed,
            'job_applications' => $jobApplications,
            'events' => $events,
            'purchased_events' => $purchasedEvents,
        ];

        return $this->success($data);
    }

    public function stats()
    {

    }
}
