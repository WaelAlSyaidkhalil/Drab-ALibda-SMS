import json

from parser import SchedulerData
from model_builder import Variables


def export_solution(
    output_file: str,
    solver,
    data: SchedulerData,
    variables: Variables,
):

    schedules = []

    for lesson in data.lessons:

        assigned_slot = None

        for slot in data.slots:

            if solver.Value(
                variables.lesson_slot[(lesson.id, slot.id)]
            ):

                assigned_slot = slot
                break

        if assigned_slot is None:
            continue

        schedules.append({
            "lesson_id": lesson.id,

            "teacher_id": lesson.teacher_id,

            "section_id": lesson.section_id,

            "subject_id": lesson.subject_id,

            "slot_id": assigned_slot.id,

            "day": assigned_slot.day,

            "period_number": assigned_slot.period_number,

            "start_time": assigned_slot.start,

            "end_time": assigned_slot.end,
        })

    with open(output_file, "w", encoding="utf-8") as f:

        json.dump(
            schedules,
            f,
            indent=4,
            ensure_ascii=False,
        )