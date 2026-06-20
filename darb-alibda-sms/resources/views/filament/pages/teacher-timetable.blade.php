<x-filament-panels::page>

    <div class="mb-6">
        {{ $this->form }}
    </div>

    @php
        $days = $this->getDays(); // Sun → Thu (5 days)
        $timeSlots = $this->getTimeSlots(); // 7 slots only
        $grid = $this->getGrid(); // 2D array: [day][time_slot_id] => schedule
    @endphp

    <div class="timetable-wrapper">

        <table class="timetable">

            <!-- HEADER: TIME SLOTS (7 columns) -->
            <thead>
                <tr>
                    <th class="day-header">Day</th>

                    @foreach ($timeSlots as $slot)
                        <th class="slot-header">
                            <div>{{ $slot->full_name }}</div>
                            <div class="slot-time">{{ $slot->start_time->format('H:i') }} - {{ $slot->end_time->format('H:i') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>

            <!-- BODY: DAYS (5 rows) -->
            <tbody>

                @foreach ($days as $day)
                    <tr>

                        <!-- DAY COLUMN -->
                        <td class="day-cell">
                            {{ $day }}
                        </td>

                        <!-- 7 EMPTY TIME SLOTS -->
                        @foreach ($timeSlots as $slot)
                            <td class="cell"><div>{{ $grid[$day->value][$slot->id]->subject->name ?? '' }}</div><div>{{ $grid[$day->value][$slot->id]->section->full_name ?? '' }}</div></td>
                        @endforeach

                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

    <style>
/* Wrapper */
.timetable-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 14px;
    border: 1px solid var(--gray-200);
    background: var(--white);
}

/* Table */
.timetable {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
    font-size: 16px;
}

/* HEADER */
.timetable thead th {
    padding: 14px 10px;
    font-size: 15px;
    font-weight: 700;
    text-align: center;
    background: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
    color: var(--gray-900);
    min-width: 120px;
    width: 120px;
}

/* Time slot header */
.slot-header {
    color: var(--primary-600);
}

.slot-time {
    font-weight: 700;
}

/* DAY COLUMN */
.day-header {
    width: 120px;
}

.day-cell {
    font-weight: 700;
    text-align: center;
    background: var(--gray-50);
    border-right: 2px solid var(--gray-200);
    color: var(--gray-900);
}

/* GRID CELLS */
.timetable td {
    text-align: center;
    border: 1px solid var(--gray-200);
    height: 120px;
    transition: 0.2s ease;
    padding: 15px;  
}

/* Hover */
.timetable td.cell:hover {
    background: var(--gray-50);
}

.timetable td.cell div:first-child {
    font-weight: 600;
    color: var(--primary-400);
}

/* ===== DARK MODE ===== */
:is(.dark) .timetable-wrapper {
    background: var(--gray-900);
    border-color: var(--gray-700);
}

:is(.dark) .timetable thead th {
    background: var(--gray-800);
    color: var(--gray-300);
    border-color: var(--gray-700);
}

:is(.dark) .day-cell {
    background: var(--gray-800);
    color: var(--gray-300);
    border-color: var(--gray-700);
}

:is(.dark) .timetable td {
    border-color: var(--gray-700);
}

:is(.dark) .timetable td.cell:hover {
    background: var(--gray-800);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .timetable {
        font-size: 14px;
        min-width: 750px;
    }

    .timetable td {
        height: 70px;
    }
}

@media (max-width: 480px) {
    .timetable {
        font-size: 13px;
        min-width: 650px;
    }
}
</style>
</x-filament-panels::page>