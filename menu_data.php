<?php
// Menu categories definition
$menu_categories = [
    'breakfast' => [
        'name' => 'فطور',
        'item_class' => 'breakfast-item'
    ],
    'main-courses' => [
        'name' => 'أطباق رئيسية',
        'item_class' => 'main-item'
    ],
    'drinks' => [
        'name' => 'مشروبات',
        'item_class' => 'drink-item'
    ]
];

// Menu items definition
$menu_items = [
    [
        'id' => '1',
        'name' => 'فطور دايت صحي',
        'price' => 85,
        'image' => 'imgs/فطور-دايت-300x300.jpg',
        'description' => 'تشكيلة من الجبن اللايت، البيض المسلوق، والخضروات الطازجة.',
        'category' => 'breakfast',
        'delay' => '0'
    ],
    [
        'id' => '2',
        'name' => 'فطور ملكي متكامل',
        'price' => 110,
        'image' => 'imgs/delicious-breakfast-on-a-light-table-royalty-free-image-863444442-1543345985.jpg',
        'description' => 'فطور شرقي أصيل مع الفول والتمية والجبن بالعسل.',
        'category' => 'breakfast',
        'delay' => '100'
    ],
    [
        'id' => '3',
        'name' => 'وجبة الغداء التقليدية',
        'price' => 220,
        'image' => 'imgs/Traditional.Sunday.Roast-01.jpg',
        'description' => 'لحم روستو مع الخضار السوتيه والأرز البسمتي المبهر.',
        'category' => 'main-courses',
        'delay' => '200'
    ],
    [
        'id' => '4',
        'name' => 'برجر لحم دبل',
        'price' => 160,
        'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=800',
        'description' => 'قطعتين من اللحم الصافي مع صوص الجبن الخاص والبيكون.',
        'category' => 'main-courses',
        'delay' => '300'
    ],
    [
        'id' => '7',
        'name' => 'كباب وكفتة مشوية',
        'price' => 280,
        'image' => 'imgs/istockphoto-492301737-612x612.jpg',
        'description' => 'نصف كيلو من الكباب والكفتة المتبلة بخلطة الذوق الملكي السرية.',
        'category' => 'main-courses',
        'delay' => '350'
    ],
    [
        'id' => '8',
        'name' => 'بيتزا سوبر سوبريم',
        'price' => 195,
        'image' => 'imgs/shutterstock_53001679-scaled.jpg',
        'description' => 'عجينة هشة غنية بالخضروات، الزيتون، واللحوم المشكلة مع الموتزاريلا.',
        'category' => 'main-courses',
        'delay' => '400'
    ],
    [
        'id' => '9',
        'name' => 'دجاج مشوي على الفحم',
        'price' => 240,
        'image' => 'imgs/image22s.jpeg',
        'description' => 'دجاجة كاملة مشوية على الفحم تقدم مع الثومية والخبز الساخن.',
        'category' => 'main-courses',
        'delay' => '450'
    ],
    [
        'id' => '5',
        'name' => 'هوت شوكلت بسكوف',
        'price' => 65,
        'image' => 'imgs/biscoff-hot-chocolate-05b-550x825.jpg',
        'description' => 'مشروب الشوكولاتة الساخنة الغني مع زبدة البسكوف والكريمة.',
        'category' => 'drinks',
        'delay' => '400'
    ],
    [
        'id' => '6',
        'name' => 'مشروبات غازية متنوعة',
        'price' => 25,
        'image' => 'imgs/مشروبات-غازية.jpg',
        'description' => 'اختر مشروبك المفضل من التشكيلة المتوفرة لدينا.',
        'category' => 'drinks',
        'delay' => '500'
    ]
];
?>
