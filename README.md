# 🚗 Locadora de Veículos API

**Desenvolvedor:** Victor Rocha  
**Stack:** Laravel 12 | PHP 8.2 | Elasticsearch 8 | JWT (Tymon) | Python (Relatórios)
**Status:** Finalizado ✅

---

## 📖 Sobre o Projeto

Uma API REST robusta, escalável e moderna para gestão de locação de veículos, construída com foco em alta performance, segurança e manutenibilidade.

Principais destaques:

- ✨ Arquitetura SOLID, Clean Code e Clean Architecture
- ⚡ Busca ultrarrápida de veículos com Elasticsearch
- 🔐 Autenticação segura via JWT (Bearer Token)
- 📊 Geração de relatórios financeiros via serviço Python dedicado

Pensado para ambientes de produção que exigem qualidade, resiliência e escalabilidade.

---

## 🏛️ Arquitetura do Projeto

```
Laravel API (PHP 8.2)
├── Autenticação (JWT)
├── CRUD de Veículos, Clientes e Aluguéis
├── Serviço de Busca via Elasticsearch
├── Integração com Serviço Python de Relatórios
└── Processamento Assíncrono (Indexação em Filas)
```

---

## 🚀 Como Rodar Localmente

### 1. Pré-requisitos
- Docker Desktop
- Python 3.13

---

### 2. Instalação do Projeto Laravel

```bash
# Clonar o repositório
git clone https://github.com/seu-usuario/locadora-api.git
cd locadora-api

# Copiar o arquivo de ambiente
cp .env.example .env

# Subir containers Docker
docker compose up -d

# Acessar o container PHP
docker exec -it locadora_app bash

# Instalar dependências e configurar o ambiente
composer install
php artisan migrate
php artisan key:generate
php artisan storage:link
php artisan jwt:secret
php artisan queue:work
```

---

### 3. Como Rodar o Serviço Python de Relatórios

```bash
# Entrar na pasta do serviço (separada ou dentro do projeto)
cd reports-service

# Criar ambiente virtual
python -m venv venv

# Ativar o ambiente virtual
# Linux/macOS:
source venv/bin/activate
# Windows:
cd venv\Scripts\activate

# Instalar dependências
pip install -r requirements.txt

# Rodar o servidor
uvicorn main:app --host 0.0.0.0 --port 8001 --reload
```

**Observação:**  
O serviço estará disponível em:  
`http://localhost:8001/reports/revenue?start=YYYY-MM-DD&end=YYYY-MM-DD`

---

## 📙 Documentação da API

### 🛡️ Autenticação

| Método | Endpoint  | Descrição | Middleware |
|:------:|:---------:|:---------|:----------:|
| POST | `/register` | Registrar novo usuário | - |
| POST | `/login` | Realizar login e obter JWT Token | - |
| POST | `/logout` | Realizar logout | auth:api |

---

### 🚗 Veículos

| Método | Endpoint  | Descrição | Middleware |
|:------:|:---------:|:---------|:----------:|
| GET | `/vehicles` | Listar todos os veículos | auth:api |
| POST | `/vehicles` | Cadastrar novo veículo | auth:api |
| GET | `/vehicles/{vehicle}` | Visualizar detalhes de um veículo | auth:api |
| PUT | `/vehicles/{vehicle}` | Atualizar informações de um veículo | auth:api |
| DELETE | `/vehicles/{vehicle}` | Deletar um veículo | auth:api |
| GET | `/vehicles/search?query=TERMO` | Buscar veículos via Elasticsearch | auth:api |

---

### 👤 Clientes

| Método | Endpoint  | Descrição | Middleware |
|:------:|:---------:|:---------|:----------:|
| GET | `/customers` | Listar todos os clientes | auth:api |
| POST | `/customers` | Cadastrar novo cliente | auth:api |
| GET | `/customers/{customer}` | Visualizar detalhes de um cliente | auth:api |
| PUT | `/customers/{customer}` | Atualizar informações de um cliente | auth:api |
| DELETE | `/customers/{customer}` | Deletar um cliente | auth:api |

---

### 📄 Aluguéis

| Método | Endpoint  | Descrição | Middleware |
|:------:|:---------:|:---------|:----------:|
| GET | `/rentals` | Listar todos os aluguéis | auth:api |
| POST | `/rentals` | Criar um novo aluguel | auth:api |
| GET | `/rentals/{rental}` | Visualizar detalhes de um aluguel | auth:api |
| PUT | `/rentals/{rental}` | Atualizar informações de um aluguel | auth:api |
| DELETE | `/rentals/{rental}` | Deletar um aluguel | auth:api |
| POST | `/rentals/{rental}/start` | Iniciar um aluguel | auth:api |
| POST | `/rentals/{rental}/end` | Finalizar um aluguel | auth:api |

---

### 📊 Relatórios

| Método | Endpoint  | Descrição | Middleware |
|:------:|:---------:|:---------|:----------:|
| GET | `/api/reports/revenue?start=YYYY-MM-DD&end=YYYY-MM-DD` | Consultar relatório de faturamento no período informado | auth:api |

---

## 📌 Observações

- Todas as rotas (exceto `/login` e `/register`) exigem autenticação via JWT Bearer Token.
- A busca de veículos é realizada utilizando Elasticsearch para resultados rápidos e eficientes.
- O fluxo de aluguéis inclui iniciar e finalizar explicitamente, garantindo o controle total sobre os status.
- O serviço de relatórios é integrado e se comunica via HTTP entre Laravel e Python.

---

## 🛠️ Tecnologias e Padrões Utilizados

- Laravel 12 (Backend API)
- Elasticsearch 8 (Search Engine)
- JWT para autenticação segura
- Redis + Queues para processamento assíncrono
- SOLID Principles, Clean Code e Clean Architecture
