@php($account = auth()->user())
@if ($account)
    @include('partials.face-frame')
    <style>
        .acct{position:relative;margin-left:14px}
        .acct-avatar{display:grid;place-items:center;width:34px;height:34px;padding:0;border:0;border-radius:50%;background:var(--lime);color:var(--accent-ink,#102321);font-size:12px;font-weight:800}
        .acct-pop{position:absolute;top:44px;right:0;z-index:50;width:260px;padding:6px;border:1px solid var(--line);border-radius:6px;background:var(--surface,var(--paper));color:var(--ink);box-shadow:0 12px 32px rgba(0,0,0,.15)}
        .acct-who{display:flex;flex-direction:column;gap:2px;padding:10px 10px 12px;border-bottom:1px solid var(--line);margin-bottom:6px}
        .acct-who strong{font-size:13px}
        .acct-who span{color:var(--muted);font-size:12px;overflow:hidden;text-overflow:ellipsis}
        .acct-note{display:block;padding:0 10px 8px;color:var(--muted);font-size:11px}
        .acct-item{display:block;width:100%;padding:9px 10px;border:0;border-radius:4px;background:transparent;color:var(--ink);font-size:13px;font-weight:650;text-align:left}
        .acct-item:hover{background:rgba(77,163,152,.12)}
        .acct-item:disabled{opacity:.6;cursor:wait}
    </style>
    <div class="acct" id="acct">
        <button class="acct-avatar" type="button" aria-haspopup="menu" aria-expanded="false" aria-label="Account menu">{{ $account->initials() }}</button>
        <div class="acct-pop" role="menu" hidden>
            <div class="acct-who"><strong>{{ $account->name }}</strong><span>{{ $account->email }}</span></div>
            <span class="acct-note" data-note hidden></span>
            <button class="acct-item" type="button" role="menuitem" data-face hidden></button>
            <button class="acct-item" type="button" role="menuitem" data-out>Sign out</button>
        </div>
    </div>
    <script>
        (function () {
            const root = document.getElementById('acct');
            const pop = root.querySelector('.acct-pop');
            const avatar = root.querySelector('.acct-avatar');
            const faceItem = root.querySelector('[data-face]');
            const note = root.querySelector('[data-note]');
            const api = (url, options = {}) => fetch(url, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                ...options,
            });
            const setOpen = (open) => {
                pop.hidden = !open;
                avatar.setAttribute('aria-expanded', String(open));
            };

            avatar.addEventListener('click', () => setOpen(pop.hidden));
            document.addEventListener('click', (event) => {
                if (!root.contains(event.target)) setOpen(false);
            });

            root.querySelector('[data-out]').addEventListener('click', async () => {
                await api('/api/auth/logout', { method: 'POST' }).catch(() => {});
                window.location.href = '{{ route('login') }}';
            });

            // Face ID is optional: the menu entry only appears when the Face ID server is configured and reachable.
            async function refreshFaceStatus() {
                try {
                    const response = await api('/api/face/status');
                    const data = await response.json();
                    if (!response.ok || !data.enabled) {
                        faceItem.hidden = true;
                        note.hidden = true;
                        return;
                    }
                    faceItem.textContent = data.has_face ? 'Replace Face ID' : 'Set up Face ID';
                    faceItem.hidden = false;
                    note.textContent = data.has_face ? 'Face ID is on' : 'Face ID is not set up';
                    note.hidden = false;
                } catch (error) {
                    faceItem.hidden = true;
                    note.hidden = true;
                }
            }

            faceItem.addEventListener('click', async () => {
                setOpen(false);
                faceItem.disabled = true;
                try {
                    const response = await api('/api/face/setup', { method: 'POST' });
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'Face ID is not available right now.');

                    const url = new URL(data.url);
                    url.searchParams.set('embed', '1');
                    url.searchParams.set('parent', window.location.origin);
                    url.searchParams.set('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
                    openFaceFrame({
                        src: url.toString(),
                        title: 'Face ID',
                        onMessage(message) {
                            if (message && message.type === 'face-enrolled') {
                                closeFaceFrame();
                                refreshFaceStatus();
                            }
                        },
                    });
                } catch (error) {
                    alert(error.message);
                } finally {
                    faceItem.disabled = false;
                }
            });

            refreshFaceStatus();
        })();
    </script>
@endif
