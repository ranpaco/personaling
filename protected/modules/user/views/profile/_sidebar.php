<?php $model = User::model()->findByPk(Yii::app()->user->id); ?>
<?php $profile = $model->profile; ?>

<div class="card margin_bottom_medium"> <?php echo CHtml::image($model->getAvatar(),'Avatar',array("width"=>"270", "height"=>"270")); ?>
  <div class="card_content vcard">
    <h4 class="fn"><?php echo $profile->first_name." ".$profile->last_name; ?></h4>
    <p class="muted">Miembro desde: <?php echo Yii::app()->dateFormatter->format("d MMM y",strtotime($model->create_at)); ?></p>
  </div>
</div>
<div>
  <ul class="nav nav-tabs nav-stacked">
    <li class="nav-header">Opciones de edición</li>
    <li class="dropdown-submenu"> <a tabindex="-1" href="#">Tu perfil</a>
      <ul class="dropdown-menu">
        <li > <?php echo CHtml::link('Datos Personales',array('profile/edit'));?> </li>
        <li> <?php echo CHtml::link('Avatar',array('profile/avatar'));?> </li>
        <li> <?php echo CHtml::link('Tu Tipo',array('profile/edittutipo'));?> </li>
      </ul>
    </li>
    <li class="dropdown-submenu"> <a tabindex="-1" href="#">Tus Pedidos </a>
      <ul class="dropdown-menu">
        <li> <?php echo CHtml::link('Pedidos Activos',array('/orden/listado'),array("title"=>"Tus pedidos activos")); ?></li>
        <li> <?php echo CHtml::link('Historial de Pedidos',array('/orden/listado'),array("title"=>"Tus pedidos nuevos y anteriores")); ?></li>
      </ul>
    </li>
    <li class="dropdown-submenu"> <a tabindex="-1" href="#">Tu Estilo </a>
      <ul class="dropdown-menu">
        <li><?php echo CHtml::link('Coctel',array('profile/edittuestilo','id'=>'coctel'),array("title"=>"Edita tu estilo Coctel")); ?></li>
        <li><?php echo CHtml::link('Fiesta',array('profile/edittuestilo','id'=>'fiesta'),array("title"=>"Edita tu estilo Fiesta")); ?></li>
        <li><?php echo CHtml::link('Playa',array('profile/edittuestilo','id'=>'playa'),array("title"=>"Edita tu estilo Playa")); ?></li>
        <li><?php echo CHtml::link('Sport',array('profile/edittuestilo','id'=>'Sport'),array("title"=>"Edita tu estilo Sport")); ?></li>
        <li><?php echo CHtml::link('Trabajo',array('profile/edittuestilo','id'=>'trabajo'),array("title"=>"Edita tu estilo Trabajo")); ?></li>
      </ul>
    </li>
    <li class="dropdown-submenu"> <a tabindex="-1" href="#"> Tus Encantos/Favoritos </a>
      <ul class="dropdown-menu">
        <li><?php echo CHtml::link('Looks',array('profile/looksencantan'),array("title"=>"Looks que te encantan")); ?></a></li>
        <li><?php echo CHtml::link('Productos',array('profile/encantan'),array("title"=>"Productos que te encantan")); ?></a></li>
      </ul>
    </li>
    <li class="dropdown-submenu"> <a tabindex="-1" href="#"> Correo electrónico y contraseña </a>
      <ul class="dropdown-menu">
        <li><?php echo CHtml::link('Cambiar correo electronico',array('changeemail'),array("title"=>"Cambia tu correo electronico")); ?></li>
        <li><?php echo CHtml::link('Cambiar Contraseña',array('changepassword'),array("title"=>"Cambia tu contraseña")); ?></a></li>
      </ul>
    </li>
    <li class="dropdown-submenu"> <a tabindex="-1" href="#"> Notificaciones </a>
      <ul class="dropdown-menu">
        <li><?php echo CHtml::link('Gestionar direcciones de Envios y Pagos.',array('direcciones'),array("title"=>"Gestiona tus direcciones")); ?></li>
      </ul>
    </li>
    <li class="dropdown-submenu"> <a tabindex="-1" href="#"> Libreta de Direcciones </a>
      <ul class="dropdown-menu">
        <li><?php echo CHtml::link('Gestionar direcciones de Envios y Pagos.',array('direcciones'),array("title"=>"Gestiona tus direcciones")); ?></li>
      </ul>
    </li>
    <li class="dropdown-submenu"> <a tabindex="-1" href="#"> Privacidad </a>
      <ul class="dropdown-menu">
        <li><?php echo CHtml::link('Informacion publica',array('privacidad'),array("title"=>"Cambia tu Informacion publica")); ?></li>
        <li><?php echo CHtml::link('Eliminar Cuenta',array('delete'),array("title"=>"Eliminar Cuenta")); ?> </li>
      </ul>
    </li>
  </ul>
</div>