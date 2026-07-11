<?php

/**
 * Dynamic subject catalog for all university faculties.
 * Categorized by major identifiers.
 */
return [
    'majors' => [
        'computer_science' => [
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
            ]
        ],
        'medicine' => [
            'levels' => [
                1 => [
                    1 => [
                        ['key' => 'med_l1s1_bio', 'name_ar' => 'بيولوجيا طبية', 'name_en' => 'Medical Biology', 'credit_hours' => 3],
                        ['key' => 'med_l1s1_chem', 'name_ar' => 'كيمياء طبية', 'name_en' => 'Medical Chemistry', 'credit_hours' => 3],
                        ['key' => 'med_l1s1_phys', 'name_ar' => 'فيزياء طبية', 'name_en' => 'Medical Physics', 'credit_hours' => 3],
                        ['key' => 'med_l1s1_english', 'name_ar' => 'مصطلحات طبية ولغة إنجليزية', 'name_en' => 'English for Medicine', 'credit_hours' => 2],
                    ],
                    2 => [
                        ['key' => 'med_l1s2_anat1', 'name_ar' => 'تشريح عام 1', 'name_en' => 'General Anatomy I', 'credit_hours' => 4],
                        ['key' => 'med_l1s2_biochem1', 'name_ar' => 'كيمياء حيوية طبية 1', 'name_en' => 'Medical Biochemistry I', 'credit_hours' => 4],
                        ['key' => 'med_l1s2_physio1', 'name_ar' => 'علم وظائف الأعضاء 1', 'name_en' => 'Physiology I', 'credit_hours' => 3],
                        ['key' => 'med_l1s2_hist1', 'name_ar' => 'علم الأنسجة 1', 'name_en' => 'Histology I', 'credit_hours' => 3],
                    ],
                ],
                2 => [
                    1 => [
                        ['key' => 'med_l2s1_anat2', 'name_ar' => 'تشريح عام 2', 'name_en' => 'General Anatomy II', 'credit_hours' => 4],
                        ['key' => 'med_l2s1_biochem2', 'name_ar' => 'كيمياء حيوية طبية 2', 'name_en' => 'Medical Biochemistry II', 'credit_hours' => 4],
                        ['key' => 'med_l2s1_physio2', 'name_ar' => 'علم وظائف الأعضاء 2', 'name_en' => 'Physiology II', 'credit_hours' => 3],
                        ['key' => 'med_l2s1_hist2', 'name_ar' => 'علم الأنسجة 2', 'name_en' => 'Histology II', 'credit_hours' => 3],
                    ],
                    2 => [
                        ['key' => 'med_l2s2_imm', 'name_ar' => 'علم المناعة', 'name_en' => 'Immunology', 'credit_hours' => 3],
                        ['key' => 'med_l2s2_path1', 'name_ar' => 'علم الأمراض العام', 'name_en' => 'General Pathology', 'credit_hours' => 4],
                        ['key' => 'med_l2s2_micro', 'name_ar' => 'علم الأحياء الدقيقة المجهرية', 'name_en' => 'Microbiology', 'credit_hours' => 4],
                        ['key' => 'med_l2s2_gene', 'name_ar' => 'وراثة طبية', 'name_en' => 'Medical Genetics', 'credit_hours' => 2],
                    ],
                ],
                3 => [
                    1 => [
                        ['key' => 'med_l3s1_path2', 'name_ar' => 'علم الأمراض الخاص', 'name_en' => 'Systemic Pathology', 'credit_hours' => 4],
                        ['key' => 'med_l3s1_phar1', 'name_ar' => 'علم الأدوية 1', 'name_en' => 'Pharmacology I', 'credit_hours' => 4],
                        ['key' => 'med_l3s1_clin_physio', 'name_ar' => 'وظائف الأعضاء السريري', 'name_en' => 'Clinical Physiology', 'credit_hours' => 3],
                        ['key' => 'med_l3s1_parasit', 'name_ar' => 'علم الطفيليات الطبية', 'name_en' => 'Parasitology', 'credit_hours' => 3],
                    ],
                    2 => [
                        ['key' => 'med_l3s2_phar2', 'name_ar' => 'علم الأدوية 2', 'name_en' => 'Pharmacology II', 'credit_hours' => 4],
                        ['key' => 'med_l3s2_comm', 'name_ar' => 'طب المجتمع والصحة العامة', 'name_en' => 'Community Medicine', 'credit_hours' => 3],
                        ['key' => 'med_l3s2_exam', 'name_ar' => 'الفحص السريري والتشخيص المعملي', 'name_en' => 'Clinical Examination', 'credit_hours' => 3],
                        ['key' => 'med_l3s2_forens', 'name_ar' => 'الطب الشرعي والسموم', 'name_en' => 'Forensic Medicine', 'credit_hours' => 2],
                    ],
                ],
                4 => [
                    1 => [
                        ['key' => 'med_l4s1_med1', 'name_ar' => 'الطب الباطني 1', 'name_en' => 'Internal Medicine I', 'credit_hours' => 6],
                        ['key' => 'med_l4s1_surg1', 'name_ar' => 'الجراحة العامة 1', 'name_en' => 'General Surgery I', 'credit_hours' => 6],
                        ['key' => 'med_l4s1_ethics', 'name_ar' => 'أخلاقيات المهنة الطبية', 'name_en' => 'Medical Ethics', 'credit_hours' => 2],
                        ['key' => 'med_l4s1_rad', 'name_ar' => 'الأشعة والتشخيص التصويري', 'name_en' => 'Radiology', 'credit_hours' => 2],
                    ],
                    2 => [
                        ['key' => 'med_l4s2_med2', 'name_ar' => 'الطب الباطني 2', 'name_en' => 'Internal Medicine II', 'credit_hours' => 6],
                        ['key' => 'med_l4s2_surg2', 'name_ar' => 'الجراحة العامة 2', 'name_en' => 'General Surgery II', 'credit_hours' => 6],
                        ['key' => 'med_l4s2_ped1', 'name_ar' => 'طب الأطفال 1', 'name_en' => 'Pediatrics I', 'credit_hours' => 4],
                        ['key' => 'med_l4s2_obs1', 'name_ar' => 'أمراض النساء والتوليد 1', 'name_en' => 'Obstetrics & Gynecology I', 'credit_hours' => 4],
                    ],
                ],
            ]
        ],
        'accounting' => [
            'levels' => [
                1 => [
                    1 => [
                        ['key' => 'acc_l1s1_principles1', 'name_ar' => 'مبادئ المحاسبة 1', 'name_en' => 'Principles of Accounting I', 'credit_hours' => 3],
                        ['key' => 'acc_l1s1_micro', 'name_ar' => 'مبادئ الاقتصاد الجزئي', 'name_en' => 'Principles of Microeconomics', 'credit_hours' => 3],
                        ['key' => 'acc_l1s1_math', 'name_ar' => 'رياضيات الأعمال', 'name_en' => 'Business Mathematics', 'credit_hours' => 3],
                        ['key' => 'acc_l1s1_intro', 'name_ar' => 'مقدمة في إدارة الأعمال', 'name_en' => 'Introduction to Business', 'credit_hours' => 3],
                    ],
                    2 => [
                        ['key' => 'acc_l1s2_principles2', 'name_ar' => 'مبادئ المحاسبة 2', 'name_en' => 'Principles of Accounting II', 'credit_hours' => 3],
                        ['key' => 'acc_l1s2_macro', 'name_ar' => 'مبادئ الاقتصاد الكلي', 'name_en' => 'Principles of Macroeconomics', 'credit_hours' => 3],
                        ['key' => 'acc_l1s2_stats', 'name_ar' => 'إحصاء الأعمال', 'name_en' => 'Business Statistics', 'credit_hours' => 3],
                        ['key' => 'acc_l1s2_law', 'name_ar' => 'القانون التجاري ومبادئ القانون', 'name_en' => 'Commercial Law', 'credit_hours' => 3],
                    ],
                ],
                2 => [
                    1 => [
                        ['key' => 'acc_l2s1_inter1', 'name_ar' => 'المحاسبة المتوسطة 1', 'name_en' => 'Intermediate Accounting I', 'credit_hours' => 3],
                        ['key' => 'acc_l2s1_cost', 'name_ar' => 'محاسبة التكاليف', 'name_en' => 'Cost Accounting', 'credit_hours' => 3],
                        ['key' => 'acc_l2s1_finance', 'name_ar' => 'الإدارة المالية', 'name_en' => 'Financial Management', 'credit_hours' => 3],
                        ['key' => 'acc_l2s1_corp_law', 'name_ar' => 'قانون الشركات والرقابة المالية', 'name_en' => 'Corporation Law', 'credit_hours' => 3],
                    ],
                    2 => [
                        ['key' => 'acc_l2s2_inter2', 'name_ar' => 'المحاسبة المتوسطة 2', 'name_en' => 'Intermediate Accounting II', 'credit_hours' => 3],
                        ['key' => 'acc_l2s2_managerial', 'name_ar' => 'المحاسبة الإدارية', 'name_en' => 'Managerial Accounting', 'credit_hours' => 3],
                        ['key' => 'acc_l2s2_tax', 'name_ar' => 'المحاسبة الضريبية والزكاة', 'name_en' => 'Tax Accounting', 'credit_hours' => 3],
                        ['key' => 'acc_l2s2_audit_principles', 'name_ar' => 'مبادئ المراجعة والتدقيق', 'name_en' => 'Auditing Principles', 'credit_hours' => 3],
                    ],
                ],
                3 => [
                    1 => [
                        ['key' => 'acc_l3s1_adv1', 'name_ar' => 'محاسبة متقدمة 1', 'name_en' => 'Advanced Accounting I', 'credit_hours' => 3],
                        ['key' => 'acc_l3s1_gov', 'name_ar' => 'المحاسبة الحكومية والقومية', 'name_en' => 'Government Accounting', 'credit_hours' => 3],
                        ['key' => 'acc_l3s1_audit_practice', 'name_ar' => 'مراجعة الحسابات وتدقيقها العملي', 'name_en' => 'Auditing Practice', 'credit_hours' => 3],
                        ['key' => 'acc_l3s1_ais', 'name_ar' => 'نظم المعلومات المحاسبية', 'name_en' => 'Accounting Information Systems', 'credit_hours' => 3],
                    ],
                    2 => [
                        ['key' => 'acc_l3s2_adv2', 'name_ar' => 'محاسبة متقدمة 2', 'name_en' => 'Advanced Accounting II', 'credit_hours' => 3],
                        ['key' => 'acc_l3s2_ias', 'name_ar' => 'معايير المحاسبة الدولية', 'name_en' => 'International Accounting Standards', 'credit_hours' => 3],
                        ['key' => 'acc_l3s2_theory', 'name_ar' => 'النظرية المحاسبية المعاصرة', 'name_en' => 'Accounting Theory', 'credit_hours' => 3],
                        ['key' => 'acc_l3s2_analysis', 'name_ar' => 'تحليل التقارير والقوائم المالية', 'name_en' => 'Financial Statement Analysis', 'credit_hours' => 3],
                    ],
                ],
                4 => [
                    1 => [
                        ['key' => 'acc_l4s1_problems', 'name_ar' => 'مشكلات محاسبية معاصرة', 'name_en' => 'Contemporary Accounting Problems', 'credit_hours' => 3],
                        ['key' => 'acc_l4s1_internal_audit', 'name_ar' => 'التدقيق والرقابة الداخلية', 'name_en' => 'Internal Auditing', 'credit_hours' => 3],
                        ['key' => 'acc_l4s1_budgeting', 'name_ar' => 'الموازنات التخطيطية والرقابة', 'name_en' => 'Budgeting and Control', 'credit_hours' => 3],
                        ['key' => 'acc_l4s1_feasibility', 'name_ar' => 'دراسات الجدوى الاقتصادية', 'name_en' => 'Feasibility Studies', 'credit_hours' => 3],
                    ],
                    2 => [
                        ['key' => 'acc_l4s2_project', 'name_ar' => 'بحث تخرج في المحاسبة', 'name_en' => 'Graduation Project in Accounting', 'credit_hours' => 4],
                        ['key' => 'acc_l4s2_ethics', 'name_ar' => 'سلوكيات وأخلاقيات المهنة', 'name_en' => 'Accounting Ethics', 'credit_hours' => 2],
                        ['key' => 'acc_l4s2_applications', 'name_ar' => 'التطبيقات الحاسوبية في المحاسبة', 'name_en' => 'Electronic Accounting Applications', 'credit_hours' => 3],
                        ['key' => 'acc_l4s2_internship', 'name_ar' => 'التدريب العملي الميداني', 'name_en' => 'Field Training', 'credit_hours' => 3],
                    ],
                ],
            ]
        ],
        'sharia_and_law' => [
            'levels' => [
                1 => [
                    1 => [
                        ['key' => 'law_l1s1_intro', 'name_ar' => 'المدخل لدراسة العلوم القانونية', 'name_en' => 'Introduction to Law Study', 'credit_hours' => 3],
                        ['key' => 'law_l1s1_sharia1', 'name_ar' => 'الفقه الإسلامي 1 (العبادات)', 'name_en' => 'Islamic Jurisprudence I', 'credit_hours' => 3],
                        ['key' => 'law_l1s1_const', 'name_ar' => 'القانون الدستوري والنظم السياسية', 'name_en' => 'Constitutional Law', 'credit_hours' => 3],
                        ['key' => 'law_l1s1_english', 'name_ar' => 'المصطلحات القانونية باللغة الأجنبية', 'name_en' => 'Legal Terminology in English', 'credit_hours' => 2],
                    ],
                    2 => [
                        ['key' => 'law_l1s2_obligation_sources', 'name_ar' => 'مصادر الالتزام (القانون المدني)', 'name_en' => 'Sources of Obligation', 'credit_hours' => 3],
                        ['key' => 'law_l1s2_sharia2', 'name_ar' => 'الفقه الإسلامي 2 (الأحوال الشخصية)', 'name_en' => 'Islamic Jurisprudence II', 'credit_hours' => 3],
                        ['key' => 'law_l1s2_admin_law', 'name_ar' => 'القانون الإداري العام', 'name_en' => 'Administrative Law', 'credit_hours' => 3],
                        ['key' => 'law_l1s2_history', 'name_ar' => 'تاريخ القانون ومؤسساته', 'name_en' => 'History of Law', 'credit_hours' => 2],
                    ],
                ],
                2 => [
                    1 => [
                        ['key' => 'law_l2s1_obligation_rules', 'name_ar' => 'أحكام الالتزام والإثبات', 'name_en' => 'Rules of Obligation', 'credit_hours' => 3],
                        ['key' => 'law_l2s1_sharia3', 'name_ar' => 'الفقه الإسلامي 3 (المعاملات المالية)', 'name_en' => 'Islamic Jurisprudence III', 'credit_hours' => 3],
                        ['key' => 'law_l2s1_penal_general', 'name_ar' => 'قانون العقوبات (القسم العام)', 'name_en' => 'Penal Law (General Part)', 'credit_hours' => 3],
                        ['key' => 'law_l2s1_intl_public', 'name_ar' => 'القانون الدولي العام والمنظمات', 'name_en' => 'Public International Law', 'credit_hours' => 3],
                    ],
                    2 => [
                        ['key' => 'law_l2s2_comm_principles', 'name_ar' => 'الأعمال التجارية والشركات', 'name_en' => 'Commercial Law (Principles & Corporations)', 'credit_hours' => 3],
                        ['key' => 'law_l2s2_family_law', 'name_ar' => 'المواريث والوصايا والوقف', 'name_en' => 'Family Law (Islamic)', 'credit_hours' => 3],
                        ['key' => 'law_l2s2_penal_special', 'name_ar' => 'قانون العقوبات (القسم الخاص)', 'name_en' => 'Penal Law (Special Part)', 'credit_hours' => 3],
                        ['key' => 'law_l2s2_finance', 'name_ar' => 'المالية العامة والتشريع الضريبي', 'name_en' => 'Public Finance', 'credit_hours' => 3],
                    ],
                ],
                3 => [
                    1 => [
                        ['key' => 'law_l3s1_contracts', 'name_ar' => 'العقود المدنية (البيع والإيجار)', 'name_en' => 'Civil Contracts (Sale & Lease)', 'credit_hours' => 3],
                        ['key' => 'law_l3s1_sharia4', 'name_ar' => 'أصول الفقه الإسلامي 1', 'name_en' => 'Islamic Jurisprudence IV (Inheritance)', 'credit_hours' => 3],
                        ['key' => 'law_l3s1_labor', 'name_ar' => 'قانون العمل والتأمينات الاجتماعية', 'name_en' => 'Labor Law', 'credit_hours' => 3],
                        ['key' => 'law_l3s1_admin_judiciary', 'name_ar' => 'القضاء الإداري والطعون الإدارية', 'name_en' => 'Administrative Judiciary', 'credit_hours' => 3],
                    ],
                    2 => [
                        ['key' => 'law_l3s2_papers', 'name_ar' => 'الأوراق التجارية والعمليات المصرفية والعملية الإفلاسية', 'name_en' => 'Commercial Papers & Bankruptcy', 'credit_hours' => 3],
                        ['key' => 'law_l3s2_sharia5', 'name_ar' => 'أصول الفقه الإسلامي 2', 'name_en' => 'Islamic Jurisprudence V (Principles of Fiqh)', 'credit_hours' => 3],
                        ['key' => 'law_l3s2_procedures', 'name_ar' => 'قانون المرافعات المدنية والتجارية', 'name_en' => 'Civil & Commercial Procedures', 'credit_hours' => 3],
                        ['key' => 'law_l3s2_intl_private', 'name_ar' => 'القانون الدولي الخاص (الجنسية والموطن)', 'name_en' => 'Private International Law', 'credit_hours' => 3],
                    ],
                ],
                4 => [
                    1 => [
                        ['key' => 'law_l4s1_criminal_proc', 'name_ar' => 'قانون الإجراءات الجزائية والمحاكمات', 'name_en' => 'Criminal Procedures Law', 'credit_hours' => 3],
                        ['key' => 'law_l4s1_maritime', 'name_ar' => 'القانون البحري والجوي', 'name_en' => 'Maritime and Aviation Law', 'credit_hours' => 3],
                        ['key' => 'law_l4s1_execution', 'name_ar' => 'قانون التنفيذ الجبري والتحكيم', 'name_en' => 'Execution Law', 'credit_hours' => 2],
                        ['key' => 'law_l4s1_issues', 'name_ar' => 'القضايا والمشكلات القانونية المعاصرة', 'name_en' => 'Contemporary Legal Issues', 'credit_hours' => 3],
                    ],
                    2 => [
                        ['key' => 'law_l4s2_project', 'name_ar' => 'مشروع تخرج في بحث قانوني', 'name_en' => 'Graduation Project in Law', 'credit_hours' => 4],
                        ['key' => 'law_l4s2_ethics', 'name_ar' => 'أخلاق وآداب مهنة المحاماة والقضاء', 'name_en' => 'Professional Ethics for Lawyers', 'credit_hours' => 2],
                        ['key' => 'law_l4s2_court', 'name_ar' => 'التطبيقات العملية (المحاكم الافتراضية)', 'name_en' => 'Judiciary Practice (Mock Trials)', 'credit_hours' => 3],
                        ['key' => 'law_l4s2_training', 'name_ar' => 'التدريب السريري والعيادة القانونية', 'name_en' => 'Legal Clinic training', 'credit_hours' => 3],
                    ],
                ],
            ]
        ],
    ]
];
