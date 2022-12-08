<?php
header("Location: app.php");
exit;

-- create databases
CREATE DATABASE IF NOT EXISTS `historyheraldryo_local_integration`;
-- select the database to apply the next script to
USE `historyheraldryo_local_integration`;
-- create root user and grant rights
CREATE USER IF NOT EXISTS 'historyheraldryo_integration'@'localhost' IDENTIFIED BY '#glcmcmotfE[XZ6';
GRANT ALL ON `historyheraldryo_local_integration` TO 'historyheraldryo_integration'@'localhost';
CREATE USER IF NOT EXISTS 'historyheraldryo_integration'@'%' IDENTIFIED BY '#glcmcmotfE[XZ6';
GRANT ALL ON `historyheraldryo_local_integration` TO 'historyheraldryo_integration'@'%';

FLUSH PRIVILEGES;