-- Inserindo fornecedores
INSERT INTO `supplier` (`name`, `location`) VALUES
('Fornecedor A', 'São Paulo'),
('Fornecedor B', 'Rio de Janeiro'),
('Fornecedor C', 'Minas Gerais');

-- Inserindo produtos
INSERT INTO `product` (`name`, `cost_price`, `sale_price`, `description`, `is_favorite`, `category`, `donation`,`img_product`,`status`, `supplier_id`) VALUES
('Camiseta Paz', 15.00, 29.90, 'Camiseta branca com estampa de pomba da paz', 1, 'Vestuário', 0, 'https://i.pinimg.com/736x/74/17/61/7417614ac80974280a70b3a5555563d4.jpg', 1 , 1),
('Caneca Amor', 8.50, 24.90, 'Caneca cerâmica com mensagens positivas', 1, 'Cozinha', 1, 'https://i.pinimg.com/736x/74/17/61/7417614ac80974280a70b3a5555563d4.jpg', 1, 2),
('Livro Harmonia', 25.00, 49.90, 'Livro sobre convivência pacífica', 0, 'Livros', 0, 'https://i.pinimg.com/736x/74/17/61/7417614ac80974280a70b3a5555563d4.jpg', 1, 3), 
('Adesivo Solidariedade', 0.80, 3.90, 'Pacote com 10 adesivos temáticos', 0, 'Papelaria', 1, 'https://i.pinimg.com/736x/74/17/61/7417614ac80974280a70b3a5555563d4.jpg', 1, 1),
('Velas Aromáticas', 12.00, 34.90, 'Kit com 3 velas relaxantes', 1, 'Decoração', 0, 'https://i.pinimg.com/736x/74/17/61/7417614ac80974280a70b3a5555563d4.jpg', 1, 2);

-- Inserindo usuários
INSERT INTO `user` (`name`, `email`, `password`) VALUES
('João Silva', 'joao@email.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Maria Souza', 'maria@email.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Carlos Oliveira', 'carlos@email.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Dev', 'dev@gmail.com', '$2y$10$IjQ9yeLeIjqw7csno9KK.uWdRofGZjRmQg2A93GrHtuHgave/BB2W');

-- Inserindo vendas
INSERT INTO `sale` (`user_id`, `total_amount_sale`, `status`, `method`, `code`, `img_sale`) VALUES
(4, 84.70, 'completed', 'manual', 'SALE123', 'https://exemplo.com/imagens/venda1.jpg'),
(4, 49.90, 'pending', 'manual', 'SALE124', 'https://exemplo.com/imagens/venda2.jpg'),
(4, 49.90, 'completed', 'manual', 'SALE125', 'https://exemplo.com/imagens/venda3.jpg'),
(4, 34.90, 'completed', 'auto', 'SALE126', 'https://exemplo.com/imagens/venda4.jpg');

-- Inserindo pedidos
INSERT INTO `order` (`sale_id`, `payment_method`, `code` , `status`, `total_amount_order`) VALUES
(1, 'credito', 'PE123', 'completed', 42.35),
(1, 'credito', 'PE124', 'completed', 42.35),
(2, 'pix', 'PE125', 'pending', 49.90),
(3, 'dinheiro', 'PE126','completed', 34.90);

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
