DELIMITER $$

DROP PROCEDURE IF EXISTS sp_crear_pedido $$
CREATE PROCEDURE sp_crear_pedido(
    IN p_cli_cedula VARCHAR(20),
    IN p_emp_id INT,
    IN p_ped_est VARCHAR(20),
    IN p_ped_total DECIMAL(10,2),
    IN p_ped_fec DATE,
    IN p_ped_hora TIME,
    OUT p_new_id INT
)
BEGIN
    INSERT INTO Pedido (cli_cedula, emp_id, ped_fec, ped_hora, ped_est, ped_total)
    VALUES (p_cli_cedula, p_emp_id, p_ped_fec, p_ped_hora, p_ped_est, p_ped_total);
    SET p_new_id = LAST_INSERT_ID();
END $$

DROP PROCEDURE IF EXISTS sp_agregar_detalle_pedido $$
CREATE PROCEDURE sp_agregar_detalle_pedido(
    IN p_ped_id INT,
    IN p_prp_id INT,
    IN p_cantidad INT,
    IN p_subtotal DECIMAL(10,2)
)
BEGIN
    INSERT INTO DetallePedido (ped_id, prp_id, dpe_can, dpe_subtotal)
    VALUES (p_ped_id, p_prp_id, p_cantidad, p_subtotal);
END $$

DROP PROCEDURE IF EXISTS sp_actualizar_estado_pedido $$
CREATE PROCEDURE sp_actualizar_estado_pedido(
    IN p_ped_id INT,
    IN p_nuevo_estado VARCHAR(20)
)
BEGIN
    UPDATE Pedido SET ped_est = p_nuevo_estado WHERE ped_id = p_ped_id;
END $$

DROP PROCEDURE IF EXISTS sp_registrar_pago $$
CREATE PROCEDURE sp_registrar_pago(
    IN p_ped_id INT,
    IN p_pag_metodo VARCHAR(20),
    IN p_pag_fec DATE,
    IN p_pag_hora TIME,
    OUT p_pag_id INT
)
BEGIN
    INSERT INTO Pago (ped_id, pag_metodo, pag_fec, pag_hora)
    VALUES (p_ped_id, p_pag_metodo, p_pag_fec, p_pag_hora);
    SET p_pag_id = LAST_INSERT_ID();
END $$

DROP PROCEDURE IF EXISTS sp_crear_compra $$
CREATE PROCEDURE sp_crear_compra(
    IN p_prov_id INT,
    IN p_com_fec DATE,
    IN p_com_tot DECIMAL(10,2),
    OUT p_com_id INT
)
BEGIN
    INSERT INTO Compra (prov_id, com_fec, com_tot)
    VALUES (p_prov_id, p_com_fec, p_com_tot);
    SET p_com_id = LAST_INSERT_ID();
END $$

DROP PROCEDURE IF EXISTS sp_agregar_detalle_compra $$
CREATE PROCEDURE sp_agregar_detalle_compra(
    IN p_com_id INT,
    IN p_ing_id INT,
    IN p_cantidad DECIMAL(10,2),
    IN p_precio DECIMAL(10,2)
)
BEGIN
    INSERT INTO DetalleCompra (com_id, ing_id, dco_can, dco_pre)
    VALUES (p_com_id, p_ing_id, p_cantidad, p_precio);
END $$

DROP PROCEDURE IF EXISTS sp_actualizar_compra $$
CREATE PROCEDURE sp_actualizar_compra(
    IN p_com_id INT,
    IN p_prov_id INT,
    IN p_com_fec DATE,
    IN p_com_tot DECIMAL(10,2)
)
BEGIN
    UPDATE Compra
    SET prov_id = p_prov_id,
        com_fec = p_com_fec,
        com_tot = p_com_tot
    WHERE com_id = p_com_id;
END $$

DROP PROCEDURE IF EXISTS sp_eliminar_detalles_compra $$
CREATE PROCEDURE sp_eliminar_detalles_compra(
    IN p_com_id INT
)
BEGIN
    DELETE FROM DetalleCompra WHERE com_id = p_com_id;
END $$

DROP PROCEDURE IF EXISTS sp_aplicar_stock_compra $$
CREATE PROCEDURE sp_aplicar_stock_compra(
    IN p_com_id INT
)
BEGIN
    UPDATE Ingrediente i
    JOIN (
        SELECT ing_id, SUM(dco_can) AS total_can
        FROM DetalleCompra
        WHERE com_id = p_com_id
        GROUP BY ing_id
    ) d ON d.ing_id = i.ing_id
    SET i.ing_stock = i.ing_stock + d.total_can;
END $$

DROP PROCEDURE IF EXISTS sp_preparar_pedido $$
CREATE PROCEDURE sp_preparar_pedido(
    IN p_ped_id INT
)
BEGIN
    -- Consumir ingredientes según receta y tamaño
    UPDATE Ingrediente i
    JOIN (
        SELECT dr.ing_id, SUM(dr.dre_can * t.tam_factor * dp.dpe_can) AS total_consumo
        FROM DetallePedido dp
        JOIN ProductoPresentacion pp ON dp.prp_id = pp.prp_id
        JOIN Producto p ON pp.pro_id = p.pro_id
        JOIN Tamano t ON pp.tam_id = t.tam_id
        JOIN Receta r ON p.rec_id = r.rec_id
        JOIN DetalleReceta dr ON r.rec_id = dr.rec_id
        JOIN Pedido ped ON ped.ped_id = dp.ped_id AND ped.ped_est = 'Pendiente'
        WHERE dp.ped_id = p_ped_id
        GROUP BY dr.ing_id
    ) c ON c.ing_id = i.ing_id
    SET i.ing_stock = i.ing_stock - c.total_consumo;

    -- Actualizar estado del pedido
    UPDATE Pedido
    SET ped_est = 'Preparado'
    WHERE ped_id = p_ped_id AND ped_est = 'Pendiente';
END $$

DELIMITER ;
