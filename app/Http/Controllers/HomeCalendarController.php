<?php

namespace App\Http\Controllers;

use App\Models\StaffReminder;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HomeCalendarController extends Controller
{
    private const MODES = ['day', 'week', 'month'];

    public function index(Request $request): View
    {
        // Use the Singapore calendar date and keep date-only comparisons in the app timezone.
        $today = CarbonImmutable::today('Asia/Singapore')->shiftTimezone(config('app.timezone'));
        $mode = $request->query('view');
        $mode = in_array($mode, self::MODES, true) ? $mode : 'month';
        $date = $request->query('date');
        $selectedDate = Validator::make(['date' => $date], ['date' => ['required', 'date_format:Y-m-d']])->passes()
            ? CarbonImmutable::parse($date)
            : $today;

        [$start, $end, $previousDate, $nextDate] = match ($mode) {
            'day' => [$selectedDate, $selectedDate, $selectedDate->subDay(), $selectedDate->addDay()],
            'week' => [
                $selectedDate->startOfWeek(CarbonImmutable::MONDAY),
                $selectedDate->endOfWeek(CarbonImmutable::SUNDAY),
                $selectedDate->subWeek(),
                $selectedDate->addWeek(),
            ],
            'month' => [
                $selectedDate->startOfMonth(),
                $selectedDate->endOfMonth(),
                $selectedDate->subMonthNoOverflow(),
                $selectedDate->addMonthNoOverflow(),
            ],
        };

        $firstDay = $mode === 'month' ? $start->startOfWeek(CarbonImmutable::MONDAY) : $start;
        $lastDay = $mode === 'month' ? $end->endOfWeek(CarbonImmutable::SUNDAY) : $end;
        $reminders = StaffReminder::query()
            ->where('starts_on', '<=', $lastDay->toDateString())
            ->where(function (Builder $query) use ($firstDay): void {
                $query->where('starts_on', '>=', $firstDay->toDateString())
                    ->orWhere('repeat', '!=', 'none');
            })
            ->orderBy('starts_on')
            ->orderBy('id')
            ->get();

        $days = [];

        for ($day = $firstDay; $day->lte($lastDay); $day = $day->addDay()) {
            $days[] = [
                'date' => $day,
                'inPeriod' => $day->betweenIncluded($start, $end),
                'notes' => $reminders->filter(fn (StaffReminder $reminder): bool => $reminder->occursOn($day))->values(),
            ];
        }

        $periodLabel = match ($mode) {
            'day' => $selectedDate->format('l, F j, Y'),
            'week' => $start->format('M j, Y').' – '.$end->format('M j, Y'),
            'month' => $selectedDate->format('F Y'),
        };

        return view('home-calendar', compact('mode', 'selectedDate', 'previousDate', 'nextDate', 'today', 'periodLabel', 'days'));
    }

    public function store(Request $request): RedirectResponse
    {
        $note = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'body' => ['nullable', 'string', 'max:1000'],
            'date' => ['required', 'date_format:Y-m-d'],
            'repeat' => ['required', Rule::in(StaffReminder::REPEAT_OPTIONS)],
            'view' => ['nullable', Rule::in(self::MODES)],
        ]);

        StaffReminder::create([
            'title' => $note['title'],
            'body' => $note['body'] ?? null,
            'starts_on' => $note['date'],
            'repeat' => $note['repeat'],
            'author_name' => $request->user()->name ?? $request->session()->get('preview.user.name', 'Admin'),
        ]);

        return redirect()
            ->route('panel.home', ['view' => $note['view'] ?? 'month', 'date' => $note['date']])
            ->with('status', __('Reminder added.'));
    }
}
