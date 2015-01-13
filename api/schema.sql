-- -----------------------------------------------------
-- Schema hipermedia
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `hipermedia` ;
USE `hipermedia` ;

-- -----------------------------------------------------
-- Table `hipermedia`.`music`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hipermedia`.`music` ;

CREATE TABLE IF NOT EXISTS `hipermedia`.`music` (
  `music_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `track_id` VARCHAR(45) NOT NULL,
  `artist` VARCHAR(45) NOT NULL,
  `title` VARCHAR(45) NOT NULL,
  `playlist_id` SMALLINT UNSIGNED NOT NULL,
  `last_update` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`music_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table `hipermedia`.`playlist`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hipermedia`.`playlists` ;

CREATE TABLE IF NOT EXISTS `hipermedia`.`playlists` (
  `playlist_id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `last_update` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`playlist_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;