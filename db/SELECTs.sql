-- Consulta básica de produtos
SELECT id, name, sale_price, category 
FROM product
WHERE product.is_favorite = 1;

-- Vendas com info do vendedor
SELECT s.id, s.total_amount, s.status, u.name AS seller_name, s.created_at
FROM sale s
JOIN seller sl ON s.seller_id = sl.id
JOIN user u ON sl.user_id = u.id;

-- Pedidos com itens detalhados
SELECT o.id, p.name AS product_name, oi.quantity, oi.unit_price, 
       (oi.quantity * oi.unit_price) AS subtotal
FROM `order` o
JOIN order_item oi ON o.id = oi.order_id
JOIN product p ON oi.product_id = p.id
WHERE o.id = 1;

-- Relatório de produtos mais vendidos
SELECT p.id, p.name, SUM(oi.quantity) AS total_sold, 
       SUM(oi.quantity * oi.unit_price) AS total_revenue
FROM product p
JOIN order_item oi ON p.id = oi.product_id
JOIN `order` o ON oi.order_id = o.id
JOIN sale s ON o.sale_id = s.id
WHERE s.status = 'completed'
GROUP BY p.id
ORDER BY total_sold DESC;

-- Consulta complexa com múltiplos joins
SELECT 
    o.id AS order_id,
    u.name AS customer_name,
    s.total_amount,
    o.payment_method,
    GROUP_CONCAT(p.name SEPARATOR ', ') AS products,
    COUNT(oi.id) AS items_count,
    o.created_at
FROM `order` o
JOIN sale s ON o.sale_id = s.id
JOIN seller sl ON s.seller_id = sl.id
JOIN user u ON sl.user_id = u.id
JOIN order_item oi ON o.id = oi.order_id
JOIN product p ON oi.product_id = p.id
GROUP BY o.id;

-- Produtos por fornecedor
SELECT s.name AS supplier_name, 
       COUNT(p.id) AS products_count,
       GROUP_CONCAT(p.name SEPARATOR ' | ') AS products
FROM supplier s
LEFT JOIN product p ON s.id = p.supplier_id
GROUP BY s.id;