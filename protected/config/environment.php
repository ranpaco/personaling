<?php
/**
     * This class helps you to config your Yii application
     * environment.
     * Any comments please post a message in the forum
     * Enjoy it!
     *
     * @name Environment
     * @author Fernando Torres | Marciano Studio
     * @version 1.0
     */
 
    class Environment {
 
        const DEVELOPMENT = 100;
        const TEST        = 200;
        const STAGE       = 300;
        const PRODUCTION  = 400;
 
        private $_mode = 0;
        private $_debug;
        private $_trace_level;
        private $_config;
        private $_country;
 
 
        /**
         * Returns the debug mode
         * @return Bool
         */
        public function getDebug() {
            return $this->_debug;
        }
 
        /**
         * Returns the trace level for YII_TRACE_LEVEL
         * @return int
         */
        public function getTraceLevel() {
            return $this->_trace_level;
        }
 
        /**
         * Returns the configuration array depending on the mode
         * you choose
         * @return array
         */
        public function getConfig() {
            return $this->_config;
        }
 
 
        /**
         * Initilizes the Environment class with the given mode
         * @param constant $mode
         */
        function __construct($mode,$country) {
            $this->_mode = $mode;
            $this->_country = $country;
            $this->setConfig();
        }
 
        /**
         * Sets the configuration for the choosen environment
         * @param constant $mode
         */
        private function setConfig() {
            switch($this->_mode) {
                case self::DEVELOPMENT:
                    $this->_config      = CMap::mergeArray  ($this->_main(), $this->_development());
                    $this->_debug       = TRUE;
                    $this->_trace_level = 3;
                    break;
                case self::TEST:
                    $this->_config      = CMap::mergeArray  ($this->_main(), $this->_test());
                    $this->_debug       = FALSE;
                    $this->_trace_level = 0;
                    break;
                case self::STAGE:
                    $this->_config      = CMap::mergeArray  ($this->_main(), $this->_stage());
                    $this->_debug       = TRUE;
                    $this->_trace_level = 0;
                    break;
                case self::PRODUCTION:
                    $this->_config      = CMap::mergeArray  ($this->_main(), $this->_production());
                    $this->_debug       = FALSE;
                    $this->_trace_level = 0;
                    break;
                default:
                    $this->_config      = $this->_main();
                    $this->_debug       = TRUE;
                    $this->_trace_level = 0;
                    break;
            }
        }
 
 
        /**
         * Main configuration
         * This is the general configuration that uses all environments
         */
        private function _main() {
            
            return array(
                'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
                'name'=>'Personaling',
                'timeZone' => 'America/Caracas', 
                'language' => 'es_ve',
                'sourceLanguage'=>'es_VE',
                // preloading 'log' component
                'preload'=>array('log','bootstrap'),
                'theme'=>'bootstrap',
                // autoloading model and component classes
                'import'=>array(
                    'application.models.*',
                    'application.widgets.ctree.*',
                    'application.components.*',
                    'application.modules.user.*',
                    'application.modules.user.models.*',
                    'application.modules.user.components.*',
                    'application.helpers.*',
                    'application.extensions.validators.age.*',
                    'ext.yii-mail.YiiMailMessage',
                    'ext.fancybox',
                    'ext.AzPay',
                ),

                'modules'=>array(
                    // uncomment the following to enable the Gii tool
                    
                    'gii'=>array(
                        'class'=>'system.gii.GiiModule',
                        'password'=>'1234',
                        // If removed, Gii defaults to localhost only. Edit carefully to taste.
                        'ipFilters'=>array('*'),
                        
                            'generatorPaths'=>array(
                                'bootstrap.gii',
                            ),
                            
                    ),
                    'user'=>array(
                        # encrypting method (php hash function)
                        'hash' => 'md5',

                        # send activation email
                        'sendActivationMail' => true,

                        # allow access for non-activated users
                        'loginNotActiv' => true,

                        # activate user on registration (only sendActivationMail = false)
                        'activeAfterRegister' => false,

                        # automatically login from registration
                        'autoLogin' => true,

                        # registration path
                        'registrationUrl' => array('/user/registration'),

                        # recovery password path
                        'recoveryUrl' => array('/user/recovery'),

                        # login form path
                        'loginUrl' => array('/user/login'),

                        # page after login
                        'returnUrl' => array('/controlpanel/index'),

                        # page after logout
                        'returnLogoutUrl' => array('/user/login'),
                    ),  
                    'reportico'=>array(),   
                    
                ),

                // application components
                'components'=>array(
                    'assetManager' => array(
                        //'linkAssets' => true,
                       // 'forceCopy'=> false,
                    ),
                    'curl' => array(
                        'class' => 'ext.Curl',
                        'options' => array(), 
                    ),  
                    'twitter' => array(
                            'class' => 'ext.yiitwitteroauth.YiiTwitter',
                            'consumer_key' => 'oLqFHegtKHu8SvXtICuEuA',
                            'consumer_secret' => 'Bf9nlFPrcb1CNIqFAMjiCUG3qSOKefIoswDqlVawx8',
                            'callback' => 'http://personaling.com/site/user/registration/twitter',
                        ), 
                    'mail' => array(
                            'class' => 'ext.yii-mail.YiiMail',
                            'transportType'=>'php',
                            'viewPath' => 'application.views.mail',             
                    ),
                    'image'=>array(
                              'class'=>'application.extensions.image.CImageComponent',
                                // GD or ImageMagick
                                'driver'=>'GD',
                                // ImageMagick setup path
                                'params'=>array('directory'=>'/opt/local/bin'),
                            ),  
                    'user'=>array(
                        // enable cookie-based authentication
                        'class' => 'WebUser',
                        'allowAutoLogin'=>true,
                        'loginUrl'=>array('/user/login'),
                    ),

                    'yexcel' => array(
                        'class' => 'ext.yexcel.Yexcel'
                    ),
                    
                    'less'=>array(
                      'class'=>'ext.less.components.Less',
                      'mode'=>'client',
                      'files'=>array(
                        'less/style.less'=>'css/style.less',
                      ),
                      'options'=>array('watch'=>false),
                    ),  
                        
                    'bootstrap'=>array(
                        'class'=>'ext.bootstrap.components.Bootstrap',
                        'responsiveCss' => true,
                    ),
                    // uncomment the following to enable URLs in path-format
                    
                    'urlManager'=>array(
                        'urlFormat'=>'path',
                        'showScriptName'=>false,
                        //'caseSensitive'=>false, 
                        'baseUrl'=>'/rpalma/sandbox/personaling',

                        'rules'=>array(
                            array(
                                'class' => 'application.components.ShortenerUrlRule',
                                'connectionID' => 'db',
                            ),
                            'productos/<alias:[a-zA-Z0-9_-]+>'=>'producto/detalle',
                            'looks/<alias:[a-zA-Z0-9_-]+>'=>'look/view',
                            'looks-personalizados'=>'tienda/look',
                            'tienda-ropa-personalizada'=>'tienda/index',
                            'outlet'=>'tienda/index/outlet/true',
                            'formas-de-pago'=>'site/formas_de_pago',
                            'envios'=>'site/condiciones_de_envios_y_encomiendas',
                            'preguntas_frecuentes'=>'site/preguntas_frecuentes',
                            'acerca-personaling'=>'site/acerca_de',
                            'registro-personaling'=>'user/registration',
                            'inicio-personaling'=>'user/login',
                            'reportico'=>'reportico',
                            'tt'=>'site/tienda',
                            'look/<id:\d+>/ps/<ps_id:\d+>'=>'look/view',
                            '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                            '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                            '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                            '<alias:[a-zA-Z0-9_-]+>'=>'user/profile/perfil'
                        ),
                    ),

                    // uncomment the following to use a MySQL database
                    
                    'db'=>array(
                        'connectionString' => 'mysql:host=mysql-personaling.cu1sufeji6uk.us-west-2.rds.amazonaws.com;dbname=db_personalingDEV',
                        'emulatePrepare' => true,
                        'username' => 'personaling',
                        'password' => 'Perso123Naling',
                        'charset' => 'utf8',
                        'tablePrefix' => 'tbl_',
                    ),
                    'user'=>array(
                        // enable cookie-based authentication
                        'class' => 'WebUser',
                        'loginUrl'=>array('/user/login'),
                        'allowAutoLogin' => true,
                    ),      
                    'errorHandler'=>array(
                        // use 'site/error' action to display errors
                        'errorAction'=>'site/error',
                    ),
                    'log'=>array(
                        'class'=>'CLogRouter',
                        'routes'=>array(
                            array(
                                'class'=>'CFileLogRoute',
                                'levels'=>'error, warning',
                            ),
                            // uncomment the following to show log messages on web pages

                        array(
                                'class' => 'CDbLogRoute',
                                'connectionID' => 'db',
                                'autoCreateLogTable' => false,
                                'logTableName' => 'tbl_logger',
                                'levels' => 'trace',
                                'categories' => 'registro,compra,tienda,admin,otro',

                        ),
                        ),
                    ),
                    'ePdf' => array(
                            'class'         => 'ext.yii-pdf.EYiiPdf',
                            'params'        => array(
                                'mpdf'     => array(
                                    'librarySourcePath' => 'application.vendors.mpdf.*',
                                    'constants'         => array(
                                        '_MPDF_TEMP_PATH' => Yii::getPathOfAlias('application.runtime'),
                                    ),
                                    'class'=>'mpdf', // the literal class filename to be loaded from the vendors folder
                                    /*'defaultParams'     => array( // More info: http://mpdf1.com/manual/index.php?tid=184
                                        'mode'              => '', //  This parameter specifies the mode of the new document.
                                        'format'            => 'A4', // format A4, A5, ...
                                        'default_font_size' => 0, // Sets the default document font size in points (pt)
                                        'default_font'      => '', // Sets the default font-family for the new document.
                                        'mgl'               => 15, // margin_left. Sets the page margins for the new document.
                                        'mgr'               => 15, // margin_right
                                        'mgt'               => 16, // margin_top
                                        'mgb'               => 16, // margin_bottom
                                        'mgh'               => 9, // margin_header
                                        'mgf'               => 9, // margin_footer
                                        'orientation'       => 'P', // landscape or portrait orientation
                                    )*/
                                ),
                                'HTML2PDF' => array(
                                    'librarySourcePath' => 'application.vendors.html2pdf.*',
                                    'classFile'         => 'html2pdf.class.php', // For adding to Yii::$classMap
                                    /*'defaultParams'     => array( // More info: http://wiki.spipu.net/doku.php?id=html2pdf:en:v4:accueil
                                        'orientation' => 'P', // landscape or portrait orientation
                                        'format'      => 'A4', // format A4, A5, ...
                                        'language'    => 'en', // language: fr, en, it ...
                                        'unicode'     => true, // TRUE means clustering the input text IS unicode (default = true)
                                        'encoding'    => 'UTF-8', // charset encoding; Default is UTF-8
                                        'marges'      => array(5, 5, 5, 8), // margins by default, in order (left, top, right, bottom)
                                    )*/
                                )
                            ),
                        ),
                ),

                // application-level parameters that can be accessed
                // using Yii::app()->params['paramName']
                'params'=>array(
                    // this is used in contact page
                    'adminEmail'=>'webmaster@example.com',
                    'metodosPago'=> array(
                        'bkCard' => true,
                        'paypal' => true,
                        'depositoTransferencia' => false,
                        'instapago' => false,
                        'mercadopago' => false,
                                                    
                    ),
                    'clientName'=>'Personaling Enterprise S.L ',
                    'clientIdentification'=>'B66202383',
                    'clientAddress'=>'Sant Pere Mes Baix, Nº 63 Principal B ',
                    'clientCity'=>'Barcelona',
                    'clientZIP'=>'08003',
                    'clientPhone'=>'934 344 634',
                    'clientEmail'=>'info@personaling.com', 
                    'PRONUNCIACION' => 'Venezolana', 
                    'currSym'=>'Bs',
                    'registerGift'=>'5', // 0: Cuando no se obsequia saldo, VALOR: cuando por registrarse se obsequia  un VALOR determinado
                    'noShipping'=> '0', // 0: Cuando se debe cobrar envio, VALOR: cuando el envío es GRATIS a partir de un VALOR determinado
                    'askId'=>true, //Para cuando se deba solicitar y mostrar la cedula/nif/rif segun el pais
                    'IVA' => '0.12',
                    'IVAtext' => '12%', 
                    'registro' => true,   
                    'mostrarMarcas'=>true,
                    'mostrarChic'=>true,
                    'country'=>'Venezuela',
                    //'zohoToken'=>'1569fa0c328f9ec6fec9a148939b74fa', 
                    'outlet'=>TRUE,
                    'zohoActive' => FALSE,
                ),
            );
        }
 
 
        /**
         * Development configuration
         * Usage:
         * - Local website
         * - Local DB
         * - Show all details on each error.
         * - Gii module enabled
         */
        private function _development () {
            if ($this->_country == 'es_ve')
                return array(
                    'timeZone' => 'America/Caracas',
                    'language' => 'es_ve',
                    'components'=>array(
                        'db'=>array(
                            'connectionString' => 'mysql:host=localhost;
                                                dbname=db_personalingT1',
                            'emulatePrepare' => true,
                            'username' => 'root',
                            'password' => '',
                            'charset' => 'utf8',
                            'tablePrefix' => 'tbl_',
                        ),
                    ),
                    'params'=>array(
                    // this is used in contact page
                        'adminEmail'=>'rpalma@upsidecorp.ch',
                        'PRONUNCIACION' => 'Venezolana',
                        'currSym'=>'Bs',
                        'noShipping'=> '0', // 0: Cuando se debe cobrar envio, VALOR: cuando el envío es GRATIS a partir de un VALOR determinado
                        'IVA' => '0.12',
                        'registerGift'=>'5', // 0: Cuando no se obsequia saldo, VALOR: cuando por registrarse se obsequia  un VALOR determinado
                        'askId'=>false, //Para cuando se deba solicitar y mostrar la cedula/nif/rif segun el pais
                        'IVAtext' => '12%',
                        'registro' => false,    
                        'mostrarMarcas'=>true,
                        'mostrarChic'=>false,
                        'country'=>'Venezuela',
                        'codigoPostal'=>false,
                        'pais'=>'espana',
                        'clientName'=>'PERSONALING C.A.',
                        'clientIdentification'=>'J-40236088-6',
                        'clientAddress'=>'Sant Pere Mes Baix, Nº 63 Principal B ',
                        'clientCity'=>'Barcelona',
                        'clientZIP'=>'08003',
                        'clientPhone'=>'934 344 634',
                        'clientEmail'=>'info@personaling.com',
                        '    ',
                        'metodosPago'=> array(
                                'bkCard' => false,
                                'paypal' => false, 
                                'prueba' => true,
                                'depositoTransferencia' => true,
                                'instapago' => true,
                            ),
                        'pagoPS'=> array(
                   
                                'paypal' => false,
                                'banco' => true,
                                'saldo' => true,
                            ),  
                        'multiLook'=> array(
                            'bodyType' => true,
                            'eyesColor' => true,
                            'hairColor' => true,
                            'womanMeasure' => true,
                            'bodyFavors' => true,
                            'skinColor' => true,
                        ),
                        'AzPayTerminal'=>'999',
                        'AzPaySecret'=> 'qwerty1234567890uiop',
                        'zohoToken' => 'db13cb372e7f29b08de5cbd299a337ea',
                        ),
                );
            if ($this->_country == 'es_es')
                return array(
                    'language' => 'es_es',
                    'timeZone' => 'Europe/Madrid', 
                    'components'=>array(
                        'db'=>array(
                            'connectionString' => 'mysql:host=mysql-personaling.cu1sufeji6uk.us-west-2.rds.amazonaws.com;dbname=db_personalingDEV',
                            'emulatePrepare' => true,
                            'username' => 'personaling',
                            'password' => 'Perso123Naling',
                            'charset' => 'utf8',
                            'tablePrefix' => 'tbl_',
                        ),
                    ),
                    'params'=>array(
                    // this is used in contact page
                        'adminEmail'=>'rpalma@upsidecorp.ch',
                        'PRONUNCIACION' => 'Española', 
                        'currSym'=>'E',
                        'noShipping'=> '0', // 0: Cuando se debe cobrar envio, VALOR: cuando el envío es GRATIS a partir de un VALOR determinado
                        'IVA' => '0.21',
                        'registerGift'=>'5', // 0: Cuando no se obsequia saldo, VALOR: cuando por registrarse se obsequia  un VALOR determinado
                        'askId'=>false, //Para cuando se deba solicitar y mostrar la cedula/nif/rif segun el pais
                        'IVAtext' => '21%', 
                        'registro' => false,    
                        'mostrarMarcas'=>true,
                        'codigoPostal'=>true,
                        'mostrarChic'=>false, 
                        'country'=>'España',
                        'pais'=>'espana',
                        'clientName'=>'Personaling Enterprise S.L ',
                        'clientIdentification'=>'B66202383',
                        'clientAddress'=>'Sant Pere Mes Baix, Nº 63 Principal B ',
                        'clientCity'=>'Barcelona',
                        'clientZIP'=>'08003',
                        'clientPhone'=>'934 344 634',
                        'clientEmail'=>'info@personaling.com',
                        '    ',
                        'pagoPS'=> array(           
                                'paypal' => true,
                                'banco' => true,
                                'saldo' => true,
                            ),
                        'metodosPago'=> array(
                            'bkCard' => true,
                            'paypal' => true, 
                            'prueba' => true,
                        ),
                        'multiLook'=> array(
                            'bodyType' => false,
                            'eyesColor' => false,
                            'hairColor' => false,
                            'womanMeasure' => false,
                            'bodyFavors' => false,
                            'skinColor' => false,
                        ),
                        'AzPayTerminal'=>'999',
                        'AzPaySecret'=> 'qwerty1234567890uiop',
                        'zohoToken'=>'1569fa0c328f9ec6fec9a148939b74fa', 
                    ),
                );
        }
 
 
        /**
         * Test configuration
         * Usage:
         * - Local website
         * - Local DB
         * - Standard production error pages (404,500, etc.)
         * @var array
         */
        private function _test() {
            return array(
 
                    // Application components
                    'components' => array(
 
                            // Database
                            'db'=>array(
                                    'connectionString' => 'Your connection string to your local testing server',
                                    'emulatePrepare' => false,
                                    'username' => 'admin',
                                    'password' => 'password',
                                    'charset' => 'utf8',
                            ),
 
 
                            // Fixture Manager for testing
                            'fixture'=>array(
                                    'class'=>'system.test.CDbFixtureManager',
                            ),
 
                            // Application Log
                            'log'=>array(
                                    'class'=>'CLogRouter',
                                    'routes'=>array(
                                            array(
                                                    'class'=>'CFileLogRoute',
                                                    'levels'=>'error, warning,trace, info',
                                            ),
 
                                            // Show log messages on web pages
                                            array(
                                                    'class'=>'CWebLogRoute',
                                                    'levels'=>'error, warning',
                                            ),
                                    ),
                            ),
                    ),
            );
        }
 
        /**
         * Stage configuration
         * Usage:
         * - Online website
         * - Production DB
         * - All details on error
         */
        private function _stage() {
            if ($this->_country == 'es_ve')
                return array(
                    'timeZone' => 'America/Caracas',
                    'language' => 'es_ve',
                    'components'=>array(
                        'db'=>array(
                            'connectionString' => 'mysql:host=mysql-personaling.cu1sufeji6uk.us-west-2.rds.amazonaws.com;
                                                dbname=db_personalingDEV_VE',
                            'emulatePrepare' => true,
                            'username' => 'personaling',
                            'password' => 'Perso123Naling',
                            'charset' => 'utf8',
                            'tablePrefix' => 'tbl_',
                        ),
                    ),
                    'params'=>array(
                    // this is used in contact page
                        'adminEmail'=>'rpalma@upsidecorp.ch',
                        'PRONUNCIACION' => 'Venezolana',
                        'currSym'=>'Bs',
                        'noShipping'=> '0', // 0: Cuando se debe cobrar envio, VALOR: cuando el envío es GRATIS a partir de un VALOR determinado
                        'IVA' => '0.12',
                        'registerGift'=>'5', // 0: Cuando no se obsequia saldo, VALOR: cuando por registrarse se obsequia  un VALOR determinado
                        'askId'=>false, //Para cuando se deba solicitar y mostrar la cedula/nif/rif segun el pais
                        'IVAtext' => '12%',
                        'registro' => false,    
                        'mostrarMarcas'=>true,
                        'mostrarChic'=>false,
                        'country'=>'Venezuela',
                        'codigoPostal'=>false,
                        'pais'=>'espana',
                        'clientName'=>'PERSONALING C.A.',
                        'clientIdentification'=>'J-40236088-6',
                        'clientAddress'=>'Sant Pere Mes Baix, Nº 63 Principal B ',
                        'clientCity'=>'Barcelona',
                        'clientZIP'=>'08003',
                        'clientPhone'=>'934 344 634',
                        'clientEmail'=>'info@personaling.com',
                        '    ',
                        'metodosPago'=> array(
                                'bkCard' => false,
                                'paypal' => false, 
                                'prueba' => true,
                                'depositoTransferencia' => true,
                                'instapago' => true,
                            ),
                        'pagoPS'=> array(
                   
                                'paypal' => false,
                                'banco' => true,
                                'saldo' => true,
                            ),  
                        'multiLook'=> array(
                            'bodyType' => true,
                            'eyesColor' => true,
                            'hairColor' => true,
                            'womanMeasure' => true,
                            'bodyFavors' => true,
                            'skinColor' => true,
                        ),
                        'AzPayTerminal'=>'999',
                        'AzPaySecret'=> 'qwerty1234567890uiop',
                        'zohoToken' => 'db13cb372e7f29b08de5cbd299a337ea',
                        ),
                );
            if ($this->_country == 'es_es')
                return array(
                    'language' => 'es_es',
                    'timeZone' => 'Europe/Madrid', 
                    'components'=>array(
                        'db'=>array(
                            'connectionString' => 'mysql:host=mysql-personaling.cu1sufeji6uk.us-west-2.rds.amazonaws.com;dbname=db_personalingDEV',
                            'emulatePrepare' => true,
                            'username' => 'personaling',
                            'password' => 'Perso123Naling',
                            'charset' => 'utf8',
                            'tablePrefix' => 'tbl_',
                        ),
                    ),
                    'params'=>array(
                    // this is used in contact page
                        'adminEmail'=>'rpalma@upsidecorp.ch',
                        'PRONUNCIACION' => 'Española', 
                        'currSym'=>'E',
                        'noShipping'=> '0', // 0: Cuando se debe cobrar envio, VALOR: cuando el envío es GRATIS a partir de un VALOR determinado
                        'IVA' => '0.21',
                        'registerGift'=>'5', // 0: Cuando no se obsequia saldo, VALOR: cuando por registrarse se obsequia  un VALOR determinado
                        'askId'=>false, //Para cuando se deba solicitar y mostrar la cedula/nif/rif segun el pais
                        'IVAtext' => '21%', 
                        'registro' => false,    
                        'mostrarMarcas'=>true,
                        'codigoPostal'=>true,
                        'mostrarChic'=>false, 
                        'country'=>'España',
                        'pais'=>'espana',
                        'clientName'=>'Personaling Enterprise S.L ',
                        'clientIdentification'=>'B66202383',
                        'clientAddress'=>'Sant Pere Mes Baix, Nº 63 Principal B ',
                        'clientCity'=>'Barcelona',
                        'clientZIP'=>'08003',
                        'clientPhone'=>'934 344 634',
                        'clientEmail'=>'info@personaling.com',
                        '    ',
                        'pagoPS'=> array(           
                                'paypal' => true,
                                'banco' => true,
                                'saldo' => true,
                            ),
                        'metodosPago'=> array(
                            'bkCard' => true,
                            'paypal' => true, 
                            'prueba' => true,
                        ),
                        'multiLook'=> array(
                            'bodyType' => false,
                            'eyesColor' => false,
                            'hairColor' => false,
                            'womanMeasure' => false,
                            'bodyFavors' => false,
                            'skinColor' => false,
                        ),
                        'AzPayTerminal'=>'999',
                        'AzPaySecret'=> 'qwerty1234567890uiop',
                        'zohoToken'=>'1569fa0c328f9ec6fec9a148939b74fa', 
                    ),
                );
        }
 
        /**
         * Production configuration
         * Usage:
         * - online website
         * - Production DB
         * - Standard production error pages (404,500, etc.)
         */
        private function _production() {
            if ($this->_country == 'es_ve')
                return array(
                    'timeZone' => 'America/Caracas',
                    'language' => 'es_ve',

                    // Application components
                    'components' => array(
 
                            // Database
                            'db'=>array(
                                    'connectionString' => 'mysql:host=mysql-personaling.cu1sufeji6uk.us-west-2.rds.amazonaws.com;dbname=db_personalingT61',
                                    'emulatePrepare' => true,
                                    'username' => 'personaling',
                                    'password' => 'Perso123Naling',
                                    'charset' => 'utf8',
                                    'tablePrefix' => 'tbl_',
                            ),

                             'less'=>array(
                                          'class'=>'ext.less.components.Less',
                                          'mode'=>'client',
                                          'files'=>array(
                                            'less/style.less'=>'css/style.less',
                                          ),
                                              'options'=>array('watch'=>false),
                                        ),
                            'urlManager'=>array(
                                'baseUrl'=>'',
                                ),
 
                            // Application Log
                            'log'=>array(
                                    'class'=>'CLogRouter',
                                    'routes'=>array(
                                            array(
                                                    'class'=>'CFileLogRoute',
                                                    'levels'=>'error, warning',
                                            ),
 
                                            // Send errors via email to the system admin
                                            array(
                                                    'class'=>'CEmailLogRoute',
                                                    'levels'=>'error, warning',
                                                    'emails'=>'admin@example.com',
                                            ),
                                    ),
                            ),
                    ),
                    'params'=>array(
                        // this is used in contact page
                        'adminEmail'=>'rpalma@upsidecorp.ch',
                        'PRONUNCIACION' => 'Venezolana',

                        'currSym'=>'Bs',
                        'noShipping'=> '0', // 0: Cuando se debe cobrar envio, VALOR: cuando el envÃ­o es GRATIS a partir de un VALOR determinado
                        'IVA' => '0.12',
                        'registerGift'=>'5', // 0: Cuando no se obsequia saldo, VALOR: cuando por registrarse se obsequia  un VALOR determinado
                        'askId'=>false, //Para cuando se deba solicitar y mostrar la cedula/nif/rif segun el pais
                        'IVAtext' => '12%',
                        'registro' => false,
                        'mostrarMarcas'=>true,
                        'mostrarChic'=>false,
                        'country'=>'Venezuela',
                        'codigoPostal'=>false,
                        'pais'=>'espana',
                        'clientName'=>'Personaling Enterprise S.L ',
                        'clientIdentification'=>'B66202383',
                        'clientAddress'=>'Sant Pere Mes Baix, NÂº 63 Principal B ',
                        'clientCity'=>'Barcelona',
                        'clientZIP'=>'08003',
                        'clientPhone'=>'934 344 634',
                        'clientEmail'=>'info@personaling.com',
                        '        ',
                        'metodosPago'=> array(
                                'bkCard' => false,
                                'paypal' => false,
                                'prueba' => true,
                                'depositoTransferencia' => true,
                                'instapago' => true,
                                                ),
                                        'pagoPS'=> array(

                                'paypal' => false,
                                'banco' => true,
                                'saldo' => true,
                            ),

                        'multiLook'=> array(
                            'bodyType' => false,
                            'eyesColor' => false,
                            'hairColor' => false,
                            'womanMeasure' => false,
                            'bodyFavors' => false,
                            'skinColor' => false,
                            ),
                        'AzPayTerminal'=>'999',
                        'AzPaySecret'=> 'qwerty1234567890uiop',
                        'zohoToken' => 'f298def0f5eae649aa473c7db3092dc3',
                        ),

                );
            if ($this->_country == 'es_es')
                return array(
                    'language' => 'es_es',
                    'timeZone' => 'Europe/Madrid',
                    'components'=>array(
                                    'db'=>array(
                                            'connectionString' => 'mysql:host=mysql-personaling.cu1sufeji6uk.us-west-2.rds.amazonaws.com;
                                               dbname=db_personalingT52',
                                            'emulatePrepare' => true,
                                            'username' => 'personaling',
                                            'password' => 'Perso123Naling',
                                            'charset' => 'utf8',
                                            'tablePrefix' => 'tbl_',
                                    ),
                    ),
                    'params'=>array(
                        // this is used in contact page
                        'adminEmail'=>'rpalma@upsidecorp.ch',
                        'PRONUNCIACION' => 'EspaÃ±ola',

                        'currSym'=>'E',
                        'noShipping'=> '1', // 0: Cuando se debe cobrar envio, VALOR: cuando el envÃ­o es GRATIS a partir de un VALOR determinado
                        'IVA' => '0.21',
                        'registerGift'=>'0', // 0: Cuando no se obsequia saldo, VALOR: cuando por registrarse se obsequia  un VALOR determinado
                        'askId'=>false, //Para cuando se deba solicitar y mostrar la cedula/nif/rif segun el pais
                        'IVAtext' => '21%',
                        'registro' => false,
                        'mostrarMarcas'=>true,
                        'mostrarChic'=>false,
                        'country'=>'EspaÃ±a',
                        'pais'=>'espana',
                        'clientName'=>'Personaling Enterprise S.L ',
                        'clientIdentification'=>'B66202383',
                        'clientAddress'=>'Sant Pere Mes Baix, NÂº 63 Principal B ',
                        'clientCity'=>'Barcelona',
                        'clientZIP'=>'08003',
                        'clientPhone'=>'934 344 634',
                        'clientEmail'=>'info@personaling.com',
                        '        ',
                        'metodosPago'=> array(
                                'bkCard' => true,
                                'paypal' => true,

                                ),
                        'multiLook'=> array(
                            'bodyType' => false,
                            'eyesColor' => true,
                            'hairColor' => true,
                            'womanMeasure' => true,
                            'bodyFavors' => true,
                            'skinColor' => true,
                        ),

                        'AzPayTerminal'=>'001',
                        'AzPaySecret'=> 'CA4AE93932ADF12EF0D2',
                        ),
                    );


        }
    }// END Environment Class

?>