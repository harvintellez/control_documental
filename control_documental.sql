-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-03-2026 a las 17:12:59
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
-- Base de datos: `control_documental`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id` int(11) NOT NULL,
  `codigo_trabajador` varchar(20) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `descripcion_oficio` text DEFAULT NULL,
  `archivo_adjunto` varchar(255) DEFAULT NULL,
  `tipo_documento` enum('Embargo Judicial','Pensión Alimenticia','Otro') DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`id`, `codigo_trabajador`, `nombre_completo`, `cedula`, `foto_perfil`, `descripcion_oficio`, `archivo_adjunto`, `tipo_documento`, `fecha_registro`) VALUES
(6, '352821', 'WINSTON PAÚL MARTÍNEZ MALTEZ', '084-141187-0000Q', 'uploads/fotos/1772812388_352821.jpg', 'Oficio de Noviembre 2017, se aplicó 25% de su salario entregados a Gilma Antonia Campos Ordoñez 084-040988-0001B, Oficio Marzo 2026, se levanta el embargo.-', 'uploads/documentos/1772812388_Oficio 352821.pdf', 'Pensión Alimenticia', '2026-03-06 15:53:08'),
(7, '364201', 'MILTON JOSE MASIAS RUIZ', '001-250690-0000W', 'uploads/fotos/1773094572_364201.jpg', 'Embargo del Banco de Finanzas por U$ 3,358.87. Actualmente tiene activo un embargo de INSTRACREDIT.', 'uploads/documentos/1773094572_escan_2026030918251200.pdf', 'Embargo Judicial', '2026-03-09 22:16:12'),
(8, '1687', 'JUAN RICARDO VEGA CHÁVEZ', '084-210883-0001J', '', 'En este caso hubo un oficio para devolver todas las sumas retenidas, conforme embargo trabado en 2025 gestionado por banco AVANZ', 'uploads/documentos/1774048343_Devolución al colaborador de todas las retenciones efectuadas.pdf', 'Embargo Judicial', '2026-03-20 23:12:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','consulta') DEFAULT 'consulta'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `rol`) VALUES
(1, 'admin', '$2y$10$N8XCvzT3ZkVtne46ZhekR.Xzc.JSag1VZ2vC3WZIUj1rl.JFM2r.e', 'admin'),
(2, 'HTellez', '$2y$10$Kiwl.9MWbm3vqGVwnHOi9ekWQvrUTy4b/bBVaGVHu0CyClTjPtVrW', 'admin'),
(3, 'usuario', '$2y$10$NWmGXXWgVG9E5OwUVNv//eWBUn3VnkBvS6U3azZng.KAFG3USGvf6', 'admin');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_trabajador` (`codigo_trabajador`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
