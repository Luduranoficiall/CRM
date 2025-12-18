CREATE TABLE IF NOT EXISTS admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(100),
    acao VARCHAR(100),
    ip VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
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