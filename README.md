<h1 align="center">
  <img src="file:///C:/Users/matheus/.cursor/projects/c-Users-matheus-Desktop-projetos-portfolio-devfood/assets/c__Users_matheus_AppData_Roaming_Cursor_User_workspaceStorage_6ab91f32b0efa8f67119d8395fb96695_images_image-7978741a-c777-40ef-9ed4-c68d5ebf9c55.png" alt="DevFood" />
</h1>

# Link do projeto

[Dev Food](https://devfood.vercel.app/)

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
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

API disponível em: `http://localhost:8000/api/v1`

### 2) Frontend (desenvolvimento)

```bash
cd frontend
npm install
cp .env.local.example .env.local
npm run dev
```

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

### 4) Deploy na Vercel (somente frontend estático)

O repositório inclui `vercel.json` na raiz. A Vercel deve usar:

- **Build Command:** `npm run build:vercel --prefix frontend`
- **Output Directory:** `frontend/out`

Não use `dist` nem o build do Vite da raiz (`npm run build` na raiz gera apenas assets do Laravel em `public/build`).

No painel da Vercel, em **Environment Variables**, defina:

```env
NEXT_PUBLIC_API_URL=https://sua-api.com/api/v1
```

A API Laravel precisa estar hospedada em outro serviço (Render, Railway, VPS etc.). A Vercel publica apenas o frontend exportado do Next.js.

### 5) Deploy em servidor PHP (produção)

Use hospedagem com PHP (VPS, Hostinger, Render, Railway etc.).

No servidor:

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env   # ajuste APP_URL para o domínio real
php artisan key:generate
php artisan migrate --force
php artisan config:cache

cd frontend && npm install && npm run build
```

No `.env` do Laravel:

```env
APP_URL=http://devfood.servermq.uk
```

**Importante:** o `APP_URL` do Laravel **não** altera o frontend já buildado.  
O frontend, quando servido pelo mesmo domínio, chama a API em `/api/v1` automaticamente (sem CORS).

Se frontend e API estiverem em domínios diferentes, defina no build:

```env
NEXT_PUBLIC_API_URL=https://seu-dominio.com/api/v1
CORS_ALLOWED_ORIGINS=https://seu-frontend.com
```

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
