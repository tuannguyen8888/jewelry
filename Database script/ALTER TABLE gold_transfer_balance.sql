ALTER TABLE gold_transfer_balance
ADD COLUMN `brand_id` INT NOT NULL DEFAULT 1 AFTER `order_date`;