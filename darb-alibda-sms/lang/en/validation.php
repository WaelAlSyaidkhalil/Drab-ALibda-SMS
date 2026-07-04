<?php

return [
    'custom' => [
        'name' => [
            'required' => 'The name field is required.',
            'max' => 'The name may not be greater than :max characters.',
        ],
        'first_name' => [
            'required' => 'The first name field is required.',
            'max' => 'The first name may not be greater than :max characters.',
        ],
        'last_name' => [
            'required' => 'The last name field is required.',
            'max' => 'The last name may not be greater than :max characters.',
        ],
        'email' => [
            'required' => 'The email field is required.',
            'email' => 'Please enter a valid email address.',
            'unique' => 'This email address is already in use.',
        ],
        'phone' => [
            'required' => 'The phone field is required.',
            'regex' => 'Please enter a valid phone number.',
            'unique' => 'This phone number is already in use.',
            'max' => 'The phone number may not be greater than :max digits.',
            'tel' => 'Please enter a valid phone number.',
        ],
        'password' => [
            'required' => 'The password field is required.',
            'min' => 'The password must be at least :min characters.',
            'max' => 'The password may not be greater than :max characters.',
        ],
        'password_confirmation' => [
            'required' => 'The password confirmation field is required.',
            'same' => 'The password confirmation does not match.',
            'min' => 'The password confirmation must be at least :min characters.',
            'max' => 'The password confirmation may not be greater than :max characters.',
        ],
        'role_id' => [
            'required' => 'The role is required.',
        ],
        'is_active' => [
            'boolean' => 'The value must be true or false.',
        ],
        'gender' => [
            'required' => 'The gender is required.',
        ],
        'birth_date' => [
            'date' => 'The birth date is invalid.',
        ],
        'national_id' => [
            'unique' => 'This national ID is already in use.',
        ],
        'registry_number' => [
            'required' => 'The registry number is required.',
            'unique' => 'The registry number is already in use.',
        ],
        'employee_number' => [
            'unique' => 'The employee number is already in use.',
        ],
        'experience_years' => [
            'numeric' => 'The experience years must be numeric.',
            'min' => 'The experience years may not be less than :min.',
        ],
        'status' => [
            'required' => 'The status is required.',
        ],
        'title' => [
            'required' => 'The title is required.',
            'max' => 'The title may not be greater than :max characters.',
        ],
        'body' => [
            'required' => 'The body field is required.',
        ],
        'audience' => [
            'required' => 'The audience is required.',
        ],
        'term_id' => [
            'required' => 'The term is required.',
        ],
        'section_id' => [
            'required' => 'The section is required.',
        ],
        'day' => [
            'required' => 'The day is required.',
        ],
        'subject_id' => [
            'required' => 'The subject is required.',
        ],
        'teacher_id' => [
            'required' => 'The teacher is required.',
        ],
        'class_id' => [
            'required' => 'The class is required.',
        ],
        'student_id' => [
            'required' => 'The student is required.',
        ],
        'full_mark' => [
            'required' => 'The full mark is required.',
            'numeric' => 'The full mark must be numeric.',
            'min' => 'The full mark must be at least :min.',
        ],
        'pass_mark' => [
            'required' => 'The pass mark is required.',
            'numeric' => 'The pass mark must be numeric.',
            'min' => 'The pass mark must be at least :min.',
            'lte' => 'The pass mark must be less than or equal to the full mark.',
        ],
        'date' => [
            'required' => 'The date is required.',
        ],
        'reason' => [
            'max' => 'The reason may not be greater than :max characters.',
        ],
        'type' => [
            'required' => 'The type is required.',
        ],
        'academic_year' => [
            'required' => 'The academic year is required.',
        ],
        'start_date' => [
            'date' => 'The start date is invalid.',
        ],
        'end_date' => [
            'date' => 'The end date is invalid.',
        ],
        'feedback' => [
            'required' => 'The feedback field is required.',
        ],
        'start_time' => [
            'required' => 'The start time is required.',
        ],
        'end_time' => [
            'required' => 'The end time is required.',
        ],
        'final_average' => [
            'numeric' => 'The final average must be numeric.',
        ],
        'final_result' => [
            'required' => 'The final result is required.',
        ],
        'role_display' => [
            'required' => 'The role display is required.',
        ],
        'mother_name' => [
            'max' => 'The mother name may not be greater than :max characters.',
        ],
        'father_name' => [
            'max' => 'The father name may not be greater than :max characters.',
        ],
        'specialization' => [
            'max' => 'The specialization may not be greater than :max characters.',
        ],
        'grade' => [
            'max' => 'The grade may not be greater than :max characters.',
        ],
        'address' => [
            'max' => 'The address may not be greater than :max characters.',
        ],
        'capacity' => [
            'required' => 'The capacity is required.',
        ],
        'code' => [
            'required' => 'The code field is required.',
            'unique' => 'The code has already been taken.',
        ],
        'subject_component_id' => [
            'required' => 'The subject component is required.',
        ],
        'mark' => [
            'required' => 'The mark is required.',
            'numeric' => 'The mark must be numeric.',
        ],
        'term1_mark' => [
            'numeric' => 'The first term mark must be numeric.',
            'minValue' => 'The first term mark may not be less than :min.',
            'maxValue' => 'The first term mark may not be greater than :max.',
        ],
        'term2_mark' => [
            'numeric' => 'The second term mark must be numeric.',
            'minValue' => 'The second term mark may not be less than :min.',
            'maxValue' => 'The second term mark may not be greater than :max.',
        ],
        'yearly_mark' => [
            'numeric' => 'The yearly mark must be numeric.',
            'minValue' => 'The yearly mark may not be less than :min.',
            'maxValue' => 'The yearly mark may not be greater than :max.',
        ],
        'result' => [
            'in' => 'The selected result is invalid.',
        ],
        'enrollment_date' => [
            'required' => 'The enrollment date is required.',
        ],
        'notes' => [
            'max' => 'The notes may not be greater than :max characters.',
        ],
        'final_average' => [
            'numeric' => 'The final average must be numeric.',
        ],
        'employment_type' => [
            'max' => 'The employment type may not be greater than :max characters.',
        ],

    ],
];
