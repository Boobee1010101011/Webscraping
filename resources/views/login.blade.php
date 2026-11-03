<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · PPI Monitoring</title>
    <style>
        :root{font-family:Inter,ui-sans-serif,system-ui,sans-serif;color-scheme:light;--ink:#171a18;--muted:#737a76;--line:#e4e6df;--paper:#fbfaf6;--surface:#fffefa;--lime:#4da398;--accent-ink:#102321;--danger:#c0392b}
        html.dark{color-scheme:dark;--ink:#f2f5ee;--muted:#a2aca4;--line:#343c36;--paper:#151916;--surface:#1d231f;--danger:#ff7a6e}
        *{box-sizing:border-box}
        html,body{margin:0;min-width:320px;background:var(--paper);color:var(--ink)}
        body{font-size:14px}
        button,input{font:inherit}
        button{cursor:pointer}
        .topbar{height:66px;display:flex;align-items:center;padding:0 42px;border-bottom:1px solid var(--line)}
        .brand{display:flex;align-items:center;gap:12px;white-space:nowrap}
        .brand-mark{width:24px;height:24px;display:grid;place-items:center;background:var(--lime);color:var(--accent-ink);font-size:17px;font-weight:900;line-height:1}
        .brand-name{font-size:15px;font-weight:850;letter-spacing:-.035em}
        .theme-toggle{display:grid;place-items:center;width:34px;height:34px;margin-left:auto;border:1px solid var(--line);border-radius:50%;background:transparent;color:var(--ink)}
        .theme-icon:before{content:'☾';font-size:16px}
        main{display:grid;place-items:center;min-height:calc(100vh - 66px);padding:32px 20px}
        .card{width:100%;max-width:400px;padding:32px 28px;border:1px solid var(--line);background:var(--surface)}
        h1{margin:0 0 6px;font-size:30px;letter-spacing:-.05em;line-height:1}
        .sub{margin:0 0 26px;color:var(--muted);font-size:13px}
        label{display:block;margin-bottom:14px;color:var(--muted);font-size:12px;font-weight:700}
        input{display:block;width:100%;height:42px;margin-top:6px;padding:0 12px;border:1px solid var(--line);border-radius:4px;outline:none;background:var(--paper);color:var(--ink)}
        input:focus{border-color:var(--lime);box-shadow:0 0 0 3px rgba(77,163,152,.18)}
        .btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;height:42px;border:1px solid transparent;border-radius:4px;background:var(--ink);color:var(--paper);font-weight:700}
        .btn:disabled{opacity:.6;cursor:wait}
        .btn.ghost{background:transparent;border-color:var(--line);color:var(--ink)}
        .btn.ghost:hover{border-color:var(--lime)}
        .error{min-height:18px;margin:0 0 12px;color:var(--danger);font-size:12px;font-weight:650}
        .divider{display:flex;align-items:center;gap:12px;margin:18px 0;color:var(--muted);font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
        .divider:before,.divider:after{flex:1;height:1px;background:var(--line);content:""}
        [hidden]{display:none!important}
        .switch{margin:22px 0 0;color:var(--muted);font-size:13px;text-align:center}
        .link{padding:0;border:0;background:none;color:var(--lime);font-weight:800;text-decoration:underline;text-underline-offset:3px}
        @media(max-width:800px){.topbar{padding:0 20px}}
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand"><span class="brand-mark">↗</span><span class="brand-name">PULSE / INTEL</span></div>
        <button class="theme-toggle" type="button" aria-label="Switch to dark mode"><span class="theme-icon"></span></button>
    </header>

    <main>
        <div class="card">
            <h1 id="auth-title">Sign in</h1>
            <p class="sub" id="auth-sub">Use your dashboard account to continue.</p>

            <form id="login-form" novalidate>
                <label>Email
                    <input type="email" name="email" autocomplete="username" required autofocus>
                </label>
                <label>Password
                    <input type="password" name="password" autocomplete="current-password" required>
                </label>
                <p class="error" id="login-error" role="alert"></p>
                <button class="btn" type="submit" id="login-submit">Sign in</button>
            </form>

            @if ($registration)
                <form id="signup-form" novalidate hidden>
                    <label>Full name
                        <input type="text" name="name" autocomplete="name" required>
                    </label>
                    <label>Email
                        <input type="email" name="email" autocomplete="email" required>
                    </label>
                    <label>Password
                        <input type="password" name="password" autocomplete="new-password" minlength="8" required>
                    </label>
                    <label>Confirm password
                        <input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required>
                    </label>
                    <p class="error" id="signup-error" role="alert"></p>
                    <button class="btn" type="submit" id="signup-submit">Create account</button>
                </form>
            @endif

            @if ($faceClient)
                <div id="face-section">
                    <div class="divider">or</div>
                    <button class="btn ghost" type="button" id="face-login">Log in with Face ID</button>
                </div>
            @endif

            @if ($registration)
                <p class="switch" id="switch-line">
                    <span id="switch-text">No account yet?</span>
                    <button class="link" type="button" id="switch-mode">Sign up</button>
                </p>
            @endif
        </div>
    </main>

    @if ($faceClient)
        @include('partials.face-frame')
    @endif

    <script>
        const themeKey = 'ppi-theme';
        const applyTheme = (theme) => {
            document.documentElement.classList.toggle('dark', theme === 'dark');
            document.querySelector('.theme-toggle').setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
        };
        applyTheme(localStorage.getItem(themeKey) || 'light');
        document.querySelector('.theme-toggle').addEventListener('click', () => {
            const theme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem(themeKey, theme);
            applyTheme(theme);
        });

        const errorBox = document.getElementById('login-error');
        const postJson = async (url, body) => {
            const response = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify(body),
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const first = data.errors ? Object.values(data.errors)[0][0] : null;
                throw new Error(first || data.message || 'Something went wrong. Please try again.');
            }
            return data;
        };
        const goToDashboard = () => { window.location.href = '{{ route('scrape-report') }}'; };

        @if ($registration)
            // Switch between the sign-in form and the sign-up form.
            const signupForm = document.getElementById('signup-form');
            const signupError = document.getElementById('signup-error');
            const signupSubmit = document.getElementById('signup-submit');
            let signingUp = false;

            function setMode(signUp) {
                signingUp = signUp;
                document.getElementById('login-form').hidden = signUp;
                signupForm.hidden = !signUp;
                const faceSection = document.getElementById('face-section');
                if (faceSection) faceSection.hidden = signUp;
                document.getElementById('auth-title').textContent = signUp ? 'Create account' : 'Sign in';
                document.getElementById('auth-sub').textContent = signUp
                    ? 'Sign up with your email. You can add Face ID afterwards.'
                    : 'Use your dashboard account to continue.';
                document.getElementById('switch-text').textContent = signUp ? 'Already have an account?' : 'No account yet?';
                document.getElementById('switch-mode').textContent = signUp ? 'Sign in' : 'Sign up';
                errorBox.textContent = '';
                signupError.textContent = '';
                (signUp ? signupForm.name : document.getElementById('login-form').email).focus();
            }

            document.getElementById('switch-mode').addEventListener('click', () => setMode(!signingUp));

            signupForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                signupError.textContent = '';
                if (signupForm.password.value !== signupForm.password_confirmation.value) {
                    signupError.textContent = 'The passwords do not match.';
                    return;
                }
                signupSubmit.disabled = true;
                try {
                    await postJson('{{ route('auth.register') }}', {
                        name: signupForm.name.value.trim(),
                        email: signupForm.email.value.trim(),
                        password: signupForm.password.value,
                        password_confirmation: signupForm.password_confirmation.value,
                    });
                    goToDashboard();
                } catch (error) {
                    signupError.textContent = error.message;
                    signupSubmit.disabled = false;
                }
            });
        @endif

        const form = document.getElementById('login-form');
        const submit = document.getElementById('login-submit');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            errorBox.textContent = '';
            submit.disabled = true;
            try {
                await postJson('{{ route('auth.login') }}', {
                    email: form.email.value.trim(),
                    password: form.password.value,
                });
                goToDashboard();
            } catch (error) {
                errorBox.textContent = error.message;
                submit.disabled = false;
            }
        });

        @if ($faceClient)
            const faceClient = @json($faceClient);
            const faceButton = document.getElementById('face-login');
            faceButton.addEventListener('click', () => {
                errorBox.textContent = '';
                const url = new URL('/face-login', faceClient);
                url.searchParams.set('redirect', window.location.origin + '/login');
                url.searchParams.set('embed', '1');
                url.searchParams.set('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
                openFaceFrame({
                    src: url.toString(),
                    title: 'Log in with Face ID',
                    async onMessage(message) {
                        if (!message || message.type !== 'face-login' || !message.code) return;
                        closeFaceFrame();
                        faceButton.disabled = true;
                        try {
                            await postJson('{{ route('auth.face') }}', { code: message.code });
                            goToDashboard();
                        } catch (error) {
                            errorBox.textContent = error.message;
                            faceButton.disabled = false;
                        }
                    },
                });
            });
        @endif
    </script>
</body>
</html>
