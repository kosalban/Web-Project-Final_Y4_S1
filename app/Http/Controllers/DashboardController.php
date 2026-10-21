<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'usersCount' => User::count(),
            'techniciansCount' => Technician::count(),
            'ticketsCount' => Ticket::count(),
            'openCount' => Ticket::whereIn('status', ['Open', 'In Progress'])->count(),
            'resolvedCount' => Ticket::where('status', 'Resolved')->count(),
            'overdueCount' => Ticket::whereIn('status', ['Open', 'In Progress'])
                ->whereDate('due_date', '<', today())
                ->count(),
            'categoriesCount' => Category::count(),
            'recentTickets' => Ticket::with(['user', 'technician', 'category'])
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }
}
