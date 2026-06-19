<x-filament-panels::page>

<style>
/* =========================
   TOOLBAR TITLE
========================= */
.fc .fc-toolbar-title {
    font-size: 24px !important;
    font-weight: 600;
    color: var(--gray-900);
}

.dark .fc .fc-toolbar-title {
    color: var(--gray-100);
}

/* =========================
   TOOLBAR WRAPPER
========================= */
.fc .fc-toolbar {
    margin-bottom: 6px;
    padding: 4px 6px;
    border-radius: 10px;
    gap: 6px;
}

/* =========================
   BUTTONS (COMPACT)
========================= */
.fc .fc-button {
    background: var(--primary-600) !important;
    border: none !important;
    border-radius: 8px !important;

    padding: 4px 8px !important;
    font-size: 15px !important;
    line-height: 1 !important;

    min-width: 28px;
    height: 28px;

    transition: all 0.15s ease-in-out;
}

.fc .fc-button:hover {
    background: var(--primary-700) !important;
    transform: translateY(-1px);
}

.fc .fc-button:active {
    transform: scale(0.97);
}

.fc .fc-button-active {
    background: var(--primary-700) !important;
}

/* =========================
   GRID CELLS (SMALL + CLEAN)
========================= */
.fc .fc-daygrid-day-frame {
    min-height: 55px !important;
    padding: 2px !important;
}

.fc .fc-daygrid-day {
    height: 55px !important;
    transition: all 0.15s ease-in-out;
}

.fc .fc-daygrid-day:hover {
    background: color-mix(in srgb, var(--primary-500) 8%, transparent);
    cursor: pointer;
}

.fc .fc-col-header-cell {
    padding: 4px 0 !important;
    background: var(--gray-50) !important;
    border: none !important;
}

.dark .fc .fc-col-header-cell {
    padding: 4px 0 !important;
    background: var(--gray-900) !important;
    border: none !important;
}

.fc .fc-col-header-cell-cushion {
    color: var(--gray-700) !important;
    font-size: 19px;
    font-weight: 600;
    border: none !important;
}

.dark .fc .fc-col-header-cell-cushion {
    color: var(--gray-300) !important;
    border: none !important;
}

/* =========================
   BORDER SOFTENING
========================= */
.fc td,
.fc th {
    border-color: color-mix(in srgb, var(--gray-200) 60%, transparent) !important;
}

.dark .fc td,
.dark .fc th {
    border-color: color-mix(in srgb, var(--gray-700) 50%, transparent) !important;
}
</style>

    {{ $this->form }}

    <div wire:ignore class="mt-6">
        <div id="calendar" style="min-height: 600px"></div>
    </div>

</x-filament-panels::page>

@assets
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
document.addEventListener('livewire:init', () => {

    const el = document.getElementById('calendar');

    if (!el || el.dataset.loaded) return;

    el.dataset.loaded = true;

    const calendar = new FullCalendar.Calendar(el, {
        initialView: 'dayGridMonth',

        height: 'auto',
        contentHeight: 550,
        aspectRatio: 1.35,

        dateClick(info) {

        const classId = @this.get('data.class_id');
        const sectionId = @this.get('data.section_id');

            if (!classId || !sectionId) {
                alert('يرجى اختيار الصف والشعبة أولاً');
                return;
            }

            const attendanceIndexUrl = @js(
                \App\Filament\Resources\Attendances\AttendanceResource::getUrl('index')
            );
            window.location.href = `${attendanceIndexUrl}?class_id=${classId}&section_id=${sectionId}&date=${info.dateStr}`;       }
    });

    setTimeout(() => {
        calendar.render();
        calendar.updateSize();
    }, 50);

    document.addEventListener('livewire:navigated', () => {
        setTimeout(() => calendar.updateSize(), 100);
    });
});
</script>
@endassets