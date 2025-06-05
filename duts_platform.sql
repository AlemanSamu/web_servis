-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-06-2025 a las 00:47:31
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `duts_platform`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `duts_transactions`
--

CREATE TABLE `duts_transactions` (
  `id` int(11) NOT NULL,
  `id_origen` int(11) DEFAULT NULL,
  `id_destino` int(11) DEFAULT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `duts_transactions`
--

INSERT INTO `duts_transactions` (`id`, `id_origen`, `id_destino`, `cantidad`, `fecha`) VALUES
(1, 4, 1, 1000000.00, '2025-06-03 23:29:50'),
(2, 4, 2, 1000000.00, '2025-06-03 23:29:50'),
(3, 4, 3, 1000000.00, '2025-06-03 23:29:50'),
(4, 1, 2, 900000.00, '2025-06-03 23:29:50'),
(5, 2, 1, 800000.00, '2025-06-03 23:29:50'),
(6, 1, 3, 200000.00, '2025-06-03 23:29:50'),
(7, 3, 1, 100000.00, '2025-06-03 23:29:50'),
(8, 2, 5, 100.00, '2025-06-04 01:02:07'),
(9, 2, 5, 99900.00, '2025-06-04 01:03:58'),
(10, 5, 2, 100000.00, '2025-06-04 01:40:55'),
(11, 2, 5, 200000.00, '2025-06-04 01:41:34'),
(12, 5, 2, 100000.00, '2025-06-04 19:44:51'),
(13, 2, 8, 100000.00, '2025-06-04 19:59:13'),
(14, 2, 9, 100000.00, '2025-06-04 19:59:18'),
(15, 2, 10, 100000.00, '2025-06-04 19:59:24'),
(16, 2, 11, 100000.00, '2025-06-04 19:59:28'),
(17, 2, 12, 100000.00, '2025-06-04 19:59:30'),
(18, 2, 13, 100000.00, '2025-06-04 19:59:33'),
(19, 2, 14, 100000.00, '2025-06-04 19:59:39'),
(20, 2, 15, 100000.00, '2025-06-04 19:59:43'),
(21, 2, 16, 100000.00, '2025-06-04 19:59:50'),
(22, 3, 17, 100000.00, '2025-06-04 20:00:42'),
(23, 3, 6, 100000.00, '2025-06-04 20:00:50'),
(24, 3, 18, 200000.00, '2025-06-04 22:52:40'),
(25, 5, 21, 50000.00, '2025-06-05 21:56:40'),
(26, 21, 5, 50000.00, '2025-06-05 22:08:34'),
(27, 5, 20, 50000.00, '2025-06-05 22:33:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `tipo_evento` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `nombre`, `descripcion`, `fecha`, `tipo_evento`) VALUES
(1, 'UTSmart 2025', 'Feria de proyectos tecnológicos', '2025-10-15', 'UTSmart'),
(2, 'CIINATIC Conferencia', 'Conferencia de innovación y TIC', '2025-09-20', 'CIINATIC'),
(3, 'Grados UTS Diciembre 2025', 'Ceremonia de grados para diciembre', '2025-12-10', 'Grados UTS'),
(4, 'Expobienestar Salud', 'Feria de salud y bienestar para la comunidad UTS', '2025-11-05', 'Expobienestar'),
(5, 'SmartFest', 'Fiesta universidad', '2025-06-05', 'Fiesta'),
(6, 'Feria cientifica', 'Feria UTS', '2025-06-13', 'Feria'),
(8, 'Feria de Modas', 'Feria', '2025-06-06', 'Feria'),
(9, 'Aniversario UTS', 'Aniversario UTS', '2025-06-06', 'Aniversario UTS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos_registro`
--

CREATE TABLE `eventos_registro` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_evento` int(11) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos_registro`
--

INSERT INTO `eventos_registro` (`id`, `id_usuario`, `id_evento`, `fecha_registro`) VALUES
(1, 1, 1, '2025-06-03 23:29:50'),
(2, 1, 2, '2025-06-03 23:29:50'),
(3, 2, 1, '2025-06-03 23:29:50'),
(4, 3, 2, '2025-06-03 23:29:50'),
(5, 1, 4, '2025-06-03 23:29:50'),
(7, 14, 5, '2025-06-04 19:53:14'),
(8, 14, 2, '2025-06-04 19:53:41'),
(9, 18, 8, '2025-06-04 22:51:47'),
(11, 21, 5, '2025-06-05 22:08:42'),
(12, 5, 5, '2025-06-05 22:33:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nombres` varchar(100) DEFAULT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ciudad` varchar(50) DEFAULT NULL,
  `pais` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `intereses` text DEFAULT NULL,
  `programa` varchar(100) DEFAULT NULL,
  `semestre` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol` enum('estudiante','profesor','admin') NOT NULL DEFAULT 'estudiante',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `nombres`, `apellidos`, `email`, `ciudad`, `pais`, `descripcion`, `intereses`, `programa`, `semestre`, `username`, `password`, `rol`, `created_at`) VALUES
(1, 'Juan', 'Pérez', 'juan@ejemplo.com', 'Bucaramanga', 'Colombia', 'Estudiante de ingeniería', 'Finanzas, Tecnología', 'Ingeniería de Sistemas', 4, 'juanp', '$2y$10$jV3SlUPRdAWOM6nYb7l6T.MunMLruYg2LQAtK.hm.CaLjUYWlBjya', 'estudiante', '2025-06-03 23:29:50'),
(2, 'Maria', 'Gómez', 'maria@ejemplo.com', 'Floridablanca', 'Colombia', 'Docente de matemáticas', 'Educación, Investigación', 'Matemáticas', NULL, 'mariag', '$2y$10$jV3SlUPRdAWOM6nYb7l6T.MunMLruYg2LQAtK.hm.CaLjUYWlBjya', 'estudiante', '2025-06-03 23:29:50'),
(3, 'Carlos', 'Ramírez', 'carlos@ejemplo.com', 'Bucaramanga', 'Colombia', 'Administrativo de la UTS', 'Gestión, Eventos', 'Administración', NULL, 'carlosr', '$2y$10$jV3SlUPRdAWOM6nYb7l6T.MunMLruYg2LQAtK.hm.CaLjUYWlBjya', 'estudiante', '2025-06-03 23:29:50'),
(4, 'Sistema', 'DUTS', 'sistema@duts.com', 'N/A', 'N/A', 'Usuario para emisión de DUTS', 'N/A', 'N/A', NULL, 'duts_system', '$2y$10$jV3SlUPRdAWOM6nYb7l6T.MunMLruYg2LQAtK.hm.CaLjUYWlBjya', '', '2025-06-03 23:29:50'),
(5, 'Marlon Fabian', 'Joya Mejía', 'marlonfabian1805@gmail.com', 'Bucaramanga', 'Colombia', 'Lindo', 'Gaming', 'Sitemas', 7, 'marlonm', '$2y$10$el.GSXXFh2j/QPXj3xKEjOWyLTZY1jvk1GLpJau7nkmYfoMKDi/Oy', 'estudiante', '2025-06-03 23:34:53'),
(6, 'Samuel', 'Aleman', 'samuel@gmail.com', 'Bucaramanga', 'Barcelona', 'Alto', 'Mujeres', 'Sistemas', 6, 'samuela', '$2y$10$IllpiLkQhNayAE4GwRmsou5iiA2rVuUcc4xkO9WK2XmDZUTZMSwue', 'estudiante', '2025-06-04 01:45:58'),
(7, 'admin', 'admin', 'admin@gmail.com', 'Bucaramanga', 'Colombia', 'administrador', 'Gaming', 'Sistemas', 6, 'admin', '$2y$10$67st7LjExqMwD/PsVl7pvOGlcMdP/oNbmzjJ0rpcFBe1CFQnMipJm', 'admin', '2025-06-04 19:42:33'),
(8, 'Pedro 39196', 'Torres', 'estudiante39196@example.com', 'Cartagena', 'Colombia', 'Estudiante de DUTS App, interesado en aprender.', 'Tecnologia, Lectura, Viajes', 'Derecho', 8, 'estudiante39196', '$2y$10$lIY12mn0UvaeEPDrOQzgCeeRQ3Y3LuCkYolkO2RWjRwkBf3cUs7ba', 'estudiante', '2025-06-04 19:50:48'),
(9, 'Juan 22122', 'Rodriguez', 'estudiante22122@example.com', 'Armenia', 'Ecuador', 'Estudiante de DUTS App, interesado en aprender.', 'Tecnologia, Lectura, Viajes', 'Administracion de Empresas', 4, 'estudiante22122', '$2y$10$G1XzBsuQRoe8EsPBV67sbO3Db1jL1GXP/dMOzl4pfnT21q7ZGOLnu', 'estudiante', '2025-06-04 19:50:48'),
(10, 'Juan 20182', 'Torres', 'estudiante20182@example.com', 'Cali', 'Ecuador', 'Estudiante de DUTS App, interesado en aprender.', 'Tecnologia, Lectura, Viajes', 'Comunicacion Social', 4, 'estudiante20182', '$2y$10$hY7j9HVY5FfNXqYyryCs9.xIUikuy0bYYNRYrs0jqBBfKzoBKMTXG', 'estudiante', '2025-06-04 19:50:48'),
(11, 'Ana 35267', 'Martinez', 'estudiante35267@example.com', 'Bogota', 'Canada', 'Estudiante de DUTS App, interesado en aprender.', 'Tecnologia, Lectura, Viajes', 'Comunicacion Social', 4, 'estudiante35267', '$2y$10$ViJiwEfq.p.BqhoOPl52LO7Y541SMlyJiO0V82b/NZctlERMxzPp.', 'estudiante', '2025-06-04 19:50:48'),
(12, 'Pedro 27923', 'Martinez', 'estudiante27923@example.com', 'Manizales', 'Argentina', 'Estudiante de DUTS App, interesado en aprender.', 'Tecnologia, Lectura, Viajes', 'Medicina', 8, 'estudiante27923', '$2y$10$6cBpHuR9Uc24n2rPVZmFG.B3zy25UYZj9FeWlq9TOtk3hiIfnKVTu', 'estudiante', '2025-06-04 19:50:48'),
(13, 'Valeria 30651', 'Gomez', 'estudiante30651@example.com', 'Popayan', 'Brasil', 'Estudiante de DUTS App, interesado en aprender.', 'Tecnologia, Lectura, Viajes', 'Marketing', 4, 'estudiante30651', '$2y$10$PnNn5f/ftJkuI03A7tmEXu1hpcQSLCcByGexuF7iXB8uDDSIeFb1.', 'estudiante', '2025-06-04 19:50:48'),
(14, 'Diego 19100', 'Perez', 'estudiante19100@example.com', 'Barranquilla', 'Ecuador', 'Estudiante de DUTS App, interesado en aprender.', 'Tecnologia, Lectura, Viajes', 'Marketing', 5, 'estudiante19100', '$2y$10$XSN0QwmCXU6dZ8Fu6/Szke/ET5eTxLYBQcQmUC/r.2w.JC3JgFpBG', 'estudiante', '2025-06-04 19:50:48'),
(15, 'Carlos 16787', 'Perez', 'estudiante16787@example.com', 'Manizales', 'Mexico', 'Estudiante de DUTS App, interesado en aprender.', 'Tecnologia, Lectura, Viajes', 'Administracion de Empresas', 2, 'estudiante16787', '$2y$10$JgxS9xQ97tJdHzVVdAcL.uUV7zioJfiWFlL6ca34VrF31MN1XmIH2', 'estudiante', '2025-06-04 19:50:48'),
(16, 'Maria 76900', 'Martinez', 'estudiante76900@example.com', 'Popayan', 'Peru', 'Estudiante de DUTS App, interesado en aprender.', 'Tecnologia, Lectura, Viajes', 'Administracion de Empresas', 3, 'estudiante76900', '$2y$10$h/Swy2hAE.kBQCunyAtWfOzcwiAVIAN6jPyFpCxEyqq2Mnod1u5f2', 'estudiante', '2025-06-04 19:50:48'),
(17, 'Andres 29786', 'Ramirez', 'estudiante29786@example.com', 'Bogota', 'España', 'Estudiante de DUTS App, interesado en aprender.', 'Tecnologia, Lectura, Viajes', 'Marketing', 3, 'estudiante29786', '$2y$10$114gf0hX9s0VUZszqq0bKeTCNwsUZ1D80xycDnxr1o.fEne46cXUK', 'estudiante', '2025-06-04 19:50:48'),
(18, 'Saul', 'Aguirre', 'saul@gmail.com', 'Bucaramanga', 'Colombia', 'Estudiante UTS', 'Gaming', 'Sistemas', 6, 'saul', '$2y$10$qn81747Eo.TQ7pyVM3ik0e9SSrkn0f2lVaA.gyHY7Ffn1jcpoqa/m', 'admin', '2025-06-04 22:50:00'),
(19, 'Raul', 'Ramirez', 'raul@gmail.com', 'Bucaramanga', 'Colombia', '', 'Gaming', 'Sitemas', 7, 'raul', '$2y$10$ZFhiAKSMh9x5ugBE7iDNfuz5hpE6bHnT7J2ZuiQarCx.V0qIAtXt.', 'admin', '2025-06-05 20:15:36'),
(20, 'Darai', 'Zalcedo', 'darai@gmail.com', 'Bucaramanga', 'Colombia', '', 'Gaming', 'Sitemas', 6, 'darai', '$2y$10$jYIBf.pfJKGltCS1SkXWruKW3eOmFmnD.PVa6BjlyXrcmEB77.BaC', 'profesor', '2025-06-05 21:21:06'),
(21, 'Camilo', 'Mendoza', 'camilo@gmail.com', 'Bucaramanga', 'Colombia', '', 'Gaming', 'Sitemas', 8, 'camilo', '$2y$10$YYc6ymVydeqr/8.aLwI9N.5tdfCt1TEcbh1ykoWN5hNjj5jKLpo0m', 'estudiante', '2025-06-05 21:55:22');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `duts_transactions`
--
ALTER TABLE `duts_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_origen` (`id_origen`),
  ADD KEY `id_destino` (`id_destino`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `eventos_registro`
--
ALTER TABLE `eventos_registro`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_evento` (`id_evento`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `duts_transactions`
--
ALTER TABLE `duts_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `eventos_registro`
--
ALTER TABLE `eventos_registro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `duts_transactions`
--
ALTER TABLE `duts_transactions`
  ADD CONSTRAINT `duts_transactions_ibfk_1` FOREIGN KEY (`id_origen`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `duts_transactions_ibfk_2` FOREIGN KEY (`id_destino`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `eventos_registro`
--
ALTER TABLE `eventos_registro`
  ADD CONSTRAINT `eventos_registro_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `eventos_registro_ibfk_2` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
