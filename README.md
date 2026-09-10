<div align="center">

<img src="public/images/Logo_rota_segura-azul.png" alt="Rota Segura" width="200" />

# Rota Segura

**Plataforma de gestão de transporte escolar para vans escolares particulares**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=flat-square&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-2-9553E9?style=flat-square)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Licença MIT](https://img.shields.io/badge/licença-MIT-green?style=flat-square)](LICENSE)

[Funcionalidades](#funcionalidades) · [Stack](#stack-técnica) · [Instalação](#instalação) · [Variáveis de ambiente](#variáveis-de-ambiente) · [Deploy](#deploy) · [Contexto acadêmico](#contexto-acadêmico)

</div>

---

## Sobre o projeto

O **Rota Segura** é uma aplicação web que conecta motoristas de vans escolares particulares aos responsáveis pelos alunos transportados. A plataforma centraliza o controle de documentação do motorista e do veículo, o gerenciamento de rotas e passageiros, o rastreamento GPS em tempo real e a comunicação entre as partes — tudo em um único sistema acessível por desktop e celular.

O projeto nasceu da necessidade real de transparência e segurança no transporte escolar privado, onde a maioria das operações ainda é feita via WhatsApp e planilhas avulsas.

---

## Funcionalidades

### Motorista
- Cadastro completo com envio de documentos pessoais (CNH, certidão de antecedentes, curso SEST SENAT, extrato RENACH)
- Cadastro e documentação da van (CRLV, seguro, fotos, autorização municipal, prefixo)
- Gerenciamento de disponibilidades (turnos, dias da semana, preço, capacidade, regiões atendidas)
- Aceite e recusa de solicitações de vagas de responsáveis
- Dashboard de passageiros com ordem de embarque arrastável
- Botão "Convidar cliente" que copia link de cadastro personalizado
- Início e encerramento de trajetos com geração automática de paradas
- Confirmação de embarque e desembarque de passageiros em tempo real
- Rastreamento GPS via `navigator.geolocation` com envio contínuo ao servidor
- Alertas de falha de GPS e conectividade offline

### Responsável
- Cadastro e gerenciamento de passageiros (filhos/dependentes)
- Cadastro de endereços de embarque e desembarque com geocodificação via Nominatim
- Marketplace para buscar e solicitar vagas em vans disponíveis na região
- Acompanhamento do trajeto em tempo real: mapa Leaflet com posição da van atualizado a cada 5 segundos
- Status individual de cada passageiro: aguardando, a bordo, chegou, falta registrada
- Registro de faltas com motivo
- Link direto para WhatsApp do motorista
- Indicador de sinal GPS desatualizado (> 30 segundos sem atualização)

### Administrador
- Painel de aprovação de motoristas e vans
- Visualização de todos os documentos enviados (CNH, certidão, CRLV, seguro, etc.)
- Aprovação ou rejeição com notificação por e-mail ao motorista
- Listagem de todos os motoristas com status de aprovação e completude de documentação

### Sistema
- Encerramento automático de rotas esquecidas após 3 horas sem atividade (`rotas:encerrar-esquecidas`)
- Limpeza automática de dados de localização com mais de 30 dias (MySQL Event Scheduler)
- Envio de e-mails com template visual personalizado (verificação, aprovação, rejeição)
- Sessões persistentes em banco de dados (compatível com containers)

---

## Stack técnica

| Camada | Tecnologia |
|---|---|
| Backend | PHP 8.2, Laravel 12 |
| Frontend | Vue 3, Inertia.js v2, Tailwind CSS 3 |
| Build | Vite 7, Laravel Vite Plugin |
| Banco de dados | MySQL 8 |
| Mapas | Leaflet 1.9.4 + OpenStreetMap / Nominatim |
| Ícones | Heroicons 2 (Vue) |
| Rotas no JS | Ziggy (tightenco/ziggy) |
| Validação BR | laravellegends/pt-br-validator |
| Autenticação | Laravel Breeze (base) com roles customizadas |
| Armazenamento | Local (dev) / S3-compatible (produção) |

---

## Arquitetura

```
rota-segura/
├── app/
│   ├── Console/Commands/       # EncerrarRotasEsquecidas (scheduler)
│   ├── Http/Controllers/
│   │   ├── Admin/              # Aprovação de motoristas
│   │   ├── Auth/               # Register (motorista / responsável), login, verify
│   │   ├── Motorista/          # Dashboard, Documentos, Van, Disponibilidade, Rota, Vínculo
│   │   └── Responsavel/        # Dashboard, Passageiro, Acompanhar, Marketplace, Presença
│   ├── Models/                 # Motorista, Responsavel, Van, Passageiro, Rota, Localizacao…
│   └── Http/Middleware/        # RoleMiddleware (motorista / responsavel / admin)
├── database/migrations/        # 40+ migrations com schema completo
├── resources/
│   └── js/
│       ├── Pages/
│       │   ├── Auth/           # Login, Register, ConfirmPassword…
│       │   ├── Motorista/      # Dashboard.vue, Documentos.vue
│       │   ├── Responsavel/    # Dashboard.vue, Marketplace.vue
│       │   └── Admin/          # Dashboard.vue
│       └── Components/
│           ├── Motorista/Dashboard/   # SecaoTrajetos, SecaoPassageiros, SecaoPerfil…
│           └── Responsavel/Dashboard/ # SecaoAcompanhar, SecaoPassageiros…
└── routes/
    ├── web.php                 # Rotas por role (middleware auth + role:*)
    └── console.php             # Scheduler: rotas:encerrar-esquecidas a cada 30min
```

### Modelo de dados (resumo)

```
pessoa ←── usuario ──→ motorista ──→ van ──→ disponibilidade
                  └──→ responsavel ──→ passageiro ──→ passageiro_endereco → endereco
                  └──→ admin

disponibilidade ←── vinculo ──→ passageiro
vinculo ←── solicitacao

rota ──→ parada ──→ parada_passageiro
rota ──→ localizacao
```

Roles são determinadas pela tabela à qual o `usuario` está vinculado (`motorista`, `responsavel` ou `admin`), sem coluna `role` — o middleware faz a verificação via relacionamento.

---

## Pré-requisitos

- PHP 8.2+
- Composer 2+
- Node.js 20+ e npm 10+
- MySQL 8.0+
- Extensões PHP: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`

---

## Instalação

```bash
# 1. Clone o repositório
git clone https://github.com/Arth-tz/rota-segura.git
cd rota-segura

# 2. Instale as dependências PHP e JS
composer install
npm install

# 3. Configure o ambiente
cp .env.example .env
php artisan key:generate

# 4. Crie o banco de dados MySQL e configure o .env (veja seção abaixo)

# 5. Execute as migrations
php artisan migrate

# 6. Compile os assets
npm run build

# 7. Inicie o servidor de desenvolvimento
composer run dev
```

O comando `composer run dev` sobe em paralelo: servidor PHP, queue worker, log viewer (Pail) e Vite HMR.

---

## Variáveis de ambiente

Copie `.env.example` para `.env` e ajuste os valores abaixo:

### Aplicação

| Variável | Exemplo | Descrição |
|---|---|---|
| `APP_NAME` | `Rota Segura` | Nome exibido nos e-mails e abas |
| `APP_ENV` | `production` | `local` no desenvolvimento |
| `APP_DEBUG` | `false` | Nunca `true` em produção |
| `APP_URL` | `https://seu-dominio.com` | URL pública da aplicação |
| `APP_KEY` | *(gerada pelo artisan)* | Chave de criptografia |

### Banco de dados

| Variável | Exemplo |
|---|---|
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | `rota_segura` |
| `DB_USERNAME` | `root` |
| `DB_PASSWORD` | `sua_senha` |

### Sessão e filas

| Variável | Valor recomendado em produção |
|---|---|
| `SESSION_DRIVER` | `database` |
| `QUEUE_CONNECTION` | `database` |
| `CACHE_STORE` | `database` |

> **Atenção:** Em ambientes containerizados (Railway, Heroku, Fly.io), o `SESSION_DRIVER` **deve** ser `database`. Drivers baseados em arquivo são perdidos entre reinicializações do container.

### Armazenamento de arquivos

Para produção, use armazenamento S3-compatible (ex: Supabase Storage, AWS S3, Cloudflare R2):

| Variável | Descrição |
|---|---|
| `FILESYSTEM_DISK` | `s3` |
| `AWS_ACCESS_KEY_ID` | Chave de acesso |
| `AWS_SECRET_ACCESS_KEY` | Chave secreta |
| `AWS_DEFAULT_REGION` | Região do bucket |
| `AWS_BUCKET` | Nome do bucket |
| `AWS_ENDPOINT` | Endpoint customizado (ex: Supabase) |
| `AWS_USE_PATH_STYLE_ENDPOINT` | `true` para Supabase |

> **Por quê S3?** Railway e plataformas similares usam sistema de arquivos efêmero — uploads são perdidos a cada redeploy sem armazenamento externo.

### E-mail

| Variável | Desenvolvimento | Produção |
|---|---|---|
| `MAIL_MAILER` | `log` | `smtp` ou `ses` |
| `MAIL_HOST` | — | Host SMTP |
| `MAIL_PORT` | — | `587` ou `465` |
| `MAIL_USERNAME` | — | Usuário SMTP |
| `MAIL_PASSWORD` | — | Senha SMTP |
| `MAIL_FROM_ADDRESS` | — | `noreply@seu-dominio.com` |

### Geocodificação

A geocodificação de endereços usa [Nominatim (OpenStreetMap)](https://nominatim.org) — gratuito, sem chave de API:

| Variável | Padrão |
|---|---|
| `NOMINATIM_BASE_URL` | `https://nominatim.openstreetmap.org` |
| `NOMINATIM_TIMEOUT` | `5` |
| `NOMINATIM_USER_AGENT` | `Rota Segura/1.0` |
| `NOMINATIM_EMAIL` | *(seu e-mail — exigido pela política de uso)* |

---

## Agendamento de tarefas

O sistema possui um comando artisan para encerramento automático de rotas esquecidas:

```bash
# Executa em dry-run (não altera o banco)
php artisan rotas:encerrar-esquecidas --dry-run

# Executa de verdade (padrão: 3 horas sem atividade)
php artisan rotas:encerrar-esquecidas

# Customizar janela de tempo
php artisan rotas:encerrar-esquecidas --horas=5
```

O scheduler do Laravel já está configurado em `routes/console.php` para executar o comando a cada 30 minutos. Em produção, adicione **uma única entrada** ao crontab do servidor:

```cron
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```

No Railway, configure um serviço de Cron com o comando: `php artisan schedule:run`

---

## Deploy (Railway)

1. **MySQL Plugin** — adicione o plugin MySQL ao projeto no Railway e configure as variáveis `DB_*`
2. **Variáveis de ambiente** — configure todas as variáveis listadas acima no painel do Railway
3. **Armazenamento** — configure Supabase Storage (ou outro S3-compatible) e defina `FILESYSTEM_DISK=s3`
4. **Session table** — execute antes do primeiro deploy:
   ```bash
   php artisan session:table
   php artisan migrate
   ```
5. **Cron Job** — crie um serviço separado de Cron no Railway com: `php artisan schedule:run`
6. **Build command:** `composer install --no-dev && npm ci && npm run build && php artisan migrate --force`
7. **Start command:** `php artisan serve --host=0.0.0.0 --port=$PORT`

---

## Contas padrão (desenvolvimento)

Após rodar as migrations, crie manualmente os usuários de teste pelo formulário de cadastro da aplicação, ou via `php artisan tinker`. Não há seeders de usuários para evitar credenciais hardcoded no repositório.

O primeiro usuário com e-mail `admin@rotasegura.com` deve ser criado com role admin diretamente no banco:

```sql
INSERT INTO admin (id_usuario) VALUES (<id_do_usuario>);
```

---

## Segurança e conformidade

- **LGPD (Lei 13.709/2018):** fotos de menores requerem consentimento explícito do responsável no momento do cadastro
- **CTB / CONTRAN:** documentação exigida de motoristas e vans segue a regulamentação federal de transporte escolar (Art. 136 do CTB, Resolução CONTRAN 912/2022)
- **Documentos sensíveis:** CNH e certidão de antecedentes do motorista nunca são exibidos para responsáveis — apenas para o administrador durante a revisão
- **Autorização por recurso:** todas as rotas de GPS, encerramento de trajeto e confirmação de passageiro verificam que o recurso pertence à van do motorista autenticado
- **Upload seguro:** arquivos são validados por MIME type e tamanho antes do armazenamento

---

## Contexto acadêmico

Este projeto foi desenvolvido como **Trabalho de Conclusão de Curso (TCC)** do Curso Técnico em Informática do [Instituto Federal de Educação, Ciência e Tecnologia do Rio Grande do Sul — Campus Canoas (IFRS Canoas)](https://canoas.ifrs.edu.br).

**Aluno:** Arthur Trentin  
**Instituição:** IFRS Campus Canoas  
**Curso:** Técnico em Informática  
**Ano:** 2026

---

## Licença

Distribuído sob a licença MIT. Veja [`LICENSE`](LICENSE) para mais informações.
