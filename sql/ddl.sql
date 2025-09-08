/*
================================================================================
* Project:       https://github.com/domisoft-dev/coviamu
* File:          ddl.sql
* Author:        domisoft-dev
* Description:   Script de creación de base de datos para el proyecto COVIAMU.
*                Contiene la creación de la base, tablas de usuarios y administradores,
*                y registros iniciales de ejemplo.
================================================================================
* Sections Overview:
* - CREATE DATABASE Domisoft: crea la base de datos principal.
* - USE Domisoft: selecciona la base de datos para operaciones posteriores.
* - CREATE TABLE users: define tabla de usuarios con campos id, nombre, email, contrasena, estado, horas y recibo.
* - CREATE TABLE admins: define tabla de administradores con campos id, nombre y contrasena.
* - INSERT INTO users: inserta usuarios de prueba con distintos estados y datos iniciales.
* - INSERT INTO admins: inserta administradores de prueba con credenciales iniciales.
================================================================================
*/

CREATE DATABASE Domisoft;
USE Domisoft;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100),
    contrasena VARCHAR(100),
    estado VARCHAR(255),
    horas INT,
    recibo VARCHAR(255),
    recibo_aprobado TINYINT(1) DEFAULT 0
);
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  contrasena VARCHAR(100)
);

INSERT INTO users (nombre, email, contrasena, estado, horas)
VALUES
('Laura', 'lauragomez@gmail.com', MD5('Laurita123'), 'no_aprobado', 0),
('Felipe', 'felipepro@gmail.com', MD5('siempre123'), 'no_aprobado', 0),
('Marta', 'marta_lopez@hotmail.com', MD5('martita55'), 'aprobado', 0);

INSERT INTO admins (nombre, contrasena)
VALUES 
('Ian', MD5('222')),
('Samuel', MD5('123')),
('Renzo', MD5('111')),
('Seba', MD5('333')),
('Manuel', MD5('777'));
