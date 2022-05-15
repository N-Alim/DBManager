-- MySQL Script generated by MySQL Workbench
-- Sun May 15 16:21:48 2022
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema db_manager
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema db_manager
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `db_manager` DEFAULT CHARACTER SET utf8 ;
USE `db_manager` ;

-- -----------------------------------------------------
-- Table `db_manager`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_manager`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(255) NOT NULL,
  `last_name` VARCHAR(255) NOT NULL,
  `mail` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `mail_UNIQUE` (`mail` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_manager`.`dbs`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_manager`.`dbs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `user_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_dbs_users_idx` (`user_id` ASC),
  CONSTRAINT `fk_dbs_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `db_manager`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;