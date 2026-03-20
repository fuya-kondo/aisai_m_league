/**
 * マスターデータ種別ごとのフォーム制御設定。
 * 表示名、対象フォーム、初期化、編集値反映、送信 payload 化を 1 か所へ集約する。
 */
(function () {
    function setFormValues(formId, values) {
        const form = document.getElementById(formId);
        if (!form) {
            return;
        }

        Object.entries(values).forEach(([name, value]) => {
            const field = form.querySelector(`[name="${name}"]`);
            if (field) {
                field.value = value ?? '';
            }
        });
    }

    function readFieldValue(form, name, fallback = '') {
        const field = form.querySelector(`[name="${name}"]`);
        return field ? field.value : fallback;
    }

    function readNumberValue(form, name, fallback = 0) {
        const field = form.querySelector(`[name="${name}"]`);
        return field ? (parseInt(field.value, 10) || fallback) : fallback;
    }

    const config = {
        badge: {
            displayName: 'バッジ',
            formId: 'badgeForm',
            reset() { setFormValues('badgeForm', { name: '', image: '', flame: '', background: '' }); },
            hydrate(record) { setFormValues('badgeForm', { name: record.name, image: record.image, flame: record.flame, background: record.background }); },
            serialize(form) { return { name: readFieldValue(form, 'name'), image: readFieldValue(form, 'image'), flame: readFieldValue(form, 'flame'), background: readFieldValue(form, 'background') }; },
            validate() { return true; },
        },
        tier: {
            displayName: 'ティア',
            formId: 'tierForm',
            reset() { setFormValues('tierForm', { name: '', color: '#000000' }); },
            hydrate(record) { setFormValues('tierForm', { name: record.name, color: record.color }); },
            serialize(form) { return { name: readFieldValue(form, 'name'), color: readFieldValue(form, 'color') }; },
            validate() { return true; },
        },
        game_day: {
            displayName: 'ゲーム日',
            formId: 'gameDayForm',
            reset() { setFormValues('gameDayForm', { game_day: '' }); },
            hydrate(record) { setFormValues('gameDayForm', { game_day: record.game_day }); },
            serialize(form) { return { game_day: readFieldValue(form, 'game_day') }; },
            validate() { return true; },
        },
        tier_history: {
            displayName: 'ティア履歴',
            formId: 'tierHistoryForm',
            reset() { setFormValues('tierHistoryForm', { u_user_id: '', m_tier_id: '', year: '' }); },
            hydrate(record) { setFormValues('tierHistoryForm', { u_user_id: record.u_user_id, m_tier_id: record.m_tier_id, year: record.year }); },
            serialize(form) { return { u_user_id: readNumberValue(form, 'u_user_id'), m_tier_id: readNumberValue(form, 'm_tier_id'), year: readFieldValue(form, 'year') }; },
            validate() { return true; },
        },
        direction: {
            displayName: '方向',
            formId: 'directionForm',
            reset() { setFormValues('directionForm', { name: '' }); },
            hydrate(record) { setFormValues('directionForm', { name: record.name }); },
            serialize(form) { return { name: readFieldValue(form, 'name') }; },
            validate() { return true; },
        },
        group: {
            displayName: 'グループ',
            formId: 'groupForm',
            reset() { setFormValues('groupForm', { name: '', m_rule_id: '' }); },
            hydrate(record) { setFormValues('groupForm', { name: record.name, m_rule_id: record.m_rule_id }); },
            serialize(form) { return { name: readFieldValue(form, 'name'), m_rule_id: readNumberValue(form, 'm_rule_id') }; },
            validate() { return true; },
        },
        rule: {
            displayName: 'ルール',
            formId: 'ruleForm',
            reset() { setFormValues('ruleForm', { name: '', start_score: '', end_score: '', point_1: '', point_2: '', point_3: '', point_4: '' }); },
            hydrate(record) { setFormValues('ruleForm', { name: record.name, start_score: record.start_score, end_score: record.end_score, point_1: record.point_1, point_2: record.point_2, point_3: record.point_3, point_4: record.point_4 }); },
            serialize(form) { return { name: readFieldValue(form, 'name'), start_score: readNumberValue(form, 'start_score'), end_score: readNumberValue(form, 'end_score'), point_1: readNumberValue(form, 'point_1'), point_2: readNumberValue(form, 'point_2'), point_3: readNumberValue(form, 'point_3'), point_4: readNumberValue(form, 'point_4') }; },
            validate() { return true; },
        },
        setting: {
            displayName: '設定',
            formId: 'settingForm',
            reset() { setFormValues('settingForm', { name: '', value: '' }); },
            hydrate(record) { setFormValues('settingForm', { name: record.name, value: record.value }); },
            serialize(form) { return { name: readFieldValue(form, 'name'), value: readNumberValue(form, 'value') }; },
            validate() { return true; },
        },
    };

    window.MasterDataConfig = {
        get(type) {
            return config[type] || null;
        },
        getAll() {
            return config;
        },
    };
})();
