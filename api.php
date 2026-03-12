<?php
/**
 * NutriScholar - Nutrition Calculation API
 * Backend PHP file for processing nutrition calculations
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get the action from request
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

try {
    switch ($action) {
        case 'calculateNutrition':
            echo json_encode(calculateNutrition($_POST));
            break;
        case 'generateMeals':
            echo json_encode(generateMeals($_POST));
            break;
        case 'savePlan':
            echo json_encode(saveDietPlan($_POST));
            break;
        case 'validateInputs':
            echo json_encode(validateInputs($_POST));
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * Calculate nutrition requirements based on patient data
 */
function calculateNutrition($data) {
    // Validate inputs
    $validation = validateInputs($data);
    if (!$validation['success']) {
        return $validation;
    }

    $weight = (float)$data['weight'];
    $height = (float)$data['height'];
    $age = (int)$data['age'];
    $gender = $data['gender'];
    $disease = $data['disease'];
    $activity = (float)$data['activity'];

    // 1. Calculate BMI
    $bmi = $weight / (pow($height / 100, 2));
    $bmiStatus = getBMIStatus($bmi);

    // 2. Calculate Ideal Body Weight (IBW) using Devine formula
    $factor = ($gender === 'male') ? 4 : 2;
    $ibw = ($height - 100) - (($height - 150) / $factor);

    // 3. Determine which weight to use
    $usedWeight = $ibw;
    $weightTypeLabel = "الوزن المثالي (IBW)";

    if ($bmi < 18.5 || $bmi >= 25) {
        // Use Adjusted Body Weight (AjBW)
        $usedWeight = $ibw + 0.4 * ($weight - $ibw);
        $weightTypeLabel = "الوزن المعدل (AjBW)";
    }

    // 4. Calculate Basal Metabolic Rate (BMR) using Harris-Benedict equation
    if ($gender === 'male') {
        $bmr = (10 * $usedWeight) + (6.25 * $height) - (5 * $age) + 5;
    } else {
        $bmr = (10 * $usedWeight) + (6.25 * $height) - (5 * $age) - 161;
    }

    // 5. Calculate Total Daily Energy Expenditure (TDEE)
    $tdee = round($bmr * $activity);

    // 6. Calculate macronutrient distribution based on disease
    $macros = getMacronutrients($disease, $tdee);

    return [
        'success' => true,
        'bmi' => round($bmi, 1),
        'bmiStatus' => $bmiStatus,
        'usedWeight' => round($usedWeight, 1),
        'weightTypeLabel' => $weightTypeLabel,
        'bmr' => round($bmr),
        'tdee' => $tdee,
        'macros' => $macros
    ];
}

/**
 * Get macronutrient distribution based on disease
 */
function getMacronutrients($disease, $tdee) {
    $macros = [];

    switch ($disease) {
        case 'hypertension':
            $macros['carbs'] = round($tdee * 0.55 / 4);
            $macros['protein'] = round($tdee * 0.18 / 4);
            $macros['fat'] = round($tdee * 0.27 / 9);
            $macros['sodium'] = '< 2300 ملغ/يوم';
            $macros['fiber'] = '25-30 غرام/يوم';
            break;

        case 'diabetes_1':
        case 'diabetes_2':
            $macros['carbs'] = round($tdee * 0.45 / 4);
            $macros['protein'] = round($tdee * 0.25 / 4);
            $macros['fat'] = round($tdee * 0.30 / 9);
            $macros['fiber'] = '> 30 غرام/يوم';
            $macros['sugar'] = '0 غرام (تجنب المحليات المضافة)';
            break;

        case 'heart_disease':
        case 'diabetes_heart':
            $macros['carbs'] = round($tdee * 0.45 / 4);
            $macros['protein'] = round($tdee * 0.25 / 4);
            $macros['fat'] = round($tdee * 0.30 / 9);
            $macros['saturatedFat'] = '< 7% من الطاقة';
            $macros['cholesterol'] = '< 200 ملغ/يوم';
            $macros['fiber'] = '> 25 غرام/يوم';
            break;

        default: // Healthy person
            $macros['carbs'] = round($tdee * 0.50 / 4);
            $macros['protein'] = round($tdee * 0.20 / 4);
            $macros['fat'] = round($tdee * 0.30 / 9);
            $macros['fiber'] = '25-30 غرام/يوم';
    }

    return $macros;
}

