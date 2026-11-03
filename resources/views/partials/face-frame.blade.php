{{-- Popup that shows a Face ID page on top of this page, so the browser's address bar never leaves the dashboard.
     The page inside reports back with postMessage; only messages from its own origin are passed on. --}}
<style>
    .ff-overlay{position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.5)}
    .ff-card{width:100%;max-width:440px;padding:14px;border:1px solid var(--line);border-radius:8px;background:var(--surface,var(--paper));color:var(--ink);box-shadow:0 20px 60px rgba(0,0,0,.25)}
    .ff-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
    .ff-head h2{margin:0;font-size:15px;letter-spacing:-.01em}
    .ff-close{display:grid;place-items:center;width:30px;height:30px;border:0;border-radius:50%;background:transparent;color:var(--muted);font-size:18px;line-height:1}
    .ff-close:hover{color:var(--ink)}
    .ff-frame{display:block;width:100%;height:600px;max-height:75vh;border:0;border-radius:6px}
</style>
<script>
    (function () {
        let current = null;

        window.closeFaceFrame = function () {
            if (!current) return;
            window.removeEventListener('message', current.onMessage);
            document.removeEventListener('keydown', current.onKey);
            current.overlay.remove();
            current = null;
        };

        window.openFaceFrame = function ({ src, title, onMessage }) {
            window.closeFaceFrame();

            const faceOrigin = new URL(src).origin;
            const overlay = document.createElement('div');
            overlay.className = 'ff-overlay';

            const card = document.createElement('div');
            card.className = 'ff-card';
            card.setAttribute('role', 'dialog');
            card.setAttribute('aria-modal', 'true');
            card.setAttribute('aria-label', title);

            const head = document.createElement('div');
            head.className = 'ff-head';
            const heading = document.createElement('h2');
            heading.textContent = title;
            const close = document.createElement('button');
            close.type = 'button';
            close.className = 'ff-close';
            close.setAttribute('aria-label', 'Close');
            close.textContent = '×';
            close.addEventListener('click', window.closeFaceFrame);
            head.append(heading, close);

            const frame = document.createElement('iframe');
            frame.className = 'ff-frame';
            frame.title = title;
            frame.allow = 'camera';
            frame.src = src;

            card.append(head, frame);
            overlay.append(card);
            document.body.append(overlay);

            const handleMessage = (event) => {
                if (event.origin !== faceOrigin || event.source !== frame.contentWindow) return;
                onMessage(event.data);
            };
            const handleKey = (event) => {
                if (event.key === 'Escape') window.closeFaceFrame();
            };
            window.addEventListener('message', handleMessage);
            document.addEventListener('keydown', handleKey);
            current = { overlay, onMessage: handleMessage, onKey: handleKey };
        };
    })();
</script>
