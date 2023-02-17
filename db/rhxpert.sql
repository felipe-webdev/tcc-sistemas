-- ----------
-------------------------------------------
-- Schema rhxpert
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `rhxpert` DEFAULT CHARACTER SET utf8 ;
USE `rhxpert` ;

-- -----------------------------------------------------
-- Table `rhxpert`.`pessoas_tipos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pessoas_tipos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `descricao_UNIQUE` (`descricao` ASC))
;


-- -----------------------------------------------------
-- Table `rhxpert`.`contatos_tipos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`contatos_tipos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `descricao_UNIQUE` (`descricao` ASC))
;


-- -----------------------------------------------------
-- Table `rhxpert`.`enderecos_tipos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`enderecos_tipos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `descricao_UNIQUE` (`descricao` ASC))
;


-- -----------------------------------------------------
-- Table `rhxpert`.`dependentes_tipos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`dependentes_tipos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `descricao_UNIQUE` (`descricao` ASC))
;


-- -----------------------------------------------------
-- Table `rhxpert`.`entidades`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`entidades` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(255) NOT NULL,
  `posfixo_usuario` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC),
  UNIQUE INDEX `posfixo_usuario_UNIQUE` (`posfixo_usuario` ASC))
;


-- -----------------------------------------------------
-- Table `rhxpert`.`pessoas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`pessoas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_tipo` INT UNSIGNED NOT NULL,
  `id_entidade` INT UNSIGNED NOT NULL,
  `nome` VARCHAR(50) NOT NULL,
  `sobrenome` VARCHAR(100) NOT NULL,
  `nascimento` DATE NOT NULL,
  `cpf` VARCHAR(11),
  PRIMARY KEY (`id`),
  INDEX `fk_pessoas_pessoas_tipos_idx` (`id_tipo` ASC),
  INDEX `fk_pessoas_entidades_idx` (`id_entidade` ASC),
  CONSTRAINT `fk_pessoas_pessoas_tipos`
    FOREIGN KEY (`id_tipo`)
    REFERENCES `rhxpert`.`pessoas_tipos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pessoas_entidades`
    FOREIGN KEY (`id_entidade`)
    REFERENCES `rhxpert`.`entidades` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;


-- -----------------------------------------------------
-- Table `rhxpert`.`contatos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`contatos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_tipo` INT UNSIGNED NOT NULL,
  `id_pessoa` INT UNSIGNED NOT NULL,
  `contato` VARCHAR(50) NOT NULL,
  `observacao` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_contatos_contatos_tipos_idx` (`id_tipo` ASC),
  INDEX `fk_contatos_pessoas_idx` (`id_pessoa` ASC),
  CONSTRAINT `fk_contatos_contatos_tipos`
    FOREIGN KEY (`id_tipo`)
    REFERENCES `rhxpert`.`contatos_tipos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_contatos_pessoas`
    FOREIGN KEY (`id_pessoa`)
    REFERENCES `rhxpert`.`pessoas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;


-- -----------------------------------------------------
-- Table `rhxpert`.`enderecos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`enderecos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_tipo` INT UNSIGNED NOT NULL,
  `id_pessoa` INT UNSIGNED NOT NULL,
  `logradouro` VARCHAR(255) NOT NULL,
  `numero` INT NOT NULL,
  `complemento_numero` VARCHAR(50) NULL,
  `bairro` VARCHAR(50) NOT NULL,
  `cidade` VARCHAR(50) NOT NULL,
  `estado` VARCHAR(50) NULL,
  `uf` VARCHAR(4) NOT NULL,
  `cep` INT NULL,
  `observacao` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_enderecos_enderecos_tipos_idx` (`id_tipo` ASC),
  INDEX `fk_enderecos_pessoas_idx` (`id_pessoa` ASC),
  CONSTRAINT `fk_enderecos_enderecos_tipos`
    FOREIGN KEY (`id_tipo`)
    REFERENCES `rhxpert`.`enderecos_tipos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_enderecos_pessoas`
    FOREIGN KEY (`id_pessoa`)
    REFERENCES `rhxpert`.`pessoas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;