/**
 * Get BMI status based on BMI value
 */
function getBMIStatus($bmi) {
    if ($bmi < 18.5) {
        return 'نحافة';
    } elseif ($bmi < 25) {
        return 'وزن مثالي';
    } elseif ($bmi < 30) {
        return 'زيادة وزن';
    } else {
        return 'سمنة';
    }
}

/**
 * Generate AI-suggested meal plan based on disease and calorie requirements
 */
function generateMeals($data) {
    $disease = $data['disease'];
    $gender = $data['gender'];
    $tdee = (int)$data['tdee'];
    $meals = [];

    // Define meal templates for each disease
    $mealTemplates = [
        'hypertension' => [
            'breakfast' => [
                'زبادي قليل الدسم مع توت',
                'بيض مسلوق مع خبز أسمر',
                'شوفان بالحليب قليل الدسم',
                'جبنة بيضاء مع خضار طازج'
            ],
            'lunch' => [
                'سمك مشوي مع أرز بني وخضار',
                'صدر دجاج مع فاصوليا خضراء',
                'شوربة العدس مع الخبز الأسمر',
                'صفار السمك مع السلطة'
            ],
            'dinner' => [
                'حساء الخضار مع خبز أسمر',
                'سلطة تونة بزيت الزيتون',
                'لحم بقري قليل الدسم مع خضار',
                'دجاج مغلي مع أرز أبيض'
            ],
            'snacks' => ['موز', 'تفاح', 'كيوي', 'بندق']
        ],
        'diabetes_1' => [
            'breakfast' => [
                'بيضتين مسلوقتين مع خبز أسمر',
                'جبنة قريش مع بندق',
                'لبن بدون سكر مع شوفان',
                'بيض مخفوق مع خضار'
            ],
            'lunch' => [
                'صدر دجاج مع عدس وخضار',
                'سمك بالفرن مع فاصوليا',
                'لحم مشوي مع الحمص',
                'دجاج مسلوق مع البقوليات'
            ],
            'dinner' => [
                'جبنة قريش مع خيار وطماطم',
                'شوربة العدس والخضار',
                'لحم مفروم قليل الدسم مع بروكلي',
                'سمك مشوي مع سلطة'
            ],
            'snacks' => ['لوز غير مملح', 'جوز', 'خيار', 'فلفل أحمر']
        ],
        'heart_disease' => [
            'breakfast' => [
                'توست أسمر بزيت الزيتون والزعتر',
                'حبوب كاملة بالحليب قليل الدسم',
                'بيض مسلوق مع تفاح',
                'جبنة بيضاء قليلة الملح'
            ],
            'lunch' => [
                'معكرونة القمح الكامل مع الخضار',
                'سمك السلمون مشوي مع أرز بني',
                'صدر دجاج مع الفاصوليا',
                'سلطة الأفوكادو والجمبري'
            ],
            'dinner' => [
                'سلطة البحر مع زيت الزيتون',
                'دجاج مشوي مع خضار من الفرن',
                'شوربة الطماطم مع الحبوب الكاملة',
                'سمك مبشور مع الخضار'
            ],
            'snacks' => ['أفوكادو', 'توت', 'عين الجمل', 'ميسلي طبيعي']
        ],
        'diabetes_heart' => [
            'breakfast' => [
                'بيض مسلوق مع خبز أسمر وزيت زيتون',
                'جبنة قريش مع لوز',
                'عصيدة الحبوب الكاملة بالحليب منزوع الدسم',
                'يوغورت بدون سكر مع جوز'
            ],
            'lunch' => [
                'سمك السلمون المشوي مع البقوليات',
                'صدر دجاج مع الأرز البني والخضار',
                'حساء العدس مع الخبز الأسمر',
                'لحم قليل الدسم مع الفاصوليا'
            ],
            'dinner' => [
                'شوربة الخضار مع السمك',
                'جبنة قريش مع الخيار والطماطم',
                'دجاج مشوي مع البروكلي',
                'سلطة التونة بزيت الزيتون'
            ],
            'snacks' => ['لوز', 'جوز', 'تفاح', 'خيار']
        ],
        'none' => [
            'breakfast' => [
                'فطائر الحبوب الكاملة مع العسل',
                'بيض مع جبنة وخبز',
                'زبادي مع الفواكه',
                'شوفان أو كورن فليكس'
            ],
            'lunch' => [
                'أرز مطهو مع دجاج وخضار',
                'معكرونة مع لحم وصلصة',
                'شطيرة مع لحم وخضار',
                'سمك مع بطاطا وسلطة'
            ],
            'dinner' => [
                'كوب زبادي مع فاكهة',
                'سلطة دجاج مشوي',
                'شوربة لحم مع خضار',
                'بيتزا صحية مع الجبنة والخضار'
            ],
            'snacks' => ['فاكهة موسمية', 'بسكويت القمح', 'فشار بزيت', 'عصير طازج']
        ]
    ];

    // Get templates for disease or default
    $templates = isset($mealTemplates[$disease]) ? $mealTemplates[$disease] : $mealTemplates['none'];

    // Generate suggested meals
    $meals = [
        'breakfast' => $templates['breakfast'][array_rand($templates['breakfast'])],
        'lunch' => $templates['lunch'][array_rand($templates['lunch'])],
        'dinner' => $templates['dinner'][array_rand($templates['dinner'])],
        'snack1' => $templates['snacks'][array_rand($templates['snacks'])],
        'snack2' => $templates['snacks'][array_rand($templates['snacks'])]
    ];

    return [
        'success' => true,
        'meals' => $meals,
        'totalCalories' => $tdee
    ];
}

