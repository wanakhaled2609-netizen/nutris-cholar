<?php
/**
 * NutriScholar Configuration File
 * Centralized settings for the application
 */

return [
    // Application Settings
    'app' => [
        'name' => 'NutriScholar',
        'version' => '2.0',
        'description' => 'Advanced Clinical Nutrition Assistant',
        'language' => 'ar',
        'timezone' => 'Africa/Cairo',
    ],

    // Database Settings (for future integration)
    'database' => [
        'enabled' => false,
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'nutrischolar',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],

    // Medical Formulas and Constants
    'formulas' => [
        // Harris-Benedict BMR Formula
        'bmr' => [
            'male' => ['a' => 10, 'b' => 6.25, 'c' => 5, 'd' => 5],
            'female' => ['a' => 10, 'b' => 6.25, 'c' => 5, 'd' => -161],
        ],

        // Ideal Body Weight (Devine Formula)
        'ibw' => [
            'male' => ['base' => 50, 'per_inch' => 2.3],
            'female' => ['base' => 45.5, 'per_inch' => 2.3],
        ],

        // Activity Multipliers
        'activity_multipliers' => [
            'sedentary' => 1.2,
            'lightly_active' => 1.375,
            'moderately_active' => 1.55,
            'very_active' => 1.725,
        ],
    ],

    // Disease-specific Nutritional Guidelines
    'diseases' => [
        'hypertension' => [
            'name' => 'ارتفاع ضغط الدم',
            'carbs' => 55,
            'protein' => 18,
            'fat' => 27,
            'special' => [
                'sodium' => '< 2300 mg/day',
                'potassium' => 'high',
                'fiber' => '25-30 g/day',
            ],
            'food_pyramid' => [
                'allowed' => ['fruits', 'vegetables', 'lean_meat', 'low_fat_dairy', 'whole_grains'],
                'avoid' => ['salt', 'canned_foods', 'red_meat', 'processed_foods'],
            ],
        ],

        'diabetes_1' => [
            'name' => 'مريض سكر (النوع الأول)',
            'carbs' => 45,
            'protein' => 25,
            'fat' => 30,
            'special' => [
                'fiber' => '> 30 g/day',
                'sugar' => '0 added sugars',
                'consistency' => 'careful meal timing',
            ],
            'food_pyramid' => [
                'allowed' => ['legumes', 'leafy_vegetables', 'whole_grains', 'nuts', 'fish'],
                'avoid' => ['white_bread', 'sugary_drinks', 'sweets', 'white_rice'],
            ],
        ],

        'diabetes_2' => [
            'name' => 'مريض سكر (النوع الثاني)',
            'carbs' => 45,
            'protein' => 25,
            'fat' => 30,
            'special' => [
                'fiber' => '> 30 g/day',
                'weight_loss' => 'emphasis',
                'sugar' => '0 added sugars',
            ],
            'food_pyramid' => [
                'allowed' => ['legumes', 'vegetables', 'whole_grains', 'chicken', 'fish'],
                'avoid' => ['white_bread', 'soft_drinks', 'candy', 'refined_grains'],
            ],
        ],

        'heart_disease' => [
            'name' => 'أمراض القلب',
            'carbs' => 45,
            'protein' => 25,
            'fat' => 30,
            'special' => [
                'saturated_fat' => '< 7%',
                'cholesterol' => '< 200 mg/day',
                'fiber' => '> 25 g/day',
                'sodium' => 'low',
            ],
            'food_pyramid' => [
                'allowed' => ['olive_oil', 'avocado', 'salmon', 'nuts', 'berries', 'leafy_greens'],
                'avoid' => ['saturated_fats', 'lard', 'sweets', 'fried_foods', 'processed_meats'],
            ],
        ],

        'diabetes_heart' => [
            'name' => 'مريض سكر مع مرض في القلب',
            'carbs' => 45,
            'protein' => 25,
            'fat' => 30,
            'special' => [
                'low_sodium' => 'strict',
                'fiber' => '> 30 g/day',
                'saturated_fat' => '< 7%',
                'sugar' => '0 added sugars',
            ],
            'food_pyramid' => [
                'allowed' => ['salmon', 'legumes', 'olive_oil', 'vegetables', 'whole_grains', 'nuts'],
                'avoid' => ['salt', 'sugars', 'fried_foods', 'red_meat', 'processed_foods'],
            ],
        ],

        'none' => [
            'name' => 'شخص صحي',
            'carbs' => 50,
            'protein' => 20,
            'fat' => 30,
            'special' => [
                'variety' => 'emphasis',
                'balance' => 'food pyramid',
            ],
            'food_pyramid' => [
                'allowed' => ['all_fruits', 'all_vegetables', 'whole_grains', 'dairy', 'proteins'],
                'avoid' => ['excessive_processed_foods'],
            ],
        ],
    ],

    // BMI Categories
    'bmi_categories' => [
        'underweight' => ['min' => 0, 'max' => 18.4, 'ar' => 'نحافة'],
        'normal' => ['min' => 18.5, 'max' => 24.9, 'ar' => 'وزن مثالي'],
        'overweight' => ['min' => 25, 'max' => 29.9, 'ar' => 'زيادة وزن'],
        'obese' => ['min' => 30, 'max' => 999, 'ar' => 'سمنة'],
    ],

    // MUAC (Mid Upper Arm Circumference) Standards
    'muac' => [
        'male' => [
            'normal' => 28,
            'underweight' => 28,
            'overweight' => 28,
        ],
        'female' => [
            'normal' => 25,
            'underweight' => 25,
            'overweight' => 25,
        ],
    ],

    // Meal Suggestions Templates
    'meals' => [
        'breakfast' => [
            'hypertension' => ['low_salt_yogurt', 'whole_wheat', 'eggs', 'fruits'],
            'diabetes_1' => ['eggs', 'nuts', 'low_carb_dairy', 'vegetables'],
            'diabetes_2' => ['eggs', 'nuts', 'plain_yogurt', 'fruits'],
            'heart_disease' => ['whole_wheat', 'olive_oil', 'berries', 'nuts'],
            'diabetes_heart' => ['eggs', 'nuts', 'olive_oil', 'fresh_vegetables'],
            'none' => ['variety', 'balanced', 'proteins', 'carbs', 'fruits'],
        ],
        'lunch' => [
            'hypertension' => ['grilled_fish', 'brown_rice', 'vegetables', 'olive_oil'],
            'diabetes_1' => ['chicken', 'legumes', 'green_beans', 'oil'],
            'diabetes_2' => ['baked_fish', 'brown_rice', 'vegetables', 'no_added_sugar'],
            'heart_disease' => ['salmon', 'whole_pasta', 'olive_oil', 'vegetables'],
            'diabetes_heart' => ['salmon', 'legumes', 'olive_oil', 'vegetables'],
            'none' => ['diverse_proteins', 'grains', 'vegetables', 'healthy_fats'],
        ],
        'dinner' => [
            'hypertension' => ['vegetable_soup', 'chicken', 'brown_bread', 'low_salt'],
            'diabetes_1' => ['tuna_salad', 'cottage_cheese', 'vegetables', 'nuts'],
            'diabetes_2' => ['grilled_meat', 'whole_grains', 'leafy_vegetables'],
            'heart_disease' => ['sea_salad', 'olive_oil', 'whole_wheat', 'vegetables'],
            'diabetes_heart' => ['tuna', 'whole_grains', 'olive_oil', 'vegetables'],
            'none' => ['variety', 'balanced_portions', 'seasonal_foods'],
        ],
        'snacks' => [
            'hypertension' => ['banana', 'apple', 'carrot', 'cucumber'],
            'diabetes_1' => ['almonds', 'walnuts', 'cucumber', 'bell_pepper'],
            'diabetes_2' => ['nuts', 'plain_yogurt', 'raw_vegetables'],
            'heart_disease' => ['avocado', 'berries', 'walnuts', 'seeds'],
            'diabetes_heart' => ['almonds', 'walnuts', 'green_apple', 'cucumber'],
            'none' => ['fruits', 'nuts', 'yogurt', 'vegetables'],
        ],
    ],

    // File Storage Settings
    'storage' => [
        'plans_dir' => 'saved_plans/',
        'max_file_size' => 5242880, // 5MB
        'allowed_formats' => ['json', 'pdf'],
        'auto_cleanup_days' => 30,
    ],

    // Error Messages (Arabic)
    'messages' => [
        'success' => 'تم العملية بنجاح!',
        'error' => 'حدث خطأ ما يرجى المحاولة لاحقاً.',
        'validation_error' => 'بيانات غير صحيحة.',
        'file_not_found' => 'الملف غير موجود.',
        'permission_denied' => 'لا توجد صلاحيات كافية.',
    ],

    // Logging
    'logging' => [
        'enabled' => true,
        'level' => 'error', // 'debug', 'info', 'warning', 'error'
        'file' => 'logs/nutrischolar.log',
    ],

    // API Settings
    'api' => [
        'rate_limit' => 100, // requests per minute
        'timeout' => 30, // seconds
        'cache_enabled' => true,
        'cache_ttl' => 3600, // 1 hour
    ],
];
?>
