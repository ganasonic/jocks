(function () {
    'use strict';
    const config = window.practiceVideoConfig;
    const container = document.getElementById('details-container');
    const form = container.closest('form');
    const submit = form.querySelector('[type="submit"]');
    let nextIndex = Math.max(-1, ...Array.from(container.querySelectorAll('.practice-video-upload'), el => Number(el.dataset.index))) + 1;
    let active = false;
    let failed = false;
    let pending = [];

    document.getElementById('add-detail-btn').addEventListener('click', () => {
        container.insertAdjacentHTML('beforeend', document.getElementById('detail-template').innerHTML.replace(/__INDEX__/g, String(nextIndex++)));
    });
    form.addEventListener('submit', event => {
        if (active || failed) {
            event.preventDefault();
            alert('動画のアップロードを完了するか、失敗した動画の追加を取り消してください。');
        }
    });
    window.addEventListener('beforeunload', event => {
        if (active) { event.preventDefault(); event.returnValue = ''; }
    });

    async function post(url, body) {
        for (let attempt = 0; attempt <= 3; attempt++) {
            try {
                const response = await fetch(url, {
                    method: 'POST', credentials: 'same-origin', body,
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const error = new Error(data.message || '動画を送信できませんでした（HTTP ' + response.status + '）。');
                    error.permanent = response.status < 500 && response.status !== 429;
                    throw error;
                }
                return data;
            } catch (error) {
                if (error.permanent || attempt === 3) throw error;
                await new Promise(resolve => setTimeout(resolve, 1000 * (attempt + 1)));
            }
        }
    }
    function body(values) {
        const data = new FormData();
        Object.keys(values).forEach(key => data.append(key, values[key]));
        return data;
    }
    function setBusy(busy) {
        active = busy;
        submit.disabled = busy || failed;
        container.querySelectorAll('.video-files, .remove-video').forEach(el => { el.disabled = busy || failed; });
    }
    function appendVideo(widget, item, path) {
        const wrapper = document.createElement('div');
        wrapper.className = 'col-md-6 video-item';
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = 'details[' + widget.dataset.index + '][video_url][]'; input.value = path;
        const label = document.createElement('div'); label.textContent = item.file.name;
        const video = document.createElement('video');
        video.controls = true; video.playsInline = true; video.preload = 'none'; video.className = 'w-100 rounded'; video.style.maxHeight = '360px';
        video.src = config.startUrl + '/' + item.token;
        const link = document.createElement('a'); link.href = video.src; link.target = '_blank'; link.rel = 'noopener'; link.textContent = '動画を開く';
        const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'btn btn-sm btn-outline-danger remove-video d-block mt-1'; remove.textContent = '動画を外す';
        wrapper.append(input, label, video, link, remove);
        widget.querySelector('.video-list').appendChild(wrapper);
    }
    async function upload(widget) {
        failed = false; setBusy(true);
        const status = widget.querySelector('.video-status');
        try {
            while (pending.length) {
                const item = pending[0];
                if (!item.token) {
                    const started = await post(config.startUrl, body({ original_name: item.file.name, total_size: item.file.size, player_id: config.playerId }));
                    item.token = started.token; item.chunkSize = started.chunk_size; item.next = 0;
                }
                const total = Math.ceil(item.file.size / item.chunkSize);
                while (item.next < total) {
                    status.textContent = item.file.name + '：' + Math.floor(item.next / total * 100) + '%（送信中）';
                    const result = await post(config.startUrl + '/' + item.token + '/chunks', body({ index: item.next, chunk: item.file.slice(item.next * item.chunkSize, (item.next + 1) * item.chunkSize) }));
                    item.next = result.next_chunk;
                }
                status.textContent = item.file.name + '：確認中…';
                const result = await post(config.startUrl + '/' + item.token + '/complete', body({}));
                appendVideo(widget, item, result.path);
                pending.shift();
            }
            status.textContent = 'アップロード完了。練習記録を保存してください。';
            widget.querySelector('.video-files').value = '';
        } catch (error) {
            failed = true;
            status.textContent = error.message + ' ';
            const retry = document.createElement('button'); retry.type = 'button'; retry.className = 'btn btn-sm btn-outline-primary'; retry.textContent = '再試行';
            retry.onclick = () => { status.textContent = ''; upload(widget); };
            const cancel = document.createElement('button'); cancel.type = 'button'; cancel.className = 'btn btn-sm btn-outline-secondary ms-2'; cancel.textContent = '未完了の追加を取り消す';
            cancel.onclick = () => { pending = []; failed = false; status.textContent = '未完了の動画追加を取り消しました。'; widget.querySelector('.video-files').value = ''; setBusy(false); };
            status.append(retry, cancel);
        } finally {
            setBusy(false);
        }
    }
    container.addEventListener('change', event => {
        if (!event.target.matches('.video-files') || active || failed) return;
        const widget = event.target.closest('.practice-video-upload');
        const files = Array.from(event.target.files);
        if (!files.length) return;
        let error = '';
        if (files.length + widget.querySelectorAll('.video-item').length > config.maxFiles) error = '動画は各メニュー最大' + config.maxFiles + '本です。';
        if (files.some(file => !/\.(mp4|mov|m4v)$/i.test(file.name) || file.size === 0 || file.size > config.maxSize)) error = '200MB以下のMP4・MOV・M4V動画を選択してください。';
        if (error) { widget.querySelector('.video-status').textContent = error; event.target.value = ''; return; }
        pending = files.map(file => ({ file }));
        upload(widget);
    });
    container.addEventListener('click', event => {
        if (event.target.matches('.remove-video') && !active && !failed) event.target.closest('.video-item').remove();
    });
}());
