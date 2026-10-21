<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Technician;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return view('reports.index', [
            ...$this->reportData($filters['from'] ?? null, $filters['to'] ?? null),
            'filters' => $filters,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $reportData = $this->reportData($filters['from'] ?? null, $filters['to'] ?? null);

        return response()->streamDownload(function () use ($reportData): void {
            $stream = fopen('php://output', 'w');

            if ($stream === false) {
                return;
            }

            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, [__('Report'), __('Value'), __('Ticket Count')]);

            foreach ($reportData['summary'] as $label => $total) {
                fputcsv($stream, [__('Ticket Summary'), __($label), $total]);
            }

            foreach ($reportData['byStatus'] as $status => $total) {
                fputcsv($stream, [__('Tickets by Status'), __($status), $total]);
            }

            foreach ($reportData['byPriority'] as $priority => $total) {
                fputcsv($stream, [__('Tickets by Priority'), __($priority), $total]);
            }

            foreach ($reportData['byCategory'] as $category) {
                fputcsv($stream, [__('Tickets by Category'), $category->name, $category->tickets_count]);
            }

            foreach ($reportData['byTechnician'] as $technician) {
                fputcsv($stream, [__('Tickets by Technician'), $technician->name, $technician->total]);
            }

            fclose($stream);
        }, 'ticket-report-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /** @return array{summary: array<string, int>, byStatus: Collection, byPriority: Collection, byCategory: Collection, byTechnician: Collection} */
    private function reportData(?string $from, ?string $to): array
    {
        $byStatus = $this->applyDateFilters(Ticket::query(), $from, $to)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $byPriority = $this->applyDateFilters(Ticket::query(), $from, $to)
            ->selectRaw('priority, COUNT(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority');

        return [
            'summary' => [
                'Total Tickets' => $byStatus->sum(),
                'Open Tickets' => $byStatus->get('Open', 0),
                'In Progress Tickets' => $byStatus->get('In Progress', 0),
                'Resolved Tickets' => $byStatus->get('Resolved', 0),
                'Closed Tickets' => $byStatus->get('Closed', 0),
                'Overdue Tickets' => $this->applyDateFilters(Ticket::query(), $from, $to)
                    ->whereIn('status', ['Open', 'In Progress'])
                    ->whereDate('due_date', '<', today())
                    ->count(),
                'High / Critical Priority Tickets' => $byPriority->get('High', 0) + $byPriority->get('Critical', 0),
            ],
            'byStatus' => $byStatus,
            'byPriority' => $byPriority,
            'byCategory' => Category::query()
                ->withCount([
                    'tickets' => fn (Builder $query): Builder => $this->applyDateFilters($query, $from, $to),
                ])
                ->orderByDesc('tickets_count')
                ->orderBy('name')
                ->get(),
            'byTechnician' => $this->applyDateFilters(Ticket::query(), $from, $to)
                ->leftJoin('technicians', 'tickets.technician_id', '=', 'technicians.id')
                ->selectRaw("COALESCE(technicians.name, 'Unassigned') as name, COUNT(tickets.id) as total")
                ->groupBy('technicians.id', 'technicians.name')
                ->orderByDesc('total')
                ->orderBy('name')
                ->get(),
        ];
    }

    private function applyDateFilters(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $query): Builder => $query->whereDate('tickets.created_at', '>=', $from))
            ->when($to, fn (Builder $query): Builder => $query->whereDate('tickets.created_at', '<=', $to));
    }
}
