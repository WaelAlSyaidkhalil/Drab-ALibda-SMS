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
                    <th class="day-header">{{ __('dashboard.enums.day_of_week.day') }}</th>

                    @foreach ($timeSlots as $slot)
                        <th class="slot-header">
                            <div>{{ $slot->full_name }}</div>
                            <small class="slot-time">{{ $slot->display_time }}</small>
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
                            {{ $day->label() }}
                        </td>

                        <!-- 7 EMPTY TIME SLOTS -->
                        @foreach ($timeSlots as $slot)
                            <td class="cell" wire:click="openCell('{{ $day }}', {{ $slot->id }})">
                                <div class="cell-content">
                                    @if(isset($grid[$day->value][$slot->id]))
                                        <div class="subject">{{ $grid[$day->value][$slot->id]->subject->name ?? '' }}</div>
                                        <div class="section">{{ $grid[$day->value][$slot->id]->section->full_name ?? '' }}</div>
                                    @else
                                        <div class="empty">__</div>
                                    @endif 
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

    <div class="mt-6">
        <button class="generate-timetable-button" wire:click="generateTimetableUsingORTools">{{ __('dashboard.buttons.generate_timetable') }}</button>
        <button class="publish-timetable-button" wire:click="publishTeacherTimetable">{{ __('dashboard.buttons.publish_timetable') }}</button>
    </div>

    <style>

        .timetable-wrapper {
            width: 100%;
            overflow-x: auto;
            border-radius: 14px;
            border: 1px solid var(--gray-200);
        }

        .timetable {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            min-width: 900px;
        }

        .timetable thead {
            border-bottom: 2px solid var(--primary-500);
        }

        .timetable th,
        .timetable td {
            height: 85px;
            text-align: center;
            vertical-align: middle;
            padding: 6px;
        }

        /* HEADER */
        .timetable th {
            background: var(--gray-800);
            font-weight: 700;
            border: none;
        }
        
        .timetable th:not(:first-child) {
            font-size: 16px;
        }

        .timetable th div {
            color: var(--primary-500);
        }

        .timetable th small {
            color: var(--gray-300);
            font-size: 12px;
        }

        /* DAY COLUMN */
        .day-header {
            width: 110px;
        }

        .day-cell {
            background: var(--gray-800);
            font-weight: 700;
            color: white;
            border: none;
            font-size: 19px;
        }

        /* CELL */
        .cell {
            cursor: pointer;
            transition: 0.2s;
        }
        
        .cell-content {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 8px;
            border-radius: 6px;
        }

        .subject {
            font-weight: 700;
            color: var(--gray-50);
            font-size: 15px;
        }

        .section {
            font-size: 12px;
            color: var(--gray-400);
        }

        .empty {
            color: var(--gray-400);
            font-size: 18px;
        }

        /* DARK MODE */
        :is(.dark) .timetable-wrapper {
            border-color: var(--gray-700);
        }

        :is(.dark) .cell-content:hover {
            background: var(--gray-800);
        }

        .generate-timetable-button,
        .publish-timetable-button {
            background-color: var(--primary-600);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .generate-timetable-button:hover,
        .publish-timetable-button:hover {
            background-color: var(--primary-700);
        }
</style>
</x-filament-panels::page>