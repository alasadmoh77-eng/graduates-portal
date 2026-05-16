<?php

/**
 * Central catalog of courses for the Computer Science / IT academic record editor.
 *
 * Edit only this file to add, remove, or change subjects. Keys must stay stable
 * once records reference them (graduate_academic_subjects.catalog_key).
 *
 * Structure: levels[1-4][semester 1-2] = list of subjects.
 * Each subject: key (unique), name_ar, name_en, credit_hours
 */
return [
    'levels' => [
        1 => [
            1 => [
                ['key' => 'cs_l1s1_intro_cs', 'name_ar' => 'مقدمة في الحاسوب', 'name_en' => 'Introduction to Computer Science', 'credit_hours' => 3],
                ['key' => 'cs_l1s1_calc1', 'name_ar' => 'تفاضل وتكامل 1', 'name_en' => 'Calculus I', 'credit_hours' => 3],
                ['key' => 'cs_l1s1_arabic1', 'name_ar' => 'لغة عربية (1)', 'name_en' => 'Arabic Language (1)', 'credit_hours' => 3],
                ['key' => 'cs_l1s1_physics', 'name_ar' => 'فيزياء عامة', 'name_en' => 'General Physics', 'credit_hours' => 3],
            ],
            2 => [
                ['key' => 'cs_l1s2_cs_skills', 'name_ar' => 'مهارات حاسوب', 'name_en' => 'Computer Skills', 'credit_hours' => 3],
                ['key' => 'cs_l1s2_discrete', 'name_ar' => 'رياضيات متقطعة', 'name_en' => 'Discrete Mathematics', 'credit_hours' => 3],
                ['key' => 'cs_l1s2_struct_prog', 'name_ar' => 'البرمجة المنظمة', 'name_en' => 'Structured Programming', 'credit_hours' => 3],
                ['key' => 'cs_l1s2_english1', 'name_ar' => 'لغة إنجليزية (1)', 'name_en' => 'English (1)', 'credit_hours' => 2],
            ],
        ],
        2 => [
            1 => [
                ['key' => 'cs_l2s1_prog', 'name_ar' => 'برمجة حاسوب', 'name_en' => 'Computer Programming', 'credit_hours' => 3],
                ['key' => 'cs_l2s1_digital', 'name_ar' => 'منطق رقمي', 'name_en' => 'Digital Logic', 'credit_hours' => 3],
                ['key' => 'cs_l2s1_linear', 'name_ar' => 'جبر خطي', 'name_en' => 'Linear Algebra', 'credit_hours' => 3],
                ['key' => 'cs_l2s1_prob', 'name_ar' => 'احتمالات وإحصاء', 'name_en' => 'Probability and Statistics', 'credit_hours' => 3],
            ],
            2 => [
                ['key' => 'cs_l2s2_ds', 'name_ar' => 'هيكلية البيانات', 'name_en' => 'Data Structures', 'credit_hours' => 3],
                ['key' => 'cs_l2s2_co', 'name_ar' => 'تنظيم الحاسوب', 'name_en' => 'Computer Organization', 'credit_hours' => 3],
                ['key' => 'cs_l2s2_ood', 'name_ar' => 'برمجة كائنية', 'name_en' => 'Object-Oriented Programming', 'credit_hours' => 3],
                ['key' => 'cs_l2s2_db_intro', 'name_ar' => 'مقدمة في قواعد البيانات', 'name_en' => 'Introduction to Databases', 'credit_hours' => 3],
            ],
        ],
        3 => [
            1 => [
                ['key' => 'cs_l3s1_networks', 'name_ar' => 'شبكات حاسوب', 'name_en' => 'Computer Networks', 'credit_hours' => 3],
                ['key' => 'cs_l3s1_os', 'name_ar' => 'نظم التشغيل', 'name_en' => 'Operating Systems', 'credit_hours' => 3],
                ['key' => 'cs_l3s1_algo', 'name_ar' => 'تصميم الخوارزميات', 'name_en' => 'Algorithm Design', 'credit_hours' => 3],
                ['key' => 'cs_l3s1_se', 'name_ar' => 'هندسة البرمجيات', 'name_en' => 'Software Engineering', 'credit_hours' => 3],
            ],
            2 => [
                ['key' => 'cs_l3s2_db_sys', 'name_ar' => 'نظم قواعد البيانات', 'name_en' => 'Database Systems', 'credit_hours' => 3],
                ['key' => 'cs_l3s2_graphics', 'name_ar' => 'رسومات حاسوب', 'name_en' => 'Computer Graphics', 'credit_hours' => 3],
                ['key' => 'cs_l3s2_security', 'name_ar' => 'أمن المعلومات', 'name_en' => 'Information Security', 'credit_hours' => 3],
                ['key' => 'cs_l3s2_web', 'name_ar' => 'تقنيات الويب', 'name_en' => 'Web Technologies', 'credit_hours' => 3],
            ],
        ],
        4 => [
            1 => [
                ['key' => 'cs_l4s1_ai', 'name_ar' => 'ذكاء اصطناعي', 'name_en' => 'Artificial Intelligence', 'credit_hours' => 3],
                ['key' => 'cs_l4s1_compilers', 'name_ar' => 'مترجمات', 'name_en' => 'Compilers', 'credit_hours' => 3],
                ['key' => 'cs_l4s1_distributed', 'name_ar' => 'نظم موزعة', 'name_en' => 'Distributed Systems', 'credit_hours' => 3],
                ['key' => 'cs_l4s1_elective1', 'name_ar' => 'اختياري (1)', 'name_en' => 'Elective (1)', 'credit_hours' => 3],
            ],
            2 => [
                ['key' => 'cs_l4s2_project', 'name_ar' => 'مشروع تخرج', 'name_en' => 'Graduation Project', 'credit_hours' => 6],
                ['key' => 'cs_l4s2_ethics', 'name_ar' => 'أخلاقيات الحوسبة', 'name_en' => 'Computing Ethics', 'credit_hours' => 2],
                ['key' => 'cs_l4s2_elective2', 'name_ar' => 'اختياري (2)', 'name_en' => 'Elective (2)', 'credit_hours' => 3],
                ['key' => 'cs_l4s2_internship', 'name_ar' => 'تدريب ميداني', 'name_en' => 'Internship', 'credit_hours' => 3],
            ],
        ],
    ],
];
