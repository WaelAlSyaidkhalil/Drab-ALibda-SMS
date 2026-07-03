from dataclasses import dataclass
from typing import Dict

from ortools.sat.python import cp_model

from parser import Lesson, Slot


@dataclass
class Variables:
    """
    Holds every decision variable used by the solver.
    """

    # (lesson_id, slot_id) -> BoolVar
    lesson_slot: Dict[tuple[int, int], cp_model.IntVar]


def build_variables(
    model: cp_model.CpModel,
    lessons: list[Lesson],
    slots: list[Slot],
) -> Variables:

    lesson_slot = {}

    for lesson in lessons:

        for slot in slots:

            lesson_slot[(lesson.id, slot.id)] = model.NewBoolVar(
                f"L{lesson.id}_S{slot.id}"
            )

    return Variables(
        lesson_slot=lesson_slot,
    )