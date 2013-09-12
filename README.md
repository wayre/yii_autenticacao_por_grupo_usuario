Yii - Exemplo de autenticação por nível/grupo de acesso.
===

###Descrição

Este é um exemplo simples que mostra como autenticar usuários e permitir acesso a páginas de acordo com o seu nível de acesso.

Para o exemplo utilizei duas tabelas Mysql:
 (`yii_usuarios` e `yii_usuarios_grupos`). O dump das tabelas estão na pasta `'protected/data'`.

####1. Primeiramente foi criado o banco de dados

```sql
CREATE TABLE yii_usuario_grupos (
  id int(2) NOT NULL AUTO_INCREMENT,
  nome varchar(45) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO yii_usuario_grupos (id, nome)
VALUES
  (1,'admin'),
  (2,'vendedor');

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
```



####2.  Configurado o arquivo `config/main.php`. 


```php
 // CWebApplication properties can be configured here.
 return array(
 	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
+	'name'=>'Yii - Estudos Autenticação por Grupo de Usuário',
 
 	// preloading 'log' component
 	'preload'=>array('log'),
      return array(
 
 	'modules'=>array(
+		
 		'gii'=>array(
 			'class'=>'system.gii.GiiModule',
+			'password'=>'root',
 			// If removed, Gii defaults to localhost only. Edit carefully to taste.
 			'ipFilters'=>array('127.0.0.1','::1'),
 		),
+		
 	),
 
 	// application components
    return array(
 		'user'=>array(
 			// enable cookie-based authentication
 			'allowAutoLogin'=>true,
+
+			'class'=>'WebUser',
+			'autoRenewCookie' => true,
 		),

 		'db'=>array(
+			'connectionString' => 'mysql:host=localhost;dbname=yii_estudos,
 			'emulatePrepare' => true,
 			'username' => 'root',
+			'password' => 'root',
 			'charset' => 'utf8',
 		),
+		
 		'errorHandler'=>array(
 			// use 'site/error' action to display errors
 			'errorAction'=>'site/error',
```

####3. Criado o model `Usuario` e gerado o crud

- Criado com o Code Generator do Yii.
`http://localhost/yii_estudos_acesso_usuario/index.php?r=gii`

####4. Arquivo protected/views/layout/main.php

Para efeito de demostração adicionei itens novos ao menu principal.


```php
				array('label'=>'Home', 'url'=>array('/site/index')),
 				array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
 				array('label'=>'Contact', 'url'=>array('/site/contact')),
+
+
+				// Exemplos de criacao de menu ( apenas oculta )
+				// novo menu visivel apenas para administrador
+				array('label'=>'VisivelSomenteParaAdmin', 'url'=>array('/usuario/index'), 'visible' => Yii::app()->user->isAdmin() ),
+
+				// novo menu adicionado
+				array('label'=>'VisivelSomenteParaVendedor', 'url'=>array('/usuario/index'), 'visible' => Yii::app()->user->isVendedor() ),
+
+				// nvo menu adicionado restrita
+				array('label'=>'RestritaParaVendedor', 'url'=>array('/usuario/admin')),
+
 				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
 				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
 			),
```

####5. Arquivo protected/components/UserIdentify.php

Foi feito algumas alterações aqui para validar o usuário vindo do banco mysql.

