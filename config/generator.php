<?php

return [
    /**
     * If any input file(image) as default will use options below.
     */
    "image" => [
        /**
         * Path for store the image.
         *
         * Available options:
         * 1. public
         * 2. storage
         * 3. S3
         */
        "disk" => "storage",

        /**
         * Will be used if image is nullable and default value is null.
         */
        "default" => "https://via.placeholder.com/350?text=No+Image+Avaiable",

        /**
         * Crop the uploaded image using intervention image.
         */
        "crop" => true,

        /**
         * When set to true the uploaded image aspect ratio will still original.
         */
        "aspect_ratio" => true,

        /**
         * Crop image size.
         */
        "width" => 500,
        "height" => 500,
    ],

    "format" => [
        /**
         * Will be used to first year on select, if any column type year.
         */
        "first_year" => 1970,

        /**
         * If any date column type will cast and display used this format, but for input date still will used Y-m-d format.
         *
         * another most common format:
         * - M d Y
         * - d F Y
         * - Y m d
         */
        "date" => "Y-m-d",

        /**
         * If any input type month will cast and display used this format.
         */
        "month" => "Y/m",

        /**
         * If any input type time will cast and display used this format.
         */
        "time" => "H:i",

        /**
         * If any datetime column type or datetime-local on input, will cast and display used this format.
         */
        "datetime" => "Y-m-d H:i:s",

        /**
         * Limit string on index view for any column type text or long text.
         */
        "limit_text" => 100,
    ],

    /**
     * It will be used for generator to manage and showing menus on sidebar views.
     *
     * Example:
     * [
     *   'header' => 'Main',
     *
     *   // All permissions in menus[] and submenus[]
     *   'permissions' => ['test view'],
     *
     *   menus' => [
     *       [
     *          'title' => 'Main Data',
     *          'icon' => '<i class="bi bi-collection-fill"></i>',
     *          'route' => null,
     *
     *          // permission always null when isset submenus
     *          'permission' => null,
     *
     *          // All permissions on submenus[] and will empty[] when submenus equals to []
     *          'permissions' => ['test view'],
     *
     *          'submenus' => [
     *                 [
     *                     'title' => 'Tests',
     *                     'route' => '/tests',
     *                     'permission' => 'test view'
     *                  ]
     *               ],
     *           ],
     *       ],
     *  ],
     *
     * This code below always changes when you use a generator, and maybe you must format the code.
     */
    "sidebars" => [
    [
        'header' => 'Master',
        'permissions' => [
            'aspek view',
            'indikator persepsi view',
            'bobot aspek view',
            'kriteria responden view',
            'indikator dampak view',
            'konversi view',
            'kuesioner view'
        ],
        'menus' => [
            [
                'title' => 'Master Data',
                'icon' => '<i class="bi bi-collection-fill"></i>',
                'route' => [
                    'aspek*',
                    'indikator-persepsi*',
                    'indikator-dampak*',
                    'bobot-aspek*',
                    'kriteria-responden*',
                    'konversi*',
                    'kuesioner*'
                ],
                'permissions' => [
                    'aspek view',
                    'indikator persepsi view',
                    'bobot aspek view',
                    'kriteria responden view',
                    'indikator dampak view',
                    'konversi view',
                    'kuesioner view'
                ],
                'submenus' => [
                    [
                        'title' => 'Aspek',
                        'route' => '/aspek',
                        'permission' => 'aspek view'
                    ],
                    [
                        'title' => 'Kuesioner',
                        'route' => '/kuesioner',
                        'permission' => 'kuesioner view'
                    ],
                    [
                        'title' => 'Indikator Persepsi',
                        'route' => '/indikator-persepsi',
                        'permission' => 'indikator persepsi view'
                    ],
                    [
                        'title' => 'Indikator Dampak',
                        'route' => '/indikator-dampak',
                        'permission' => 'indikator dampak view'
                    ],
                    [
                        'title' => 'Konversi',
                        'route' => '/konversi',
                        'permission' => 'konversi view'
                    ],
                    [
                        'title' => 'Bobot Aspek',
                        'route' => '/bobot-aspek',
                        'permission' => 'bobot aspek view'
                    ],
                    [
                        'title' => 'Kriteria Responden',
                        'route' => '/kriteria-responden',
                        'permission' => 'kriteria responden view'
                    ]
                ]
            ]
        ]
    ],
    [
        'header' => 'Wa',
        'permissions' => [
            'pesan wa view',
            'wa blast view',
            'single sender view'
        ],
        'menus' => [
            [
                'title' => 'Wa Blasting',
                'icon' => '<i class="bi bi-whatsapp"></i>',
                'route' => [
                    'pesan-wa*',
                    'wa-blast*',
                    'single-sender*'
                ],
                'permissions' => [
                    'pesan wa view',
                    'wa blast view',
                    'single sender view'
                ],
                'submenus' => [
                    [
                        'title' => 'Pesan WA',
                        'route' => '/pesan-wa',
                        'permission' => 'pesan wa view'
                    ],
                    [
                        'title' => 'Config Wa Blast',
                        'route' => '/wa-blast',
                        'permission' => 'wa blast view'
                    ],
                    [
                        'title' => 'Single Sender',
                        'route' => '/single-sender',
                        'permission' => 'single sender view'
                    ]
                ]
            ]
        ]
    ],
    [
        'header' => 'Persiapan',
        'permissions' => [
            'pembuatan project view',
            'project view'
        ],
        'menus' => [
            [
                'title' => 'Persiapan',
                'icon' => '<i class="bi bi-folder"></i>',
                'route' => [
                    'pembuatan-project*',
                    'project*'
                ],
                'permissions' => [
                    'pembuatan project view',
                    'project view'
                ],
                'submenus' => [
                    [
                        'title' => 'Pembuatan Project',
                        'route' => '/pembuatan-project',
                        'permission' => 'pembuatan project view'
                    ],
                    [
                        'title' => 'Management Project',
                        'route' => '/project',
                        'permission' => 'project view'
                    ]
                ]
            ]
        ]
    ],
    [
        'header' => 'Pelaksanaan',
        'permissions' => [
            'data sekunder view',
            'penyebaran kuesioner view',
            'pengumpulan data view'
        ],
        'menus' => [
            [
                'title' => 'Pelaksanaan',
                'icon' => '<i class="bi bi-folder"></i>',
                'route' => [
                    'data-sekunder*',
                    'penyebaran-kuesioner*',
                    'pengumpulan-data*'
                ],
                'permissions' => [
                    'data sekunder view',
                    'penyebaran kuesioner view',
                    'pengumpulan data view'
                ],
                'submenus' => [
                    [
                        'title' => 'Data Sekunder',
                        'route' => '/data-sekunder',
                        'permission' => 'data sekunder view'
                    ],
                    [
                        'title' => 'Penyebaran Kuesioner',
                        'route' => '/penyebaran-kuesioner',
                        'permission' => 'penyebaran kuesioner view'
                    ],
                    [
                        'title' => 'Pengumpulan Data',
                        'route' => '/pengumpulan-data',
                        'permission' => 'pengumpulan data view'
                    ]
                ]
            ]
        ]
    ],
    [
        'header' => 'Utilities',
        'permissions' => [
            'user view',
            'role & permission view',
            'setting view'
        ],
        'menus' => [
            [
                'title' => 'Utilities',
                'icon' => '<i class="bi bi-gear-fill"></i>',
                'route' => [
                    'users*',
                    'roles*',
                    'setting*'
                ],
                'permissions' => [
                    'user view',
                    'role & permission view',
                    'setting view'
                ],
                'submenus' => [
                    [
                        'title' => 'Setting',
                        'route' => '/setting',
                        'permission' => 'setting view'
                    ],
                    [
                        'title' => 'User',
                        'route' => '/users',
                        'permission' => 'user view'
                    ],
                    [
                        'title' => 'Roles & permissions',
                        'route' => '/roles',
                        'permission' => 'role & permission view'
                    ]
                ]
            ]
        ]
    ]
]
];
