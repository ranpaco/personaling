<tr>    
    
    
    <td>
        <?php echo $data->id; ?>
    </td>    
    <td>
        <?php
        if ($data->estado == 1) {
            echo "Inactiva";
        } else if ($data->estado == 2) {
            echo "Activa";
        } else if ($data->estado == 3) {
            echo "Aplicada";
        }
        ?> 
    </td>
    <td>
        <?php echo $data->monto; ?>
    </td>
    <td>
        <?php echo date("d/m/Y", $data->getInicioVigencia()); ?>
    </td>
    <td>
        <?php echo date("d/m/Y", $data->getFinVigencia()); ?>
    </td>
    <td>        
        <?php echo $data->fecha_uso ? date("d/m/Y", $data->getFechaUso()) . " - ".
                $data->UserBeneficiario->profile->first_name
                : "No Aplicada"; ?>
    </td>    
    <td>
        <div class="dropdown"> 
            <a class="dropdown-toggle btn" id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="" title="acciones">
                <i class="icon-cog"></i> <b class='caret'></b>
            </a> 
            <!-- Link or button to toggle dropdown -->
            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dLabel">
                <li>
                    <?php echo CHtml::link('<i class="icon-eye-open">  </i>  Ver Detalles', "#modalGiftCard", array(
                        'data-toggle' => "modal",
                        'onClick' => "ver({$data->id})",
                    )); ?>            
                </li>
                
                    <?php if ($data->estado == 1) { ?>
                        
                    <?php }else if ($data->estado == 2) { ?>
                        <li>
                        <?php echo  CHtml::link("<i class='icon-ban-circle'></i> Desactivar",
                                        $this->createUrl('index',array('id'=>$data->id)),
                                        array(
                                        'id'=>'linkDesactivar-'.$data->id)
                                    ); ?>
                        </li>
                        <li>
                        <?php echo CHtml::link('<i class="icon-envelope">  </i>  Enviar', array("enviar", "id" => $data->id)); ?>
                        </li>
                    <?php } ?>
                
                
                    
            </ul>
        </div>
    </td>
</tr>
