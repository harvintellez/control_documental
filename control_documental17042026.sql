-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-04-2026 a las 22:14:16
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
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `nombre_adjunto` varchar(255) DEFAULT NULL,
  `usuario_registro` varchar(255) DEFAULT NULL,
  `valor_inicial` decimal(10,2) DEFAULT NULL,
  `valor_final` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`id`, `codigo_trabajador`, `nombre_completo`, `cedula`, `foto_perfil`, `descripcion_oficio`, `archivo_adjunto`, `tipo_documento`, `fecha_registro`, `nombre_adjunto`, `usuario_registro`, `valor_inicial`, `valor_final`) VALUES
(6, '352821', 'WINSTON PAÚL MARTÍNEZ MALTEZ', '084-141187-0000Q', 'uploads/fotos/1772812388_352821.jpg', 'Oficio de Noviembre 2017, se aplicó 25% de su salario entregados a Gilma Antonia Campos Ordoñez 084-040988-0001B, Oficio Marzo 2026, se levanta el embargo. Fin del Caso.-', 'uploads/documentos/1772812388_Oficio 352821.pdf', 'Pensión Alimenticia', '2026-03-06 15:53:08', NULL, NULL, NULL, NULL),
(7, '364201', 'MILTON JOSE MASIAS RUIZ', '001-250690-0000W', 'uploads/fotos/1773094572_364201.jpg', 'Embargo del Banco de Finanzas por U$ 3,358.87. Actualmente tiene activo un embargo de INSTRACREDIT.', 'uploads/documentos/1773094572_escan_2026030918251200.pdf', 'Embargo Judicial', '2026-03-09 22:16:12', NULL, NULL, NULL, NULL),
(8, '1687', 'JUAN RICARDO VEGA CHÁVEZ', '084-210883-0001J', '', 'En este caso hubo un oficio para devolver todas las sumas retenidas, conforme embargo trabado en 2025 gestionado por banco AVANZ', 'uploads/documentos/1774048343_Devolución al colaborador de todas las retenciones efectuadas.pdf', 'Embargo Judicial', '2026-03-20 23:12:23', NULL, NULL, NULL, NULL),
(10, '98477', 'MELVIN MARTIN ESPINOZA HERNANDEZ', '084-290782-0003C', '', '15% a favor de su hija Helen María Dávila. Monto entregado a Zoraya del Rosario Dávila Rodríguez.', 'uploads/documentos/1775658450_98477 Melvin Espinoza.pdf', 'Pensión Alimenticia', '2026-04-08 14:27:30', NULL, NULL, NULL, NULL),
(11, '359234', 'MARCOS DAVID VARGAS MORALES', '281-090594-0003G', '', 'El monto deberá ser entregado Verónica Toval Solis.', 'uploads/documentos/1776282663_Modificación Embargo Marcos Vargas_0001.pdf', 'Pensión Alimenticia', '2026-04-15 19:51:03', NULL, NULL, NULL, NULL),
(13, '352432', 'HARVIN RAFAEL TELLEZ TELLEZ', '281-061281-005t', NULL, 'informacion de prueba.-', 'uploads/documentos/doc_1776456204_69e2920c197ae.pdf', 'Otro', '2026-04-17 20:03:24', NULL, NULL, 12.50, 20.50);

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
(2, 'htellez', '$2y$10$Kiwl.9MWbm3vqGVwnHOi9ekWQvrUTy4b/bBVaGVHu0CyClTjPtVrW', 'admin'),
(5, 'usuario', '$2y$10$FLos7Lv2jNnHPM53qrH3AuuWMkRvpK8PfSFwQ/7wt9QZuvq.RMXsS', 'consulta'),
(7, 'amontealegre', '$2y$10$InIR5QQPsS7DlGXVnZHjcuFRfAQgyCS9YGOE0Kh3s9Qwvnp7aHcRW', 'admin'),
(8, 'amachado', '$2y$10$D52HmO.d0eRjsLzgT5Dviu829vDpekZs98VDOZCqHdRhKg88CJfeK', 'admin'),
(9, 'mcarrillo', '$2y$10$MoDM4j8uNbSzn0H2XK5stutMrhV3Yto3qm/SZyQJnOAA0Ml5Bc4ba', 'consulta'),
(10, 'nmadrigal', '$2y$10$LZhSH8mJmGyrCdYdb0E03uEwWMUKoeoSBBQy1pc7kCTxuOatAfyNe', 'consulta'),
(11, 'aramos', '$2y$10$1hEC57UQGhKq7XityoPUIeSQu.XdnxEggfCecYHULNu91qhxFmY.q', 'consulta'),
(12, 'jvalladares', '$2y$10$Wf2KngB6.lYJF4.5aixCZuWlTTPltOz3MlY7lqpMnThDXMBp/xZiS', 'consulta'),
(13, 'bgodinez', '$2y$10$TLhN13TY27yGISvpWy.8peaHM4gdJLmgn6dIowB0AEkqaWpnDD0SK', 'consulta');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
