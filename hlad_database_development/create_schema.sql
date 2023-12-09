-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema hlad
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `hlad` ;

-- -----------------------------------------------------
-- Schema hlad
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `hlad` DEFAULT CHARACTER SET utf8 ;
USE `hlad` ;

-- -----------------------------------------------------
-- Table `hlad`.`uzivatel`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`uzivatel` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nick` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE UNIQUE INDEX `nick_UNIQUE` ON `hlad`.`uzivatel` (`nick` ASC);
CREATE UNIQUE INDEX `email_UNIQUE` ON `hlad`.`uzivatel` (`password` ASC);


-- -----------------------------------------------------
-- Table `hlad`.`recept`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`recept` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(40) NOT NULL,
  `description` VARCHAR(1000) NULL,
  `sukromny` TINYINT NOT NULL,
  `poc_zobrazeni` INT NOT NULL DEFAULT 0,
  `poc_likes` INT NOT NULL DEFAULT 0,
  `uzivatel_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_recept_uzivatel1`
    FOREIGN KEY (`uzivatel_id`)
    REFERENCES `hlad`.`uzivatel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_recept_uzivatel1_idx` ON `hlad`.`recept` (`uzivatel_id` ASC);


-- -----------------------------------------------------
-- Table `hlad`.`kategoria_receptu`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`kategoria_receptu` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE UNIQUE INDEX `nazov_UNIQUE` ON `hlad`.`kategoria_receptu` (`name` ASC);


-- -----------------------------------------------------
-- Table `hlad`.`recept_has_kategoria_receptu`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`recept_has_kategoria_receptu` (
  `recept_id` INT NOT NULL,
  `kategoria_receptu_id` INT NOT NULL,
  PRIMARY KEY (`recept_id`, `kategoria_receptu_id`),
  CONSTRAINT `fk_recept_has_kategoria_receptu_recept`
    FOREIGN KEY (`recept_id`)
    REFERENCES `hlad`.`recept` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_recept_has_kategoria_receptu_kategoria_receptu1`
    FOREIGN KEY (`kategoria_receptu_id`)
    REFERENCES `hlad`.`kategoria_receptu` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_recept_has_kategoria_receptu_kategoria_receptu1_idx` ON `hlad`.`recept_has_kategoria_receptu` (`kategoria_receptu_id` ASC);

CREATE INDEX `fk_recept_has_kategoria_receptu_recept_idx` ON `hlad`.`recept_has_kategoria_receptu` (`recept_id` ASC);


-- -----------------------------------------------------
-- Table `hlad`.`ingrediencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`ingrediencia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE UNIQUE INDEX `name_UNIQUE` ON `hlad`.`ingrediencia` (`name` ASC);


-- -----------------------------------------------------
-- Table `hlad`.`merna_jednotka`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`merna_jednotka` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `unit` VARCHAR(5) NOT NULL,
  `nazov` VARCHAR(45) NOT NULL,
  `typ` CHAR(1) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE UNIQUE INDEX `nazov_UNIQUE` ON `hlad`.`merna_jednotka` (`nazov` ASC);

CREATE UNIQUE INDEX `unit_UNIQUE` ON `hlad`.`merna_jednotka` (`unit` ASC);


-- -----------------------------------------------------
-- Table `hlad`.`ingrediencia_receptu`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`ingrediencia_receptu` (
  `recept_id` INT NOT NULL,
  `ingrediencia_id` INT NOT NULL,
  `mnozstvo` FLOAT NOT NULL,
  `merna_jednotka_id` INT NOT NULL,
  PRIMARY KEY (`recept_id`, `ingrediencia_id`),
  CONSTRAINT `fk_recept_has_ingrediencia_recept1`
    FOREIGN KEY (`recept_id`)
    REFERENCES `hlad`.`recept` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_recept_has_ingrediencia_ingrediencia1`
    FOREIGN KEY (`ingrediencia_id`)
    REFERENCES `hlad`.`ingrediencia` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ingrediencia_receptu_merna_jednotka1`
    FOREIGN KEY (`merna_jednotka_id`)
    REFERENCES `hlad`.`merna_jednotka` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_recept_has_ingrediencia_ingrediencia1_idx` ON `hlad`.`ingrediencia_receptu` (`ingrediencia_id` ASC);

CREATE INDEX `fk_recept_has_ingrediencia_recept1_idx` ON `hlad`.`ingrediencia_receptu` (`recept_id` ASC);

CREATE INDEX `fk_ingrediencia_receptu_merna_jednotka1_idx` ON `hlad`.`ingrediencia_receptu` (`merna_jednotka_id` ASC);


