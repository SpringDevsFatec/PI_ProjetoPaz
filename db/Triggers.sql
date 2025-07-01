-- TRIGGERS

DELIMITER $$

-- Trigger for sum all order items and update the total_amount
CREATE TRIGGER trg_update_total_amount_after_status_change
AFTER UPDATE ON sale
FOR EACH ROW
BEGIN
  DECLARE total DECIMAL(10,2);

  IF OLD.status <> NEW.status THEN
    SELECT SUM(oi.unit_price * oi.quantity)
    INTO total
    FROM order_item oi
    JOIN `order` o ON o.id = oi.order_id
    WHERE o.sale_id = NEW.id;

    UPDATE sale
    SET total_amount = IFNULL(total, 0.00)
    WHERE id = NEW.id;
  END IF;
END$$

-- Trigger para User

-- INSERT
CREATE TRIGGER trg_user_insert
AFTER INSERT ON user
FOR EACH ROW
BEGIN
  INSERT INTO user_log (user_id, action, name, email)
  VALUES (NEW.id, 'INSERT', NEW.name, NEW.email);
END$$

-- UPDATE
CREATE TRIGGER trg_user_update
AFTER UPDATE ON user
FOR EACH ROW
BEGIN
  INSERT INTO user_log (user_id, action, name, email)
  VALUES (NEW.id, 'UPDATE', NEW.name, NEW.email);
END$$

-- DELETE
CREATE TRIGGER trg_user_delete
AFTER DELETE ON user
FOR EACH ROW
BEGIN
  INSERT INTO user_log (user_id, action, name, email)
  VALUES (OLD.id, 'DELETE', OLD.name, OLD.email);
END$$

-- Trigger para Product

-- INSERT
CREATE TRIGGER trg_product_insert
AFTER INSERT ON product
FOR EACH ROW
BEGIN
  INSERT INTO product_log (
    product_id, action, name, cost_price, sale_price, description, category, donation, is_favorite, supplier_id
  )
  VALUES (
    NEW.id, 'INSERT', NEW.name, NEW.cost_price, NEW.sale_price, NEW.description, NEW.category, NEW.donation, NEW.is_favorite, NEW.supplier_id
  );
END$$

-- UPDATE
CREATE TRIGGER trg_product_update
AFTER UPDATE ON product
FOR EACH ROW
BEGIN
  INSERT INTO product_log (
    product_id, action, name, cost_price, sale_price, description, category, donation, is_favorite, supplier_id
  )
  VALUES (
    NEW.id, 'UPDATE', NEW.name, NEW.cost_price, NEW.sale_price, NEW.description, NEW.category, NEW.donation, NEW.is_favorite, NEW.supplier_id
  );
END$$

-- DELETE
CREATE TRIGGER trg_product_delete
AFTER DELETE ON product
FOR EACH ROW
BEGIN
  INSERT INTO product_log (
    product_id, action, name, cost_price, sale_price, description, category, donation, is_favorite, supplier_id
  )
  VALUES (
    OLD.id, 'DELETE', OLD.name, OLD.cost_price, OLD.sale_price, OLD.description, OLD.category, OLD.donation, OLD.is_favorite, OLD.supplier_id
  );
END$$

DELIMITER ;
