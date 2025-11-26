<?php

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

if (!function_exists('loadEnv')) {
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'helpers.php';
}

loadEnv(dirname(__DIR__));

return [
    'timezone' => env('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'),
    'metadata' => [
        'contract' => env('APP_CONTRACT', '788922w'),
        'author' => [
            'name' => env('APP_AUTHOR_NAME', 'Nguyễn Minh Hoàng'),
            'email' => env('APP_AUTHOR_EMAIL', 'hoangnguyenminh.nina@gmail.com'),
            'timefinish' => env('APP_AUTHOR_FINISH', '27/05/2022'),
        ],
    ],
    'arrayDomainSSL' => [],
    'database' => [
        'server-name' => env('APP_SERVER_NAME', $_SERVER['SERVER_NAME'] ?? 'localhost'),
        'url' => env('APP_BASE_URL', '/'),
        'type' => env('DB_CONNECTION', 'mysql'),
        'host' => env('DB_HOST', 'localhost'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'dbname' => env('DB_DATABASE', 'cuuho'),
        'port' => (int)env('DB_PORT', 3306),
        'prefix' => env('DB_PREFIX', 'table_'),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
    ],
    'website' => [
        'error-reporting' => env('APP_DEBUG', false),
        'secret' => env('APP_SECRET', '$nina@'),
        'salt' => env('APP_SALT', 'swKJjeS!t'),
        'debug-developer' => (bool)env('APP_DEBUG_DEVELOPER', true),
        'debug-css' => (bool)env('APP_DEBUG_CSS', true),
        'debug-js' => (bool)env('APP_DEBUG_JS', true),
        'scss-auto-compile' => (bool)env('APP_SCSS_AUTO_COMPILE', false),
        'index' => (bool)env('APP_INDEX', false),
        'image' => [
            'hasWebp' => (bool)env('APP_IMAGE_WEBP', false),
        ],
        'video' => [
            'extension' => ['mp4', 'mkv'],
            'poster' => [
                'width' => 700,
                'height' => 610,
                'extension' => '.jpg|.png|.jpeg',
            ],
            'allow-size' => '100Mb',
            'max-size' => 100 * 1024 * 1024,
        ],
        'upload' => [
            'max-width' => 1600,
            'max-height' => 1600,
        ],
        'lang' => [
            'vi' => 'Tiếng Việt',
        ],
        'lang-doc' => 'vi',
        'slug' => [
            'vi' => 'Tiếng Việt',
        ],
        'seo' => [
            'vi' => 'Tiếng Việt',
        ],
        'comlang' => [
            "gioi-thieu" => ["vi" => "gioi-thieu", "en" => "about-us"],
            "san-pham" => ["vi" => "san-pham", "en" => "product"],
            "tin-tuc" => ["vi" => "tin-tuc", "en" => "news"],
            "dich-vu" => ["vi" => "dich-vu", "en" => "news"],
            "dao-tao" => ["vi" => "dao-tao", "en" => "news"],
            "tuyen-dung" => ["vi" => "tuyen-dung", "en" => "recruitment"],
            "thu-vien-anh" => ["vi" => "thu-vien-anh", "en" => "gallery"],
            "video" => ["vi" => "video", "en" => "video"],
            "lien-he" => ["vi" => "lien-he", "en" => "contact"],
        ],
    ],
    'order' => [
        'ship' => false,
    ],
    'cart' => [
        'active' => true,
    ],
    'coppy' => [
        'lock' => false,
    ],
    'careers' => [
        'casi' => "Ca sĩ",
        'youtuber' => "Youtuber",
        'kysu' => "Kỹ Sư",
        'cogiao' => "Cô Giáo",
        'laptrinhvien' => "Lập Trình Viên",
        'mauanh' => "Mẫu Ảnh",
        'tiktoker' => "Tiktoker",
    ],
    'login' => [
        'admin' => 'LoginAdmin' . env('APP_CONTRACT', '788922w'),
        'member' => 'LoginMember' . env('APP_CONTRACT', '788922w'),
        'attempt' => 5,
        'delay' => 15,
    ],
    'googleAPI' => [
        'recaptcha' => [
            'active' => true,
            'urlapi' => 'https://www.google.com/recaptcha/api/siteverify',
            'sitekey' => env('RECAPTCHA_SITE_KEY', '6Ld_l2clAAAAAG9amJdEX-ghOQ3PdOyAXBPk88ib'),
            'secretkey' => env('RECAPTCHA_SECRET_KEY', '6Ld_l2clAAAAAH_xLNyfa2UuuiprJquQSmBD8prg'),
        ],
    ],
    'oneSignal' => [
        'active' => false,
        'id' => '',
        'restId' => '',
    ],
    'license' => [
        'version' => "7.1.0",
    ],
];

