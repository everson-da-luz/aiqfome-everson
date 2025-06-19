USE aiqfome-everson;

CREATE TABLE `aiqfome-everson`.customer (
	id INTEGER auto_increment NOT NULL,
	name varchar(100) NOT NULL,
	email varchar(100) NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL,
	CONSTRAINT customer_pk PRIMARY KEY (id),
	CONSTRAINT customer_unique_email UNIQUE KEY (email)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `aiqfome-everson`.favorite (
	id INTEGER auto_increment NOT NULL,
	customer_id INTEGER NOT NULL,
	product_id INTEGER NOT NULL,
	title varchar(255) NULL,
	image varchar(255) NULL,
	price DECIMAL(11,2) NULL,
	review TEXT NULL,
	created_at datetime DEFAULT NULL,
	CONSTRAINT favorite_pk PRIMARY KEY (id),
	CONSTRAINT favorite_unique_customer_product UNIQUE KEY (customer_id,product_id),
	CONSTRAINT favorite_customer_fk FOREIGN KEY (customer_id) REFERENCES `aiqfome-everson`.customer(id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `aiqfome-everson`.`user` (
	id INTEGER auto_increment NOT NULL,
	username varchar(255) NOT NULL,
	password varchar(255) NOT NULL,
	token_api varchar(255) NULL,
	created_at DATETIME NULL,
	CONSTRAINT user_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO user (username, password, created_at) VALUES ('admin', 'e10adc3949ba59abbe56e057f20f883e', NOW());