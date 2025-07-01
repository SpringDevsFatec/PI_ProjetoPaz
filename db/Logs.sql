-- LOG's

--Log User

CREATE TABLE user_log (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT,
  action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
  name VARCHAR(100),
  email VARCHAR(100),
  password VARCHAR(100),
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

-- Log Product

CREATE TABLE product_log (
  id INT NOT NULL AUTO_INCREMENT,
  product_id INT,
  action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
  name VARCHAR(100),
  cost_price DECIMAL(10,2),
  sale_price DECIMAL(10,2),
  description TEXT,
  category VARCHAR(50),
  donation TINYINT(1),
  is_favorite TINYINT(1),
  supplier_id INT,
  img_product varchar(50) DEFAULT NULL,
  status tinyint(1) DEFAULT 0,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
