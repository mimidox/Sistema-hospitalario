-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-12-2025 a las 03:50:14
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `medilab`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_administrador` (IN `p_usuario_id` INT, IN `p_username` VARCHAR(50), IN `p_nombre` VARCHAR(50), IN `p_paterno` VARCHAR(50), IN `p_materno` VARCHAR(50), IN `p_correo` VARCHAR(100), IN `p_telefono` VARCHAR(20), IN `p_cargo` VARCHAR(100), IN `p_area_id` INT, IN `p_cambiar_password` BOOLEAN, IN `p_nuevo_password` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Actualizar usuario
    IF p_cambiar_password THEN
        UPDATE usuario 
        SET username = p_username,
            nombre = p_nombre,
            paterno = p_paterno,
            materno = p_materno,
            correo = p_correo,
            telefono = p_telefono,
            contraseña = SHA2(p_nuevo_password, 256),
            fecha_actualizacion = NOW()
        WHERE usuario_id = p_usuario_id;
    ELSE
        UPDATE usuario 
        SET username = p_username,
            nombre = p_nombre,
            paterno = p_paterno,
            materno = p_materno,
            correo = p_correo,
            telefono = p_telefono,
            fecha_actualizacion = NOW()
        WHERE usuario_id = p_usuario_id;
    END IF;
    
    -- Actualizar administrativo
    UPDATE administrativo 
    SET cargo = p_cargo,
        area_id = p_area_id
    WHERE usuario_id = p_usuario_id;
    
    -- Registrar en auditoría
    INSERT INTO auditoria (usuario_id, accion, fecha)
    VALUES (p_usuario_id, 'Actualización de administrador', NOW());
    
    COMMIT;
    
    SELECT 'Administrador actualizado exitosamente' as mensaje;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_estadisticas_areas` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_area_id INT;
    DECLARE v_nombre_area VARCHAR(100);
    DECLARE v_total_administradores INT;
    
    DECLARE cur_areas CURSOR FOR 
        SELECT area_id, nombre_area 
        FROM area;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Tabla temporal para resultados
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_estadisticas_areas (
        area_id INT,
        nombre_area VARCHAR(100),
        total_administradores INT,
        fecha_actualizacion TIMESTAMP
    );
    
    TRUNCATE TABLE temp_estadisticas_areas;
    
    OPEN cur_areas;
    
    read_loop: LOOP
        FETCH cur_areas INTO v_area_id, v_nombre_area;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Contar administradores por área
        SELECT COUNT(*) INTO v_total_administradores
        FROM administrativo 
        WHERE area_id = v_area_id;
        
        -- Insertar en tabla temporal
        INSERT INTO temp_estadisticas_areas 
        VALUES (v_area_id, v_nombre_area, v_total_administradores, NOW());
        
    END LOOP;
    
    CLOSE cur_areas;
    
    -- Mostrar resultados
    SELECT * FROM temp_estadisticas_areas ORDER BY total_administradores DESC;
    
    DROP TABLE temp_estadisticas_areas;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_crear_administrador` (IN `p_username` VARCHAR(50), IN `p_nombre` VARCHAR(50), IN `p_paterno` VARCHAR(50), IN `p_materno` VARCHAR(50), IN `p_correo` VARCHAR(100), IN `p_password` VARCHAR(255), IN `p_telefono` VARCHAR(20), IN `p_cargo` VARCHAR(100), IN `p_area_id` INT)   BEGIN
    DECLARE v_usuario_id INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Insertar en usuario
    INSERT INTO usuario (
        username, nombre, paterno, materno, correo, contraseña, telefono, fecha_registro
    ) VALUES (
        p_username, p_nombre, p_paterno, p_materno, p_correo, 
        SHA2(p_password, 256), p_telefono, NOW()
    );
    
    SET v_usuario_id = LAST_INSERT_ID();
    
    -- Insertar en administrativo
    INSERT INTO administrativo (usuario_id, cargo, area_id)
    VALUES (v_usuario_id, p_cargo, p_area_id);
    
    -- Registrar en auditoría
    INSERT INTO auditoria (usuario_id, accion, fecha)
    VALUES (v_usuario_id, 'Creación de administrador', NOW());
    
    COMMIT;
    
    SELECT 'Administrador creado exitosamente' as mensaje, v_usuario_id as usuario_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reporte_administradores_inactivos` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_usuario_id INT;
    DECLARE v_nombre_completo VARCHAR(200);
    DECLARE v_ultima_actividad DATE;
    DECLARE v_dias_inactivo INT;
    
    DECLARE cur_inactivos CURSOR FOR 
        SELECT 
            u.usuario_id,
            CONCAT(u.nombre, ' ', u.paterno) as nombre_completo,
            MAX(au.fecha) as ultima_actividad,
            DATEDIFF(NOW(), MAX(au.fecha)) as dias_inactivo
        FROM usuario u
        INNER JOIN administrativo a ON u.usuario_id = a.usuario_id
        LEFT JOIN auditoria au ON u.usuario_id = au.usuario_id
        GROUP BY u.usuario_id, u.nombre, u.paterno
        HAVING ultima_actividad IS NULL OR dias_inactivo > 30;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_inactivos (
        usuario_id INT,
        nombre_completo VARCHAR(200),
        ultima_actividad DATE,
        dias_inactivo INT,
        estado VARCHAR(20)
    );
    
    TRUNCATE TABLE temp_inactivos;
    
    OPEN cur_inactivos;
    
    read_loop: LOOP
        FETCH cur_inactivos INTO v_usuario_id, v_nombre_completo, v_ultima_actividad, v_dias_inactivo;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Determinar estado
        IF v_ultima_actividad IS NULL THEN
            INSERT INTO temp_inactivos VALUES (v_usuario_id, v_nombre_completo, NULL, NULL, 'NUNCA ACTIVO');
        ELSEIF v_dias_inactivo > 90 THEN
            INSERT INTO temp_inactivos VALUES (v_usuario_id, v_nombre_completo, v_ultima_actividad, v_dias_inactivo, 'MUY INACTIVO');
        ELSE
            INSERT INTO temp_inactivos VALUES (v_usuario_id, v_nombre_completo, v_ultima_actividad, v_dias_inactivo, 'INACTIVO');
        END IF;
        
    END LOOP;
    
    CLOSE cur_inactivos;
    
    -- Mostrar reporte
    SELECT * FROM temp_inactivos ORDER BY dias_inactivo DESC;
    
    DROP TABLE temp_inactivos;
END$$

--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_obtener_info_administrador` (`p_usuario_id` INT) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC READS SQL DATA BEGIN
    DECLARE v_info TEXT;
    
    SELECT CONCAT(
        'Usuario: ', u.username, ' | ',
        'Nombre: ', u.nombre, ' ', u.paterno, ' ', COALESCE(u.materno, ''), ' | ',
        'Cargo: ', a.cargo, ' | ',
        'Área: ', COALESCE(ar.nombre_area, 'No asignada'), ' | ',
        'Correo: ', u.correo, ' | ',
        'Teléfono: ', COALESCE(u.telefono, 'No registrado')
    ) INTO v_info
    FROM usuario u
    INNER JOIN administrativo a ON u.usuario_id = a.usuario_id
    LEFT JOIN area ar ON a.area_id = ar.area_id
    WHERE u.usuario_id = p_usuario_id;
    
    RETURN COALESCE(v_info, 'Administrador no encontrado');
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `fn_username_disponible` (`p_username` VARCHAR(50)) RETURNS TINYINT(1) DETERMINISTIC READS SQL DATA BEGIN
    DECLARE v_count INT;
    
    SELECT COUNT(*) INTO v_count
    FROM usuario 
    WHERE username = p_username;
    
    RETURN v_count = 0;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administrativo`
--