-- -----------------------------------------------------
-- Table `hlad`.`image`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`image` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `original_name` VARCHAR(45) NOT NULL,
  `recept_id` INT NULL,
  `uzivatel_id` INT NULL,
  `path` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_image_recept1`
    FOREIGN KEY (`recept_id`)
    REFERENCES `hlad`.`recept` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_image_uzivatel1`
    FOREIGN KEY (`uzivatel_id`)
    REFERENCES `hlad`.`uzivatel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_image_recept1_idx` ON `hlad`.`image` (`recept_id` ASC);

CREATE INDEX `fk_image_uzivatel1_idx` ON `hlad`.`image` (`uzivatel_id` ASC);


-- -----------------------------------------------------
-- Table `hlad`.`migration_data`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`migration_data` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `date` VARCHAR(45) NOT NULL,
  `mig_name` VARCHAR(45) NOT NULL,
  `version` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hlad`.`uzivatel_can_see_recept`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`uzivatel_can_see_recept` (
  `uzivatel_id` INT NOT NULL,
  `recept_id` INT NOT NULL,
  PRIMARY KEY (`uzivatel_id`, `recept_id`),
  CONSTRAINT `fk_uzivatel_has_recept_uzivatel1`
    FOREIGN KEY (`uzivatel_id`)
    REFERENCES `hlad`.`uzivatel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_uzivatel_has_recept_recept1`
    FOREIGN KEY (`recept_id`)
    REFERENCES `hlad`.`recept` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_uzivatel_has_recept_recept1_idx` ON `hlad`.`uzivatel_can_see_recept` (`recept_id` ASC);

CREATE INDEX `fk_uzivatel_has_recept_uzivatel1_idx` ON `hlad`.`uzivatel_can_see_recept` (`uzivatel_id` ASC);


-- -----------------------------------------------------
-- Table `hlad`.`skupina_uzivatelov`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`skupina_uzivatelov` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `hlad`.`skupina_can_see_recept`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`skupina_can_see_recept` (
  `skupina_uzivatelov_id` INT NOT NULL,
  `recept_id` INT NOT NULL,
  PRIMARY KEY (`skupina_uzivatelov_id`, `recept_id`),
  CONSTRAINT `fk_skupina_uzivatelov_has_recept_skupina_uzivatelov1`
    FOREIGN KEY (`skupina_uzivatelov_id`)
    REFERENCES `hlad`.`skupina_uzivatelov` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_skupina_uzivatelov_has_recept_recept1`
    FOREIGN KEY (`recept_id`)
    REFERENCES `hlad`.`recept` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_skupina_uzivatelov_has_recept_recept1_idx` ON `hlad`.`skupina_can_see_recept` (`recept_id` ASC);

CREATE INDEX `fk_skupina_uzivatelov_has_recept_skupina_uzivatelov1_idx` ON `hlad`.`skupina_can_see_recept` (`skupina_uzivatelov_id` ASC);


-- -----------------------------------------------------
-- Table `hlad`.`mail_subscription`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`mail_subscription` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `mail` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE UNIQUE INDEX `mail_UNIQUE` ON `hlad`.`mail_subscription` (`mail` ASC);


-- -----------------------------------------------------
-- Table `hlad`.`hodnotenie`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hlad`.`hodnotenie` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uzivatel_id` INT NOT NULL,
  `recept_id` INT NOT NULL,
  `hodnotenie` INT(1) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_hodnotenie_uzivatel1`
    FOREIGN KEY (`uzivatel_id`)
    REFERENCES `hlad`.`uzivatel` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_hodnotenie_recept1`
    FOREIGN KEY (`recept_id`)
    REFERENCES `hlad`.`recept` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_hodnotenie_uzivatel1_idx` ON `hlad`.`hodnotenie` (`uzivatel_id` ASC);

CREATE INDEX `fk_hodnotenie_recept1_idx` ON `hlad`.`hodnotenie` (`recept_id` ASC);

ALTER TABLE `hlad`.`image` ADD UNIQUE(`name`);
ALTER TABLE `hlad`.`migration_data` ADD UNIQUE(`mig_name`);
ALTER TABLE `hlad`.`migration_data` ADD UNIQUE(`version`);
ALTER TABLE `hlad`.`skupina_uzivatelov` ADD UNIQUE(`name`);
ALTER TABLE `hlad`.`uzivatel` ADD UNIQUE(`nick`);
ALTER TABLE `hlad`.`uzivatel` ADD UNIQUE(`email`);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
