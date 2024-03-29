<?php

/* @var $perfil Profile*/

$this->pageTitle = Yii::app()->name . ' - ' . UserModule::t("Aplicar para Personal Shopper");

?>



<?php if (Yii::app()->user->hasFlash('registration')): ?>
    <div class="success">
    <?php echo Yii::app()->user->getFlash('registration'); ?>
    </div>
    <?php else: ?>
    <div class="container margin_top">
        <div class="row">
            <div class="span6 offset3">               
                <div class="page-header"><h1 class="text_align_center">Forma parte de los Personal Shoppers en Personaling</h1></div>
                <div class="row-fluid margin_bottom_medium margin_top_medium">
                    <div id="boton_facebook" class="span6 offset3 margin_bottom text_align_center"><a title="Registrate con facebook" class="transition_all" onclick="check_fb()" href="#">Regístrate con Facebook</a></div>
                </div>
                  <!-- <div id="boton_twitter" class="span5 offset2 margin_bottom"> <a id="registro_twitter" title="Registrate con Twitter" class="transition_all" href="<?php echo Yii::app()->request->baseUrl; ?>/user/registration/twitterStart">Regístrate con Twitter</a>  -->
                  <!--                            <script type="IN/Login" data-onAuth="onLinkedInAuth"></script>--> 
                <!-- </div> -->

                <section class="bg_color3 margin_top  margin_bottom_small padding_small box_1">

    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'registration-form',
        'htmlOptions' => array('class' => 'personaling_form', 'enctype'=>'multipart/form-data',),
        //'type'=>'stacked',        
        'type' => 'inline',
        'enableAjaxValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    ));
    ?>
                    <fieldset>

                        <legend class="text_align_center">O aplica llenando este formulario: </legend>
                        <?php echo $form->errorSummary(array($model, $profile), 'Faltan algunos campos por completar:'); ?>
                        <?php
                        if (isset($_GET['request_ids'])) {
                            //echo $_GET['request_ids'];
                            $requests = explode(',', $_GET['request_ids']);

                            echo CHtml::hiddenField('facebook_request', $requests[0]);
                        }
                        ?>

                        <div class="control-group row-fluid">
                            <div class="controls">
                                <!--[if IE 9]> 
                                        <label>Correo:</label>
                                <![endif]--> 
                                <?php
                                echo $form->textFieldRow($model, 'email', array("class" => "span12"));
                                echo $form->error($model, 'email');
                                ?>
                            </div>
                        </div>

                        <div class="control-group row-fluid">
                            <div class="controls">	
                                <!--[if IE 9]> 
                                        <label>Contraseña:</label>
                                <![endif]--> 
                                <?php
                                echo $form->passwordFieldRow($model, 'password', array('class' => 'span12'));
                                echo $form->error($model, 'password');
                                ?>
                            </div>
                        </div>
                        

    <?php 
    $profileFields = $profile->getFields();
    $face = $twi = null;
    if ($profileFields) {
        foreach ($profileFields as $field) {
           // echo $field->varname;
            ?>
            <div class="control-group">
                <div class="controls row-fluid">
                    <?php
                    if ($widgetEdit = $field->widgetEdit($profile)) {
                        echo $widgetEdit;
                    } elseif ($field->range) {
                        if ($field->varname == 'sex')
                            echo $form->radioButtonListInlineRow($profile, $field->varname, Profile::range($field->range));
                        else
                            echo $form->dropDownListRow($profile, $field->varname, Profile::range($field->range));
                        //echo $form->error($profile,$field->varname);
                    } elseif ($field->field_type == "TEXT" || $field->varname == "bio") {

                        echo$form->textArea($profile, $field->varname, array('rows' => 4,
                            'class' => "span12", "placeholder" => "Cuéntanos un poco sobre tí..."));
                        echo $form->error($profile, $field->varname);
                    } elseif ($field->field_type == "DATE") {
                        echo $form->labelEx($profile, $field->varname, array('class' => 'span3'));
                        echo ' ';
                        echo $form->DropDownList($profile, 'day', User::getDaysArray(), array('class' => 'span3'));
                        echo ' ';
                        echo $form->DropDownList($profile, 'month', User::getMonthsArray(), array('class' => 'span3'));
                        echo ' ';
                        echo $form->DropDownList($profile, 'year', User::getYearsArray(), array('class' => 'span3'));

                        echo $form->hiddenField($profile, $field->varname);
                        echo CHtml::hiddenField('facebook_id', '', array('id' => 'facebook_id', 'name' => 'facebook_id'));
                        //echo $form->textFieldRow($profile,$field->varname,array('class'=>'span5','maxlength'=>(($field->field_size)?$field->field_size:255)));
                        echo $form->error($profile, $field->varname);
                    } else {

                        //------------- condicion para mostar label en IE9 ON ----------------//
                        if ($field->varname == 'first_name') {
                            ?>
                            <!--[if IE 9]> 
                                    <label>Nombre:</label>
                            <![endif]--> 
                        <?php
                        } elseif ($field->varname == 'last_name') {
                        ?>
                            <!--[if IE 9]> 
                                    <label>Apellido:</label>
                            <![endif]--> 
                        <?php
                        }
                        elseif ($field->varname == 'blog') {
//                            $face = $field;
//                            continue;
                        }elseif ($field->varname == 'web') {
//                            $twi = $field;
//                            continue;
                        }
                        //------------- condicion para mostar label en IE9 OFF ----------------//

                        echo $form->textFieldRow($profile, $field->varname, array('class' => 'span12 '));    
                        echo $form->error($profile, $field->varname);
                    }
                    ?>
                </div>
            </div>
                    <?php
                }
                
                ?>
                <div class="control-group">
                    <div class="controls row-fluid">
                        
                        <div id="container" class="text_align_center margin_bottom margin_top">
                            <?php echo CHtml::image($model->getAvatar(),'Avatar',array("width"=>"135", "height"=>"135","class"=>"img_1")); ?>
                        </div>
                        
                         
                        
                        
                        
                    </div>
                </div>           
                        <?php 
                            echo $form->fileFieldRow($model, 'avatarPs', array('class' => 'well well-small span5'));
                        ?>
                        <?php echo CHtml::hiddenField('valido','1'); ?>
                              <?php echo CHtml::hiddenField('avatar_x','0'); ?>
                              <?php echo CHtml::hiddenField('avatar_y','0'); ?>
                        <output id="filesInfo"></output>     
                <?php
            }
            ?>
                        

                <hr/>
                Al hacer clic en "Enviar Solicitud" estas indicando que has leído y aceptado los <a href="<?php echo Yii::app()->getBaseUrl(); ?>/site/terminos_de_servicio" title="Términos y condiciones" target="_blank">Términos de Servicio</a> y la <a href="<?php echo Yii::app()->getBaseUrl(); ?>/site/politicas_y_privacidad" title="Politicas de Privacidad" target="_blank">Políticas de Privacidad</a>. 
                <div class="padding_top_medium"> 
                        

                <?php
                $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType' => 'submit',
                    'label' => 'Enviar Solicitud',
                    'type' => 'danger', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
                    'size' => 'large', // null, 'large', 'small' or 'mini'
                    'htmlOptions' => array('class'=>'btn-block'),
                ));
                ?>
                </div>

            </fieldset>
    <?php $this->endWidget(); ?>
                </section>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>

                    $(document).ready(function() {

                        window.fbAsyncInit = function() {
                            FB.init({
                                appId: '323808071078482', // App ID secret c8987a5ca5c5a9febf1e6948a0de53e2
                                channelUrl: 'http://personaling.com/test24/user/registration', // Channel File
                                status: true, // check login status
                                cookie: true, // enable cookies to allow the server to access the session
                                xfbml: true, // parse XFBML
                                oauth: true
                            });

                        };

                        (function(d) {
                            var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
                            if (d.getElementById(id)) {
                                return;
                            }
                            js = d.createElement('script');
                            js.id = id;
                            js.async = true;
                            js.src = "//connect.facebook.net/en_US/all.js";
                            ref.parentNode.insertBefore(js, ref);
                        }(document));
                    });

                    function check_fb() {
                        FB.getLoginStatus(function(response) {
                            console.log("response: " + response.status);
                            if (response.status === 'connected') {
                                // está conectado a facebook y además ya tiene permiso de usar la aplicacion personaling

                                console.log('Welcome!  Fetching your information.... ');

                                FB.api('/me', function(response) {
                                    console.log('Nombre: ' + response.id + '.\nE-mail: ' + response.email);
                                    console.log(response.birthday);
                                    console.log(response);

                                    //	$("#registration-form").fadeOut(100,function(){

                                    $('#facebook_id').val(response.id);
                                    $('#RegistrationForm_password').val('');
                                    $('#RegistrationForm_email').val(response.email);
                                    $('#Profile_first_name').val(response.first_name);
                                    $('#Profile_last_name').val(response.last_name);

                                    var fecha = response.birthday;
                                    var n = fecha.split("/"); // 0 mes, 1 dia, 2 año

                                    $('#Profile_day').val(n[1]);
                                    $('#Profile_month').val(n[0]);
                                    $('#Profile_year').val(n[2]);

                                    if (response.gender == 'male')
                                    {
                                        $('#Profile_sex_1').attr('checked', true);
                                    }

                                    if (response.gender == 'female')
                                    {
                                        $('#Profile_sex_0').attr('checked', true);
                                    }

                                     $('#Profile_facebook').val(response.username);
                                     $('#Profile_url').val(response.username);
                                    $('#registration-form').submit();

                                    //	});

                                    //	$("#registration-form").fadeIn(100,function(){});         

                                    /*
                                     $.ajax({
                                     url: 'registration', // accion
                                     data: {'facebook_id': response.id, 'email' : response.email, 'birthday': response.birthday, 'gender' : response.gender, 'first': response.first_name, 'last': response.last_name},
                                     type: 'POST',
                                     dataType: 'html',
                                     success: function(data) {
                                     console.log(data);
                                     alert("registró");
                                     //window.location = "http://careerdays.ch/aiesec/user/profile/create";
                                     }
                                     });*/

                                }, {scope: 'email,user_birthday'});
                            } else {
                                FB.login(function(response) {
                                    if (response.authResponse) {
                                        //user is already logged in and connected (using information)
                                        console.log('Welcome!  Fetching your information.... ');

                                        FB.api('/me', function(response) {
                                            console.log('Nombre: ' + response.id + '.\nE-mail: ' + response.email);
                                            console.log(response.user_birthday);

                                            //$("#registration-form").fadeOut(100,function(){

                                            $('#facebook_id').val(response.id);
                                            $('#RegistrationForm_password').val('1234');
                                            $('#RegistrationForm_email').val(response.email);
                                            $('#Profile_first_name').val(response.first_name);
                                            $('#Profile_last_name').val(response.last_name);

                                            var fecha = response.birthday;
                                            var n = fecha.split("/"); // 0 mes, 1 dia, 2 año

                                            $('#Profile_day').val(n[1]);
                                            $('#Profile_month').val(n[0]);
                                            $('#Profile_year').val(n[2]);

                                            if (response.gender == 'male')
                                            {
                                                $('#Profile_sex_1').attr('checked', true);
                                            }

                                            if (response.gender == 'female')
                                            {
                                                $('#Profile_sex_0').attr('checked', true);
                                            }

                                           
                                            
                                            $('#registration-form').submit();

                                            //	});

                                            //	$("#registration-form").fadeIn(100,function(){});

                                            /*
                                             var pass=12345;
                                             
                                             $.ajax({
                                             url: '', // accion
                                             data: {email : response.email, birthday : response.birthday, gender : response.gender, first: response.first_name, last: response.last_name, password: pass},
                                             type: 'POST',
                                             dataType: 'html',
                                             success: function(data) {
                                             console.log(data);
                                             alert("registró");
                                             //window.location = "http://careerdays.ch/aiesec/user/profile/create";
                                             }
                                             });*/

                                        });

                                    } else {
                                        console.log('User cancelled login or did not fully authorize.');
                                    }
                                }, {scope: 'email,user_birthday'});
                            }
                        });
                    }

                    function check_twitter() {
                        console.log("Twitter");
                    }
