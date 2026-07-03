from collections import defaultdict
from ortools.sat.python import cp_model

from parser import SchedulerData
from model_builder import Variables


# ---------------------------------------------------------
# Every lesson must be scheduled exactly once
# ---------------------------------------------------------

def add_lesson_constraints(
    model: cp_model.CpModel,
    data: SchedulerData,
    variables: Variables,
):

    for lesson in data.lessons:

        model.AddExactlyOne(
            variables.lesson_slot[(lesson.id, slot.id)]
            for slot in data.slots
        )


# ---------------------------------------------------------
# Teacher conflict
#
# A teacher cannot teach two lessons
# in the same slot.
# ---------------------------------------------------------

def add_teacher_constraints(
    model: cp_model.CpModel,
    data: SchedulerData,
    variables: Variables,
):

    teacher_lessons = defaultdict(list)

    for lesson in data.lessons:
        teacher_lessons[lesson.teacher_id].append(lesson)

    for teacher_id, lessons in teacher_lessons.items():

        for slot in data.slots:

            model.AddAtMostOne(
                variables.lesson_slot[(lesson.id, slot.id)]
                for lesson in lessons
            )


# ---------------------------------------------------------
# Section conflict
#
# A section cannot have two lessons
# in the same slot.
# ---------------------------------------------------------

def add_section_constraints(
    model: cp_model.CpModel,
    data: SchedulerData,
    variables: Variables,
):

    section_lessons = defaultdict(list)

    for lesson in data.lessons:
        section_lessons[lesson.section_id].append(lesson)

    for section_id, lessons in section_lessons.items():

        for slot in data.slots:

            model.AddAtMostOne(
                variables.lesson_slot[(lesson.id, slot.id)]
                for lesson in lessons
            )