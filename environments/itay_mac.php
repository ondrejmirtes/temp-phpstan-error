<?php

function lifeCycle()
{
    return 'itay_development';
}

function app_env()
{
    $base_ip = 'sitelms.loc';
    if ($_SERVER["PHP_SELF"] == '/aeonflux/index.php' || strpos($_SERVER["PHP_SELF"], 'devtools/') !== false) {
        $base = 'localhost';
    } else {
        $base = $base_ip;
    }

    $ret = [
        'u' => null,
        'debug' => true,
        'debug_verbosity' => 2, // [1] no debug trace, [2] debug trace up to 4 lines, no args, [3] debug trace up to 4 lines + args , [4] full debug trace
        'use_https' => true,
        'env_type' => 'development',
        'emulate_emails' => true,

        'minify_js' => false,
        'delta-tracking' => [],

        'log' => [
            'prefix' => 'LMS_',
            'handler' => 'File', // 'FileSessionReq',//'Redis',//ColoredFile',//'ErrorMonitorEmail',//'Stdio',//'Nan'
            'verbosity' => 4,
            'uri' => '/var/log/lms2/',
            'low_memory_footprint' => false
        ],

        'paths' => [
            'flyspray' => 'http://projects.sitel.org/flyspray/',
            'baseUrl' => $base,
            'sub' => [
                'www' => [
                    'docRoot' => '/var/www/lms2',
                    'relativePath' => '',
                    'base_url' => "www.{$base_ip}",
                    'static' => 'https://staticassets.sitelms.org/www' //'https://localhost/lmsstatic/www'
                ],
                'next' => [
                    'base_url' => "{$base_ip}/lms2/next",
                    'static' => 'https://localhost/lmsstatic/next' // 'https://d2q6htnmvqvy4.cloudfront.net/next' //
                ],
                'api' => [
                    'relativePath' => '/api',
                    'base_url' => "{$base_ip}/api"
                ],
                'aeonflux' => [
                    'base_url' => 'localhost/aeonflux',
                    'static' => 'https://localhost/lmsstatic/aeonflux' // 'https://d24w5lxiv0rbus.cloudfront.net/aeonflux'//,'http://lmsstatic.s3-website-us-east-1.amazonaws.com/aeonflux',//
                ],
                'ramasaml' => [
                    'base_url' => "{$base_ip}/ramasaml"
                ],
                'devtools' => [
                    'base_url' => 'localhost/devtools'
                ]
            ],
            'lms-apps' => [
                'educator' => [
                    'quizBuilder' => 'http://localhost:3005/content/quizbuilder'
                ],
                'reports' => [
                    'hrsmartview' => 'http://localhost:3000', //single page app,  
                    'mayhem'      => 'http://localhost:3000/mayhem'
                ]
            ]
        ]
    ];

    $ret['u'] = function ($subdomain) use ($ret) {
        return $ret['paths']['sub'][$subdomain]['base_url'];
    };
    return $ret;
}
