-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 12-08-2024 a las 01:31:31
-- Versión del servidor: 8.0.30
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bd_website`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_temp` (IN `codigo` INT, IN `token_user` VARCHAR(50))   BEGIN 
    	DECLARE precio_actual decimal(10,2); SELECT servi_precio INTO precio_actual FROM servicio WHERE cod_servicio = codigo; 
   	 	INSERT INTO detalle_temp(token_user,cod_servicio,temp_preciototal) VALUES (token_user,codigo,precio_actual); 
    	SELECT tmp.id_temp,tmp.cod_servicio,p.servi_nombre,p.tiempo,tmp.temp_preciototal FROM detalle_temp tmp 
    	INNER JOIN servicio p ON tmp.cod_servicio = p.cod_servicio WHERE tmp.token_user = token_user; 
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `add_detalle_tempp` (IN `nombre` VARCHAR(100), IN `empresa` VARCHAR(100), IN `cantidad` INT, IN `preciouni` DECIMAL(10,2), IN `token_user` VARCHAR(50))   BEGIN 
    	DECLARE precio_actual decimal(10,2);
        INSERT INTO detalle_tempp(token_usuario,nombre_pro,empresa,cantidad,precio_uni) VALUES (token_user,nombre,empresa,cantidad,preciouni);
        
        SELECT tmp.id_tempp, tmp.nombre_pro,tmp.empresa,tmp.cantidad,tmp.precio_uni FROM detalle_tempp tmp WHERE tmp.token_usuario = token_user;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `anular_factura` (`n_comprobante` INT)   BEGIN 
    	DECLARE existe_factura int;
        DECLARE registros int;
        DECLARE a int;
        
        DECLARE cod_servicio int;
        
        SET existe_factura = (SELECT COUNT(*) FROM comprobante WHERE id_comprobante = n_comprobante AND com_estado = 1);
        IF existe_factura > 0 THEN 
        	CREATE TEMPORARY TABLE tbl_temp(
            	id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                cod_servi BIGINT);
                SET a = 1;
                SET registros = (SELECT COUNT(*) FROM detalle_comprobante WHERE id_comprobante = n_comprobante);
                IF registros > 0 THEN 
                	INSERT INTO tbl_temp(cod_servi) SELECT cod_servicio FROM detalle_comprobante WHERE id_comprobante = n_comprobante;
                    WHILE a <= registros DO 
                    	SELECT cod_servi INTO cod_servicio FROM tbl_temp WHERE id = a; 
                        SET a=a+1;
                    END WHILE;
                    UPDATE comprobante SET com_estado = 2 WHERE id_comprobante = n_comprobante;
                    DROP TABLE tbl_temp;
                    SELECT * FROM comprobante WHERE id_comprobante = n_comprobante;
                END IF;
        ELSE 
        	SELECT 0 comprobante;
        END IF;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `anular_factura_egre` (IN `no_externo` INT)   BEGIN 
    	DECLARE existe_egreso INT;
        DECLARE registro INT;
        DECLARE a INT;
        
        DECLARE nom_producto VARCHAR(100);
        DECLARE emp_producto VARCHAR(100);
        DECLARE pre_producto DECIMAL(10,2);
        DECLARE cant_producto INT;
        
        SET existe_egreso = (SELECT COUNT(*) FROM externo WHERE id_externo = no_externo AND ext_estado = 1);
        
        IF existe_egreso > 0 THEN 
        	CREATE TEMPORARY TABLE tbl_tmpp(
            id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            Empresa VARCHAR(100),
            Nombre VARCHAR(100),
            Precio DECIMAL(10,2),
            Cantidad INT);
            
            SET a = 1;
            
            SET registro =(SELECT COUNT(*) FROM detalle_externo WHERE id_externo = no_externo);
            IF registro > 0 THEN 
            	INSERT INTO tbl_tmpp(Empresa,Nombre,Precio,Cantidad) SELECT empresa,nombre_pro,precio_uni,cantidad FROM detalle_externo WHERE id_externo = no_externo;
                
                WHILE a <= registro DO 
                	SELECT 	Empresa,Nombre,Precio,Cantidad INTO emp_producto,nom_producto,pre_producto,cant_producto FROM tbl_tmpp WHERE id = a;
                    SET a=a+1;
                END WHILE;
                	UPDATE externo SET ext_estado = 2 WHERE id_externo = no_externo;
                    DROP TABLE tbl_tmpp;
                    SELECT * FROM externo WHERE id_externo = no_externo;
            END IF;
        ELSE 
         	SELECT 0 externo;
        END IF;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `dataDashboard` ()   BEGIN
    	DECLARE usuarios int;
        DECLARE clientes int;
        DECLARE servicios int;
        DECLARE ventas int;
        DECLARE egresos int;
        DECLARE totalfactura DECIMAL(10,2);
        DECLARE totalegreso DECIMAL(10,2);
        DECLARE totalfijo DECIMAL(10,2);
        DECLARE totalpersonal DECIMAL(10,2);
        DECLARE totalvariable DECIMAL(10,2);
        
        SELECT COUNT(*) INTO usuarios FROM usuario WHERE usu_estado !=10;
        SELECT COUNT(*) INTO clientes FROM cliente WHERE cli_estado !=10;
        SELECT COUNT(*) INTO servicios FROM servicio WHERE servi_estado !=10;
        SELECT COUNT(*) INTO ventas FROM comprobante WHERE com_fechaemi >CURDATE() AND com_estado !=10;
        SELECT COUNT(*) INTO egresos FROM ext WHERE fechaemi >CURDATE() AND estado !=10;
        SELECT ROUND(SUM(com_totalfactura),2) INTO totalfactura FROM comprobante WHERE com_fechaemi >CURDATE() AND com_estado !=2;
        
        SELECT ROUND(SUM(total),2) INTO totalegreso FROM ext WHERE fechaemi >CURDATE() AND estado !=10;
         SELECT ROUND(SUM(fj_monto),2) INTO totalfijo FROM egreso_fijo WHERE fj_fecha >CURDATE() AND fj_estado !=10;
        
        SELECT ROUND(SUM(ep_total),2) INTO totalpersonal FROM egreso_personal WHERE ep_fecha >CURDATE() AND estado !=10;
        
        SELECT ROUND(SUM(total),2) INTO totalvariable FROM egreso_variable WHERE fecha >CURDATE() AND estado !=10;
        
        SELECT usuarios,clientes,servicios,ventas,egresos,totalfactura,totalegreso,totalvariable,totalfijo,totalpersonal;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_temp` (IN `id_detalle` INT, IN `token` VARCHAR(50))   BEGIN
    	DELETE FROM detalle_temp WHERE id_temp = id_detalle;
        SELECT s.tiempo,tmp.id_temp, tmp.cod_servicio, s.servi_nombre,tmp.temp_preciototal FROM detalle_temp tmp INNER JOIN servicio s 
        ON tmp.cod_servicio = s.cod_servicio WHERE tmp.token_user = token;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `del_detalle_tempp` (`id_detalle` INT, `token` VARCHAR(100))   BEGIN	
    	DELETE FROM detalle_tempp WHERE id_tempp = id_detalle;
        
        SELECT tmp.id_tempp,tmp.nombre_pro, tmp.empresa, tmp.cantidad,tmp.precio_uni FROM detalle_tempp tmp WHERE tmp.token_usuario = token;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_venta` (IN `cod_usuario` INT, IN `cod_cliente` INT, IN `token` VARCHAR(50), IN `cod_tipo` INT)   BEGIN
    	DECLARE comprobante INT;
        
		DECLARE registros INT;
        DECLARE total DECIMAL(10,2);
        
        
        DECLARE tmp_cod_servicio int;
        DECLARE a int;
        SET a =1;
    
    	CREATE TEMPORARY TABLE tbl_tmp_tokenuser(
        		id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        		cod_ser BIGINT);
                
                
		SET registros = ( SELECT COUNT(*) FROM detalle_temp WHERE token_user = token);
        IF registros > 0 THEN 
        	INSERT INTO tbl_tmp_tokenuser(cod_ser) SELECT cod_servicio FROM detalle_temp WHERE token_user = token;
            
            INSERT INTO comprobante(id_usuario,id_cliente,id_tc)
            VALUES(cod_usuario,cod_cliente,cod_tipo);
            SET comprobante = LAST_INSERT_ID();
            INSERT INTO detalle_comprobante(id_comprobante, cod_servicio,temp_preciototal) SELECT (comprobante) AS id_comprobante, cod_servicio,temp_preciototal FROM 						detalle_temp WHERE token_user = token;
            WHILE a<= registros DO
            SELECT cod_ser INTO tmp_cod_servicio FROM tbl_tmp_tokenuser WHERE id = a;
			SET a=a+1;
            END WHILE;
            SET total = (SELECT SUM(temp_preciototal) FROM detalle_temp WHERE token_user = token);
            UPDATE comprobante SET com_totalfactura = total WHERE id_comprobante = comprobante;
            DELETE FROM detalle_temp WHERE token_user = token;
            TRUNCATE TABLE tbl_tmp_tokenuser;
            SELECT * FROM comprobante WHERE id_comprobante  = comprobante;
        ELSE
        	SELECT 0;
        END IF;
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `procesar_venta_egreso` (IN `cod_usuario` INT, IN `cod_usu` INT, IN `token` VARCHAR(100))   BEGIN 
    	DECLARE egreso INT;
        	
        DECLARE registro INT;
        DECLARE total DECIMAL(10,2);
        
        DECLARE tmp_nom_producto VARCHAR(100);
        DECLARE tmp_empresa VARCHAR(100);
        DECLARE tmp_preciouni DECIMAL(10,2);
        DECLARE tmp_cant_producto INT;
        DECLARE a INT;
        SET a = 1;
        
        CREATE TEMPORARY TABLE tbl_tmp_tokenusuario (
        	id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            empresa VARCHAR(100),
            nombre_prod VARCHAR(100),
            precio_uni DECIMAL(10,2),
            cant_prod INT);
        SET registro = (SELECT COUNT(*) FROM detalle_tempp WHERE token_usuario = token);
        IF registro > 0 THEN
        	INSERT INTO tbl_tmp_tokenusuario(empresa,nombre_prod,precio_uni,cant_prod) SELECT empresa,nombre_pro,precio_uni,cantidad FROM detalle_tempp WHERE token_usuario = 				token;
            
            INSERT INTO externo(id_usuario,id_usu) VALUES (cod_usuario,cod_usu);
            SET egreso = LAST_INSERT_ID();
            
            INSERT INTO detalle_externo(id_externo,empresa,nombre_pro,precio_uni,cantidad) SELECT (egreso) AS 						id_externo,empresa,nombre_pro,precio_uni,cantidad FROM detalle_tempp WHERE token_usuario = token;
            
            WHILE a <= registro DO
            	SELECT empresa,nombre_prod,precio_uni,cant_prod INTO tmp_empresa,tmp_nom_producto,tmp_preciouni,tmp_cant_producto FROM tbl_tmp_tokenusuario WHERE id = a;
                SET a=a+1;
            END WHILE;
            SET total = (SELECT SUM(cantidad * precio_uni) FROM detalle_tempp WHERE token_usuario = token);
            UPDATE externo SET ext_totalegreso = total WHERE id_externo	 = egreso;
            DELETE FROM detalle_tempp WHERE token_usuario = token;
            TRUNCATE TABLE tbl_tmp_tokenusuario;
            SELECT * FROM externo WHERE id_externo = egreso;
        ELSE  
        	SELECT 0;
        END IF;
    END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int NOT NULL,
  `id_tipodoc` int DEFAULT NULL,
  `cli_documento` varchar(11) NOT NULL,
  `cli_nombre` varchar(100) NOT NULL,
  `cli_telefono` varchar(9) NOT NULL,
  `cli_direccion` text NOT NULL,
  `cli_fechagr` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario` int NOT NULL,
  `cli_estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `id_tipodoc`, `cli_documento`, `cli_nombre`, `cli_telefono`, `cli_direccion`, `cli_fechagr`, `id_usuario`, `cli_estado`) VALUES
