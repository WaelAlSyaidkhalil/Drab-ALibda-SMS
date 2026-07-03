from ortools.sat.python import cp_model
import sys

from parser import load_input
from model_builder import build_variables
from constraints import (
    add_teacher_constraints,
    add_section_constraints,
    add_lesson_constraints,
)
from objective import add_objective
from exporter import export_solution


def main():

    if len(sys.argv) < 3:
        print("Usage:")
        print("python scheduler.py input.json output.json")
        return

    input_file = sys.argv[1]
    output_file = sys.argv[2]

    print("Loading data...")

    data = load_input(input_file)

    print(f"Lessons : {len(data.lessons)}")
    print(f"Teachers: {len(data.teachers)}")
    print(f"Sections: {len(data.sections)}")
    print(f"Slots   : {len(data.slots)}")

    model = cp_model.CpModel()

    print("Creating variables...")

    variables = build_variables(
        model=model,
        lessons=data.lessons,
        slots=data.slots,
    )

    print("Adding lesson constraints...")

    add_lesson_constraints(
        model,
        data,
        variables,
    )

    print("Adding teacher constraints...")

    add_teacher_constraints(
        model,
        data,
        variables,
    )

    print("Adding section constraints...")

    add_section_constraints(
        model,
        data,
        variables,
    )

    print("Adding objective...")

    add_objective(
        model,
        data,
        variables,
    )

    solver = cp_model.CpSolver()

    solver.parameters.max_time_in_seconds = 120

    solver.parameters.num_search_workers = 8

    print("Solving...")

    status = solver.Solve(model)

    if status not in (
        cp_model.OPTIMAL,
        cp_model.FEASIBLE,
    ):
        print("No solution found.")
        return

    print("Solution found!")

    export_solution(
        output_file,
        solver,
        data,
        variables,
    )

    print("Done.")


if __name__ == "__main__":
    main()