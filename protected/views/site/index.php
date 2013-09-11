<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<h1>Informações Extras</h1>

<h4>Abaixo é um exemplo de como acessar od dados do usuário logado.</h4>
<p>Acesse o sistema com os usuarios/senhas:</p>

<ul>
	<li>admin/admin</li>
	<li>vendedor/vendedor</li>
</ul>

<hr>

<?php if( !Yii::app()->user->isGuest ): ?>
	<p>
		Usuário logado:	<?php echo isset(Yii::app()->user->nome) ? Yii::app()->user->nome : null; ?>
	</p>

	<?php echo Yii::app()->user->isADMIN() ? "Sou um administrador." : "Sou um Vendedor" ?>

	<ul>
		<li>user: <?php echo isset(Yii::app()->user->user) ? Yii::app()->user->user : null; ?></li>
		<li>nome: <?php echo isset(Yii::app()->user->nome) ? Yii::app()->user->nome : null; ?></li>
		<li>email: <?php echo isset(Yii::app()->user->email) ? Yii::app()->user->email : null; ?></li>
		<li>id grupo: <?php echo isset(Yii::app()->user->id_grupo) ? Yii::app()->user->id_grupo : null; ?></li>
	</ul>

<?php else: ?>

	<p>Voce ainda nao esta logado.</p>

<?php endif ?>