/**
 * Validate input data
 */
function validateInputs($data) {
    $errors = [];

    if (empty($data['weight']) || $data['weight'] <= 0) {
        $errors[] = 'الوزن مطلوب وأكبر من صفر';
    }

    if (empty($data['height']) || $data['height'] <= 0) {
        $errors[] = 'الطول مطلوب وأكبر من صفر';
    }

    if (empty($data['age']) || $data['age'] <= 0 || $data['age'] > 150) {
        $errors[] = 'العمر مطلوب وبحد معقول';
    }

    if (!isset($data['gender']) || !in_array($data['gender'], ['male', 'female'])) {
        $errors[] = 'الجنس غير صحيح';
    }

    if (!isset($data['activity']) || $data['activity'] <= 0) {
        $errors[] = 'مستوى النشاط غير صحيح';
    }

    if (count($errors) > 0) {
        return [
            'success' => false,
            'message' => implode('\n', $errors)
        ];
    }

    return ['success' => true];
}

/**
 * Save diet plan to database (optional)
 */
function saveDietPlan($data) {
    // This is a placeholder for database integration
    // You can extend this to save to a database

    $filename = 'plans_' . date('Y-m-d_H-i-s') . '_' . md5(microtime()) . '.json';
    $filepath = __DIR__ . '/saved_plans/' . $filename;

    // Create directory if not exists
    if (!is_dir(__DIR__ . '/saved_plans')) {
        mkdir(__DIR__ . '/saved_plans', 0755, true);
    }

    $planData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'patientName' => $data['name'] ?? 'Unknown',
        'data' => $data
    ];

    if (file_put_contents($filepath, json_encode($planData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        return [
            'success' => true,
            'message' => 'تم حفظ الخطة بنجاح',
            'planId' => basename($filename, '.json')
        ];
    } else {
        return [
            'success' => false,
            'message' => 'فشل حفظ الخطة'
        ];
    }
}
?>