CREATE TABLE `administrativo` (
  `administrativo_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `area_id` int(11) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administrativo`
--

INSERT INTO `administrativo` (`administrativo_id`, `usuario_id`, `area_id`, `cargo`) VALUES
(1, 11, 1, 'Recepcionista'),
(2, 12, 2, 'Archivista'),
(3, 13, 3, 'Contador'),
(4, 14, 1, 'Secretaria'),
(5, 15, 4, 'Cajero'),
(6, 16, 1, 'Asistente General'),
(7, 17, 5, 'Coordinador de Área'),
(8, 18, 5, 'Supervisor'),
(9, 19, 1, 'Auxiliar Administrativo'),
(10, 20, 6, 'RRHH');

--
-- Disparadores `administrativo`
--
DELIMITER $$
CREATE TRIGGER `tr_after_insert_administrativo` AFTER INSERT ON `administrativo` FOR EACH ROW BEGIN
    INSERT INTO auditoria (usuario_id, accion, fecha)
    VALUES (NEW.usuario_id, CONCAT('Asignado como administrativo - Cargo: ', NEW.cargo), NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area`
--

CREATE TABLE `area` (
  `area_id` int(11) NOT NULL,
  `nombre_area` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `area`
--

INSERT INTO `area` (`area_id`, `nombre_area`, `descripcion`) VALUES
(1, 'Administrativa', 'Gestión principal'),
(2, 'Documentación', 'Manejo de fichas'),
(3, 'Logística', 'Recursos y materiales'),
(4, 'Consultas', 'Área de atención'),
(5, 'Emergencias', 'Urgencias'),
(6, 'Farmacia', 'Suministros médicos'),
(7, 'Hospitalización', 'Pacientes internados'),
(8, 'Imágenes', 'Radiología'),
(9, 'Laboratorio', 'Muestras clínicas'),
(10, 'Dirección', 'Organización general');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `auditoria_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consulta`
--

CREATE TABLE `consulta` (
  `consulta_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `ficha_id` int(11) NOT NULL,
  `motivo_consulta` varchar(255) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `consulta`
--

INSERT INTO `consulta` (`consulta_id`, `medico_id`, `ficha_id`, `motivo_consulta`, `fecha`, `tipo`) VALUES
(1, 1, 1, 'Dolor de pecho', '2025-01-01', 'General'),
(2, 2, 2, 'Fiebre', '2025-01-02', 'General'),
(3, 3, 3, 'Dolor de cabeza', '2025-01-03', 'Especialidad'),
(4, 4, 4, 'Erupción', '2025-01-04', 'General'),
(5, 5, 5, 'Fractura leve', '2025-01-05', 'Trauma'),
(6, 6, 6, 'Revisión', '2025-01-06', 'General'),
(7, 7, 7, 'Control', '2025-01-07', 'General'),
(8, 8, 8, 'Confusión', '2025-01-08', 'Especialidad'),
(9, 9, 9, 'Manchas', '2025-01-09', 'General'),
(10, 10, 10, 'Dolor lumbar', '2025-01-10', 'Trauma'),
(11, 1, 1, 'Control presión arterial', '2025-01-11', 'Seguimiento'),
(12, 2, 2, 'Dolor de cabeza persistente', '2025-01-12', 'Especialidad'),
(13, 3, 3, 'Control pediátrico', '2025-01-13', 'Control'),
(14, 4, 4, 'Erupción cutánea', '2025-01-14', 'General'),
(15, 5, 5, 'Dolor abdominal', '2025-01-15', 'Urgencia'),
(16, 6, 6, 'Revisión post-operatoria', '2025-01-16', 'Control'),
(17, 7, 7, 'Quimioterapia', '2025-01-17', 'Tratamiento'),
(18, 8, 8, 'Evaluación neurológica', '2025-01-18', 'Especialidad'),
(19, 9, 9, 'Consulta pre-operatoria', '2025-01-19', 'Evaluación'),
(20, 10, 10, 'Infección urinaria', '2025-01-20', 'General'),
(21, 1, 1, 'Control mensual', '2025-01-21', 'Seguimiento'),
(22, 2, 2, 'Migraña aguda', '2025-01-22', 'Urgencia'),
(23, 3, 3, 'Vacunación', '2025-01-23', 'Preventivo'),
(24, 4, 4, 'Alergia cutánea', '2025-01-24', 'General'),
(25, 5, 5, 'Control ginecológico', '2025-01-25', 'Rutina'),
(26, 1, 21, 'Control cardiológico', '2025-01-26', 'Seguimiento'),
(27, 1, 15, 'Dolor torácico', '2025-01-27', 'Urgencia'),
(28, 1, 7, 'Evaluación preoperatoria', '2025-01-28', 'Evaluación'),
(29, 1, 12, 'Arritmia', '2025-01-29', 'Especialidad'),
(30, 1, 18, 'Hipertensión refractaria', '2025-01-30', 'Control'),
(31, 2, 22, 'Cefalea crónica', '2025-01-26', 'Especialidad'),
(32, 2, 8, 'Mareos y vértigo', '2025-01-27', 'General'),
(33, 2, 13, 'Pérdida de memoria', '2025-01-28', 'Evaluación'),
(34, 2, 20, 'Migraña intensa', '2025-01-29', 'Urgencia'),
(35, 2, 16, 'Temblor en manos', '2025-01-30', 'Control'),
(36, 3, 23, 'Control de niño sano', '2025-01-26', 'Rutina'),
(37, 3, 3, 'Fiebre persistente', '2025-01-27', 'Urgencia'),
(38, 3, 9, 'Vacunación', '2025-01-28', 'Preventivo'),
(39, 3, 14, 'Infección respiratoria', '2025-01-29', 'General'),
(40, 3, 19, 'Control de crecimiento', '2025-01-30', 'Seguimiento'),
(41, 4, 24, 'Acné severo', '2025-01-26', 'Especialidad'),
(42, 4, 4, 'Dermatitis atópica', '2025-01-27', 'Control'),
(43, 4, 10, 'Psoriasis', '2025-01-28', 'Tratamiento'),
(44, 4, 17, 'Lunares sospechosos', '2025-01-29', 'Evaluación'),
(45, 4, 11, 'Urticaria crónica', '2025-01-30', 'General'),
(46, 5, 25, 'Control ginecológico anual', '2025-01-26', 'Rutina'),
(47, 5, 5, 'Dolor pélvico', '2025-01-27', 'Urgencia'),
(48, 5, 12, 'Amenorrea', '2025-01-28', 'Especialidad'),
(49, 5, 18, 'Planificación familiar', '2025-01-29', 'Consulta'),
(50, 5, 24, 'Seguimiento embarazo', '2025-01-30', 'Control');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultorio`
--

CREATE TABLE `consultorio` (
  `consultorio_id` int(11) NOT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `piso` int(11) NOT NULL,
  `numero` int(11) NOT NULL,
  `estado` enum('Disponible','Ocupado') NOT NULL DEFAULT 'Disponible',
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `consultorio`
--

INSERT INTO `consultorio` (`consultorio_id`, `tipo`, `piso`, `numero`, `estado`, `descripcion`) VALUES
(1, 'General', 1, 101, 'Disponible', 'Consultas rápidas'),
(2, 'Pediatría', 1, 102, 'Ocupado', 'Atención para niños'),
(3, 'Cardiología', 2, 201, 'Disponible', 'Atención cardíaca'),
(4, 'Dermatología', 2, 202, 'Disponible', 'Tratamiento de piel'),
(5, 'Traumatología', 3, 301, 'Ocupado', 'Atención de lesiones'),
(6, 'Ginecología', 3, 302, 'Disponible', 'Control y revisiones'),
(7, 'Neurología', 4, 401, 'Disponible', 'Estudios neurológicos'),
(8, 'Psicología', 4, 402, 'Ocupado', 'Atención psicológica'),
(9, 'Odontología', 5, 501, 'Disponible', 'Atención dental'),
(10, 'Nutrición', 5, 502, 'Disponible', 'Planificación alimentaria'),
(11, 'Oftalmología', 2, 203, 'Disponible', 'Exámenes visuales completos'),
(12, 'Otorrinolaringología', 2, 204, 'Ocupado', 'Atención ORL especializada'),
(13, 'Psiquiatría', 3, 303, 'Disponible', 'Consulta psiquiátrica'),
(14, 'Endocrinología', 3, 304, 'Disponible', 'Control metabólico'),
(15, 'Nefrología', 4, 403, 'Ocupado', 'Consulta renal'),
(16, 'Reumatología', 4, 404, 'Disponible', 'Enfermedades reumáticas'),
(17, 'Alergología', 5, 503, 'Ocupado', 'Pruebas alérgicas'),
(18, 'Oncología', 5, 504, 'Disponible', 'Consulta oncológica'),
(19, 'Cirugía Plástica', 6, 601, 'Disponible', 'Consulta preoperatoria'),
(20, 'Medicina General', 1, 105, 'Ocupado', 'Consultas generales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultorio_equipo`
--

CREATE TABLE `consultorio_equipo` (
  `consultorio_id` int(11) NOT NULL,
  `equipo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `consultorio_equipo`
--

INSERT INTO `consultorio_equipo` (`consultorio_id`, `equipo_id`) VALUES
(1, 1),
(1, 2),
(2, 3),
(2, 4),
(3, 1),
(3, 5),
(4, 6),
(5, 1),
(5, 7),
(6, 8),
(7, 9),
(8, 4),
(8, 10),
(9, 2),
(10, 3),
(11, 5),
(11, 11),
(12, 13),
(12, 18),
(13, 4),
(13, 10),
(14, 5),
(14, 13),
(15, 13),
(15, 14),
(16, 5),
(16, 13),
(17, 5),
(17, 13),
(18, 13),
(18, 14),
(19, 17),
(19, 18),
(20, 1),
(20, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo_medico`
--

CREATE TABLE `equipo_medico` (
  `equipo_id` int(11) NOT NULL,
  `funcionalidad` varchar(100) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `fecha_adquisicion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipo_medico`
--

INSERT INTO `equipo_medico` (`equipo_id`, `funcionalidad`, `categoria`, `fecha_adquisicion`) VALUES
(1, 'Monitoreo de signos vitales', 'Monitorización', '2023-01-15'),
(2, 'Administración de medicamentos', 'Terapia Intravenosa', '2023-02-20'),
(3, 'Ventilación pulmonar asistida', 'Respiratorio', '2022-11-10'),
(4, 'Diagnóstico por imágenes', 'Imagenología', '2023-03-05'),
(5, 'Medición de presión arterial', 'Diagnóstico', '2023-04-18'),
(6, 'Succión de secreciones', 'Aspiración', '2023-01-30'),
(7, 'Administración de oxígeno', 'Oxigenoterapia', '2022-12-22'),
(8, 'Medición de glucosa en sangre', 'Diagnóstico', '2023-05-10'),
(9, 'Esterilización de instrumentos', 'Esterilización', '2023-02-28'),
(10, 'Transporte de pacientes', 'Movilidad', '2023-03-15'),
(11, 'Electrocardiógrafo digital', 'Cardiología', '2024-03-15'),
(12, 'Ventilador de transporte', 'Emergencias', '2024-05-20'),
(13, 'Monitor multiparamétrico', 'UCI', '2024-01-10'),
(14, 'Bomba de infusión', 'Terapia', '2024-07-08'),
(15, 'Desfibrilador manual', 'Reanimación', '2024-09-12'),
(16, 'Nebulizador compresor', 'Respiratorio', '2024-04-25'),
(17, 'Electrobisturí', 'Quirúrgico', '2024-08-30'),
(18, 'Lámpara quirúrgica LED', 'Quirúrgico', '2024-02-14'),
(19, 'Carro de paro', 'Emergencias', '2024-06-18'),
(20, 'Ecógrafo portátil', 'Diagnóstico', '2024-11-05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ficha`
--

CREATE TABLE `ficha` (
  `ficha_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `seguro_id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `nro_ficha` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ficha`
--

INSERT INTO `ficha` (`ficha_id`, `paciente_id`, `seguro_id`, `fecha`, `hora`, `estado`, `nro_ficha`) VALUES
(1, 1, 1, '2025-01-01', '08:00:00', 'activa', 'F001'),
(2, 2, 3, '2025-01-02', '08:30:00', 'activa', 'F002'),
(3, 3, 4, '2025-01-03', '09:00:00', 'activa', 'F003'),
(4, 4, 2, '2025-01-04', '09:30:00', 'activa', 'F004'),
(5, 5, 1, '2025-01-05', '10:00:00', 'activa', 'F005'),
(6, 6, 6, '2025-01-06', '10:30:00', 'activa', 'F006'),
(7, 7, 7, '2025-01-07', '11:00:00', 'activa', 'F007'),
(8, 8, 8, '2025-01-08', '11:30:00', 'activa', 'F008'),
(9, 9, 9, '2025-01-09', '12:00:00', 'activa', 'F009'),
(10, 10, 10, '2025-01-10', '12:30:00', 'activa', 'F010'),
(11, 1, 1, '2025-01-11', '08:15:00', 'activa', 'F011'),
(12, 2, 3, '2025-01-12', '09:30:00', 'activa', 'F012'),
(13, 3, 4, '2025-01-13', '10:45:00', 'activa', 'F013'),
(14, 4, 2, '2025-01-14', '11:00:00', 'activa', 'F014'),
(15, 5, 1, '2025-01-15', '14:20:00', 'activa', 'F015'),
(16, 6, 6, '2025-01-16', '15:30:00', 'activa', 'F016'),
(17, 7, 7, '2025-01-17', '16:45:00', 'activa', 'F017'),
(18, 8, 8, '2025-01-18', '17:00:00', 'activa', 'F018'),
(19, 9, 9, '2025-01-19', '08:30:00', 'activa', 'F019'),
(20, 10, 10, '2025-01-20', '09:45:00', 'activa', 'F020'),
(21, 11, 1, '2025-01-21', '08:00:00', 'activa', 'F021'),
(22, 12, 2, '2025-01-22', '09:15:00', 'activa', 'F022'),
(23, 13, 3, '2025-01-23', '10:30:00', 'activa', 'F023'),
(24, 14, 4, '2025-01-24', '11:45:00', 'activa', 'F024'),
(25, 15, 5, '2025-01-25', '14:00:00', 'activa', 'F025'),
(26, 16, 6, '2025-01-26', '15:15:00', 'activa', 'F026'),
(27, 17, 7, '2025-01-27', '16:30:00', 'activa', 'F027'),
(28, 18, 8, '2025-01-28', '17:45:00', 'activa', 'F028'),
(29, 19, 9, '2025-01-29', '08:30:00', 'activa', 'F029'),
(30, 20, 10, '2025-01-30', '09:45:00', 'activa', 'F030');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitacion`
--

CREATE TABLE `habitacion` (
  `habitacion_id` int(11) NOT NULL,
  `nro_habitacion` varchar(20) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `habitacion`
--

INSERT INTO `habitacion` (`habitacion_id`, `nro_habitacion`, `tipo`, `estado`) VALUES
(1, '101', 'Individual', 'Libre'),
(2, '102', 'Individual', 'Libre'),
(3, '103', 'Doble', 'Ocupada'),
(4, '104', 'Doble', 'Libre'),
(5, '201', 'Individual', 'Libre'),
(6, '202', 'Individual', 'Libre'),
(7, '203', 'Doble', 'Libre'),
(8, '204', 'Doble', 'Ocupada'),
(9, '301', 'Individual', 'Libre'),
(10, '302', 'Doble', 'Libre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_medico`
--

CREATE TABLE `historial_medico` (
  `historial_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `antecedentes` text DEFAULT NULL,
  `alergias` text DEFAULT NULL,
  `diagnosticos` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_medico`
--

INSERT INTO `historial_medico` (`historial_id`, `paciente_id`, `antecedentes`, `alergias`, `diagnosticos`) VALUES
(1, 1, 'Familiar', 'Ninguna', 'Arritmia'),
(2, 2, 'Ninguno', 'Penicilina', 'Gripe'),
(3, 3, 'Familiar', 'Ninguna', 'Migraña'),
(4, 4, 'Ninguno', 'Polvo', 'Dermatitis'),
(5, 5, 'Accidente', 'Ninguna', 'Fractura'),
(6, 6, 'Familiar', 'Ninguna', 'Anemia'),
(7, 7, 'Familiar', 'Ninguna', 'Hipertensión'),
(8, 8, 'Ninguno', 'Polen', 'Alergia leve'),
(9, 9, 'Ninguno', 'Ninguna', 'Nauseas'),
(10, 10, 'Ninguno', 'Ninguna', 'Dolor muscular'),
(11, 11, 'Hipertensión familiar', 'Penicilina', 'Hipertensión arterial'),
(12, 12, 'Diabetes tipo 2', 'Ninguna', 'Diabetes mellitus'),
(13, 13, 'Ninguno', 'Mariscos', 'Alergia alimentaria'),
(14, 14, 'Asma bronquial', 'Ácaros', 'Asma moderada'),
(15, 15, 'Dislipidemia familiar', 'Ninguna', 'Hipercolesterolemia'),
(16, 16, 'Obesidad', 'Lactosa', 'Intolerancia a la lactosa'),
(17, 17, 'Ninguno', 'Ninguna', 'Ansiedad generalizada'),
(18, 18, 'Hipotiroidismo', 'Yodo', 'Hipotiroidismo subclínico'),
(19, 19, 'Artritis reumatoide', 'Ninguna', 'Artritis seropositiva'),
(20, 20, 'Migraña familiar', 'Sulfas', 'Migraña crónica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hospitalizacion`
--

CREATE TABLE `hospitalizacion` (
  `hospitalizacion_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) NOT NULL,
  `habitacion_id` int(11) NOT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_alta` date DEFAULT NULL,
  `diagnostico` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `hospitalizacion`
--

INSERT INTO `hospitalizacion` (`hospitalizacion_id`, `paciente_id`, `medico_id`, `habitacion_id`, `fecha_ingreso`, `fecha_alta`, `diagnostico`) VALUES
(1, 1, 1, 1, '2025-01-01', '2025-01-03', NULL),
(2, 2, 2, 3, '2025-01-02', '2025-01-05', NULL),
(3, 3, 3, 4, '2025-01-03', '2025-01-06', NULL),
(4, 4, 4, 8, '2025-01-04', '2025-01-07', NULL),
(5, 5, 5, 7, '2025-01-05', '2025-01-10', NULL),
(6, 6, 6, 2, '2025-01-06', '2025-01-08', NULL),
(7, 7, 7, 9, '2025-01-07', '2025-01-09', NULL),
(8, 8, 8, 10, '2025-01-08', '2025-01-12', NULL),
(9, 9, 9, 5, '2025-01-09', '2025-01-11', NULL),
(10, 10, 10, 6, '2025-01-10', '2025-01-13', NULL),
(11, 11, 1, 2, '2025-01-21', '2025-01-25', 'Insuficiencia cardíaca'),
(12, 12, 2, 4, '2025-01-22', '2025-01-26', 'Accidente cerebrovascular'),
(13, 13, 3, 6, '2025-01-23', '2025-01-27', 'Neumonía pediátrica'),
(14, 14, 4, 8, '2025-01-24', '2025-01-28', 'Dermatitis severa'),
(15, 15, 5, 10, '2025-01-25', '2025-01-29', 'Quiste ovárico'),
(16, 16, 6, 1, '2025-01-26', '2025-01-30', 'Fractura de tibia'),
(17, 17, 7, 3, '2025-01-27', '2025-01-31', 'Quimioterapia'),
(18, 18, 8, 5, '2025-01-28', '2025-02-01', 'Neumonía bacteriana'),
(19, 19, 9, 7, '2025-01-29', '2025-02-02', 'Apéndicitis aguda'),
(20, 20, 10, 9, '2025-01-30', '2025-02-03', 'Cólico renal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicamentos`
--

CREATE TABLE `medicamentos` (
  `medicamento_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `composicion` varchar(255) DEFAULT NULL,
  `tipo_administracion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicamentos`
--

INSERT INTO `medicamentos` (`medicamento_id`, `nombre`, `composicion`, `tipo_administracion`) VALUES
(1, 'Paracetamol', '500mg', 'Oral'),
(2, 'Ibuprofeno', '400mg', 'Oral'),
(3, 'Amoxicilina', '500mg', 'Oral'),
(4, 'Cotrimoxazol', '800mg', 'Oral'),
(5, 'Diclofenaco', '75mg', 'Intramuscular'),
(6, 'Loratadina', '10mg', 'Oral'),
(7, 'Omeprazol', '20mg', 'Oral'),
(8, 'Suero Fisiológico', '1L', 'Intravenoso'),
(9, 'Tramadol', '50mg', 'Oral'),
(10, 'Prednisona', '10mg', 'Oral'),
(11, 'Atorvastatina', '20mg', 'Oral'),
(12, 'Metformina', '850mg', 'Oral'),
(13, 'Losartán', '50mg', 'Oral'),
(14, 'Amlodipino', '5mg', 'Oral'),
(15, 'Insulina Glargina', '100UI/ml', 'Subcutánea'),
(16, 'Salbutamol', '100mcg', 'Inhalación'),
(17, 'Fluticasona', '50mcg', 'Inhalación'),
(18, 'Warfarina', '5mg', 'Oral'),
(19, 'Levotiroxina', '100mcg', 'Oral'),
(20, 'Clopidogrel', '75mg', 'Oral');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medico`
--

CREATE TABLE `medico` (
  `medico_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `nro_licencia` varchar(50) DEFAULT NULL,
  `años_experiencia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medico`
--

INSERT INTO `medico` (`medico_id`, `usuario_id`, `especialidad`, `nro_licencia`, `años_experiencia`) VALUES
(1, 22, 'Cardiología', 'LIC001', 10),
(2, 23, 'Neurología', 'LIC002', 8),
(3, 24, 'Pediatría', 'LIC003', 6),
(4, 25, 'Dermatología', 'LIC004', 2),
(5, 26, 'Ginecología', 'LIC005', 1),
(6, 27, 'Traumatología', 'LIC006', 5),
(7, 28, 'Oncología', 'LIC007', 8),
(8, 29, 'Medicina Interna', 'LIC008', 4),
(9, 30, 'Cirugía General', 'LIC009', 3),
(10, 31, 'Urología', 'LIC0010', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paciente`
--

CREATE TABLE `paciente` (
  `paciente_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_de_sangre` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paciente`
--

INSERT INTO `paciente` (`paciente_id`, `usuario_id`, `tipo_de_sangre`) VALUES
(1, 1, 'A+'),
(2, 2, 'O-'),
(3, 3, 'AB+'),
(4, 4, 'B+'),
(5, 5, 'O+'),
(6, 6, 'A-'),
(7, 7, 'B-'),
(8, 8, 'O+'),
(9, 9, 'A+'),
(10, 10, 'O-'),
(11, 33, 'A+'),
(12, 34, 'B+'),
(13, 35, 'O-'),
(14, 36, 'AB+'),
(15, 37, 'A-'),
(16, 38, 'B-'),
(17, 39, 'O+'),
(18, 40, 'A+'),
(19, 41, 'AB-'),
(20, 42, 'O-');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receta`
--

CREATE TABLE `receta` (
  `receta_id` int(11) NOT NULL,
  `tratamiento_id` int(11) NOT NULL,
  `medicamento_id` int(11) NOT NULL,
  `dosis` varchar(100) DEFAULT NULL,
  `duracion` varchar(100) DEFAULT NULL,
  `frecuencia` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `receta`
--

INSERT INTO `receta` (`receta_id`, `tratamiento_id`, `medicamento_id`, `dosis`, `duracion`, `frecuencia`) VALUES
(1, 1, 1, '1 tableta', '5 días', 'Cada 8h'),
(2, 2, 6, '1 tableta', '4 días', 'Cada 24h'),
(3, 3, 3, '1 tableta', '4 días', 'Cada 12h'),
(4, 4, 10, '1 tableta', '4 días', 'Cada 24h'),
(5, 5, 5, '1 ampolla', '7 días', 'Cada 24h'),
(6, 6, 7, '1 tableta', '5 días', 'Cada 24h'),
(7, 7, 2, '1 tableta', '5 días', 'Cada 12h'),
(8, 8, 6, '1 tableta', '5 días', 'Cada 24h'),
(9, 9, 8, '250ml', '2 días', 'Continuo'),
(10, 10, 9, '1 tableta', '5 días', 'Cada 12h'),
(11, 1, 2, '1 tableta', '3 días', 'Cada 8 horas'),
(12, 2, 3, '1 tableta', '7 días', 'Cada 12 horas'),
(13, 3, 4, '2 tabletas', '5 días', 'Cada 24 horas'),
(14, 4, 5, '1 ampolla', '3 días', 'Cada 24 horas'),
(15, 5, 6, '1 tableta', '10 días', 'Cada 24 horas'),
(16, 6, 7, '1 cápsula', '30 días', 'Cada 24 horas'),
(17, 7, 8, '500 ml', '1 día', 'Continuo'),
(18, 8, 9, '1 tableta', '7 días', 'Cada 12 horas'),
(19, 9, 10, '2 tabletas', '5 días', 'Cada 24 horas'),
(20, 10, 1, '1 tableta', '3 días', 'Cada 6 horas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos_hospitalarios`
--

CREATE TABLE `recursos_hospitalarios` (
  `recurso_id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `area_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recursos_hospitalarios`
--

INSERT INTO `recursos_hospitalarios` (`recurso_id`, `nombre`, `ubicacion`, `area_id`) VALUES
(1, 'Monitor de signos vitales', 'Sala de Emergencias', 1),
(2, 'Ventilador mecánico', 'Unidad de Cuidados Intensivos', 2),
(3, 'Equipo de rayos X', 'Departamento de Radiología', 3),
(4, 'Carro de paro cardíaco', 'Sala de Emergencias', 1),
(5, 'Bomba de infusión', 'Piso de Hospitalización', 4),
(6, 'Ecógrafo portátil', 'Consulta Externa', 5),
(7, 'Desfibrilador', 'Unidad de Cuidados Intensivos', 2),
(8, 'Estación de trabajo de enfermería', 'Piso de Hospitalización', 4),
(9, 'Mesa quirúrgica', 'Quirófano Principal', 6),
(10, 'Lámpara quirúrgica', 'Quirófano Principal', 6),
(11, 'Respirador de alta frecuencia', 'UCI Neonatal', 5),
(12, 'Carro de anestesia', 'Quirófano Principal', 6),
(13, 'Monitor fetal central', 'Sala de Partos', 7),
(14, 'Bomba de jeringa', 'UCI Adultos', 5),
(15, 'Mesa quirúrgica eléctrica', 'Quirófano 2', 6),
(16, 'Aspirador quirúrgico', 'Sala de Operaciones', 6),
(17, 'Cámara hiperbárica', 'Medicina Hiperbárica', 4),
(18, 'Laringoscopio video', 'Emergencias', 5),
(19, 'Videogastroscopio', 'Endoscopía', 4),
(20, 'Unidad de hemodiálisis', 'Nefrología', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguro_medico`
--

CREATE TABLE `seguro_medico` (
  `seguro_id` int(11) NOT NULL,
  `tipo_seguro` varchar(100) DEFAULT NULL,
  `poliza` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `seguro_medico`
--

INSERT INTO `seguro_medico` (`seguro_id`, `tipo_seguro`, `poliza`) VALUES
(1, 'Caja Nacional', 'A1'),
(2, 'Caja Cordes', 'B1'),
(3, 'COSSMIL', 'C1'),
(4, 'UNIVida', 'D1'),
(5, 'Privado Gold', 'P1'),
(6, 'Privado Silver', 'P2'),
(7, 'SUS', 'S1'),
(8, 'SUS', 'S2'),
(9, 'Privado Basic', 'P3'),
(10, 'UNIVida', 'D2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('ej5XZRkg5QHQefdKLenldr1AFJKB7bTPqGdfmrvu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaGtKdmhIOFBkMzN6NENzT1c0R2RMbnc1ckVjTjN5OHdBSUNKbnZ3aSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9mb3JtbG9naW4iO3M6NToicm91dGUiO3M6MjM6IkNvbnRyb2xJbmljaW8uZm9ybWxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1764643711),
('jXZvdLdYOvnxwUvSMiqzTQkIgQCD2p4Jv0pMUOHu', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YToxMjp7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo2OiJfdG9rZW4iO3M6NDA6IjQ3Y1V1U1MxNlA4UWQ1ZGN0ZGljbWxsNjBhV0o3Z2NDMUp3dnNSREQiO3M6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ4OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvb2J0ZW5lci11c3VhcmlvLWNvbXBsZXRvLzEiO3M6NToicm91dGUiO3M6MjQ6Im9idGVuZXIudXN1YXJpby5jb21wbGV0byI7fXM6MTA6InVzdWFyaW9faWQiO2k6MjA7czo4OiJ1c2VybmFtZSI7czo0OiJhZG01IjtzOjY6Im5vbWJyZSI7czo4OiJWZXJvbmljYSI7czo3OiJwYXRlcm5vIjtzOjY6IlZhcmdhcyI7czo3OiJtYXRlcm5vIjtzOjY6IkFsZmFybyI7czo2OiJjb3JyZW8iO3M6MTM6InZ2MjBAbWFpbC5jb20iO3M6ODoidGVsZWZvbm8iO3M6ODoiNzIwMjAyMDIiO3M6Mzoicm9sIjtzOjE0OiJhZG1pbmlzdHJhdGl2byI7czo5OiJsb2dnZWRfaW4iO2I6MTt9', 1764566956);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tratamiento`
--

CREATE TABLE `tratamiento` (
  `tratamiento_id` int(11) NOT NULL,
  `historial_id` int(11) NOT NULL,
  `tipo_tratamiento` varchar(150) DEFAULT NULL,
  `fecha_ini` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tratamiento`
--

INSERT INTO `tratamiento` (`tratamiento_id`, `historial_id`, `tipo_tratamiento`, `fecha_ini`, `fecha_fin`) VALUES
(1, 1, 'Medicamentos', '2025-01-01', '2025-01-05'),
(2, 2, 'Reposo', '2025-01-02', '2025-01-06'),
(3, 3, 'Medicamentos', '2025-01-03', '2025-01-07'),
(4, 4, 'Cremas', '2025-01-04', '2025-01-08'),
(5, 5, 'Inmovilización', '2025-01-05', '2025-01-20'),
(6, 6, 'Vitaminas', '2025-01-06', '2025-01-10'),
(7, 7, 'Medicamentos', '2025-01-07', '2025-01-12'),
(8, 8, 'Antialérgicos', '2025-01-08', '2025-01-14'),
(9, 9, 'Rehidratación', '2025-01-09', '2025-01-11'),
(10, 10, 'Masajes', '2025-01-10', '2025-01-15'),
(11, 1, 'Terapia física', '2025-01-11', '2025-02-11'),
(12, 2, 'Antibióticos', '2025-01-12', '2025-01-19'),
(13, 3, 'Analgésicos', '2025-01-13', '2025-01-20'),
(14, 4, 'Antihistamínicos', '2025-01-14', '2025-01-28'),
(15, 5, 'Rehabilitación', '2025-01-15', '2025-03-15'),
(16, 6, 'Suplementos vitamínicos', '2025-01-16', '2025-04-16'),
(17, 7, 'Control hipertensión', '2025-01-17', '2025-07-17'),
(18, 8, 'Inmunoterapia', '2025-01-18', '2025-06-18'),
(19, 9, 'Hidratación intravenosa', '2025-01-19', '2025-01-21'),
(20, 10, 'Fisioterapia', '2025-01-20', '2025-02-20'),
(21, 11, 'Medicación cardíaca', '2025-01-21', '2025-04-21'),
(22, 12, 'Anticoagulantes', '2025-01-22', '2025-07-22'),
(23, 13, 'Antibióticos pediátricos', '2025-01-23', '2025-02-02'),
(24, 14, 'Corticoides tópicos', '2025-01-24', '2025-03-24'),
(25, 15, 'Terapia hormonal', '2025-01-25', '2025-06-25'),
(26, 16, 'Inmovilización y analgésicos', '2025-01-26', '2025-03-26'),
(27, 17, 'Quimioterapia ciclo 1', '2025-01-27', '2025-02-10'),
(28, 18, 'Antibióticos intravenosos', '2025-01-28', '2025-02-05'),
(29, 19, 'Post-operatorio apendicectomía', '2025-01-29', '2025-02-12'),
(30, 20, 'Analgésicos y antiespasmódicos', '2025-01-30', '2025-02-08'),
(31, 11, 'Medicación cardíaca', '2025-01-21', '2025-04-21'),
(32, 12, 'Anticoagulantes', '2025-01-22', '2025-07-22'),
(33, 13, 'Antibióticos pediátricos', '2025-01-23', '2025-02-02'),
(34, 14, 'Corticoides tópicos', '2025-01-24', '2025-03-24'),
(35, 15, 'Terapia hormonal', '2025-01-25', '2025-06-25'),
(36, 16, 'Inmovilización y analgésicos', '2025-01-26', '2025-03-26'),
(37, 17, 'Quimioterapia ciclo 1', '2025-01-27', '2025-02-10'),
(38, 18, 'Antibióticos intravenosos', '2025-01-28', '2025-02-05'),
(39, 19, 'Post-operatorio apendicectomía', '2025-01-29', '2025-02-12'),
(40, 20, 'Analgésicos y antiespasmódicos', '2025-01-30', '2025-02-08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `usuario_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `paterno` varchar(50) DEFAULT NULL,
  `materno` varchar(50) DEFAULT NULL,
  `genero` enum('M','F','O') DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `calle` varchar(100) DEFAULT NULL,
  `zona` varchar(100) DEFAULT NULL,
  `municipio` varchar(100) DEFAULT NULL,
  `fec_nac` date DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`usuario_id`, `username`, `nombre`, `paterno`, `materno`, `genero`, `correo`, `contraseña`, `telefono`, `calle`, `zona`, `municipio`, `fec_nac`, `estado`) VALUES
(1, 'jperez', 'Juan', 'Perez', 'Lopez', 'M', 'jp1@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '70111111', 'Almte', 'Centro', 'La Paz', '2000-05-10', 'activo'),
(2, 'mfern', 'Maria', 'Fernandez', 'Rios', 'F', 'mf2@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '70222222', '15', 'Sopocachi', 'La Paz', '1999-03-02', 'activo'),
(3, 'cruzp', 'Pablo', 'Cruz', 'Medina', 'M', 'pc3@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '70333333', 'A', 'Satélite', 'El Alto', '1998-11-15', 'activo'),
(4, 'analo', 'Ana', 'Lopez', 'Perez', 'F', 'al4@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '70444444', 'B', 'Miraflores', 'La Paz', '1997-09-21', 'activo'),
(5, 'lvega', 'Luis', 'Vega', 'Mamani', 'M', 'lv5@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '70555555', 'C', '16 Julio', 'El Alto', '2001-06-19', 'activo'),
(6, 'rquiroz', 'Rosa', 'Quiroz', 'Alvarez', 'F', 'rq6@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '70666666', '12', 'Calacoto', 'La Paz', '1995-12-30', 'activo'),
(7, 'cman', 'Carlos', 'Mancilla', 'Choque', 'M', 'cm7@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '70777777', '17', 'Achumani', 'La Paz', '2000-02-02', 'activo'),
(8, 'jram', 'Julia', 'Ramirez', 'Cortez', 'F', 'jr8@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '70888888', 'D', 'Mercedario', 'El Alto', '1999-01-14', 'activo'),
(9, 'robel', 'Roberto', 'Beltran', 'Zarate', 'M', 'rb9@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '70999999', '22', 'Obrajes', 'La Paz', '1996-04-07', 'activo'),
(10, 'sarab', 'Sara', 'Blanco', 'Luna', 'F', 'sb10@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '71010101', 'E', 'San Pedro', 'La Paz', '1998-08-11', 'activo'),
(11, 'dmed1', 'Diego', 'Mendoza', 'Salas', 'M', 'dm11@mail.com', '88d4266fd4e6338d13b845fcf289579d209c897823b9217da3e161936f031589', '71111111', 'F', 'Centro', 'La Paz', '1980-03-12', 'activo'),
(12, 'dmed2', 'Gabriela', 'Ramos', 'Silva', 'F', 'gr12@mail.com', '88d4266fd4e6338d13b845fcf289579d209c897823b9217da3e161936f031589', '71222222', 'G', 'Sopocachi', 'La Paz', '1979-07-22', 'activo'),
(13, 'dmed3', 'Hector', 'Villalba', 'Lima', 'M', 'hv13@mail.com', '88d4266fd4e6338d13b845fcf289579d209c897823b9217da3e161936f031589', '71333333', 'H', 'Miraflores', 'La Paz', '1985-10-05', 'activo'),
(14, 'dmed4', 'Sonia', 'Torrez', 'Mena', 'F', 'st14@mail.com', '88d4266fd4e6338d13b845fcf289579d209c897823b9217da3e161936f031589', '71444444', 'I', 'Obrajes', 'La Paz', '1989-12-24', 'activo'),
(15, 'dmed5', 'Marco', 'Alarcon', 'Yujra', 'M', 'ma15@mail.com', '88d4266fd4e6338d13b845fcf289579d209c897823b9217da3e161936f031589', '71555555', 'J', 'Pampahasi', 'El Alto', '1982-09-29', 'activo'),
(16, 'adm1', 'Erika', 'Guzman', 'Roca', 'F', 'eg16@mail.com', '9af15b336e6a9619928537df30b2e6a2376569fcf9d7e773eccede65606529a0', '71666666', 'K', 'Centro', 'La Paz', '1990-01-01', 'activo'),
(17, 'adm2', 'Alvaro', 'Choque', 'Limachi', 'M', 'ac17@mail.com', '9af15b336e6a9619928537df30b2e6a2376569fcf9d7e773eccede65606529a0', '71777777', 'L', 'Sopocachi', 'La Paz', '1992-05-17', 'activo'),
(18, 'adm3', 'Nadia', 'Salazar', 'Poma', 'F', 'ns18@mail.com', '9af15b336e6a9619928537df30b2e6a2376569fcf9d7e773eccede65606529a0', '71888888', 'M', 'San Pedro', 'La Paz', '1991-03-03', 'activo'),
(19, 'adm4', 'Ricardo', 'Huanca', 'Flores', 'M', 'rh19@mail.com', '9af15b336e6a9619928537df30b2e6a2376569fcf9d7e773eccede65606529a0', '71999999', 'N', 'Senkata', 'El Alto', '1988-06-06', 'activo'),
(20, 'adm5', 'Veronica', 'Vargas', 'Alfaro', 'F', 'vv20@mail.com', '9af15b336e6a9619928537df30b2e6a2376569fcf9d7e773eccede65606529a0', '72020202', 'O', 'Miraflores', 'La Paz', '1993-07-27', 'activo'),
(22, 'doc1', 'Eduardo', 'Ríos', 'Navarro', 'M', 'doctor1@mail.com', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', '71111111', 'DC1', 'ZD1', 'M1', '1980-06-20', 'activo'),
(23, 'doc2', 'Sofía', 'Cárdenas', 'Ruiz', 'F', 'doctor2@mail.com', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', '72222222', 'DC2', 'ZD1', 'M1', '1984-03-15', 'activo'),
(24, 'doc3', 'Hugo', 'Márquez', 'Soto', 'M', 'doctor3@mail.com', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', '73333333', 'DC3', 'ZD2', 'M1', '1978-11-05', 'activo'),
(25, 'doc4', 'Elena', 'Blanco', 'Velásquez', 'F', 'doctor4@mail.com', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', '74444444', 'DC4', 'ZD2', 'M1', '1987-01-25', 'activo'),
(26, 'doc5', 'Fernando', 'Rivas', 'López', 'M', 'doctor5@mail.com', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', '75555555', 'DC5', 'ZD3', 'M1', '1981-09-17', 'activo'),
(27, 'doc6', 'Valeria', 'Guzmán', 'Herrera', 'F', 'doctor6@mail.com', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', '76666666', 'DC6', 'ZD3', 'M1', '1985-02-10', 'activo'),
(28, 'doc7', 'Ricardo', 'Torrez', 'Molina', 'M', 'doctor7@mail.com', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', '77777777', 'DC7', 'ZD4', 'M1', '1979-05-12', 'activo'),
(29, 'doc8', 'Paula', 'Medina', 'Rivero', 'F', 'doctor8@mail.com', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', '78888888', 'DC8', 'ZD4', 'M1', '1983-12-01', 'activo'),
(30, 'doc9', 'Germán', 'Aguirre', 'Céspedes', 'M', 'doctor9@mail.com', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', '79999999', 'DC9', 'ZD5', 'M1', '1977-08-08', 'activo'),
(31, 'doc10', 'Juliana', 'Romero', 'Gallardo', 'F', 'doctor10@mail.com', 'd74ff0ee8da3b9806b18c877dbf29bbde50b5bd8e4dad7a3a725000feb82e8f1', '70000001', 'DC10', 'ZD5', 'M1', '1986-04-30', 'activo'),
(32, 'viviadmin', 'Vivian', 'Echave', 'Quisbert', 'F', 'viktorvikki@gmail.com', '9af15b336e6a9619928537df30b2e6a2376569fcf9d7e773eccede65606529a0', '34543656', NULL, NULL, NULL, NULL, 'activo'),
(33, 'mcastillo', 'Miguel', 'Castillo', 'Rojas', 'M', 'mc33@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '72111111', 'P', 'Centro', 'La Paz', '1992-08-15', 'activo'),
(34, 'lherrera', 'Laura', 'Herrera', 'Mendez', 'F', 'lh34@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '72222222', 'Q', 'Sopocachi', 'La Paz', '1988-12-03', 'activo'),
(35, 'pgomez', 'Pedro', 'Gomez', 'Silva', 'M', 'pg35@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '72333333', 'R', 'Miraflores', 'La Paz', '1975-03-22', 'activo'),
(36, 'acastro', 'Ana', 'Castro', 'Lopez', 'F', 'ac36@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '72444444', 'S', 'Obrajes', 'La Paz', '1999-07-11', 'activo'),
(37, 'druiz', 'Daniel', 'Ruiz', 'Martinez', 'M', 'dr37@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '72555555', 'T', 'San Pedro', 'La Paz', '1983-11-29', 'activo'),
(38, 'cmorales', 'Carla', 'Morales', 'Vargas', 'F', 'cm38@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '72666666', 'U', 'Calacoto', 'La Paz', '1990-04-17', 'activo'),
(39, 'jortiz', 'Javier', 'Ortiz', 'Flores', 'M', 'jo39@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '72777777', 'V', 'Achumani', 'La Paz', '1987-09-05', 'activo'),
(40, 'psanchez', 'Patricia', 'Sanchez', 'Diaz', 'F', 'ps40@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '72888888', 'W', '16 Julio', 'El Alto', '1995-01-25', 'activo'),
(41, 'rgonzales', 'Roberto', 'Gonzales', 'Perez', 'M', 'rg41@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '72999999', 'X', 'Satélite', 'El Alto', '1981-06-13', 'activo'),
(42, 'mrodriguez', 'Marcela', 'Rodriguez', 'Gutierrez', 'F', 'mr42@mail.com', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', '73000000', 'Y', 'Mercedario', 'El Alto', '1993-10-08', 'activo');

--
-- Disparadores `usuario`
--
DELIMITER $$
CREATE TRIGGER `tr_before_delete_usuario` BEFORE DELETE ON `usuario` FOR EACH ROW BEGIN
    DECLARE v_tiene_roles INT;
    
    -- Verificar si el usuario tiene roles asignados
    SELECT COUNT(*) INTO v_tiene_roles
    FROM (
        SELECT usuario_id FROM administrativo WHERE usuario_id = OLD.usuario_id
        UNION ALL
        SELECT usuario_id FROM medico WHERE usuario_id = OLD.usuario_id
        UNION ALL
        SELECT usuario_id FROM paciente WHERE usuario_id = OLD.usuario_id
    ) as roles;
    
    IF v_tiene_roles > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se puede eliminar usuario. Tiene roles asignados. Elimine primero los roles.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_administradores_completo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_administradores_completo` (
`usuario_id` int(11)
,`username` varchar(50)
,`nombre` varchar(50)
,`paterno` varchar(50)
,`materno` varchar(50)
,`nombre_completo` varchar(152)
,`correo` varchar(100)
,`telefono` varchar(20)
,`genero` enum('M','F','O')
,`fec_nac` date
,`administrativo_id` int(11)
,`cargo` varchar(100)
,`area_id` int(11)
,`area` varchar(100)
,`descripcion_area` text
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_reporte_actividades_administradores`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_reporte_actividades_administradores` (
`usuario_id` int(11)
,`administrador` varchar(101)
,`cargo` varchar(100)
,`area` varchar(100)
,`total_actividades` bigint(21)
,`ultima_actividad` timestamp
,`mes_ultima_actividad` varchar(7)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_usuarios_publicos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_usuarios_publicos` (
`usuario_id` int(11)
,`username` varchar(50)
,`nombre` varchar(50)
,`paterno` varchar(50)
,`materno` varchar(50)
,`correo` varchar(100)
,`telefono` varchar(20)
,`genero` enum('M','F','O')
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_administradores_completo`
--
DROP TABLE IF EXISTS `vw_administradores_completo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_administradores_completo`  AS SELECT `u`.`usuario_id` AS `usuario_id`, `u`.`username` AS `username`, `u`.`nombre` AS `nombre`, `u`.`paterno` AS `paterno`, `u`.`materno` AS `materno`, concat(`u`.`nombre`,' ',`u`.`paterno`,' ',coalesce(`u`.`materno`,'')) AS `nombre_completo`, `u`.`correo` AS `correo`, `u`.`telefono` AS `telefono`, `u`.`genero` AS `genero`, `u`.`fec_nac` AS `fec_nac`, `a`.`administrativo_id` AS `administrativo_id`, `a`.`cargo` AS `cargo`, `a`.`area_id` AS `area_id`, `ar`.`nombre_area` AS `area`, `ar`.`descripcion` AS `descripcion_area` FROM ((`usuario` `u` join `administrativo` `a` on(`u`.`usuario_id` = `a`.`usuario_id`)) left join `area` `ar` on(`a`.`area_id` = `ar`.`area_id`)) WHERE `u`.`estado` = 'activo' ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_reporte_actividades_administradores`
--
DROP TABLE IF EXISTS `vw_reporte_actividades_administradores`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_reporte_actividades_administradores`  AS SELECT `u`.`usuario_id` AS `usuario_id`, concat(`u`.`nombre`,' ',`u`.`paterno`) AS `administrador`, `a`.`cargo` AS `cargo`, `ar`.`nombre_area` AS `area`, count(`au`.`auditoria_id`) AS `total_actividades`, max(`au`.`fecha`) AS `ultima_actividad`, date_format(max(`au`.`fecha`),'%Y-%m') AS `mes_ultima_actividad` FROM (((`usuario` `u` join `administrativo` `a` on(`u`.`usuario_id` = `a`.`usuario_id`)) left join `area` `ar` on(`a`.`area_id` = `ar`.`area_id`)) left join `auditoria` `au` on(`u`.`usuario_id` = `au`.`usuario_id`)) WHERE `au`.`fecha` >= current_timestamp() - interval 30 day GROUP BY `u`.`usuario_id`, `u`.`nombre`, `u`.`paterno`, `a`.`cargo`, `ar`.`nombre_area` ORDER BY count(`au`.`auditoria_id`) DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_usuarios_publicos`
--
DROP TABLE IF EXISTS `vw_usuarios_publicos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_usuarios_publicos`  AS SELECT `usuario`.`usuario_id` AS `usuario_id`, `usuario`.`username` AS `username`, `usuario`.`nombre` AS `nombre`, `usuario`.`paterno` AS `paterno`, `usuario`.`materno` AS `materno`, `usuario`.`correo` AS `correo`, `usuario`.`telefono` AS `telefono`, `usuario`.`genero` AS `genero` FROM `usuario` WHERE `usuario`.`estado` = 'activo' ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administrativo`
--
ALTER TABLE `administrativo`
  ADD PRIMARY KEY (`administrativo_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `area_id` (`area_id`);

--
-- Indices de la tabla `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`area_id`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`auditoria_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `consulta`
--
ALTER TABLE `consulta`
  ADD PRIMARY KEY (`consulta_id`),
  ADD KEY `medico_id` (`medico_id`),
  ADD KEY `ficha_id` (`ficha_id`);

--
-- Indices de la tabla `consultorio`
--
ALTER TABLE `consultorio`
  ADD PRIMARY KEY (`consultorio_id`);

--
-- Indices de la tabla `consultorio_equipo`
--
ALTER TABLE `consultorio_equipo`
  ADD PRIMARY KEY (`consultorio_id`,`equipo_id`),
  ADD KEY `equipo_id` (`equipo_id`);

--
-- Indices de la tabla `equipo_medico`
--
ALTER TABLE `equipo_medico`
  ADD PRIMARY KEY (`equipo_id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `ficha`
--
ALTER TABLE `ficha`
  ADD PRIMARY KEY (`ficha_id`),
  ADD UNIQUE KEY `nro_ficha` (`nro_ficha`),
  ADD KEY `paciente_id` (`paciente_id`),
  ADD KEY `seguro_id` (`seguro_id`);

--
-- Indices de la tabla `habitacion`
--
ALTER TABLE `habitacion`
  ADD PRIMARY KEY (`habitacion_id`);

--
-- Indices de la tabla `historial_medico`
--
ALTER TABLE `historial_medico`
  ADD PRIMARY KEY (`historial_id`),
  ADD KEY `paciente_id` (`paciente_id`);

--
-- Indices de la tabla `hospitalizacion`
--
ALTER TABLE `hospitalizacion`
  ADD PRIMARY KEY (`hospitalizacion_id`),
  ADD KEY `paciente_id` (`paciente_id`),
  ADD KEY `medico_id` (`medico_id`),
  ADD KEY `habitacion_id` (`habitacion_id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  ADD PRIMARY KEY (`medicamento_id`);

--
-- Indices de la tabla `medico`
--
ALTER TABLE `medico`
  ADD PRIMARY KEY (`medico_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `paciente`
--
ALTER TABLE `paciente`
  ADD PRIMARY KEY (`paciente_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `receta`
--
ALTER TABLE `receta`
  ADD PRIMARY KEY (`receta_id`),
  ADD KEY `tratamiento_id` (`tratamiento_id`),
  ADD KEY `medicamento_id` (`medicamento_id`);

--
-- Indices de la tabla `recursos_hospitalarios`
--
ALTER TABLE `recursos_hospitalarios`
  ADD PRIMARY KEY (`recurso_id`),
  ADD KEY `area_id` (`area_id`);

--
-- Indices de la tabla `seguro_medico`
--
ALTER TABLE `seguro_medico`
  ADD PRIMARY KEY (`seguro_id`),
  ADD UNIQUE KEY `poliza` (`poliza`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `tratamiento`
--
ALTER TABLE `tratamiento`
  ADD PRIMARY KEY (`tratamiento_id`),
  ADD KEY `historial_id` (`historial_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administrativo`
--
ALTER TABLE `administrativo`
  MODIFY `administrativo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `area`
--
ALTER TABLE `area`
  MODIFY `area_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `auditoria_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `consulta`
--
ALTER TABLE `consulta`
  MODIFY `consulta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `consultorio`
--
ALTER TABLE `consultorio`
  MODIFY `consultorio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `equipo_medico`
--
ALTER TABLE `equipo_medico`
  MODIFY `equipo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ficha`
--
ALTER TABLE `ficha`
  MODIFY `ficha_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `habitacion`
--
ALTER TABLE `habitacion`
  MODIFY `habitacion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `historial_medico`
--
ALTER TABLE `historial_medico`
  MODIFY `historial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `hospitalizacion`
--
ALTER TABLE `hospitalizacion`
  MODIFY `hospitalizacion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `medicamentos`
--
ALTER TABLE `medicamentos`
  MODIFY `medicamento_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `medico`
--
ALTER TABLE `medico`
  MODIFY `medico_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `paciente`
--
ALTER TABLE `paciente`
  MODIFY `paciente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `receta`
--
ALTER TABLE `receta`
  MODIFY `receta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `recursos_hospitalarios`
--
ALTER TABLE `recursos_hospitalarios`
  MODIFY `recurso_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `seguro_medico`
--
ALTER TABLE `seguro_medico`
  MODIFY `seguro_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tratamiento`
--
ALTER TABLE `tratamiento`
  MODIFY `tratamiento_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `administrativo`
--
ALTER TABLE `administrativo`
  ADD CONSTRAINT `administrativo_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`),
  ADD CONSTRAINT `administrativo_ibfk_2` FOREIGN KEY (`area_id`) REFERENCES `area` (`area_id`);

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `consulta`
--
ALTER TABLE `consulta`
  ADD CONSTRAINT `consulta_ibfk_1` FOREIGN KEY (`medico_id`) REFERENCES `medico` (`medico_id`),
  ADD CONSTRAINT `consulta_ibfk_2` FOREIGN KEY (`ficha_id`) REFERENCES `ficha` (`ficha_id`);

--
-- Filtros para la tabla `consultorio_equipo`
--
ALTER TABLE `consultorio_equipo`
  ADD CONSTRAINT `consultorio_equipo_ibfk_1` FOREIGN KEY (`consultorio_id`) REFERENCES `consultorio` (`consultorio_id`),
  ADD CONSTRAINT `consultorio_equipo_ibfk_2` FOREIGN KEY (`equipo_id`) REFERENCES `equipo_medico` (`equipo_id`);

--
-- Filtros para la tabla `ficha`
--
ALTER TABLE `ficha`
  ADD CONSTRAINT `ficha_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`paciente_id`),
  ADD CONSTRAINT `ficha_ibfk_2` FOREIGN KEY (`seguro_id`) REFERENCES `seguro_medico` (`seguro_id`);

--
-- Filtros para la tabla `historial_medico`
--
ALTER TABLE `historial_medico`
  ADD CONSTRAINT `historial_medico_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`paciente_id`);

--
-- Filtros para la tabla `hospitalizacion`
--
ALTER TABLE `hospitalizacion`
  ADD CONSTRAINT `hospitalizacion_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`paciente_id`),
  ADD CONSTRAINT `hospitalizacion_ibfk_2` FOREIGN KEY (`medico_id`) REFERENCES `medico` (`medico_id`),
  ADD CONSTRAINT `hospitalizacion_ibfk_3` FOREIGN KEY (`habitacion_id`) REFERENCES `habitacion` (`habitacion_id`);

--
-- Filtros para la tabla `medico`
--
ALTER TABLE `medico`
  ADD CONSTRAINT `medico_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `paciente`
--
ALTER TABLE `paciente`
  ADD CONSTRAINT `paciente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `receta`
--
ALTER TABLE `receta`
  ADD CONSTRAINT `receta_ibfk_1` FOREIGN KEY (`tratamiento_id`) REFERENCES `tratamiento` (`tratamiento_id`),
  ADD CONSTRAINT `receta_ibfk_2` FOREIGN KEY (`medicamento_id`) REFERENCES `medicamentos` (`medicamento_id`);

--
-- Filtros para la tabla `recursos_hospitalarios`
--
ALTER TABLE `recursos_hospitalarios`
  ADD CONSTRAINT `recursos_hospitalarios_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `area` (`area_id`);

--
-- Filtros para la tabla `tratamiento`
--
ALTER TABLE `tratamiento`
  ADD CONSTRAINT `tratamiento_ibfk_1` FOREIGN KEY (`historial_id`) REFERENCES `historial_medico` (`historial_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
