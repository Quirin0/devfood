<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin DevFood - Google OAuth</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; margin: 0; padding: 24px; }
        .card { max-width: 680px; margin: 30px auto; background: #fff; border-radius: 12px; padding: 22px; box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        h1 { margin: 0 0 6px; font-size: 24px; color: #1f1f1f; }
        p { margin: 0 0 16px; color: #666; font-size: 14px; }
        label { display: block; margin: 10px 0 6px; font-size: 13px; font-weight: 700; color: #333; }
        input { width: 100%; box-sizing: border-box; border: 1px solid #ddd; border-radius: 8px; padding: 10px; }
        button { background: #ea1d2c; color: #fff; border: 0; border-radius: 8px; padding: 11px 16px; font-weight: 700; cursor: pointer; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 10px; }
        .success { margin-bottom: 12px; padding: 10px; border-radius: 8px; background: #e8f5ee; color: #2f7a4e; font-size: 14px; }
        .error { margin-top: 8px; color: #d01a27; font-size: 13px; }
        .actions { display: flex; justify-content: flex-end; margin-top: 16px; }
        .logout { background: #fff; color: #ea1d2c; border: 1px solid #ea1d2c; }
    </style>
</head>
<body>
<div class="card">
    <div class="topbar">
        <div>
            <h1>Configurar Google OAuth</h1>
            <p>Esta área altera as variáveis no arquivo <code>.env</code>.</p>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="logout">Sair</button>
        </form>
    </div>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.google.update') }}">
        @csrf
        <label for="client_id">GOOGLE_CLIENT_ID</label>
        <input id="client_id" name="client_id" value="{{ old('client_id', $clientId) }}" required>
        @error('client_id') <div class="error">{{ $message }}</div> @enderror

        <label for="client_secret">GOOGLE_CLIENT_SECRET</label>
        <input id="client_secret" name="client_secret" value="{{ old('client_secret', $clientSecret) }}" required>
        @error('client_secret') <div class="error">{{ $message }}</div> @enderror

        <label for="callback_url">GOOGLE_CALLBACK_URL</label>
        <input id="callback_url" name="callback_url" type="url" value="{{ old('callback_url', $callbackUrl) }}" required>
        @error('callback_url') <div class="error">{{ $message }}</div> @enderror

        <div class="actions">
            <button type="submit">Salvar configuração</button>
        </div>
    </form>
</div>
</body>
</html>

