-- Inserindo fornecedores
INSERT INTO `supplier` (`name`, `location`) VALUES
('Fornecedor A', 'São Paulo'),
('Fornecedor B', 'Rio de Janeiro'),
('Fornecedor C', 'Minas Gerais');

-- Inserindo produtos
INSERT INTO `product` (`name`, `cost_price`, `sale_price`, `description`, `is_favorite`, `category`, `donation`, `supplier_id`, `image`) VALUES
('Achocolatado', 2.00, 4.00, 'Copo de achocolatado', 1, 'Bebida', 0, 1, 'https://project-paz-product.s3.us-east-1.amazonaws.com/Achocolatado.png'),
('Agua', 1.00, 2.99, 'Uma garrafa de agua', 1, 'Bebida', 0, 2, 'https://project-paz-product.s3.us-east-1.amazonaws.com/Agua1.png'),
('Biblia', 12.00, 20.00, 'Um Livro sobre Deus', 0, 'Livros', 0, 3, 'https://project-paz-product.s3.us-east-1.amazonaws.com/Biblia.png'),
('Bolo de padaria', 1.50, 5.00, 'Um delicioso bolo de padaria', 1, 'Alimento', 0, 1, 'https://project-paz-product.s3.us-east-1.amazonaws.com/BoloDePadaria.png'),
('Bolo doado', 00.00, 5.00, 'Um bolo adquirido via doação', 1, 'Alimento', 1, 1, 'https://project-paz-product.s3.us-east-1.amazonaws.com/BoloDoado.png'),
('Brioche', 1.50, 4.00, 'Um brioche', 0, 'Alimento', 0, 2, 'https://project-paz-product.s3.us-east-1.amazonaws.com/Brioche.png'),
('Café', 1.00, 3.00, 'Um copinho de café', 1, 'Bebida', 0, 2, 'https://project-paz-product.s3.us-east-1.amazonaws.com/Cafe1.png'),
('Café com leite', 1.00, 3.00, 'Um copo com café com leite', 1, 'Bebida', 0, 2, 'https://project-paz-product.s3.us-east-1.amazonaws.com/CafeComLeite.png'),
('Pao de Mel', 3.00, 4.00, 'Deliciosos pães de mel', 1, 'Alimento', 0, 2, 'https://project-paz-product.s3.us-east-1.amazonaws.com/PaoDeMel.png'),
('Pao de mel grande', 4.00, 8.00, 'Deliciosos pães de mel', 1, 'Alimento', 0, 2, 'https://project-paz-product.s3.us-east-1.amazonaws.com/PaoDeMelGrande.png'),
('Terço', 8.00, 15.00, 'Um terço', 0, 'Objeto religioso', 0, 2, 'https://project-paz-product.s3.us-east-1.amazonaws.com/Ter%C3%A7o.png');

-- Inserindo usuários
INSERT INTO `user` (`name`, `email`, `password`) VALUES
('Admin', 'admin@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Daniel', 'aumenteavoz@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Diego', 'diego.rsilva14@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Gabriel Defendi', 'bielsdef@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Graziely', 'grazymoreirasilva04@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Pedro Almeida', 'pedroooalmeida09@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Bruno', 'brunogon.080409@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Eduardo', 'dumaranidesousa@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Felipe', 'af9785102@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Giuliano', 'comodorolih@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Lauro', 'lauromoura25@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Mariana', 'mari.maia0302@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Monique', 'monique-colucci@hotmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p'),
('Mauricio Sobral', 'sobralmauricio12@gmail.com', '$2y$10$N7h3m8uVr6z9kQ1wLpB4E.9mZJvXyRcT2nS3dF4gH5jK6lM7nO8p');

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
