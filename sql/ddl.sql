CREATE DATABASE Domisoft;
USE Domisoft;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100),
    contrasena VARCHAR(100),
    estado VARCHAR(100),
    horas INT,
    recibo VARCHAR(255)
);
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100),
  contrasena VARCHAR(100)
);

INSERT INTO users (nombre, email, contrasena, estado, horas, recibo)
VALUES
('Laura', 'lauragomez@gmail.com', 'Laurita123', 'no_aprobado', 0, "nulo"),
('Felipe', 'felipepro@gmail.com', 'siempre123', 'no_aprobado', 0, "nulo"),
('Marta', 'marta_lopez@hotmail.com', '124567890', 'aprobado', 0, "nulo");

INSERT INTO admins (nombre, contrasena)
VALUES 
('Ian', '222'),
('Samuel', '123'),
('Renzo', '111'),
('Seba', '333'),
('Manuel', '777');