(1, NULL, '20480555598', 'WEBSITE MARKETING DIGITAL', '978888891', 'AV. Mariscal Nieto #480 Int. I-8 2piso - Lambayeque, Chiclayo,Chiclayo', '2023-04-28 11:53:12', 1, 1),
(2, NULL, '10454564311', 'El Horno Chiclayo', '982230621', 'Calle Las Diamelas N° 506 - Urb. Santa Victoria - Chiclayo', '2023-05-05 09:59:10', 1, 1),
(3, NULL, '11111111', 'website', '98736621', 'av marwuin', '2024-01-10 17:40:33', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobante`
--

CREATE TABLE `comprobante` (
  `id_comprobante` int NOT NULL,
  `com_fechaemi` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario` int NOT NULL,
  `id_cliente` int NOT NULL,
  `com_totalfactura` decimal(10,2) NOT NULL,
  `com_estado` int NOT NULL DEFAULT '1',
  `id_tc` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `comprobante`
--

INSERT INTO `comprobante` (`id_comprobante`, `com_fechaemi`, `id_usuario`, `id_cliente`, `com_totalfactura`, `com_estado`, `id_tc`) VALUES
(11, '2023-05-26 11:23:28', 2, 1, 2499.60, 1, 1),
(12, '2024-01-10 17:40:51', 2, 3, 549.90, 2, 2),
(13, '2024-07-28 10:10:00', 2, 3, 269.80, 1, 1),
(14, '2024-07-28 10:10:40', 2, 3, 1549.80, 1, 2),
(15, '2024-07-28 10:10:57', 2, 3, 249.90, 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id_configuracion` bigint NOT NULL,
  `confi_ndocumento` varchar(20) NOT NULL,
  `confi_nombrelegal` varchar(100) NOT NULL,
  `confi_nombrecomer` varchar(100) NOT NULL,
  `confi_telefono` bigint NOT NULL,
  `confi_correo` varchar(200) NOT NULL,
  `confi_direccion` varchar(100) NOT NULL,
  `confi_igv` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id_configuracion`, `confi_ndocumento`, `confi_nombrelegal`, `confi_nombrecomer`, `confi_telefono`, `confi_correo`, `confi_direccion`, `confi_igv`) VALUES
(1, '20480555598', 'WEBSITE CORPORATION E.I.R.L', 'WEBSITE MARKETING DIGITAL', 978888891, 'atencionalcliente@website.com.pe', 'AV. Mariscal Nieto #480 Int. I-8 2piso - Lambayeque, Chiclayo,Chiclayo', 18.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_comprobante`
--

CREATE TABLE `detalle_comprobante` (
  `id_deco` int NOT NULL,
  `id_comprobante` int NOT NULL,
  `cod_servicio` int NOT NULL,
  `temp_preciototal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `detalle_comprobante`
--

INSERT INTO `detalle_comprobante` (`id_deco`, `id_comprobante`, `cod_servicio`, `temp_preciototal`) VALUES
(78, 1, 12, 999.90),
(79, 2, 23, 449.90),
(80, 2, 40, 149.90),
(81, 3, 1, 549.90),
(82, 3, 12, 999.90),
(83, 3, 34, 199.90),
(84, 4, 1, 549.90),
(85, 5, 2, 699.90),
(86, 5, 5, 499.90),
(88, 6, 8, 1099.90),
(89, 7, 1, 549.90),
(90, 7, 5, 499.90),
(91, 8, 30, 399.90),
(92, 8, 4, 1199.90),
(94, 9, 1, 549.90),
(95, 9, 3, 799.90),
(96, 9, 5, 499.90),
(97, 10, 1, 549.90),
(98, 10, 2, 699.90),
(99, 11, 1, 549.90),
(100, 11, 2, 699.90),
(101, 11, 1, 549.90),
(102, 11, 2, 699.90),
(103, 12, 1, 549.90),
(104, 13, 54, 69.90),
(105, 13, 69, 199.90),
(107, 14, 9, 449.90),
(108, 14, 8, 1099.90),
(110, 15, 61, 249.90);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_temp`
--

CREATE TABLE `detalle_temp` (
  `id_temp` int NOT NULL,
  `token_user` varchar(50) NOT NULL,
  `cod_servicio` int NOT NULL,
  `temp_preciototal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `detalle_temp`
--

INSERT INTO `detalle_temp` (`id_temp`, `token_user`, `cod_servicio`, `temp_preciototal`) VALUES
(273, 'c4ca4238a0b923820dcc509a6f75849b', 2, 699.90);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egreso_fijo`
--

CREATE TABLE `egreso_fijo` (
  `id_fijo` int NOT NULL,
  `fj_servicio` varchar(100) NOT NULL,
  `fj_empresa` varchar(100) NOT NULL,
  `fj_descripcion` text NOT NULL,
  `fj_monto` decimal(10,2) NOT NULL,
  `fj_fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fj_estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `egreso_fijo`
--

INSERT INTO `egreso_fijo` (`id_fijo`, `fj_servicio`, `fj_empresa`, `fj_descripcion`, `fj_monto`, `fj_fecha`, `fj_estado`) VALUES
(1, 'full connected', 'Ninguna', 'rrgfdbdgd', 12.00, '2023-05-26 15:39:29', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egreso_personal`
--

CREATE TABLE `egreso_personal` (
  `id_egpe` int NOT NULL,
  `ep_nombre` varchar(100) NOT NULL,
  `ep_ndocumento` varchar(12) NOT NULL,
  `ep_cargo` varchar(100) NOT NULL,
  `ep_total` decimal(10,2) NOT NULL,
  `ep_fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `egreso_personal`
--

INSERT INTO `egreso_personal` (`id_egpe`, `ep_nombre`, `ep_ndocumento`, `ep_cargo`, `ep_total`, `ep_fecha`, `estado`) VALUES
(1, 'El Horno Chiclayo', '123456', 'trabajador', 200.30, '2023-05-26 13:22:52', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egreso_variable`
--

CREATE TABLE `egreso_variable` (
  `id_variable` int NOT NULL,
  `gastos` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `egreso_variable`
--

INSERT INTO `egreso_variable` (`id_variable`, `gastos`, `descripcion`, `total`, `fecha`, `estado`) VALUES
(1, 'qweqwe', 'dfgdgdf', 124.50, '2023-05-26 16:00:21', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrada`
--

CREATE TABLE `entrada` (
  `id_entrada` int NOT NULL,
  `cod_servicio` int NOT NULL,
  `ent_fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ent_precio` decimal(10,2) NOT NULL,
  `id_usuario` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ext`
--

CREATE TABLE `ext` (
  `id_externo` int NOT NULL,
  `id_tipod` int DEFAULT NULL,
  `n_documento` varchar(12) NOT NULL,
  `razon_social` varchar(100) NOT NULL,
  `telefono` varchar(9) NOT NULL,
  `ciudad` varchar(50) NOT NULL,
  `departamento` varchar(50) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `detalle` text NOT NULL,
  `fechaemi` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `ext`
--

INSERT INTO `ext` (`id_externo`, `id_tipod`, `n_documento`, `razon_social`, `telefono`, `ciudad`, `departamento`, `direccion`, `subtotal`, `igv`, `total`, `detalle`, `fechaemi`, `estado`) VALUES
(1, NULL, 'asa', 'as', '12', 'qwqw', 'qwqw', 'wqwq', 12.00, 12.00, 12.00, 'qwqw', '2023-05-12 16:06:52', 1),
(2, NULL, '2222', 'prueba', '123456', 'de', 'de', 'calle los inkas #458', 123.00, 21.00, 12.00, 'detalle', '2023-05-12 16:07:10', 1),
(3, NULL, '12', 'prueba', '123456', 'de', 'de', 'calle los inkas #458', 12.00, 12.00, 12.00, 'sdfddsd', '2023-05-12 16:10:58', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int NOT NULL,
  `rol` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Asistente'),
(3, 'Cajero'),
(4, 'Vendedor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `cod_servicio` int NOT NULL,
  `servi_nombre` varchar(100) NOT NULL,
  `id_tiposer` int NOT NULL,
  `tiempo` varchar(2) NOT NULL,
  `servi_precio` decimal(10,2) NOT NULL,
  `id_usuario` int NOT NULL,
  `servi_fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `servi_estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`cod_servicio`, `servi_nombre`, `id_tiposer`, `tiempo`, `servi_precio`, `id_usuario`, `servi_fecha`, `servi_estado`) VALUES
(1, 'NUEVO PLAN SOCIAL MEDIA I', 1, '3', 549.90, 1, '2023-04-28 10:52:02', 0),
(2, 'NUEVO PLAN SOCIAL MEDIA VII', 1, '3', 999.90, 1, '2023-04-28 10:52:36', 1),
(3, 'NUEVO PLAN SOCIAL MEDIA III', 1, '3', 799.90, 1, '2023-04-28 10:52:49', 1),
(4, 'NUEVO PLAN SOCIAL MEDIA IV', 1, '3', 1199.90, 1, '2023-04-28 10:53:05', 1),
(5, 'NUEVO PLAN SOCIAL MEDIA I', 1, '6', 499.90, 1, '2023-04-28 10:53:19', 1),
(6, 'NUEVO PLAN SOCIAL MEDIA II', 1, '6', 649.90, 1, '2023-04-28 10:53:32', 1),
(7, 'NUEVO PLAN SOCIAL MEDIA III', 1, '6', 749.90, 1, '2023-04-28 10:54:49', 1),
(8, 'NUEVO PLAN SOCIAL MEDIA IV', 1, '6', 1099.90, 1, '2023-04-28 10:55:05', 1),
(9, 'NUEVO PLAN SOCIAL MEDIA I', 1, '12', 449.90, 1, '2023-04-28 10:55:21', 1),
(10, 'NUEVO PLAN SOCIAL MEDIA II', 1, '12', 599.90, 1, '2023-04-28 10:55:43', 1),
(11, 'NUEVO PLAN SOCIAL MEDIA III', 1, '12', 699.90, 1, '2023-04-28 10:55:58', 1),
(12, 'NUEVO PLAN SOCIAL MEDIA IV', 1, '12', 999.90, 1, '2023-04-28 10:56:19', 1),
(13, 'NUEVO PACK FLYERS I (10 FLYERS)', 2, '3', 349.90, 1, '2023-04-28 10:57:25', 1),
(14, 'NUEVO PACK FLYERS II (20 FLYERS)', 2, '3', 449.90, 1, '2023-04-28 10:57:41', 1),
(15, 'NUEVO PACK FLYERS III (30 FLYERS)', 2, '3', 549.90, 1, '2023-04-28 10:57:55', 1),
(16, 'NUEVO PACK FLYERS IV (40 FLYERS)', 2, '3', 649.90, 1, '2023-04-28 10:58:48', 1),
(17, 'NUEVO PACK FLYERS I (10 FLYERS)', 2, '6', 299.90, 1, '2023-04-28 11:00:06', 1),
(18, 'NUEVO PACK FLYERS II (20 FLYERS)', 2, '6', 399.90, 1, '2023-04-28 11:00:29', 1),
(19, 'NUEVO PACK FLYERS III (30 FLYERS)', 2, '6', 499.90, 1, '2023-04-28 11:00:45', 1),
(20, 'NUEVO PACK FLYERS IV (40 FLYERS)', 2, '6', 599.90, 1, '2023-04-28 11:00:57', 1),
(21, 'NUEVO PACK FLYERS I (10 FLYERS)', 2, '12', 259.90, 1, '2023-04-28 11:01:21', 1),
(22, 'NUEVO PACK FLYERS II (20 FLYERS)', 2, '12', 349.90, 1, '2023-04-28 11:03:19', 1),
(23, 'NUEVO PACK FLYERS III (30 FLYERS)', 2, '12', 449.90, 1, '2023-04-28 11:03:37', 1),
(24, 'NUEVO PACK FLYERS IV (40 FLYERS)', 2, '12', 549.90, 1, '2023-04-28 11:03:48', 1),
(25, 'NUEVO PLAN FULL CONNECTED I', 3, '12', 699.90, 1, '2023-04-28 11:04:12', 1),
(26, 'NUEVO PLAN FULL CONNECTED II', 3, '12', 899.90, 1, '2023-04-28 11:04:50', 1),
(27, 'NUEVO PLAN FULL CONNECTED III', 3, '12', 1099.90, 1, '2023-04-28 11:05:05', 1),
(28, 'NUEVO PLAN FULL CONNECTED IV', 3, '12', 1299.90, 1, '2023-04-28 11:05:20', 1),
(29, 'HOSTING BUSINESS I', 4, '12', 299.90, 1, '2023-04-28 11:05:39', 1),
(30, 'HOSTING BUSINESS II', 4, '12', 399.90, 1, '2023-04-28 11:05:53', 1),
(31, 'HOSTING BUSINESS III', 4, '12', 499.90, 1, '2023-04-28 11:06:05', 1),
(32, 'HOSTING BUSINESS IV', 4, '12', 699.90, 1, '2023-04-28 11:06:17', 1),
(33, 'DOMINIO .COM / .BIZ / .NET / .TV / .INFO', 5, '12', 99.90, 1, '2023-04-28 11:09:49', 1),
(34, 'DOMINIO .COM.PE / .PE ', 5, '12', 199.90, 1, '2023-04-28 11:10:01', 1),
(35, 'DOMINIO . EDU.PE / .GOB.PE', 5, '12', 199.90, 1, '2023-04-28 11:10:18', 1),
(36, 'WEBSITE BUSINESS LITE', 6, 'SC', 449.90, 1, '2023-04-28 11:33:37', 1),
(37, 'WEBSITE BUSINESS PRO', 6, 'SC', 999.90, 1, '2023-04-28 12:08:28', 1),
(38, 'WEBSITE BUSINESS PLUS', 6, 'SC', 1899.90, 1, '2023-04-28 12:24:12', 1),
(39, 'WEBSITE BUSINESS MEGA', 6, 'SC', 2799.90, 1, '2023-04-28 12:24:28', 1),
(40, 'VÍDEO DE 15\" OFF (IMÁGENES + TEXTO)', 7, 'SC', 149.90, 1, '2023-04-28 12:24:46', 1),
(41, 'VIDEO DE 30\" OFF (IMÁGENES + TEXTO)', 7, 'SC', 199.90, 1, '2023-04-28 12:25:02', 1),
(42, 'VIDEO DE 60\" OFF (IMÁGENES + TEXTO)', 7, 'SC', 2499.90, 1, '2023-04-28 12:25:30', 1),
(43, 'VIDEO DE 2\" OFF (IMÁGENES + TEXTO)', 7, 'SC', 299.90, 1, '2023-04-28 12:25:47', 1),
(44, 'VÍDEO DE 15\" ON (IMÁGENES O FILMACIÓN  + TEXTO)', 7, 'SC', 249.90, 1, '2023-04-28 12:26:19', 1),
(45, 'VIDEO DE 30\" ON (IMÁGENES  O FILMACIÓN + TEXTO)', 7, 'SC', 299.90, 1, '2023-04-28 12:26:39', 1),
(46, 'VIDEO DE 60\" ON (IMÁGENES  O FILMACIÓN + TEXTO)', 7, 'SC', 349.00, 1, '2023-04-28 12:27:10', 1),
(47, 'VIDEO DE 2\" ON (IMÁGENES  O FILMACIÓN  + TEXTO)', 7, 'SC', 399.90, 1, '2023-04-28 12:27:39', 1),
(48, 'FOTOGRAFIA + EDICIÓN (10 FOTOS)', 12, 'SC', 149.90, 1, '2023-04-28 12:28:05', 1),
(49, 'FOTOGRAFIA + EDICIÓN + ILUMINACIÓN (10 FOTOS)', 12, 'SC', 199.90, 1, '2023-04-28 12:28:29', 1),
(50, 'FOTOGRAFIA + EDICIÓN (20 FOTOS)', 12, 'SC', 249.90, 1, '2023-04-28 12:28:43', 1),
(51, 'FOTOGRAFIA + EDICIÓN + ILUMINACIÓN (20 FOTOS)', 12, 'SC', 299.90, 1, '2023-04-28 12:28:57', 1),
(52, 'FOTOGRAFIA + EDICIÓN (30 FOTOS)', 12, 'SC', 349.00, 1, '2023-04-28 12:29:12', 1),
(53, 'FOTOGRAFIA + EDICIÓN + ILUMINACIÓN (30 FOTOS)', 12, 'SC', 399.00, 1, '2023-04-28 12:29:28', 1),
(54, 'DISEÑO FORMATO FACEBOOK (FLYERS)', 8, 'SC', 69.90, 1, '2023-04-28 12:30:08', 1),
(55, 'DISEÑO CORP. TARJETA PERSONALES', 8, 'SC', 69.90, 1, '2023-04-28 12:30:21', 1),
(56, 'DISEÑO CORP. DE FOTOCHECK', 8, 'SC', 69.90, 1, '2023-04-28 12:30:38', 1),
(57, 'DISEÑO CORP. DE FOLDERS', 8, 'SC', 69.90, 1, '2023-04-28 12:30:51', 1),
(58, 'DISEÑO CORP.  DE HOJAS MEMBRETADAS', 8, 'SC', 69.90, 1, '2023-04-28 12:31:01', 1),
(59, 'DISEÑO CORP.  BANNER DIGITALES', 8, 'SC', 69.90, 1, '2023-04-28 12:31:11', 1),
(60, 'DISEÑO DE GIGANTOGRAFIA', 8, 'SC', 99.90, 1, '2023-04-28 12:31:26', 1),
(61, 'DISEÑO CORP. DE BROCHURE A4 (5 Hojas)', 8, 'SC', 249.90, 1, '2023-04-28 12:31:41', 1),
(62, 'DISEÑO CORP. DE UNIFORMES (CAMISA, POLO, PANTALON / H-M)', 8, 'SC', 249.90, 1, '2023-04-28 12:31:53', 1),
(63, 'CREACIÓN DE SIMPLE', 9, 'SC', 299.00, 1, '2023-04-28 12:32:11', 1),
(64, 'CREACIÓN DE LOGO PRO', 9, 'SC', 499.00, 1, '2023-04-28 12:32:23', 1),
(65, 'CREACIÓN DE LOGO PRO + MANUAL DE IDENTIDIAD', 9, 'SC', 799.90, 1, '2023-04-28 12:32:39', 1),
(66, 'CREACIÓN DE LOGO + MANUAL + DISEÑOS CORPORATIVOS', 9, 'SC', 999.90, 1, '2023-04-28 12:32:50', 1),
(67, 'MESSENGER BOTS - TEXTO - (PAGO ÚNICO)', 10, 'SC', 199.90, 1, '2023-04-28 12:33:06', 1),
(68, 'MESSENGER BOTS - TEXTO + IMAGENES (PAGO ÚNICO)', 10, 'SC', 499.90, 1, '2023-04-28 12:33:20', 1),
(69, 'GESTIÓN Y ASESORIA DE CAMPAÑAS ADS', 11, 'SC', 199.90, 1, '2023-04-28 12:33:34', 1),
(70, 'GESTIÓN DE CREACIÓN DE PLAN DE MARKETING DIGITAL', 11, 'SC', 99.90, 1, '2023-04-28 12:33:46', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_servicio`
--

CREATE TABLE `tipo_servicio` (
  `id_tiposer` int NOT NULL,
  `tise_nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tipo_servicio`
--

INSERT INTO `tipo_servicio` (`id_tiposer`, `tise_nombre`) VALUES
(1, 'PLANES DE ADMIN. REDES SOCIALES'),
(2, 'PACK FLYERS'),
(3, 'NUEVOS PLANES FULL CONNECTD'),
(4, 'HOSTING LINUX'),
(5, 'DOMINIOS'),
(6, 'PLANES DE PAGINAS WEB'),
(7, 'VIDEOS DE CONTENIDOS'),
(8, 'DISEÑO GRÁFICO'),
(9, 'BRANDING Y RE-BRANDING'),
(10, 'CHATS BOTS'),
(11, 'SERVICIOS PROFESIONALES'),
(12, 'SESIÓN FOTOGRÁFICAS PARA CONTENIDOS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ti_comprobante`
--

CREATE TABLE `ti_comprobante` (
  `id_tc` int NOT NULL,
  `nombre` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `ti_comprobante`
--

INSERT INTO `ti_comprobante` (`id_tc`, `nombre`) VALUES
(1, 'BOLETA'),
(2, 'FACTURA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ti_documento`
--

CREATE TABLE `ti_documento` (
  `id` int NOT NULL,
  `k` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int NOT NULL,
  `usu_nombre` varchar(50) NOT NULL,
  `usu_correo` varchar(100) NOT NULL,
  `usu_usuario` varchar(15) NOT NULL,
  `usu_clave` varchar(100) NOT NULL,
  `id_rol` int NOT NULL,
  `usu_estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `usu_nombre`, `usu_correo`, `usu_usuario`, `usu_clave`, `id_rol`, `usu_estado`) VALUES
(1, 'Manuel Castillo', 'mcastillo@website.com.pe', 'mcastillo', '471c63b3d4bf35332d078733c51dd932', 1, 1),
(2, 'admin', 'ce@gmail.com', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, 1),
(3, 'asistente', 'prueba2@gmail.com', 'asistente', '15028d82f1f887339fe4d4c9c2b58b5f', 2, 1),
(4, 'cajero', 'cajero@gmail.com', 'cajero', 'f80bb5a954ee71b40f1c31b79734d82d', 3, 1),
(5, 'vendedor', 'vendedor@gmail.com', 'vendedor', '0407e8c8285ab85509ac2884025dcf42', 4, 0),
(7, 'asdasd', 'aasdasdsa', 'asdasd', 'asdasd', 1, 1),
(8, '', 'asdasd', 'asdasd', 'asdasd', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usu_egreso`
--

CREATE TABLE `usu_egreso` (
  `id_usu` int NOT NULL,
  `u_documento` varchar(12) NOT NULL,
  `u_nombre` varchar(100) NOT NULL,
  `u_telefono` varchar(9) NOT NULL,
  `u_direccion` text NOT NULL,
  `u_fechaing` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario` int NOT NULL,
  `u_estado` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  ADD PRIMARY KEY (`id_comprobante`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_tc` (`id_tc`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id_configuracion`);

--
-- Indices de la tabla `detalle_comprobante`
--
ALTER TABLE `detalle_comprobante`
  ADD PRIMARY KEY (`id_deco`),
  ADD KEY `id_comprobante` (`id_comprobante`),
  ADD KEY `cod_servicio` (`cod_servicio`);

--
-- Indices de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  ADD PRIMARY KEY (`id_temp`),
  ADD KEY `token_user` (`token_user`),
  ADD KEY `cod_servicio` (`cod_servicio`);

--
-- Indices de la tabla `egreso_fijo`
--
ALTER TABLE `egreso_fijo`
  ADD PRIMARY KEY (`id_fijo`);

--
-- Indices de la tabla `egreso_personal`
--
ALTER TABLE `egreso_personal`
  ADD PRIMARY KEY (`id_egpe`);

--
-- Indices de la tabla `egreso_variable`
--
ALTER TABLE `egreso_variable`
  ADD PRIMARY KEY (`id_variable`);

--
-- Indices de la tabla `entrada`
--
ALTER TABLE `entrada`
  ADD PRIMARY KEY (`id_entrada`),
  ADD KEY `cod_servicio` (`cod_servicio`);

--
-- Indices de la tabla `ext`
--
ALTER TABLE `ext`
  ADD PRIMARY KEY (`id_externo`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`cod_servicio`),
  ADD KEY `id_tiposer` (`id_tiposer`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `tipo_servicio`
--
ALTER TABLE `tipo_servicio`
  ADD PRIMARY KEY (`id_tiposer`);

--
-- Indices de la tabla `ti_comprobante`
--
ALTER TABLE `ti_comprobante`
  ADD PRIMARY KEY (`id_tc`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `usu_egreso`
--
ALTER TABLE `usu_egreso`
  ADD PRIMARY KEY (`id_usu`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `comprobante`
--
ALTER TABLE `comprobante`
  MODIFY `id_comprobante` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id_configuracion` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_comprobante`
--
ALTER TABLE `detalle_comprobante`
  MODIFY `id_deco` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de la tabla `detalle_temp`
--
ALTER TABLE `detalle_temp`
  MODIFY `id_temp` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=346;

--
-- AUTO_INCREMENT de la tabla `egreso_fijo`
--
ALTER TABLE `egreso_fijo`
  MODIFY `id_fijo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `egreso_personal`
--
ALTER TABLE `egreso_personal`
  MODIFY `id_egpe` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `egreso_variable`
--
ALTER TABLE `egreso_variable`
  MODIFY `id_variable` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `entrada`
--
ALTER TABLE `entrada`
  MODIFY `id_entrada` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ext`
--
ALTER TABLE `ext`
  MODIFY `id_externo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `cod_servicio` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `tipo_servicio`
--
ALTER TABLE `tipo_servicio`
  MODIFY `id_tiposer` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `ti_comprobante`
--
ALTER TABLE `ti_comprobante`
  MODIFY `id_tc` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usu_egreso`
--
ALTER TABLE `usu_egreso`
  MODIFY `id_usu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comprobante`
--
ALTER TABLE `comprobante`
  ADD CONSTRAINT `comprobante_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comprobante_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comprobante_ibfk_3` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comprobante_ibfk_4` FOREIGN KEY (`id_tc`) REFERENCES `ti_comprobante` (`id_tc`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
