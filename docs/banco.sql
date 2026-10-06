DROP DATABASE IF EXISTS competicaoRobosAPI;

CREATE DATABASE competicaoRobosAPI;
USE competicaoRobosAPI;

CREATE TABLE participantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(40) NOT NULL,
    robo VARCHAR(14) NOT NULL,
    tempo DECIMAL(14,3) NOT NULL
);

CREATE TABLE partidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomeparticipante1 VARCHAR(40) NOT NULL,
    nomeparticipante2 VARCHAR(40) NOT NULL
);