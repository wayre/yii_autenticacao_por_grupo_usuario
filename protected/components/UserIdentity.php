<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		/* Aqui tenta carregar um model usuario com o usuario digitado na pagina de login
		caso de sucesso vai verificar a senha que é igual. Aqui a senha nao tem criptografia para efeitos didáticos. */
		$model=Usuario::model()->findByAttributes(array('user'=>$this->username));
		
		// VERIFICA SE EXISTE O USUARIO NO SISTEMA E SE A SENHA ESTA CORRETA
		if( empty($model) ){
		    
		    $this->errorCode=self::ERROR_USERNAME_INVALID;

		}
		else if($model->pass!==$this->password){
		    
		    $this->errorCode=self::ERROR_PASSWORD_INVALID;

		}
		else{

			// se chegou aqui quer dizer que o usuario e senha estao corretos
			// o próximo passo agora abaixo será salvar os dados do usuário logado.

			// este foreach pega todos os campos do model usuario e guarda com o commando setState()
			foreach ($model as $key => $value) {
				$this->setState($key, (string) $value);
			}

			// esta variavel será usada para identificar o grupo do usuario no arquivo components/WebUser
			$this->setState('grupo_usuario', $model->id_grupo);


			$this->errorCode=self::ERROR_NONE;

		}

    return !$this->errorCode;

  }
}