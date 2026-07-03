from collections import defaultdict

from parser import SchedulerData


def lessons_by_teacher(data: SchedulerData):
    groups = defaultdict(list)

    for lesson in data.lessons:
        groups[lesson.teacher_id].append(lesson)

    return groups


def lessons_by_section(data: SchedulerData):
    groups = defaultdict(list)

    for lesson in data.lessons:
        groups[lesson.section_id].append(lesson)

    return groups


def lessons_by_subject(data: SchedulerData):
    groups = defaultdict(list)

    for lesson in data.lessons:
        groups[lesson.subject_id].append(lesson)

    return groups


def lessons_by_section_subject(data: SchedulerData):
    groups = defaultdict(list)

    for lesson in data.lessons:
        groups[(lesson.section_id, lesson.subject_id)].append(lesson)

    return groups


def slots_by_day(data: SchedulerData):
    groups = defaultdict(list)

    for slot in data.slots:
        groups[slot.day].append(slot)

    return groups


def slots_by_period(data: SchedulerData):
    groups = defaultdict(list)

    for slot in data.slots:
        groups[slot.period_number].append(slot)

    return groups