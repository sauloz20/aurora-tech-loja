CREATE DATABASE IF NOT EXISTS crud_produtos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crud_produtos;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS pedido_itens;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS produtos;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE produtos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    marca VARCHAR(80) NOT NULL DEFAULT 'Aurora',
    modelo VARCHAR(120) NOT NULL DEFAULT '',
    descricao VARCHAR(2000) NOT NULL,
    especificacoes TEXT NULL,
    categoria ENUM('Celulares', 'Notebooks', 'Tablets', 'Acessórios', 'Áudio', 'Casa inteligente') NOT NULL,
    preco DECIMAL(10,2) UNSIGNED NOT NULL,
    estoque INT UNSIGNED NOT NULL DEFAULT 0,
    ano SMALLINT UNSIGNED NOT NULL,
    status ENUM('Ativo', 'Inativo') NOT NULL DEFAULT 'Ativo',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_produtos_status (status),
    INDEX idx_produtos_categoria (categoria),
    INDEX idx_produtos_estoque (estoque)
) ENGINE=InnoDB;

CREATE TABLE pedidos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_nome VARCHAR(120) NOT NULL,
    cliente_email VARCHAR(160) NOT NULL,
    cep VARCHAR(9) NOT NULL,
    endereco VARCHAR(180) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    pagamento ENUM('Pix', 'Cartão', 'Boleto') NOT NULL,
    subtotal DECIMAL(10,2) UNSIGNED NOT NULL,
    frete DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0,
    total DECIMAL(10,2) UNSIGNED NOT NULL,
    status ENUM('Recebido', 'Pago', 'Enviado', 'Entregue', 'Cancelado') NOT NULL DEFAULT 'Recebido',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pedidos_email (cliente_email),
    INDEX idx_pedidos_status (status)
) ENGINE=InnoDB;

CREATE TABLE pedido_itens (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT UNSIGNED NOT NULL,
    produto_id INT UNSIGNED NULL,
    produto_nome VARCHAR(120) NOT NULL,
    preco_unitario DECIMAL(10,2) UNSIGNED NOT NULL,
    quantidade INT UNSIGNED NOT NULL,
    total DECIMAL(10,2) UNSIGNED NOT NULL,
    CONSTRAINT fk_pedido_itens_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_pedido_itens_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE SET NULL,
    INDEX idx_pedido_itens_pedido (pedido_id)
) ENGINE=InnoDB;

INSERT INTO produtos (nome, marca, modelo, descricao, especificacoes, categoria, preco, estoque, ano, status) VALUES
('Aurora X Pro 256GB', 'Aurora', 'X Pro', 'Smartphone com câmera avançada, tela brilhante e bateria para o dia inteiro.', 'Tela AMOLED 6,7 polegadas|256 GB de armazenamento|12 GB de RAM|Câmera tripla 50 MP|Bateria 5.000 mAh', 'Celulares', 3299.90, 14, 2025, 'Ativo'),
('Aurora Lite 128GB', 'Aurora', 'Lite', 'Celular equilibrado para comunicação, fotos e entretenimento.', 'Tela 6,5 polegadas|128 GB de armazenamento|8 GB de RAM|Câmera dupla 48 MP|Bateria 4.500 mAh', 'Celulares', 1499.90, 22, 2025, 'Ativo'),
('Notebook Aurora Air 14', 'Aurora', 'Air 14', 'Leve, rápido e pronto para estudos, trabalho e criação.', 'Tela 14 polegadas Full HD|Processador Intel Core i5|16 GB de RAM|SSD 512 GB|Bateria até 10 horas', 'Notebooks', 4299.90, 8, 2025, 'Ativo'),
('Notebook Aurora Pro 15', 'Aurora', 'Pro 15', 'Desempenho para programação, design e multitarefas.', 'Tela 15,6 polegadas|Processador Intel Core i7|32 GB de RAM|SSD 1 TB|Placa gráfica dedicada', 'Notebooks', 6799.90, 6, 2025, 'Ativo'),
('Tablet Aurora Tab 11', 'Aurora', 'Tab 11', 'Tela ampla para leitura, aulas, vídeos e produtividade.', 'Tela 11 polegadas 2K|128 GB de armazenamento|8 GB de RAM|Caneta compatível|Bateria 8.000 mAh', 'Tablets', 2199.90, 11, 2025, 'Ativo'),
('Tablet Aurora Mini', 'Aurora', 'Mini', 'Compacto e confortável para levar a qualquer lugar.', 'Tela 8,7 polegadas|64 GB de armazenamento|4 GB de RAM|Câmera 8 MP|Bateria 5.100 mAh', 'Tablets', 1299.90, 16, 2025, 'Ativo'),
('Hub USB-C Essencial', 'Aurora', 'Hub 7 em 1', 'Mais conexões para seu notebook em um design compacto.', 'HDMI 4K|USB 3.0|Leitor SD|USB-C Power Delivery|Alumínio', 'Acessórios', 159.90, 25, 2025, 'Ativo'),
('Carregador Turbo 65W', 'Aurora', 'Turbo 65', 'Carregamento rápido para celulares, tablets e notebooks compatíveis.', '65 W de potência|Duas portas USB-C|Proteção contra sobrecarga|Bivolt automático', 'Acessórios', 189.90, 30, 2025, 'Ativo'),
('Fone Pulse ANC', 'Aurora', 'Pulse ANC', 'Cancelamento de ruído e conforto para acompanhar sua rotina.', 'Cancelamento ativo de ruído|Bluetooth 5.3|Até 35 horas de bateria|Microfone integrado', 'Áudio', 349.90, 18, 2025, 'Ativo'),
('Caixa de Som Wave', 'Aurora', 'Wave', 'Som envolvente para ambientes internos e momentos especiais.', 'Potência 30 W|Bluetooth 5.2|Resistente a respingos|Até 12 horas de bateria', 'Áudio', 499.90, 10, 2025, 'Ativo'),
('Lâmpada Smart Color', 'Aurora', 'Smart Color', 'Iluminação inteligente com controle pelo celular.', 'Wi-Fi 2.4 GHz|16 milhões de cores|Agendamento|Compatível com assistentes de voz', 'Casa inteligente', 89.90, 40, 2025, 'Ativo'),
('Câmera Smart Home', 'Aurora', 'Smart Home Cam', 'Monitoramento conectado para cuidar da sua casa de onde estiver.', 'Imagem Full HD|Visão noturna|Detecção de movimento|Áudio bidirecional|Armazenamento em nuvem', 'Casa inteligente', 279.90, 13, 2025, 'Ativo');
