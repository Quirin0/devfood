<h1 align="center">
  <img src="file:///C:/Users/matheus/.cursor/projects/c-Users-matheus-Desktop-projetos-portfolio-devfood/assets/c__Users_matheus_AppData_Roaming_Cursor_User_workspaceStorage_6ab91f32b0efa8f67119d8395fb96695_images_image-7978741a-c777-40ef-9ed4-c68d5ebf9c55.png" alt="DevFood" />
</h1>

# Link do projeto

[Dev Food](https://devfoods.vercel.app/)

# 🔍 Sumário

- [Sobre](#-sobre)
- [Arquitetura](#-arquitetura)
- [Funcionalidades](#-funcionalidades)
- [Tecnologias utilizadas](#-tecnologias-utilizadas)
- [Estrutura de pastas](#-estrutura-de-pastas)
- [Como rodar o projeto](#-como-rodar-o-projeto)
- [Configuração do Google OAuth](#-configuração-do-google-oauth)
- [Principais endpoints da API](#-principais-endpoints-da-api)
- [Próximos passos](#-próximos-passos)

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
- Redirecionamento para login em rotas protegidas (perfil e pedidos)
- Listagem de categorias e restaurantes
- Página de restaurante com cardápio
- Página de detalhe do produto com observações
- Adição de itens na sacola
- Finalização de pedido quando autenticado
- Listagem de pedidos realizados
- Toasts e interações de UI com animações
- Carrosséis e seções recomendadas na home

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

## 📁 Estrutura de pastas

```bash
devfood/
├── app/                     # Controllers, models, services (Laravel)
├── database/                # Migrations, seeders e SQLite
├── routes/                  # Definição das rotas da API
├── frontend/                # Aplicação Next.js
│   ├── src/app/             # Rotas e páginas
│   ├── src/components/      # Componentes visuais
│   └── src/context/         # Contextos (sacola, toast etc.)
├── public/frontend/         # Build estático copiado do Next
└── scripts/                 # Scripts utilitários do projeto
```

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

## 👨‍🚀 Próximos passos

- Melhorar ainda mais a experiência desktop
- Adicionar mais estados de feedback e confirmação em ações críticas
- Expandir regras de pedido (status e histórico mais detalhado)

---

Desenvolvido por 🐉 **Matheus Quirino**
