ALTER TABLE `jewelry_business`.`gold_stocks_issues` 
ADD COLUMN `object_type` INT(1) NOT NULL DEFAULT 1 COMMENT 'Loại đối tượng (0: Khách hàng; 1: NCC; 2: NĐT; 3: Nhân viên)' AFTER `order_date`,
CHANGE COLUMN `supplier_id` `object_id` INT(11) NULL DEFAULT NULL ;
