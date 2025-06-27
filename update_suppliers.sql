-- Update Writing Tools to be split between National Book Store and Office Warehouse
UPDATE products 
SET supplier = 'Office Warehouse'
WHERE category = 'Writing Tools' AND product_id % 2 = 0;

-- Update Technology products to be from PC Express and Octagon
UPDATE products 
SET supplier = 'PC Express'
WHERE category = 'Technology' AND product_id % 2 = 0;

UPDATE products 
SET supplier = 'Octagon'
WHERE category = 'Technology' AND product_id % 2 = 1;

-- Update Filing & Organization products to Office Warehouse
UPDATE products 
SET supplier = 'Office Warehouse'
WHERE category = 'Filing & Organization';

-- Update Art Supplies to Shoppe Depot
UPDATE products 
SET supplier = 'Shoppe Depot'
WHERE category = 'Art Supplies';

-- Update Personal Care products to Watsons
UPDATE products 
SET supplier = 'Watsons'
WHERE category = 'Personal Care';

-- Update Math Tools to Octagon
UPDATE products 
SET supplier = 'Octagon'
WHERE category = 'Math Tools';

-- Update Classroom Tools to Office Warehouse
UPDATE products 
SET supplier = 'Office Warehouse'
WHERE category = 'Classroom Tools';

-- Update Paper Products to be split between National Book Store and Office Warehouse
UPDATE products 
SET supplier = 'Office Warehouse'
WHERE category = 'Paper Products' AND product_id % 2 = 0;

-- Update Bags to Shoppe Depot
UPDATE products 
SET supplier = 'Shoppe Depot'
WHERE category = 'Bags';

-- Update Notebook & Paper to be split between National Book Store and Office Warehouse
UPDATE products 
SET supplier = 'Office Warehouse'
WHERE category = 'Notebook & Paper' AND product_id % 2 = 0;

-- Update Scaler products to Office Warehouse
UPDATE products 
SET supplier = 'Office Warehouse'
WHERE category = 'Scaler'; 