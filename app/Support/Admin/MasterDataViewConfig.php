<?php
namespace App\Support\Admin;

/**
 * マスターデータ管理画面の表示設定を集約する support class。
 * view はこの設定を受け取って描画だけを行い、列定義やフォーム定義は PHP 側の 1 か所で管理する。
 */
final class MasterDataViewConfig
{
    public static function buildSections(array $pageData): array
    {
        $textCell = static fn(mixed $value): array => ['type' => 'text', 'value' => (string) $value];
        $colorCell = static fn(mixed $value): array => ['type' => 'color', 'value' => (string) $value];
        $buildRows = static function (array $records, string $idKey, array $cellBuilders): array {
            $rows = [];
            foreach ($records as $record) {
                $cells = [];
                foreach ($cellBuilders as $cellBuilder) {
                    $cells[] = $cellBuilder($record);
                }

                $rows[] = [
                    'id' => $record[$idKey],
                    'recordJson' => json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_APOS | JSON_HEX_QUOT),
                    'cells' => $cells,
                ];
            }

            return $rows;
        };

        return [
            [
                'type' => 'badge',
                'title' => 'バッジ管理',
                'columns' => ['ID', '名前', '画像', '枠', '背景'],
                'rows' => $buildRows($pageData['badges'], 'm_badge_id', [
                    static fn(array $record): array => $textCell($record['m_badge_id']),
                    static fn(array $record): array => $textCell($record['name']),
                    static fn(array $record): array => $textCell($record['image']),
                    static fn(array $record): array => $textCell($record['flame']),
                    static fn(array $record): array => $textCell($record['background']),
                ]),
            ],
            [
                'type' => 'tier',
                'title' => 'ティア管理',
                'columns' => ['ID', '名前', '色'],
                'rows' => $buildRows($pageData['tiers'], 'm_tier_id', [
                    static fn(array $record): array => $textCell($record['m_tier_id']),
                    static fn(array $record): array => $textCell($record['name']),
                    static fn(array $record): array => $colorCell($record['color']),
                ]),
            ],
            [
                'type' => 'game_day',
                'title' => 'ゲーム日管理',
                'columns' => ['ID', '日付', '時間'],
                'rows' => $buildRows($pageData['gameDays'], 'game_day', [
                    static fn(array $record): array => $textCell($record['game_day']),
                    static fn(array $record): array => $textCell($record['game_day']),
                    static fn(array $record): array => $textCell('-'),
                ]),
            ],
            [
                'type' => 'tier_history',
                'title' => 'ティア履歴管理',
                'columns' => ['ID', 'ユーザーID', 'ティアID', '年'],
                'rows' => $buildRows($pageData['tierHistory'], 'u_user_tier_history_id', [
                    static fn(array $record): array => $textCell($record['u_user_tier_history_id']),
                    static fn(array $record): array => $textCell($record['u_user_id']),
                    static fn(array $record): array => $textCell($record['m_tier_id']),
                    static fn(array $record): array => $textCell($record['year']),
                ]),
            ],
            [
                'type' => 'direction',
                'title' => '方向管理',
                'columns' => ['ID', '名前'],
                'rows' => $buildRows($pageData['directions'], 'm_direction_id', [
                    static fn(array $record): array => $textCell($record['m_direction_id']),
                    static fn(array $record): array => $textCell($record['name']),
                ]),
            ],
            [
                'type' => 'group',
                'title' => 'グループ管理',
                'columns' => ['ID', '名前', 'ルールID'],
                'rows' => $buildRows($pageData['groups'], 'm_group_id', [
                    static fn(array $record): array => $textCell($record['m_group_id']),
                    static fn(array $record): array => $textCell($record['name']),
                    static fn(array $record): array => $textCell($record['m_rule_id']),
                ]),
            ],
            [
                'type' => 'rule',
                'title' => 'ルール管理',
                'columns' => ['ID', '名前', '開始点数', '終了点数', '1位ウマオカ', '2位ウマオカ', '3位ウマオカ', '4位ウマオカ'],
                'rows' => $buildRows($pageData['rules'], 'm_rule_id', [
                    static fn(array $record): array => $textCell($record['m_rule_id']),
                    static fn(array $record): array => $textCell($record['name']),
                    static fn(array $record): array => $textCell($record['start_score']),
                    static fn(array $record): array => $textCell($record['end_score']),
                    static fn(array $record): array => $textCell($record['point_1']),
                    static fn(array $record): array => $textCell($record['point_2']),
                    static fn(array $record): array => $textCell($record['point_3']),
                    static fn(array $record): array => $textCell($record['point_4']),
                ]),
            ],
            [
                'type' => 'setting',
                'title' => '設定管理',
                'columns' => ['ID', '名前', '値'],
                'rows' => $buildRows($pageData['settings'], 'm_setting_id', [
                    static fn(array $record): array => $textCell($record['m_setting_id']),
                    static fn(array $record): array => $textCell($record['name']),
                    static fn(array $record): array => $textCell($record['value']),
                ]),
            ],
        ];
    }

    public static function buildForms(array $pageData = []): array
    {
        $tierHistoryYearRange = self::resolveTierHistoryYearRange($pageData['tierHistory'] ?? []);

        return [
            ['formId' => 'badgeForm', 'fields' => [
                ['id' => 'badgeName', 'name' => 'name', 'label' => '名前', 'type' => 'text', 'required' => true],
                ['id' => 'badgeImage', 'name' => 'image', 'label' => '画像', 'type' => 'text'],
                ['id' => 'badgeFlame', 'name' => 'flame', 'label' => '枠', 'type' => 'text'],
                ['id' => 'badgeBackground', 'name' => 'background', 'label' => '背景', 'type' => 'text'],
            ]],
            ['formId' => 'tierForm', 'fields' => [
                ['id' => 'tierName', 'name' => 'name', 'label' => '名前', 'type' => 'text', 'required' => true],
                ['id' => 'tierColor', 'name' => 'color', 'label' => '色', 'type' => 'color', 'required' => true],
            ]],
            ['formId' => 'gameDayForm', 'fields' => [
                ['id' => 'gameDayDate', 'name' => 'game_day', 'label' => '日付', 'type' => 'date', 'required' => true],
            ]],
            ['formId' => 'tierHistoryForm', 'fields' => [
                ['id' => 'tierHistoryUserId', 'name' => 'u_user_id', 'label' => 'ユーザーID', 'type' => 'number', 'required' => true],
                ['id' => 'tierHistoryTierId', 'name' => 'm_tier_id', 'label' => 'ティアID', 'type' => 'number', 'required' => true],
                ['id' => 'tierHistoryYear', 'name' => 'year', 'label' => '年', 'type' => 'number', 'min' => $tierHistoryYearRange['min'], 'max' => $tierHistoryYearRange['max'], 'required' => true],
            ]],
            ['formId' => 'directionForm', 'fields' => [
                ['id' => 'directionName', 'name' => 'name', 'label' => '名前', 'type' => 'text', 'required' => true],
            ]],
            ['formId' => 'groupForm', 'fields' => [
                ['id' => 'groupName', 'name' => 'name', 'label' => '名前', 'type' => 'text', 'required' => true],
                ['id' => 'groupRuleId', 'name' => 'm_rule_id', 'label' => 'ルールID', 'type' => 'number', 'min' => 0],
            ]],
            ['formId' => 'ruleForm', 'fields' => [
                ['id' => 'ruleName', 'name' => 'name', 'label' => '名前', 'type' => 'text', 'required' => true],
                ['id' => 'ruleStartScore', 'name' => 'start_score', 'label' => '開始点数', 'type' => 'number', 'min' => 0],
                ['id' => 'ruleEndScore', 'name' => 'end_score', 'label' => '終了点数', 'type' => 'number', 'min' => 0],
                ['id' => 'rulePoint1', 'name' => 'point_1', 'label' => '1位ウマオカ', 'type' => 'number'],
                ['id' => 'rulePoint2', 'name' => 'point_2', 'label' => '2位ウマオカ', 'type' => 'number'],
                ['id' => 'rulePoint3', 'name' => 'point_3', 'label' => '3位ウマオカ', 'type' => 'number'],
                ['id' => 'rulePoint4', 'name' => 'point_4', 'label' => '4位ウマオカ', 'type' => 'number'],
            ]],
            ['formId' => 'settingForm', 'fields' => [
                ['id' => 'settingName', 'name' => 'name', 'label' => '名前', 'type' => 'text', 'required' => true],
                ['id' => 'settingValue', 'name' => 'value', 'label' => '値', 'type' => 'number', 'required' => true],
            ]],
        ];
    }

    private static function resolveTierHistoryYearRange(array $tierHistoryRecords): array
    {
        $years = [];
        foreach ($tierHistoryRecords as $record) {
            $year = isset($record['year']) ? (int)$record['year'] : 0;
            if ($year > 0) {
                $years[] = $year;
            }
        }

        if (empty($years)) {
            $currentYear = (int)date('Y');
            return ['min' => $currentYear, 'max' => $currentYear];
        }

        return ['min' => min($years), 'max' => max($years)];
    }
}
