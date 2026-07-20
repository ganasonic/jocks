<?php
/**
 * home config
 *
 * @author       yutao
 * @since        PHP 7.0.1
 * @version      1.0.0
 * @date         2018-1-15 13:09:05
 * @copyright    Copyright(C) bravesoft Inc.
 */
return [
    //menu config
    'homemenu1' => [
        'menu01' => [
            'name'      => 'マイシフト',
            'sub_name'  => 'シフト登録・確認',
            'url'       => '/myshift',
            'icon'      => 'fas fa-calendar-alt',
            'class'     => 'border-orange-100 bg-orange-50 text-orange-500',
        ],
        'menu02' => [
            'name'      => 'シフト詳細',
            'sub_name'  => 'シフト詳細確認',
            'url'       => '/detail',
            'icon'      => 'fas fa-calendar-check',
            'class'     => 'border-orange-100 bg-orange-50 text-orange-500',
        ],
        'menu03' => [
            'name'      => '食事確認',
            'sub_name'  => '従食寮食確認',
            'url'       => '/meal',
            'icon'      => 'fas fa-utensils',
            'class'     => 'border-yellow-100 bg-yellow-50 text-yellow-600',
        ],
        'menu09' => [
            'name'      => '宿泊確認',
            'sub_name'  => '寮宿泊確認',
            'url'       => '/dormitorystay',
            'icon'      => 'fas fa-bed',
            'class'     => 'border-blue-100 bg-blue-50 text-blue-500',
        ],
        'menu11' => [
            'name'      => '日時管理',
            'sub_name'  => 'コンディショニング',
            'url'       => '/conditions',
            'icon'      => 'fas fa-heartbeat',
            'class'     => 'border-teal-100 bg-teal-50 text-teal-500',
        ],
        'menu12' => [
            'name'      => '目標設定',
            'sub_name'  => '自身の競技計画',
            'url'       => '/goals',
            'icon'      => 'fas fa-chart-line',
            'class'     => 'border-indigo-100 bg-indigo-50 text-indigo-600',
        ],
        'menu13' => [
            'name'      => 'トレーニング',
            'sub_name'  => 'トレーニング計画',
            'url'       => '/trainings',
            'icon'      => 'fas fa-dumbbell',
            'class'     => 'border-green-100 bg-green-50 text-green-600',
        ],
        'menu14' => [
            'name'      => '練習管理',
            'sub_name'  => 'スケジュール・日誌',
            'url'       => '/practices',
            'icon'      => 'fas fa-clipboard-list',
            'class'     => 'border-emerald-100 bg-emerald-50 text-emerald-600',
        ],
        'menu15' => [
            'name'      => '栄養管理',
            'sub_name'  => '栄養素・カロリー追跡',
            'url'       => '/nutritions',
            'icon'      => 'fas fa-apple-alt',
            'class'     => 'border-amber-100 bg-amber-50 text-amber-600',
        ],
        'menu99' => [
            'name'      => 'ログアウト',
            'sub_name'  => 'Logout',
            'url'       => '/logout',
            'icon'      => 'fas fa-sign-out-alt',
            'class'     => 'border-gray-200 bg-gray-100 text-gray-500',
        ],
    ]
];
