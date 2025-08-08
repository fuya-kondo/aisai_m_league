<?php
// カラム名の定義
$statsNameConfig = [
    'u_user_id'                 => 'ユーザID',
    'name'                      => '名前',
    'play_count'                => '対局数',
    'rank_count'                => '順位',
    'rank_probability'          => '順位率',
    'average_rank'              => '平均順位',
    'sum_base_score'            => '素点',
    'average_score'             => '平均点',
    'sum_point'                 => '合計ポイント',
    'average_point'             => '平均ポイント',
    'hight_score'               => '最高点',
    'play_count_direction'      => '対局数(各家)',
    'rank_count_direction'      => '順位(各家)',
    'average_rank_direction'    => '平均順位(各家)',
    'rank_probability_direction'=> '順位率(各家)',
    'sum_base_score_direction'  => '素点(各家)',
    'average_score_direction'   => '平均点(各家)',
    'sum_point_direction'       => '合計ポイント(各家)',
    'average_point_direction'   => '平均ポイント(各家)',
    'over_second_probability'   => '連対率',
    'over_third_probability'    => 'ラス回避率',
    'mistake_count'             => 'チョンボ数',
    'ranking'                   => '順位',
];
// 順位の定義
$rankConfig = [
    '1'     => '1位',
    '2'     => '2位',
    '3'     => '3位',
    '4'     => '4位',
    '1=1'   => '1位(同率)',
    '2=2'   => '2位(同率)',
    '3=3'   => '3位(同率)',
];
// 各家の成績用のカラムの定義
$directionConfig = [
    'play_count_direction' => [ 1 => $mDirectionList[1], 2 => $mDirectionList[2], 3 => $mDirectionList[3], 4 => $mDirectionList[4] ],
];
$statsColumnAllConfig_1 = [
    'play_count'                => '対局数',
    'sum_point'                 => '合計ポイント',
    'sum_base_score'            => '素点',
    'average_point'             => '半荘平均ポイント',
    'average_score'             => '半荘平均点',
    'hight_score'               => '最高点',
    'average_rank'              => '平均順位',
    'rank_count'                => [ 1 => '1位', 2 => '2位', 3 => '3位', 4 => '4位' ],
    'rank_probability'          => [ 1 => '1位率', 2 => '2位率', 3 => '3位率', 4 => '4位率' ],
    'over_second_probability'   => '連対率',
    'over_third_probability'    => 'ラス回避率',
    'mistake_count'             => 'チョンボ数',
];
$statsColumnAllConfig_2 = [
    'play_count_direction'          => '対局数',
    'sum_point_direction'           => '合計ポイント',
    'sum_base_score_direction'      => '素点',
    'average_point_direction'       => '半荘平均ポイント',
    'average_score_direction'       => '半荘平均点',
    'average_rank_direction'        => '平均順位',
    'rank_count_direction'          => [ 1 => '1位', 2 => '2位', 3 => '3位', 4 => '4位' ],
    'rank_probability_direction'    => [ 1 => '1位率', 2 => '2位率', 3 => '3位率', 4 => '4位率' ],
];
// 表示成績カラム定義1 (今日の成績、成績一覧で使用)
$displayStatsColumn_1 = [
    'ranking'           => '順位',
    'name'              => '名前',
    'sum_point'         => '合計点',
    'rank_count'        => [ 1 => '1位', 2 => '2位', 3 => '3位', 4 => '4位' ],
];
// 表示成績カラム定義2 (個人の成績一覧で使用)
$displayStatsColumn_2 = [
    'sub_score' => '点差',
    'sub_point' => 'ポイント差',
];