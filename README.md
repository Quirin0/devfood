<h1 align="center">
  <img src="file:///C:/Users/matheus/.cursor/projects/c-Users-matheus-Desktop-projetos-portfolio-devfood/assets/c__Users_matheus_AppData_Roaming_Cursor_User_workspaceStorage_6ab91f32b0efa8f67119d8395fb96695_images_image-7978741a-c777-40ef-9ed4-c68d5ebf9c55.png" alt="DevFood" />
</h1>

# Link do projeto

Configure `APP_URL` no `.env` com o domínio publicado (ex.: `https://seudominio.com`).

# 🔍 Sumário

- [Sobre](#-sobre)
- [Arquitetura](#-arquitetura)
- [Funcionalidades](#-funcionalidades)
- [Tecnologias utilizadas](#-tecnologias-utilizadas)
- [Como rodar o projeto](#-como-rodar-o-projeto)
- [Configuração do Google OAuth](#-configuração-do-google-oauth)
- [Principais endpoints da API](#-principais-endpoints-da-api)

## 📗 Sobre

O **DevFood** é um clone de app de delivery inspirado no iFood, construído para portfólio com foco em:

- experiência **mobile first**
- fluxo real de pedidos
- autenticação com Google
- arquitetura separada entre API e frontend

O projeto utiliza **Laravel** no backend para disponibilizar os dados e regras de negócio, e **Next.js** no frontend para a interface.  
No processo de build, o frontend é exportado e copiado para `public/frontend`, permitindo que o Laravel sirva a aplicação completa.

---

## 🧱 Arquitetura

- **Backend (Laravel 12):** API REST em `/api/v1`, autenticação JWT, Socialite para Google, migrations e seeders.
- **Frontend (Next.js 16 + React 19):** interface da aplicação, páginas de home, restaurante, produto, carrinho, pedidos, login e perfil.
- **Banco de dados (SQLite):** ambiente simples para desenvolvimento local.
- **Integração de build:** o comando de build do frontend copia os arquivos estáticos para o Laravel.

---

## 💻 Funcionalidades

- Layout inspirado em app de delivery (mobile first)
- Login com Google
- Favoritar restaurantes
- Visualizar pedidos

---

## 🚀 Tecnologias utilizadas

### Backend

- PHP 8.2+
- Laravel 12
- Laravel Socialite
- Firebase PHP-JWT
- SQLite

### Frontend

- Next.js 16
- React 19
- TypeScript
- Tailwind CSS 4

### Ferramentas e scripts

- Node.js / npm
- Scripts customizados para copiar assets e publicar build no Laravel
- Script para download e normalização de imagens do catálogo

---

## 🎮 Como rodar o projeto

### 1) Backend (Laravel)

```bash
# na raiz do projeto
composer install
cp .env.example .env
# Ajuste APP_URL=http://localhost:8000 no .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

API disponível em: `{APP_URL}/api/v1` (padrão `http://localhost:8000/api/v1`)

### 2) Frontend (desenvolvimento)

```bash
cd frontend
npm install
npm run dev
```

O `npm run dev` sincroniza `APP_URL` do `.env` da raiz para o frontend automaticamente.

Frontend em: `http://localhost:3000`

### 3) Build para servir tudo pelo Laravel

```bash
cd frontend
npm run build
```

Esse comando:

- gera o build do Next.js
- copia o resultado para `public/frontend`
- copia os assets necessários para `public/_next`

Depois disso, acessando `http://localhost:8000`, o Laravel já entrega o frontend.

### 4) Deploy no cPanel (Laravel)

O Laravel entrega **API + frontend** no mesmo domínio. Tudo depende de `APP_URL` no `.env`.

**No `.env` de produção:**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com
```

**Build do frontend** (local ou no servidor, com Node.js):

```bash
cd frontend
npm install
npm run build
```

O `npm run build` lê `APP_URL` do `.env` da raiz, gera o frontend e copia para `public/frontend`.

**No servidor (SSH ou terminal do cPanel):**

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

**Document root do domínio:** aponte para a pasta `public/` do projeto (não para a raiz do repositório).

**Permissões:** `storage/` e `bootstrap/cache/` graváveis pelo PHP (775 ou 755, conforme o host).

**Google OAuth:** no Google Cloud Console, use a URI com o mesmo domínio de `APP_URL`:

```txt
https://seudominio.com/api/v1/auth/google/callback
```

Em produção o frontend chama `/api/v1` no mesmo domínio — sem CORS entre origens diferentes.

---

## 🔐 Configuração do Google OAuth

No arquivo `.env` da raiz:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_CALLBACK_URL="${APP_URL}/api/v1/auth/google/callback"
```

No Google Cloud Console, configure a URI de redirecionamento autorizada:

```txt
http://localhost:8000/api/v1/auth/google/callback
```

Se necessário, limpe cache de config:

```bash
php artisan config:clear
```

---

## 🔌 Principais endpoints da API

- `GET /api/v1/banners`
- `GET /api/v1/categories`
- `GET /api/v1/restaurants`
- `GET /api/v1/restaurants/{slug}`
- `GET /api/v1/products`
- `POST /api/v1/auth/login`
- `GET /api/v1/auth/google/redirect`
- `GET /api/v1/auth/google/callback`
- `GET /api/v1/orders` (autenticado)
- `POST /api/v1/orders` (autenticado)

---

Desenvolvido por 🐉 **Matheus Quirino**
