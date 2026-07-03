from dataclasses import dataclass
from typing import List
import json


# ==========================================================
# Models
# ==========================================================

@dataclass
class Teacher:
    id: int
    name: str


@dataclass
class Section:
    id: int
    class_id: int
    name: str


@dataclass
class Slot:
    id: int
    day: str
    period_id: int
    period_number: int
    start: str
    end: str


@dataclass
class Lesson:
    id: int
    teacher_id: int
    subject_id: int
    section_id: int
    class_id: int


@dataclass
class SchedulerData:
    teachers: List[Teacher]
    sections: List[Section]
    slots: List[Slot]
    lessons: List[Lesson]


# ==========================================================
# Parser
# ==========================================================

def load_input(path: str) -> SchedulerData:

    with open(path, "r", encoding="utf-8") as f:
        raw = json.load(f)

    teachers = [
        Teacher(
            id=t["id"],
            name=t["name"],
        )
        for t in raw["teachers"]
    ]

    sections = [
        Section(
            id=s["id"],
            class_id=s["class_id"],
            name=s["name"],
        )
        for s in raw["sections"]
    ]

    slots = [
        Slot(
            id=slot["id"],
            day=slot["day"],
            period_id=slot["period_id"],
            period_number=slot["period_number"],
            start=slot["start"],
            end=slot["end"],
        )
        for slot in raw["slots"]
    ]

    lessons = [
        Lesson(
            id=l["id"],
            teacher_id=l["teacher_id"],
            subject_id=l["subject_id"],
            section_id=l["section_id"],
            class_id=l["class_id"],
        )
        for l in raw["lessons"]
    ]

    return SchedulerData(
        teachers=teachers,
        sections=sections,
        slots=slots,
        lessons=lessons,
    )