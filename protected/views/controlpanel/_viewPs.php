<?php if ($data->status == 0) { ?>
    <tr class="warning">
<?php } else { ?>
    <tr class=" <?php echo ($data->personal_shopper == 2) ? 'info' : ''; ?> ">
<?php } ?>
    <td><input name="Check" type="checkbox" value="Check"></td>
    
    <!--IMAGEN-->
    
    <td>
        <?php
        // <img src="images/kitten.png" width="70" height="70" alt="avatar">
        echo CHtml::image($data->getAvatar(), 'Avatar', array("width" => "70", "height" => "70"));
        ?>
    </td>
    
    <!--DATOS PERSONALES-->
    
    <td>
        <h5 class="no_margin_bottom"> <?php echo $data->profile->first_name . ' '
                . $data->profile->last_name; ?></h5>
        <small><strong>ID</strong>: <?php echo $data->id; ?><br/>
            <?php
            if ($data->personal_shopper == 1) {
                if ($data->ps_destacado == 1) {
                    ?>
                    <span class="label label-warning">Personal Shopper Destacado</span>
                    <?php
                } else {
                    echo 'Personal Shopper';
                }
            } else if ($data->personal_shopper == 2) {
                echo '<span class="label label-info"> Aplicante Personal Shopper</span>';
            } else {
                echo '';
            }
            ?> </small>
    </td>
        
    <td>
        <small><?php echo $data->email; ?><br/>
            <strong>Telf.</strong>: <?php echo $data->profile->tlf_celular; ?> <br/>
            <strong>Ciudad</strong>: <?php echo $data->profile->ciudad; ?> <br/>
            <?php if ($data->status == User::STATUS_NOACTIVE) { ?>
                <strong class="text-warning text-center">Cuenta no validada</strong>
            <?php } else if ($data->status == User::STATUS_BANNED) { ?>          
                <strong class="text-error text-center">Cuenta bloqueada</strong>
            <?php } ?>   
        </small>
    </td>
    
    <!--LOOKS COMPLETOS-->
    <td><?php echo $data->ordenCount; ?></td>
    
    <!--PRODUCTOS VENDIDOS-->
    <td><?php echo $data->direccionCount; ?></td>
    
    <!--COMISION ACTUAL-->
    <td>
        <?php echo "5 %"; ?>
    </td>
    
    <!--SALDO EN COMISIONES-->
    <td><?php echo $data->getSaldoPorComisiones();echo " Bs." ?></td>
    
    <!--SALDO TOTAL-->
    <td>
        <?php $saldo = Profile::model()->getSaldo($data->id);
        echo Yii::app()->numberFormatter->formatDecimal($saldo) . " Bs."; ?>
    </td>
    
    <!--FECHA DE REGISTRO-->
    <td>
        <?php if ($data->getCreatetime()) echo date("d/m/Y", $data->getCreatetime());
            else echo 'N/D'; ?>
    </td>
    
    <!--VER DETALLES-->
    <td style="text-align: center">
       <?php echo CHtml::link('<i class="icon-eye-open"></i>',
               array("profile/perfil", "id" => $data->id)); ?>            
    </td>
</tr>
<div id='myModal' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
</div>
<div id='saldoCarga' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
</div>
