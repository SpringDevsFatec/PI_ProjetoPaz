-- TRIGGERS

--Trigger for Sum all itens ordens and update te total_amount

DELIMITER $$

CREATE TRIGGER trg_update_total_amount_after_status_change
AFTER UPDATE ON sale
FOR EACH ROW
BEGIN
  -- Checa se o status foi alterado
  IF OLD.status <> NEW.status THEN
    DECLARE total DECIMAL(10,2);

    -- Calcula a soma dos itens dos pedidos dessa venda
    SELECT SUM(oi.unit_price * oi.quantity)
    INTO total
    FROM order_item oi
    JOIN `order` o ON o.id = oi.order_id
    WHERE o.sale_id = NEW.id;

    -- Atualiza o total_amount na tabela sale
    UPDATE sale
    SET total_amount = IFNULL(total, 0.00)
    WHERE id = NEW.id;
  END IF;
END$$

DELIMITER ;

-- Trigger para User

-- INSERT
CREATE TRIGGER trg_user_insert
AFTER INSERT ON user
FOR EACH ROW
INSERT INTO user_log (user_id, action, name, email)
VALUES (NEW.id, 'INSERT', NEW.name, NEW.email);

-- UPDATE
CREATE TRIGGER trg_user_update
AFTER UPDATE ON user
FOR EACH ROW
INSERT INTO user_log (user_id, action, name, email)
VALUES (NEW.id, 'UPDATE', NEW.name, NEW.email);

-- DELETE
CREATE TRIGGER trg_user_delete
AFTER DELETE ON user
FOR EACH ROW
INSERT INTO user_log (user_id, action, name, email)
VALUES (OLD.id, 'DELETE', OLD.name, OLD.email);


-- Trigger para Product

-- INSERT
CREATE TRIGGER trg_product_insert
AFTER INSERT ON product
FOR EACH ROW
INSERT INTO product_log (
  product_id, action, name, cost_price, sale_price, description, category, donation, is_favorite, supplier_id
)
VALUES (
  NEW.id, 'INSERT', NEW.name, NEW.cost_price, NEW.sale_price, NEW.description, NEW.category, NEW.donation, NEW.is_favorite, NEW.supplier_id
);

-- UPDATE
CREATE TRIGGER trg_product_update
AFTER UPDATE ON product
FOR EACH ROW
INSERT INTO product_log (
  product_id, action, name, cost_price, sale_price, description, category, donation, is_favorite, supplier_id
)
VALUES (
  NEW.id, 'UPDATE', NEW.name, NEW.cost_price, NEW.sale_price, NEW.description, NEW.category, NEW.donation, NEW.is_favorite, NEW.supplier_id
);

-- DELETE
CREATE TRIGGER trg_product_delete
AFTER DELETE ON product
FOR EACH ROW
INSERT INTO product_log (
  product_id, action, name, cost_price, sale_price, description, category, donation, is_favorite, supplier_id
)
VALUES (
  OLD.id, 'DELETE', OLD.name, OLD.cost_price, OLD.sale_price, OLD.description, OLD.category, OLD.donation, OLD.is_favorite, OLD.supplier_id
);