-- -----------------------------------------------------
-- Table `rhxpert`.`areas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`areas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  `observacao` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC))
;


-- -----------------------------------------------------
-- Table `rhxpert`.`cargos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`cargos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_area` INT UNSIGNED NOT NULL,
  `nome` VARCHAR(50) NOT NULL,
  `salario` DOUBLE UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC),
  INDEX `fk_cargos_areas_idx` (`id_area` ASC),
  CONSTRAINT `fk_cargos_areas`
    FOREIGN KEY (`id_area`)
    REFERENCES `rhxpert`.`areas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;


-- -----------------------------------------------------
-- Table `rhxpert`.`funcionarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`funcionarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pessoa` INT UNSIGNED NOT NULL,
  `id_cargo` INT UNSIGNED NOT NULL,
  `ativo` TINYINT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_funcionarios_pessoas_idx` (`id_pessoa` ASC),
  INDEX `fk_funcionarios_cargos_idx` (`id_cargo` ASC),
  CONSTRAINT `fk_funcionarios_pessoas`
    FOREIGN KEY (`id_pessoa`)
    REFERENCES `rhxpert`.`pessoas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_funcionarios_cargos`
    FOREIGN KEY (`id_cargo`)
    REFERENCES `rhxpert`.`cargos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;


-- -----------------------------------------------------
-- Table `rhxpert`.`dependentes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`dependentes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_tipo` INT UNSIGNED NOT NULL,
  `id_pessoa` INT UNSIGNED NOT NULL,
  `id_funcionario` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_dependentes_dependentes_tipos_idx` (`id_tipo` ASC),
  INDEX `fk_dependentes_pessoas_idx` (`id_pessoa` ASC),
  INDEX `fk_dependentes_funcionarios_idx` (`id_funcionario` ASC),
  CONSTRAINT `fk_dependentes_dependentes_tipos`
    FOREIGN KEY (`id_tipo`)
    REFERENCES `rhxpert`.`dependentes_tipos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dependentes_pessoas`
    FOREIGN KEY (`id_pessoa`)
    REFERENCES `rhxpert`.`pessoas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_dependentes_funcionarios`
    FOREIGN KEY (`id_funcionario`)
    REFERENCES `rhxpert`.`funcionarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;


-- -----------------------------------------------------
-- Table `rhxpert`.`bonificacoes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`bonificacoes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `valor` DOUBLE UNSIGNED NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  `id_funcionario` INT UNSIGNED NOT NULL,
  `ativo` CHAR NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_bonificacoes_funcionarios_idx` (`id_funcionario` ASC),
  CONSTRAINT `fk_bonificacoes_funcionarios`
    FOREIGN KEY (`id_funcionario`)
    REFERENCES `rhxpert`.`funcionarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;


-- -----------------------------------------------------
-- Table `rhxpert`.`usuarios_grupos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`usuarios_grupos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC))
;


-- -----------------------------------------------------
-- Table `rhxpert`.`usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rhxpert`.`usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_grupo` INT UNSIGNED NOT NULL,
  `id_funcionario` INT UNSIGNED NOT NULL,
  `usuario` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `usuario_UNIQUE` (`usuario` ASC),
  INDEX `fk_usuarios_usuarios_grupos_idx` (`id_grupo` ASC),
  INDEX `fk_usuarios_funcionarios_idx` (`id_funcionario` ASC),
  CONSTRAINT `fk_usuarios_usuarios_grupos`
    FOREIGN KEY (`id_grupo`)
    REFERENCES `rhxpert`.`usuarios_grupos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_funcionarios`
    FOREIGN KEY (`id_funcionario`)
    REFERENCES `rhxpert`.`funcionarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;