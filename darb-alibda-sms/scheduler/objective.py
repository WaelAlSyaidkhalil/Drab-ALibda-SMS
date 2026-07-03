from collections import defaultdict
from ortools.sat.python import cp_model

from parser import SchedulerData
from model_builder import Variables


def add_objective(
    model: cp_model.CpModel,
    data: SchedulerData,
    variables: Variables,
):

    penalties = []

    #
    # Group slots by day
    #
    slots_by_day = defaultdict(list)

    for slot in data.slots:
        slots_by_day[slot.day].append(slot)

    #
    # Group lessons by (section, subject)
    #
    lesson_groups = defaultdict(list)

    for lesson in data.lessons:
        lesson_groups[
            (lesson.section_id, lesson.subject_id)
        ].append(lesson)

    #
    # For every subject in every section
    #
    for _, lessons in lesson_groups.items():

        #
        # Did this subject appear today?
        #
        for day, day_slots in slots_by_day.items():

            day_used = model.NewBoolVar(
                f"day_used_{lessons[0].section_id}_{lessons[0].subject_id}_{day}"
            )

            lesson_occurrences = []

            for lesson in lessons:
                for slot in day_slots:
                    lesson_occurrences.append(
                        variables.lesson_slot[(lesson.id, slot.id)]
                    )

            #
            # If any lesson is placed today
            #
            model.Add(sum(lesson_occurrences) >= 1).OnlyEnforceIf(day_used)

            model.Add(sum(lesson_occurrences) == 0).OnlyEnforceIf(
                day_used.Not()
            )

            #
            # Reward using the day
            #
            penalties.append(day_used)

    #
    # Maximize spread
    #
    model.Maximize(sum(penalties))