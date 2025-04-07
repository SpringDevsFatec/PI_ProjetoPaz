-- Inserindo fornecedores
INSERT INTO `supplier` (`name`, `location`) VALUES
('Fornecedor A', 'São Paulo'),
('Fornecedor B', 'Rio de Janeiro'),
('Fornecedor C', 'Minas Gerais');

-- Inserindo produtos
INSERT INTO `product` (`name`, `cost_price`, `sale_price`, `description`, `is_favorite`, `category`, `donation_amount`, `supplier_id`) VALUES
('Camiseta Paz', 15.00, 29.90, 'Camiseta branca com estampa de pomba da paz', 1, 'Vestuário', 2.00, 1),
('Caneca Amor', 8.50, 24.90, 'Caneca cerâmica com mensagens positivas', 1, 'Cozinha', 1.50, 2),
('Livro Harmonia', 25.00, 49.90, 'Livro sobre convivência pacífica', 0, 'Livros', 5.00, 3),
('Adesivo Solidariedade', 0.80, 3.90, 'Pacote com 10 adesivos temáticos', 0, 'Papelaria', 0.50, 1),
('Velas Aromáticas', 12.00, 34.90, 'Kit com 3 velas relaxantes', 1, 'Decoração', 3.00, 2);

-- Inserindo usuários
INSERT INTO `user` (`name`, `email`, `password`) VALUES
('João Silva', 'joao@email.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Maria Souza', 'maria@email.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Carlos Oliveira', 'carlos@email.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p');

-- Inserindo vendedores
INSERT INTO `seller` (`user_id`, `commission_rate`) VALUES
(1, 5.00),
(2, 7.50);

-- Inserindo vendas
INSERT INTO `sale` (`seller_id`, `total_amount`, `status`) VALUES
(1, 84.70, 'completed'),
(2, 49.90, 'completed'),
(1, 34.90, 'pending');

-- Inserindo pedidos
INSERT INTO `order` (`sale_id`, `payment_method`) VALUES
(1, 'credit'),
(2, 'pix'),
(3, 'cash');

-- Inserindo itens dos pedidos
INSERT INTO `order_item` (`product_id`, `order_id`, `quantity`, `unit_price`) VALUES
(1, 1, 2, 29.90),
(2, 1, 1, 24.90),
(3, 2, 1, 49.90),
(5, 3, 1, 34.90);

-- Inserindo imagens dos pedidos
INSERT INTO `order_image` (`order_id`, `image_url`) VALUES
(1, 'https://exemplo.com/imagens/pedido1-1.jpg'),
(1, 'https://exemplo.com/imagens/pedido1-2.jpg'),
(2, 'https://exemplo.com/imagens/pedido2-1.jpg');