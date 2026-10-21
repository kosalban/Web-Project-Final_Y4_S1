<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use App\Notifications\TicketActivityNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'status' => ['nullable', 'in:Open,In Progress,Resolved,Closed'],
            'priority' => ['nullable', 'in:Low,Medium,High,Critical'],
            'technician_id' => ['nullable', 'integer', 'exists:technicians,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'requester_id' => ['nullable', 'integer', 'exists:users,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        return view('tickets.index', [
            'tickets' => $this->filteredTickets($filters)->paginate(10)->withQueryString(),
            'technicians' => Technician::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'requesters' => User::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('tickets.create', [
            'categories' => Category::where('status', 'Active')->orderBy('name')->get(),
            'technicians' => Technician::where('status', 'Active')->orderBy('name')->get(),
            'requesters' => $this->canSelectRequester()
                ? User::orderBy('name')->get()
                : collect([auth()->user()]),
        ]);
    }

    public function store(Request $request)
    {
        $canSelectRequester = $this->canSelectRequester();
        $data = $request->validate([
            'title' => 'required|max:200',
            'description' => 'required|max:5000',
            'category_id' => 'required|exists:categories,id',
            'technician_id' => 'nullable|exists:technicians,id',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'status' => 'required|in:Open,In Progress,Resolved,Closed',
            'requester_id' => $canSelectRequester ? ['required', 'exists:users,id'] : ['prohibited'],
            'department' => 'nullable|string|max:150',
            'asset' => 'nullable|string|max:200',
            'due_date' => 'nullable|date|after_or_equal:today',
            'resolution' => 'nullable|string|max:5000',
            'root_cause' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,txt|max:10240',
        ]);

        $data['user_id'] = $canSelectRequester ? $data['requester_id'] : auth()->id();
        unset($data['requester_id'], $data['attachment']);

        $ticket = Ticket::create($data);

        if ($request->hasFile('attachment')) {
            $ticket->update([
                'attachment_path' => $request->file('attachment')->store('tickets/'.$ticket->id, 'local'),
                'attachment_name' => $request->file('attachment')->getClientOriginalName(),
            ]);
        }

        $actor = $request->user();
        $this->recordActivity($ticket, $actor, 'created', ['status' => $ticket->status]);

        if ($ticket->technician_id !== null) {
            $technician = Technician::find($ticket->technician_id);
            $this->recordActivity($ticket, $actor, 'assigned', [
                'from' => null,
                'to' => $technician?->name,
            ]);
            $this->notifyTicketParticipants(
                $ticket,
                $actor,
                'Ticket assigned',
                'Ticket #:ticket_id was assigned to :technician.',
                [
                    'ticket_id' => $ticket->id,
                    'technician' => $technician?->name ?? 'Unassigned',
                ],
            );
        }

        return redirect()->route('tickets.show', $ticket)->with('success', __('Ticket created successfully.'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'technician', 'category', 'comments.user', 'activities.user']);

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        return view('tickets.edit', [
            'ticket' => $ticket,
            'categories' => Category::where('status', 'Active')->orderBy('name')->get(),
            'technicians' => Technician::where('status', 'Active')->orderBy('name')->get(),
            'requesters' => $this->canSelectRequester()
                ? User::orderBy('name')->get()
                : collect([$ticket->user]),
        ]);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $canSelectRequester = $this->canSelectRequester();
        $data = $request->validate([
            'title' => 'required|max:200',
            'description' => 'required|max:5000',
            'category_id' => 'required|exists:categories,id',
            'technician_id' => 'nullable|exists:technicians,id',
            'priority' => 'required|in:Low,Medium,High,Critical',
            'status' => 'required|in:Open,In Progress,Resolved,Closed',
            'requester_id' => $canSelectRequester ? ['required', 'exists:users,id'] : ['prohibited'],
            'department' => 'nullable|string|max:150',
            'asset' => 'nullable|string|max:200',
            'due_date' => 'nullable|date',
            'resolution' => 'nullable|string|max:5000',
            'root_cause' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,txt|max:10240',
        ]);

        if ($canSelectRequester) {
            $data['user_id'] = $data['requester_id'];
        }

        unset($data['requester_id'], $data['attachment']);

        $previousAttachmentPath = $ticket->attachment_path;
        $previousStatus = $ticket->status;
        $previousTechnician = Technician::find($ticket->technician_id)?->name;

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('tickets/'.$ticket->id, 'local');
            $data['attachment_name'] = $request->file('attachment')->getClientOriginalName();
        }

        $ticket->update($data);

        $actor = $request->user();
        $updates = [];

        if ($ticket->wasChanged('technician_id')) {
            $technicianName = Technician::find($ticket->technician_id)?->name;
            $this->recordActivity($ticket, $actor, 'assigned', [
                'from' => $previousTechnician,
                'to' => $technicianName,
            ]);
            $updates[] = $technicianName === null
                ? ['key' => 'The technician assignment was removed', 'replace' => []]
                : ['key' => 'The ticket was assigned to :technician', 'replace' => ['technician' => $technicianName]];
        }

        if ($ticket->wasChanged('status')) {
            $this->recordActivity($ticket, $actor, 'status_changed', [
                'from' => $previousStatus,
                'to' => $ticket->status,
            ]);
            $updates[] = [
                'key' => 'Status changed from :from to :to',
                'replace' => ['from' => $previousStatus, 'to' => $ticket->status],
            ];
        }

        $changedFields = array_values(array_diff(
            array_keys($ticket->getChanges()),
            ['updated_at', 'status', 'technician_id'],
        ));

        if ($changedFields !== []) {
            $this->recordActivity($ticket, $actor, 'updated', ['fields' => $changedFields]);
            $updates[] = ['key' => 'Ticket details changed', 'replace' => []];
        }

        if ($updates !== []) {
            $this->notifyTicketParticipants(
                $ticket,
                $actor,
                'Ticket updated',
                'Ticket #:ticket_id was updated: :updates.',
                [
                    'ticket_id' => $ticket->id,
                    'updates' => $updates,
                ],
            );
        }

        if ($request->hasFile('attachment') && $previousAttachmentPath) {
            Storage::disk('local')->delete($previousAttachmentPath);
        }

        return redirect()->route('tickets.show', $ticket)->with('success', __('Ticket updated successfully.'));
    }

    public function downloadAttachment(Ticket $ticket)
    {
        abort_unless(
            $ticket->attachment_path && Storage::disk('local')->exists($ticket->attachment_path),
            404,
        );

        return Storage::disk('local')->download($ticket->attachment_path, $ticket->attachment_name);
    }

    public function print(Ticket $ticket)
    {
        $ticket->load(['user', 'technician', 'category', 'comments.user']);

        return view('tickets.print', compact('ticket'));
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', __('Ticket deleted.'));
    }

    public function comment(Request $request, Ticket $ticket)
    {
        $data = $request->validate(['comment' => 'required|max:2000']);

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => $data['comment'],
        ]);

        $actor = $request->user();
        $this->recordActivity($ticket, $actor, 'commented', ['comment_id' => $comment->id]);
        $this->notifyTicketParticipants(
            $ticket,
            $actor,
            'New ticket comment',
            'A comment was added to ticket #:ticket_id.',
            ['ticket_id' => $ticket->id],
        );

        return back()->with('success', __('Comment added.'));
    }

    /** @param array<string, mixed> $filters */
    private function filteredTickets(array $filters): Builder
    {
        return Ticket::query()
            ->with(['user', 'technician', 'category'])
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['priority'] ?? null, fn (Builder $query, string $priority) => $query->where('priority', $priority))
            ->when($filters['technician_id'] ?? null, fn (Builder $query, string $technicianId) => $query->where('technician_id', $technicianId))
            ->when($filters['category_id'] ?? null, fn (Builder $query, string $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['requester_id'] ?? null, fn (Builder $query, string $requesterId) => $query->where('user_id', $requesterId))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $nestedQuery) use ($search): void {
                    $nestedQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->latest();
    }

    /** @param array<string, mixed> $details */
    private function recordActivity(Ticket $ticket, User $actor, string $type, array $details): void
    {
        $ticket->activities()->create([
            'user_id' => $actor->id,
            'type' => $type,
            'details' => $details,
        ]);
    }

    private function notifyTicketParticipants(
        Ticket $ticket,
        User $actor,
        string $title,
        string $message,
        array $replacements = [],
    ): void
    {
        $recipients = User::query()
            ->where(function (Builder $query) use ($ticket): void {
                $query->where('id', $ticket->user_id)->orWhere('role', 'Admin');
            })
            ->where('id', '!=', $actor->id)
            ->get();

        foreach ($recipients as $recipient) {
            $locale = $recipient->locale;
            $localizedReplacements = $replacements;

            if (isset($localizedReplacements['updates'])) {
                $localizedReplacements['updates'] = implode('; ', array_map(
                    fn (array $update): string => $this->translateNotificationUpdate($update, $locale),
                    $localizedReplacements['updates'],
                ));
            }

            $recipient->notify(new TicketActivityNotification(
                $ticket->id,
                $this->translateNotificationText($title, [], $locale),
                $this->translateNotificationText($message, $localizedReplacements, $locale),
            ));
        }
    }

    /**
     * @param array<string, int|string> $replacements
     */
    private function translateNotificationText(string $key, array $replacements, string $locale): string
    {
        $placeholders = [];

        foreach ($replacements as $name => $value) {
            $placeholders[':'.$name] = (string) $value;
        }

        return strtr(Lang::get($key, [], $locale), $placeholders);
    }

    /**
     * @param array{key: string, replace: array<string, string>} $update
     */
    private function translateNotificationUpdate(array $update, string $locale): string
    {
        $replacements = [];

        foreach ($update['replace'] as $name => $value) {
            $replacements[$name] = in_array($name, ['from', 'to'], true)
                ? Lang::get($value, [], $locale)
                : $value;
        }

        return $this->translateNotificationText($update['key'], $replacements, $locale);
    }

    private function canSelectRequester(): bool
    {
        $user = auth()->user();

        return $user !== null && ($user->isAdmin() || $user->isStaff());
    }
}