```php
 	public function authenticate()
 	{
-		$users=array(
-			// username => password
-			'demo'=>'demo',
-			'admin'=>'admin',
-		);
-		if(!isset($users[$this->username]))
-			$this->errorCode=self::ERROR_USERNAME_INVALID;
-		elseif($users[$this->username]!==$this->password)
-			$this->errorCode=self::ERROR_PASSWORD_INVALID;
-		else
+		/* Aqui tenta carregar um model usuario com o usuario digitado na pagina de login
+		caso de sucesso vai verificar a senha que é igual. Aqui a senha nao tem criptografia para efeitos didáticos. */
+		$model=Usuario::model()->findByAttributes(array('user'=>$this->username));
+		
+		// VERIFICA SE EXISTE O USUARIO NO SISTEMA E SE A SENHA ESTA CORRETA
+		if( empty($model) ){
+		    
+		    $this->errorCode=self::ERROR_USERNAME_INVALID;
+
+		}
+		else if($model->pass!==$this->password){
+		    
+		    $this->errorCode=self::ERROR_PASSWORD_INVALID;
+
+		}
+		else{
+
+			// se chegou aqui quer dizer que o usuario e senha estao corretos
+			// o próximo passo agora abaixo será salvar os dados do usuário logado.
+
+			// este foreach pega todos os campos do model usuario e guarda com o commando setState()
+			foreach ($model as $key => $value) {
+				$this->setState($key, (string) $value);
+			}
+
+			// esta variavel será usada para identificar o grupo do usuario no arquivo components/WebUser
+			$this->setState('grupo_usuario', $model->id_grupo);
+
+
 			$this->errorCode=self::ERROR_NONE;
-		return !$this->errorCode;
-	}
+
+		}
+
+    return !$this->errorCode;
+
+  }
 }
 ```
 
 
####6. Arquivo protected/components/WebUser.php
 
 Criado este arquivo de extende de `CWebUser` para adicionar novos métodos.
 
 
 ```php
 +<?php
+
+/**
+ * Sobrecarga de CWebUser para definir alguns métodos mais.
+ */
+class WebUser extends CWebUser{
+    const ADMIN = 1;
+    const VENDEDOR = 2;
+
+	public function isAdmin(){
+		return Yii::app()->user->getState("grupo_usuario") == self::ADMIN ? true : false;
+	}
+
+	public function isVendedor(){
+		return Yii::app()->user->getState("grupo_usuario") == self::VENDEDOR ? true : false;
+	}
+
+}
 ```

####Exemplo de como criar mais um nível de acesso 


Caso quisesse adicionar mais um nível de acesso `'VIEWER'` apenas faria o sequinte:

#### protected/components/WebUser.php
```php
    const ADMIN = 1;
     const VENDEDOR = 2;
+    const VIEWER = 3;
 
  public function isAdmin(){
    return Yii::app()->user->getState("grupo_usuario") == self::ADMIN ? true : false;
@@ -15,4 +16,8 @@ class WebUser extends CWebUser{
    return Yii::app()->user->getState("grupo_usuario") == self::VENDEDOR ? true : false;
  }
 
+ public function isViewer(){
+   return Yii::app()->user->getState("grupo_usuario") == self::VIEWER ? true : false;
+ }
+
 }
```

Abaixo vamos dar permissão ao novo grupo `viewer` a pagina admin.

#### protected/controllers/UsuarioController.php
```php
      ),
      array('allow', // allow admin and viewer user to perform 'admin' and 'delete' actions
        'actions'=>array('admin','delete'),
-       'users'=>array('admin'),
+       'users'=>array('admin','viewer'),
      ),
      array('deny',  // deny all users
        'users'=>array('*'),
```

Vamos aumentar o menu principal com mais um elemento somente para testar  o `Yii::app()->user->isViewer()`

#### protected/views/layouts/main.php
```php
        // novo menu adicionado restrita
        array('label'=>'RestritaParaVendedor', 'url'=>array('/usuario/admin')),
 
+       // novo menu adicionado
+       array('label'=>'VisivelSomenteParaViewer', 'url'=>array('/usuario/index'), 'visible' => Yii::app()->user->isViewer() ),
+
+
        array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
        array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
      ),
```

#####Para finalizar é possível acessar os dados do usuário logado desta forma:

```php
	Usuário logado:	<?php echo Yii::app()->user->nome; ?>
  
  <?php echo Yii::app()->user->isADMIN() ? "Sou um administrador." : "Sou um Vendedor" ?>
	
  <ul>
		<li>user: <?php echo Yii::app()->user->user; ?></li>
		<li>nome: <?php echo Yii::app()->user->nome; ?></li>
		<li>email: <?php echo Yii::app()->user->email; ?></li>
		<li>id grupo: <?php echo Yii::app()->user->id_grupo; ?></li>
		<li><?php echo Yii::app()->user->nome; ?></li>
	</ul>
```

É isso ai.  `=)`
