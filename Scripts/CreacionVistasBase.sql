-- Vistas de envoltura para todas las tablas base
-- Permiten que las consultas de la aplicación lean siempre desde vistas
-- en lugar de acceder directamente a las tablas.

DROP VIEW IF EXISTS vw_cliente;
CREATE VIEW vw_cliente AS SELECT * FROM Cliente;

DROP VIEW IF EXISTS vw_empleado;
CREATE VIEW vw_empleado AS SELECT * FROM Empleado;

DROP VIEW IF EXISTS vw_cajero;
CREATE VIEW vw_cajero AS SELECT * FROM Cajero;

DROP VIEW IF EXISTS vw_repostero;
CREATE VIEW vw_repostero AS SELECT * FROM Repostero;

DROP VIEW IF EXISTS vw_domiciliario;
CREATE VIEW vw_domiciliario AS SELECT * FROM Domiciliario;

DROP VIEW IF EXISTS vw_producto;
CREATE VIEW vw_producto AS SELECT * FROM Producto;

DROP VIEW IF EXISTS vw_producto_presentacion;
CREATE VIEW vw_producto_presentacion AS SELECT * FROM ProductoPresentacion;

DROP VIEW IF EXISTS vw_tamano;
CREATE VIEW vw_tamano AS SELECT * FROM Tamano;

DROP VIEW IF EXISTS vw_pedido;
CREATE VIEW vw_pedido AS SELECT * FROM Pedido;

DROP VIEW IF EXISTS vw_detalle_pedido;
CREATE VIEW vw_detalle_pedido AS SELECT * FROM DetallePedido;

DROP VIEW IF EXISTS vw_pago;
CREATE VIEW vw_pago AS SELECT * FROM Pago;

DROP VIEW IF EXISTS vw_compra;
CREATE VIEW vw_compra AS SELECT * FROM Compra;

DROP VIEW IF EXISTS vw_detalle_compra;
CREATE VIEW vw_detalle_compra AS SELECT * FROM DetalleCompra;

DROP VIEW IF EXISTS vw_ingrediente;
CREATE VIEW vw_ingrediente AS SELECT * FROM Ingrediente;

DROP VIEW IF EXISTS vw_proveedor;
CREATE VIEW vw_proveedor AS SELECT * FROM Proveedor;

DROP VIEW IF EXISTS vw_receta;
CREATE VIEW vw_receta AS SELECT * FROM Receta;

DROP VIEW IF EXISTS vw_detalle_receta;
CREATE VIEW vw_detalle_receta AS SELECT * FROM DetalleReceta;

-- Vistas utilitarias con información enriquecida para el frontal
DROP VIEW IF EXISTS vw_productos_presentaciones_ext;
CREATE VIEW vw_productos_presentaciones_ext AS
SELECT 
    p.pro_id,
    p.pro_nom,
    p.rec_id,
    pp.prp_id,
    pp.prp_precio,
    t.tam_id,
    t.tam_nom,
    t.tam_factor
FROM ProductoPresentacion pp
JOIN Producto p ON pp.pro_id = p.pro_id
JOIN Tamano t ON pp.tam_id = t.tam_id;

DROP VIEW IF EXISTS vw_pagos_pedidos_clientes;
CREATE VIEW vw_pagos_pedidos_clientes AS
SELECT 
    pa.pag_id,
    pa.pag_fec,
    pa.pag_hora,
    pa.pag_metodo,
    pe.ped_id,
    pe.ped_total,
    pe.ped_est,
    pe.ped_fec,
    c.cli_cedula,
    c.cli_nom,
    c.cli_apellido,
    c.cli_tel
FROM Pago pa
JOIN Pedido pe ON pa.ped_id = pe.ped_id
LEFT JOIN Cliente c ON pe.cli_cedula = c.cli_cedula;

DROP VIEW IF EXISTS vw_pedidos_detalle_ext;
CREATE VIEW vw_pedidos_detalle_ext AS
SELECT 
    pe.ped_id,
    pe.cli_cedula,
    pe.emp_id,
    pe.ped_fec,
    pe.ped_hora,
    pe.ped_est,
    pe.ped_total,
    dp.prp_id,
    dp.dpe_can,
    dp.dpe_subtotal,
    p.pro_nom,
    t.tam_nom,
    t.tam_factor
FROM Pedido pe
JOIN DetallePedido dp ON dp.ped_id = pe.ped_id
JOIN ProductoPresentacion pp ON dp.prp_id = pp.prp_id
JOIN Producto p ON pp.pro_id = p.pro_id
JOIN Tamano t ON pp.tam_id = t.tam_id;

DROP VIEW IF EXISTS vw_compras_detalle_ext;
CREATE VIEW vw_compras_detalle_ext AS
SELECT 
    c.com_id,
    c.prov_id,
    c.com_fec,
    c.com_tot,
    dc.ing_id,
    dc.dco_can,
    dc.dco_pre,
    i.ing_nom,
    i.ing_um
FROM Compra c
JOIN DetalleCompra dc ON c.com_id = dc.com_id
JOIN Ingrediente i ON dc.ing_id = i.ing_id;
