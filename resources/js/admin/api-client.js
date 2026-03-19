/**
 * 管理画面用の API client。
 * JSON 応答の取得とメッセージ抽出、FormData POST を統一する。
 */
(function () {
    async function postForm(action, fields = {}, endpoint = '') {
        const formData = new FormData();
        formData.append('action', action);
        Object.entries(fields).forEach(([name, value]) => {
            formData.append(name, value);
        });

        const response = await fetch(endpoint, { method: 'POST', body: formData });
        return response.json();
    }

    function getMessage(payload, fallbackMessage) {
        return payload.message || (payload.data && payload.data.message) || payload.error || fallbackMessage;
    }

    window.AdminApiClient = {
        getMessage,
        postForm,
    };
})();