$('#Profile_day').on('change', validarFecha);
$('#Profile_month').on('change', validarFecha);
$('#Profile_year').on('change', validarFecha);

function validarFecha(){
        var day = $('#Profile_day').val();
        var month = $('#Profile_month').val();
        var year = $('#Profile_anio').val();
        
        if(day != '-1' && month != '-1' && year != '-1'){
                if(validarAnio(day, month, year)){
                        $('#Campana_ventas_fin').val(year+'-'+month+'-'+day+' 23:59:59');
                }else{
                        $('#Profile_day').val('-1'); 
                        $('#Profile_month').val('-1');
                        $('#Profile_year').val('-1');
                }
        }
}

function validarAnio(dia, mes, anio){
    var numDias = 31;
    //console.log('Dia: '+dia+' - Mes: '+mes+' - Año: '+anio);

    if(mes == 4 || mes == 6 || mes == 9 || mes == 11){
        numDias = 30;
    }

    if(mes == 2){
        if(comprobarSiBisisesto(anio)){
            numDias = 29;
        }else{
            numDias = 28;
        }
    }

    if(dia > numDias){
        return false;
    }
    return true;
}

function comprobarSiBisisesto(anio){
    if ( ( anio % 100 != 0) && ((anio % 4 == 0) || (anio % 400 == 0))) {
        return true;
    }
    else {
        return false;
    }
}
</script>
