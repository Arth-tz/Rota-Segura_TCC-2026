# 🚐 Rota Segura

Sistema web de apoio ao transporte escolar particular, desenvolvido como TCC do curso técnico.

## 📋 Sobre o Projeto

O Rota Segura conecta responsáveis e motoristas de vans escolares, permitindo o acompanhamento em tempo real das rotas, gerenciamento de alunos e controle de embarques/desembarques.

> ⚠️ Este sistema **não** realiza transações financeiras. Valores exibidos são meramente informativos.

## 👥 Perfis de Usuário

- **Admin** — gerencia usuários, motoristas e alunos
- **Motorista** — executa rotas e registra embarques
- **Responsável** — acompanha o transporte do(s) aluno(s)

## 🛠️ Stack Tecnológico

- **Backend:** PHP 8.3 + Laravel 12
- **Frontend:** Vue.js 3 + Inertia.js
- **CSS:** Tailwind CSS v4
- **Banco de dados:** MySQL
- **Build tool:** Vite
- **Servidor local:** Laragon

## 📦 Pré-requisitos

- PHP 8.3+
- Composer
- Node.js
- MySQL
- Laragon (ou outro servidor local)

## 🚀 Instalação

```bash
# Clonar o repositório
git clone https://github.com/seu-usuario/rota-segura.git
cd rota-segura

# Instalar dependências PHP
composer install

# Instalar dependências JS
npm install --legacy-peer-deps

# Copiar e configurar o .env
cp .env.example .env
php artisan key:generate
```

Configure o banco de dados no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rota_segura
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# Criar tabelas e popular banco com dados de teste
php artisan migrate:fresh --seed
```

## ▶️ Rodando o projeto

```bash
# Terminal 1 — servidor Laravel (via Laragon, já roda automaticamente)

# Terminal 2 — compilar frontend
npm run dev
```

Acesse: `http://localhost/rota-segura/public`

**Credenciais de teste:**
| Perfil | Email | Senha |
|--------|-------|-------|
| Admin | admin@rotasegura.com | password |

## 🗄️ Estrutura do Banco de Dados

| Tabela | Descrição |
|--------|-----------|
| `users` | Todos os usuários do sistema |
| `responsaveis` | Dados específicos dos responsáveis |
| `motoristas` | CNH, documentos e status de aprovação |
| `alunos` | Dados dos alunos e associações |
| `vans` | Veículos e documentação |

## 📁 Estrutura do Projeto

```
rota-segura/
├── app/
│   ├── Enums/
│   │   └── UserRole.php
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/
│   │       ├── Motorista/
│   │       └── Responsavel/
│   └── Models/
│       ├── User.php
│       ├── Responsavel.php
│       ├── Motorista.php
│       ├── Aluno.php
│       └── Van.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
└── resources/
    └── js/
        └── Pages/
            ├── Admin/
            ├── Motorista/
            └── Responsavel/
```

##  Comandos Úteis

```bash
# Recriar banco do zero
php artisan migrate:fresh --seed

# Limpar cache
php artisan config:clear
php artisan cache:clear

# Criar migration
php artisan make:migration create_tabela_table

# Criar model
php artisan make:model NomeModel

# Criar controller
php artisan make:controller NomeController
```

##  Licença

Projeto acadêmico — TCC Curso Técnico.