ALTER TABLE `selling_info` ADD `estadoEnvio` INT NULL AFTER `social`; 
ALTER TABLE `selling_info` CHANGE `estadoEnvio` `estadoEnvio` INT NULL DEFAULT '0'; 