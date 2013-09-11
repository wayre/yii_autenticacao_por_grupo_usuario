

DROP TABLE IF EXISTS yii_usuario_grupos;

CREATE TABLE yii_usuario_grupos (
  id int(2) NOT NULL AUTO_INCREMENT,
  nome varchar(45) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO yii_usuario_grupos (id, nome)
VALUES
  (1,'admin'),
  (2,'vendedor');


DROP TABLE IF EXISTS yii_usuarios;

CREATE TABLE yii_usuarios (
  id int(6) NOT NULL AUTO_INCREMENT,
  id_grupo int(2) NOT NULL,
  user varchar(8) NOT NULL,
  pass varchar(8) NOT NULL,
  nome varchar(60) NOT NULL,
  email varchar(60) NOT NULL,
  endereco varchar(60) DEFAULT NULL,
  numero varchar(6) DEFAULT NULL,
  bairro varchar(60) DEFAULT NULL,
  cidade varchar(60) DEFAULT NULL,
  uf varchar(2) DEFAULT NULL,
  cep varchar(10) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



INSERT INTO yii_usuarios (id, id_grupo, user, pass, nome, email, endereco, numero, bairro, cidade, uf, cep)
VALUES
  (1,1,'admin','admin','Administrador','admin@demo.com',NULL,NULL,NULL,NULL,NULL,NULL),
  (2,2,'vendedor','vendedor','Vendedor','vendedor@demo.com',NULL,NULL,NULL,NULL,NULL,NULL);

