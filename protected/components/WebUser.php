<?php

/**
 * Sobrecarga de CWebUser para definir alguns métodos mais.
 */
class WebUser extends CWebUser{
    const ADMIN = 1;
    const VENDEDOR = 2;
    const VIEWER = 3;

	public function isAdmin(){
		return Yii::app()->user->getState("grupo_usuario") == self::ADMIN ? true : false;
	}

	public function isVendedor(){
		return Yii::app()->user->getState("grupo_usuario") == self::VENDEDOR ? true : false;
	}

	public function isViewer(){
		return Yii::app()->user->getState("grupo_usuario") == self::VIEWER ? true : false;
	}

}
