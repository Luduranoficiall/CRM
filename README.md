# CRM SaaS - PHP + MySQL + Tailwind

## Instalação

1. **Clone o projeto:**
   ```bash
   git clone <repo-url>
   ```
2. **Crie o banco de dados MySQL:**
   - Nome sugerido: `crm`
   - Execute o script `/leads.sql` para criar a tabela:
     ```sql
     CREATE TABLE leads (
         id INT AUTO_INCREMENT PRIMARY KEY,
         nome VARCHAR(100) NOT NULL,
         email VARCHAR(100) NOT NULL,
         telefone VARCHAR(30) NOT NULL,
         instagram VARCHAR(100),
         ramo VARCHAR(100),
         faturamento_raw VARCHAR(50),
         faturamento_categoria VARCHAR(20),
         invest_raw VARCHAR(50),
         invest_categoria VARCHAR(20),
         objetivo TEXT,
         faz_trafego VARCHAR(10),
         tags_ai TEXT,
         score_potencial INT,
         urgencia VARCHAR(10),
         status_kanban VARCHAR(20) DEFAULT 'Cold',
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );
     ```
3. **Configure o acesso MySQL em `/config.php`**
   - Ajuste usuário, senha e banco conforme seu ambiente.

4. **Instale dependências:**
   - Tailwind já está via CDN.
   - SortableJS já está via CDN.

5. **Acesse:**
   - Quiz: `/quiz.php`
   - Admin: `/admin/login.php` (usuário: `admin`, senha: `123456`)

## Estrutura de Pastas

- `/assets/css` — Estilos customizados
- `/assets/js` — Scripts JS (quiz, kanban)
- `/assets/img` — Imagens
- `/components` — Componentes PHP reutilizáveis
- `/api` — Endpoints PHP
- `/admin` — Painel administrativo
- `/config.php` — Conexão MySQL
- `/quiz.php` — Página de captura de leads
- `/leads.sql` — Script SQL da tabela

## Gemini 3
- Integração mockada, substitua pela chamada real da API Gemini 3 no arquivo `/api/new-lead.php`.

## Visual
- Tons de azul premium
- Layout responsivo, moderno, SaaS
- Kanban com drag & drop
- Cards arredondados, sombras suaves, microinterações

---
Dúvidas? Fale com o desenvolvedor.