<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin DevFood - Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; margin: 0; padding: 24px; }
        .card { max-width: 420px; margin: 40px auto; background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        h1 { margin: 0 0 6px; font-size: 22px; color: #1f1f1f; }
        p { margin: 0 0 16px; color: #666; font-size: 14px; }
        label { display: block; margin: 10px 0 6px; font-size: 13px; font-weight: 700; color: #333; }
        input { width: 100%; box-sizing: border-box; border: 1px solid #ddd; border-radius: 8px; padding: 10px; }
        button { width: 100%; margin-top: 14px; background: #ea1d2c; color: #fff; border: 0; border-radius: 8px; padding: 11px; font-weight: 700; cursor: pointer; }
        .error { margin-top: 10px; color: #d01a27; font-size: 13px; }
        .hint { margin-top: 12px; font-size: 12px; color: #717171; }
    </style>
</head>
<body>
<div class="card">
    <h1>Admin DevFood</h1>
    <p>Acesso para configuração do Google OAuth.</p>

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <label for="email">E-mail</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>

        <label for="password">Senha</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Entrar</button>
    </form>

    @error('email')
    <div class="error">{{ $message }}</div>
    @enderror

    <div class="hint">
        Usuário padrão: <strong>admin@admin.com</strong><br>
        Senha padrão: <strong>admin</strong>
    </div>
</div>
</body>
</html>

