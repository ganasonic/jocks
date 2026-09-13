// Run with: node tests/JavaScript/practice-videos.test.js
const assert = require('assert');
const fs = require('fs');
const vm = require('vm');

function setup(handler) {
    const events = {};
    const submit = { disabled: false };
    const status = { textContent: '', children: [], append(...nodes) { this.children.push(...nodes); } };
    const list = { children: [], appendChild(node) { this.children.push(node); } };
    const fileInput = { disabled: false, value: '', matches: selector => selector === '.video-files', closest: () => widget };
    const widget = {
        dataset: { index: '0' },
        querySelector: selector => ({ '.video-status': status, '.video-list': list, '.video-files': fileInput }[selector]),
        querySelectorAll: () => list.children
    };
    const form = { querySelector: () => submit, addEventListener: (name, fn) => { events['form:' + name] = fn; } };
    const container = {
        closest: () => form,
        querySelectorAll: selector => selector === '.practice-video-upload' ? [widget] : [fileInput],
        addEventListener: (name, fn) => { events[name] = fn; },
        insertAdjacentHTML: (position, html) => { events.added = html; }
    };
    const add = { addEventListener: (name, fn) => { events.add = fn; } };
    const document = {
        getElementById: id => ({ 'details-container': container, 'add-detail-btn': add, 'detail-template': { innerHTML: 'details[__INDEX__][menu_name]' } }[id]),
        querySelector: () => ({ content: 'csrf' }),
        createElement: tag => ({ tag, style: {}, children: [], append(...nodes) { this.children.push(...nodes); } })
    };
    const calls = [];
    const context = {
        document, console, setTimeout: fn => { fn(); }, alert: () => {},
        FormData: class { constructor() { this.values = {}; } append(k, v) { this.values[k] = v; } },
        window: {
            practiceVideoConfig: { startUrl: '/practice-videos', playerId: 7, maxSize: 200 * 1024 * 1024, maxFiles: 10 },
            addEventListener: () => {}
        },
        fetch: async (url, options) => {
            const call = { url, data: options.body.values };
            calls.push(call);
            return handler(call, calls);
        }
    };
    vm.runInNewContext(fs.readFileSync('public/js/practice-videos.js', 'utf8'), context);
    return { events, widget, fileInput, list, status, calls, submit };
}
const ok = data => ({ ok: true, json: async () => data });
const file = (name, size) => ({ name, size, slice: (start, end) => ({ size: Math.min(end, size) - start }) });
const tick = () => new Promise(resolve => setImmediate(resolve));
async function settle(state) {
    for (let i = 0; i < 100 && state.submit.disabled && !state.status.children.length; i++) await tick();
}

(async () => {
    let token = 0;
    let retry = true;
    const state = setup(call => {
        if (call.url === '/practice-videos') return ok({ token: String(++token), chunk_size: 4194304 });
        if (call.url.endsWith('/chunks')) {
            if (retry) { retry = false; throw new Error('network interrupted'); }
            return ok({ next_chunk: Number(call.data.index) + 1 });
        }
        return ok({ path: 'practice-videos/7/' + token + '.mp4' });
    });
    state.events.add(); assert.strictEqual(state.events.added, 'details[1][menu_name]');
    state.events.add(); assert.strictEqual(state.events.added, 'details[2][menu_name]');
    state.fileInput.files = [file('first.mp4', 4194336), file('second.mov', 32)];
    state.events.change({ target: state.fileInput });
    assert.strictEqual(state.submit.disabled, true);
    let prevented = false;
    state.events['form:submit']({ preventDefault() { prevented = true; } });
    assert.strictEqual(prevented, true);
    await settle(state);
    assert.strictEqual(state.submit.disabled, false, state.status.textContent);
    assert.strictEqual(state.list.children.length, 2);
    const chunks = state.calls.filter(call => call.url.endsWith('/chunks'));
    assert.deepStrictEqual(chunks.map(call => call.data.chunk.size), [4194304, 4194304, 32, 32]);
    assert.deepStrictEqual(chunks.map(call => call.data.index), [0, 0, 1, 0]);
    assert.strictEqual(state.list.children[0].children[0].name, 'details[0][video_url][]');

    const failed = setup(call => call.url === '/practice-videos'
        ? ok({ token: 'bad', chunk_size: 4194304 })
        : { ok: false, status: 422, json: async () => ({ message: 'invalid file' }) });
    failed.fileInput.files = [file('bad.mp4', 32)];
    failed.events.change({ target: failed.fileInput });
    await settle(failed);
    assert.strictEqual(failed.submit.disabled, true);
    assert.strictEqual(failed.status.children.length, 2);
    failed.status.children[1].onclick();
    assert.strictEqual(failed.submit.disabled, false);
    assert.strictEqual(failed.list.children.length, 0);

    const invalid = setup(() => { throw new Error('Must not upload invalid selection'); });
    invalid.fileInput.files = [file('invalid.exe', 32)];
    invalid.events.change({ target: invalid.fileInput });
    assert.strictEqual(invalid.calls.length, 0);
    console.log('Passed: dynamic menus, 4MB splitting, retry, multiple files, save blocking, cancellation, invalid selection.');
})().catch(error => { console.error(error); process.exitCode = 1; });
