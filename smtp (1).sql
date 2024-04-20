-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 20-04-2024 a las 00:37:11
-- Versión del servidor: 8.0.31
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `smtp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `advertencia`
--

DROP TABLE IF EXISTS `advertencia`;
CREATE TABLE IF NOT EXISTS `advertencia` (
  `id_advertencia` int NOT NULL AUTO_INCREMENT,
  `id_conductor` varchar(18) NOT NULL,
  `n_advertencias` int NOT NULL,
  PRIMARY KEY (`id_advertencia`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `advertencia`
--

INSERT INTO `advertencia` (`id_advertencia`, `id_conductor`, `n_advertencias`) VALUES
(4, 'DEMO1456YGHKTYH678', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cap_time_viajes`
--

DROP TABLE IF EXISTS `cap_time_viajes`;
CREATE TABLE IF NOT EXISTS `cap_time_viajes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `conductor` varchar(18) DEFAULT NULL,
  `id_horario` int DEFAULT NULL,
  `id_viaje` int DEFAULT NULL,
  `id_ruta` int DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `T1` time DEFAULT NULL,
  `T2` time DEFAULT NULL,
  `T3` time DEFAULT NULL,
  `T4` time DEFAULT NULL,
  `T5` time DEFAULT NULL,
  `T6` time DEFAULT NULL,
  `T7` time DEFAULT NULL,
  `T8` time DEFAULT NULL,
  `Retardos` int DEFAULT NULL,
  `Estatus` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_horario` (`id_horario`),
  KEY `id_viaje` (`id_viaje`),
  KEY `id_ruta` (`id_ruta`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cap_time_viajes`
--

INSERT INTO `cap_time_viajes` (`id`, `conductor`, `id_horario`, `id_viaje`, `id_ruta`, `Fecha`, `T1`, `T2`, `T3`, `T4`, `T5`, `T6`, `T7`, `T8`, `Retardos`, `Estatus`) VALUES
(37, 'DEMO1456YGHKTYH678', 1, 53, 5, '2024-04-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(41, 'KAGS981107MCLLRLA3', 2, 57, 5, '2024-04-05', '07:00:00', '07:16:12', '07:32:23', '07:52:54', '08:03:32', '08:17:51', '08:36:32', '08:48:32', 2, 'Finalizado'),
(42, 'XEKP890214HMCNSNB6', 3, 58, 5, '2024-04-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(43, 'ZWDL760330HDFMTRC9', 4, 59, 5, '2024-04-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(46, 'DEMO1456YGHKTYH678', 2, 62, 6, '2024-04-05', '07:00:00', '07:17:32', '07:36:23', '07:51:54', '08:05:32', '08:16:51', '08:36:32', '08:51:32', 5, 'Finalizado'),
(47, 'DEMO1456YGHKTYH678', 3, 63, 7, '2024-04-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(48, 'DEMO1456YGHKTYH678', 4, 64, 8, '2024-04-05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '');

--
-- Disparadores `cap_time_viajes`
--
DROP TRIGGER IF EXISTS `actualizar_advertencia`;
DELIMITER $$
CREATE TRIGGER `actualizar_advertencia` AFTER UPDATE ON `cap_time_viajes` FOR EACH ROW BEGIN
    -- Declarar variable para verificar la existencia de una entrada para el conductor
    SET @existe = (SELECT COUNT(*) FROM advertencia WHERE id_conductor = NEW.conductor);
    
    -- Verificar si el nuevo valor de retardos es mayor o igual a 3
    IF NEW.retardos >= 3 THEN
        -- Si existe una entrada para el conductor, actualizar el número de advertencias
        IF @existe > 0 THEN
            UPDATE advertencia SET n_advertencias = n_advertencias + 1 WHERE id_conductor = NEW.conductor;
        ELSE
            -- Si no existe una entrada para el conductor, insertar una nueva entrada en la tabla "advertencia"
            INSERT INTO advertencia (id_conductor, n_advertencias) VALUES (NEW.conductor, 1);
        END IF;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `actualizar_estatus_viaje`;
DELIMITER $$
CREATE TRIGGER `actualizar_estatus_viaje` AFTER UPDATE ON `cap_time_viajes` FOR EACH ROW BEGIN
    IF NEW.Estatus = 'Finalizado' THEN
        -- Actualizar el campo Estatus en la tabla creacion_viajes
        UPDATE creacion_viajes SET Estatus = 'Finalizado' WHERE id_viaje = NEW.id_viaje;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `actualizar_y_bloquear`;
DELIMITER $$
CREATE TRIGGER `actualizar_y_bloquear` AFTER UPDATE ON `cap_time_viajes` FOR EACH ROW BEGIN
    DECLARE num_advertencias INT;
    
    -- Obtener el número actual de advertencias para el conductor
    SELECT n_advertencias INTO num_advertencias
    FROM advertencia
    WHERE id_conductor = NEW.conductor;
    
    -- Actualizar el número de advertencias si el número de retardos es mayor o igual a 3
    IF NEW.retardos >= 3 THEN
        -- Verificar si ya existe una entrada para este conductor en la tabla "advertencia"
        IF num_advertencias >= 3 THEN
            -- Insertar en la tabla "conductores_bloqueados" si el número de advertencias es mayor o igual a 3
            INSERT INTO conductores_bloqueados (id_conductor, id_viaje, fecha)
            VALUES (NEW.conductor, NEW.id_viaje, CURDATE());
        ELSE
            -- Si no se excede el límite de advertencias, actualizar el número de advertencias
            UPDATE advertencia SET n_advertencias = n_advertencias + 1 WHERE id_conductor = NEW.conductor;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conductores_bloqueados`
--

DROP TABLE IF EXISTS `conductores_bloqueados`;
CREATE TABLE IF NOT EXISTS `conductores_bloqueados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_conductor` varchar(18) DEFAULT NULL,
  `id_viaje` int DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `creacion_viajes`
--

DROP TABLE IF EXISTS `creacion_viajes`;
CREATE TABLE IF NOT EXISTS `creacion_viajes` (
  `id_viaje` int NOT NULL AUTO_INCREMENT,
  `conductor` varchar(18) DEFAULT NULL,
  `id_vehiculo` int DEFAULT NULL,
  `id_ruta` int DEFAULT NULL,
  `Horario` int DEFAULT NULL,
  `Fecha` date NOT NULL,
  `Estatus` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_viaje`),
  KEY `id_ruta` (`id_ruta`),
  KEY `creacion_viajes_ibfk_2` (`id_vehiculo`),
  KEY `conductor` (`conductor`),
  KEY `Horario` (`Horario`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `creacion_viajes`
--

INSERT INTO `creacion_viajes` (`id_viaje`, `conductor`, `id_vehiculo`, `id_ruta`, `Horario`, `Fecha`, `Estatus`) VALUES
(53, 'DEMO1456YGHKTYH678', 2, 5, 1, '2024-04-05', NULL),
(57, 'KAGS981107MCLLRLA3', 4, 5, 2, '2024-04-05', 'Finalizado'),
(58, 'XEKP890214HMCNSNB6', 5, 5, 3, '2024-04-05', NULL),
(59, 'ZWDL760330HDFMTRC9', 2, 5, 4, '2024-04-05', NULL),
(62, 'DEMO1456YGHKTYH678', 2, 6, 2, '2024-04-05', 'Finalizado'),
(63, 'DEMO1456YGHKTYH678', 2, 7, 3, '2024-04-05', NULL),
(64, 'DEMO1456YGHKTYH678', 7, 8, 4, '2024-04-05', NULL);

--
-- Disparadores `creacion_viajes`
--
DROP TRIGGER IF EXISTS `after_insert_creacion_viajes`;
DELIMITER $$
CREATE TRIGGER `after_insert_creacion_viajes` AFTER INSERT ON `creacion_viajes` FOR EACH ROW BEGIN
    INSERT INTO cap_time_viajes ( conductor, id_horario, id_viaje,id_ruta, Fecha)
    VALUES ( NEW.conductor, NEW.Horario, NEW.id_viaje,NEW.id_ruta, NEW.Fecha);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

DROP TABLE IF EXISTS `empleados`;
CREATE TABLE IF NOT EXISTS `empleados` (
  `CURP` varchar(18) NOT NULL,
  `Nombre` varchar(50) DEFAULT NULL,
  `ApellidoP` varchar(50) DEFAULT NULL,
  `ApellidoM` varchar(50) DEFAULT NULL,
  `Domicilio` text,
  `Telefono` varchar(20) DEFAULT NULL,
  `Puesto` int DEFAULT NULL,
  `Clave` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`CURP`),
  KEY `Puesto` (`Puesto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`CURP`, `Nombre`, `ApellidoP`, `ApellidoM`, `Domicilio`, `Telefono`, `Puesto`, `Clave`) VALUES
('AATF020530HMCPLLA0', 'Hiram Jafet', 'Velazquez', 'Santander', 'Tecamac, edo. mex', '5443434343', 2, '123'),
('DEMO1456YGHKTYH678', 'Fernando', 'Sanchez', 'Perez', 'av. 5 de mayo, tecamac. edo.mex', '5543442232', 3, '123'),
('KAGS981107MCLLRLA3', 'Jorge', 'Servin ', 'Alvarez', 'Calle Ficticia #123, Colonia Imaginaria, Ciudad Falsa, Estado Inexistente, Código Postal: 12345', '5543251782', 3, '123'),
('TAFA020530HMCPLLA0', 'Alexis', 'Tapia', 'Flores', 'Jimenez', '5583928042', 1, '123'),
('XEKP890214HMCNSNB6', 'Daniel', 'Santiago', 'Cruz', 'Avenida de los Sueños #456, Barrio Irreal, Pueblo Fantasma, Región Ficticia, Código Postal: 54321', '5567894311', 3, '123'),
('ZWDL760330HDFMTRC9', 'Humberto', 'Piñon', 'Dominguez', 'Calle de la Ilusión #789, Colonia de Ensueño, Ciudad de los Deseos, Estado Imaginario, Código Postal: 67890', '5541232343', 3, '123');

--
-- Disparadores `empleados`
--
DROP TRIGGER IF EXISTS `after_delete_empleado`;
DELIMITER $$
CREATE TRIGGER `after_delete_empleado` AFTER DELETE ON `empleados` FOR EACH ROW BEGIN
    DELETE FROM usuarios WHERE CURP = OLD.CURP;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_insert_empleado`;
DELIMITER $$
CREATE TRIGGER `after_insert_empleado` AFTER INSERT ON `empleados` FOR EACH ROW BEGIN
    INSERT INTO usuarios (CURP, Clave, puesto) VALUES (NEW.CURP, NEW.clave, NEW.puesto);
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_update_empleado`;
DELIMITER $$
CREATE TRIGGER `after_update_empleado` AFTER UPDATE ON `empleados` FOR EACH ROW BEGIN
    UPDATE usuarios 
    SET CURP = NEW.CURP, Clave = NEW.Clave, puesto = NEW.Puesto
    WHERE CURP = OLD.CURP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estaciones`
--

DROP TABLE IF EXISTS `estaciones`;
CREATE TABLE IF NOT EXISTS `estaciones` (
  `id_estacion` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) DEFAULT NULL,
  `id_rutas` int DEFAULT NULL,
  `QR` varchar(50) DEFAULT NULL,
  `imagen` varchar(60) NOT NULL,
  PRIMARY KEY (`id_estacion`),
  KEY `id_rutas` (`id_rutas`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `estaciones`
--

INSERT INTO `estaciones` (`id_estacion`, `nombre`, `id_rutas`, `QR`, `imagen`) VALUES
(20, 'Terminal Ojo de Agua', 5, 'QR_estaciones/qr_estacion_20.png', 'imagenes/ojo de agua.png'),
(21, 'boulevard Ojo de Agua', 5, 'QR_estaciones/qr_estacion_21.png', 'imagenes/ojo de agua 2.png'),
(22, 'Tonanitla', 5, 'QR_estaciones/qr_estacion_22.png', 'imagenes/tonanitla.jpeg'),
(23, 'Xaltocan', 5, 'QR_estaciones/qr_estacion_23.png', 'imagenes/xaltocan.jpeg'),
(24, 'San Francisco Nextlalpan', 5, 'QR_estaciones/qr_estacion_24.png', 'imagenes/san francisco N.png'),
(25, 'Santa Ana Nextlalpan', 5, 'QR_estaciones/qr_estacion_25.png', 'imagenes/santa ana.jpeg'),
(26, 'San andres Jaltenco', 5, 'QR_estaciones/qr_estacion_26.png', 'imagenes/san andres.jpeg'),
(27, 'Zumpango', 5, 'QR_estaciones/qr_estacion_27.png', 'imagenes/zumpango.png'),
(28, 'Zumpango de Ocampo', 6, 'QR_estaciones/qr_estacion_28.png', 'imagenes/zumpango.png'),
(29, 'Paseos del Lago II', 6, 'QR_estaciones/qr_estacion_29.png', 'imagenes/paseos.png'),
(30, 'Los Reyes Acozac', 6, 'QR_estaciones/qr_estacion_30.png', 'imagenes/reyes.jpeg'),
(31, 'Tecamac', 6, 'QR_estaciones/qr_estacion_31.png', 'imagenes/tecamac.jpeg'),
(32, 'San Francisco', 6, 'QR_estaciones/qr_estacion_32.png', 'imagenes/san francisco T.png'),
(33, 'Ozumbilla', 6, 'QR_estaciones/qr_estacion_33.png', 'imagenes/ozumbilla.png'),
(34, 'Loma Bonita', 6, 'QR_estaciones/qr_estacion_34.png', 'imagenes/loma bonita.jpeg'),
(35, 'Terminal Ojo de Agua', 6, 'QR_estaciones/qr_estacion_35.png', 'imagenes/ojo de agua.png'),
(36, 'Terminal Ojo de Agua', 7, 'QR_estaciones/qr_estacion_36.png', 'imagenes/ojo de agua.png'),
(37, 'Ozumbilla', 7, 'QR_estaciones/qr_estacion_37.png', 'imagenes/ozumbilla.png'),
(38, 'San Francisco', 7, 'QR_estaciones/qr_estacion_38.png', 'imagenes/san francisco T.png'),
(39, 'Quetzalcóatl', 7, 'QR_estaciones/qr_estacion_39.png', 'imagenes/quetza.jpg'),
(40, 'Tecamac', 7, 'QR_estaciones/qr_estacion_40.png', 'imagenes/tecamac.jpeg'),
(41, 'Central de Abastos', 8, 'QR_estaciones/qr_estacion_41.png', 'imagenes/central de abastos.png'),
(42, 'Loma Bonita', 8, 'QR_estaciones/qr_estacion_42.png', 'imagenes/loma bonita.jpeg'),
(43, 'Ozumbilla', 8, 'QR_estaciones/qr_estacion_43.png', 'imagenes/ozumbilla.png'),
(44, 'San Francisco', 8, 'QR_estaciones/qr_estacion_44.png', 'imagenes/san francisco T.png'),
(45, 'Tecamac', 8, NULL, 'imagenes/tecamac.jpeg'),
(51, 'Reyes acozac', 8, NULL, 'imagenes/reyes.jpeg'),
(52, 'Paseos del Lago II', 8, NULL, 'imagenes/paseos.png'),
(53, 'Zumpango', 8, NULL, 'imagenes/zumpango.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario`
--

DROP TABLE IF EXISTS `horario`;
CREATE TABLE IF NOT EXISTS `horario` (
  `id` int NOT NULL,
  `horario` varchar(10) NOT NULL,
  `T1` time NOT NULL,
  `T2` time NOT NULL,
  `T3` time NOT NULL,
  `T4` time NOT NULL,
  `T5` time NOT NULL,
  `T6` time NOT NULL,
  `T7` time NOT NULL,
  `T8` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `horario`
--

INSERT INTO `horario` (`id`, `horario`, `T1`, `T2`, `T3`, `T4`, `T5`, `T6`, `T7`, `T8`) VALUES
(1, '5-7', '05:00:00', '05:15:00', '05:30:00', '05:45:00', '06:00:00', '06:15:00', '06:30:00', '06:45:00'),
(2, '7-9', '07:00:00', '07:15:00', '07:30:00', '07:45:00', '08:00:00', '08:15:00', '08:30:00', '08:45:00'),
(3, '9-11', '09:00:00', '09:15:00', '09:30:00', '09:45:00', '10:00:00', '10:15:00', '10:30:00', '10:45:00'),
(4, '11-13', '11:00:00', '11:15:00', '11:30:00', '11:45:00', '12:00:00', '12:15:00', '12:30:00', '12:45:00'),
(5, '13-15', '13:00:00', '13:15:00', '13:30:00', '13:45:00', '14:00:00', '14:15:00', '14:30:00', '14:45:00'),
(6, '15-17', '15:00:00', '15:15:00', '15:30:00', '15:45:00', '16:00:00', '16:15:00', '16:30:00', '16:45:00'),
(7, '17-19', '17:00:00', '17:15:00', '17:30:00', '17:45:00', '18:00:00', '18:15:00', '18:30:00', '18:45:00'),
(8, '19-21', '19:00:00', '19:15:00', '19:30:00', '19:45:00', '20:00:00', '20:15:00', '20:30:00', '20:45:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista_retardos`
--

DROP TABLE IF EXISTS `lista_retardos`;
CREATE TABLE IF NOT EXISTS `lista_retardos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_conductor` varchar(18) DEFAULT NULL,
  `id_viaje` int DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_conductor` (`id_conductor`),
  KEY `id_viaje` (`id_viaje`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `lista_retardos`
--

INSERT INTO `lista_retardos` (`id`, `id_conductor`, `id_viaje`, `fecha`) VALUES
(12, 'KAGS981107MCLLRLA3', 57, '2024-04-05'),
(13, 'DEMO1456YGHKTYH678', 62, '2024-04-05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recorrido`
--

DROP TABLE IF EXISTS `recorrido`;
CREATE TABLE IF NOT EXISTS `recorrido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_viaje` int DEFAULT NULL,
  `registro` int DEFAULT NULL,
  `informacion` text,
  PRIMARY KEY (`id`),
  KEY `id_viaje` (`id_viaje`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id_roles` int NOT NULL AUTO_INCREMENT,
  `puesto` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_roles`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_roles`, `puesto`) VALUES
(1, 'Administrador'),
(2, 'Operador'),
(3, 'Conductor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutas`
--

DROP TABLE IF EXISTS `rutas`;
CREATE TABLE IF NOT EXISTS `rutas` (
  `id_rutas` int NOT NULL AUTO_INCREMENT,
  `origen` varchar(30) DEFAULT NULL,
  `destino` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_rutas`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `rutas`
--

INSERT INTO `rutas` (`id_rutas`, `origen`, `destino`) VALUES
(5, 'Terminal Ojo de Agua', 'Zumpango'),
(6, 'Zumpango', 'Terminal Ojo de Agua'),
(7, 'Terminal Ojo de Agua', 'Tecamac'),
(8, 'Central de Abastos', 'Zumpango');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuarios` int NOT NULL AUTO_INCREMENT,
  `CURP` varchar(18) DEFAULT NULL,
  `clave` varchar(30) DEFAULT NULL,
  `puesto` int DEFAULT NULL,
  PRIMARY KEY (`id_usuarios`),
  KEY `puesto` (`puesto`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuarios`, `CURP`, `clave`, `puesto`) VALUES
(1, 'TAFA020530HMCPLLA0', '123', 1),
(8, 'AATF020530HMCPLLA0', '123', 2),
(9, 'DEMO1456YGHKTYH678', '123', 3),
(11, 'KAGS981107MCLLRLA3', '123', 3),
(12, 'XEKP890214HMCNSNB6', '123', 3),
(13, 'ZWDL760330HDFMTRC9', '123', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
CREATE TABLE IF NOT EXISTS `vehiculos` (
  `id_vehiculos` int NOT NULL AUTO_INCREMENT,
  `marca` varchar(30) DEFAULT NULL,
  `modelo` varchar(30) DEFAULT NULL,
  `anio` year DEFAULT NULL,
  `numPlacas` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id_vehiculos`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id_vehiculos`, `marca`, `modelo`, `anio`, `numPlacas`) VALUES
(2, 'Volvo', 'Pick-up', 2021, 'J2KT5'),
(4, 'Jeep', 'Rubicon', 2022, 'AS34TF'),
(5, 'Mercedez- Benz', 'G63AMG', 2019, 'ASF332'),
(6, 'Chevrolet', 'Groove', 2009, 'TGDF43'),
(7, 'Mercedez', ' Premier', 2016, 'TG12AS');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cap_time_viajes`
--
ALTER TABLE `cap_time_viajes`
  ADD CONSTRAINT `cap_time_viajes_ibfk_1` FOREIGN KEY (`id_horario`) REFERENCES `horario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cap_time_viajes_ibfk_2` FOREIGN KEY (`id_viaje`) REFERENCES `creacion_viajes` (`id_viaje`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cap_time_viajes_ibfk_3` FOREIGN KEY (`id_ruta`) REFERENCES `rutas` (`id_rutas`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Filtros para la tabla `creacion_viajes`
--
ALTER TABLE `creacion_viajes`
  ADD CONSTRAINT `creacion_viajes_ibfk_1` FOREIGN KEY (`id_ruta`) REFERENCES `rutas` (`id_rutas`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `creacion_viajes_ibfk_2` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculos`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `creacion_viajes_ibfk_3` FOREIGN KEY (`conductor`) REFERENCES `empleados` (`CURP`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `creacion_viajes_ibfk_4` FOREIGN KEY (`Horario`) REFERENCES `horario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`Puesto`) REFERENCES `roles` (`id_roles`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `estaciones`
--
ALTER TABLE `estaciones`
  ADD CONSTRAINT `estaciones_ibfk_1` FOREIGN KEY (`id_rutas`) REFERENCES `rutas` (`id_rutas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lista_retardos`
--
ALTER TABLE `lista_retardos`
  ADD CONSTRAINT `lista_retardos_ibfk_1` FOREIGN KEY (`id_conductor`) REFERENCES `empleados` (`CURP`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lista_retardos_ibfk_2` FOREIGN KEY (`id_viaje`) REFERENCES `creacion_viajes` (`id_viaje`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recorrido`
--
ALTER TABLE `recorrido`
  ADD CONSTRAINT `recorrido_ibfk_1` FOREIGN KEY (`id_viaje`) REFERENCES `creacion_viajes` (`id_viaje`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`puesto`) REFERENCES `roles` (`id_roles`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
