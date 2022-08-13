import uuid
from collections import OrderedDict



PATH_MODELS = '../app/models/'
PATH_VIEWS = '../app/views/'
PATH_INHERITS = '/var/www/guadalupana/'
PATH_CONTROLLERS = '../app/controllers/'
TARGET_FILE_EXTEN = '.php'


################################################################################
########################### INICIO DE SECCION EDITABLE #########################
nombreCaja = 'guadalupana'
copiaCaja = '/var/www/guadalupana/'
alias = "guadalupana"

dbConfig = {
    'host' : 'localhost',
    'database': 'guadalupana',
    'user' : 'alfredo',
    'password': 'Alfredo2020+'
}

globalConfig = {
    'cM': True,     ## habilitar la creacion de modelos
    'cS': True,     ## habilitar la creacion de controladores SCAFFOLD
    'cC': True,     ## habilitar la creacion controladores APPCONTROLLER
    'cI': True,     ## habilitar la insercion en la tabla de configuracion
    'cL': True,     ## habilitar la insercion en la tabla mymenugenerador
    'cK': False,     ## detectar y corregir tablas sin llave primaria ************
    'startNum' : 50, ## inicio del indice para ordenar campos de la tabla de configuracion
    'stepsNum' : 10, ## incremento del indice para ordenar campos de la tabla de configuracion
    'readRelations': False,    ## habilitar la lectura de relaciones
########################### FIN DE SECCION EDITABLE ############################
#########################################################################33#####


################################################################################
##################### NO EDITAR A PARTIR DE ESTE COMENTARIO ####################
    'prefix'  : 'WflsTest',
    'fromDir' : copiaCaja+'/',
    'toDir'   : '../../default/app/views/index/',
    'ctrlFile': '../../default/app/controllers/index_controller.php',
    'ctrlDir' : '../../default/app/controllers/',
    'urlPath' : '/'+nombreCaja+'/default/public/',
    'dirToCopy':{
            'clases':  '../../default/app/views/',
            'imprimir':'../../default/app/views/',
            'excel':'../../default/public/excel/',
            'javascript':'../../default/public/javascript/'
        },
    'filesToEdit' : [
        '../../default/public/index.php',
        '../../default/public/javascript/AutoBuildSicap/index-chart.js',
        '../../default/app/config/databases.php',
        '../../default/app/views/index/ValidaUsuario.php',
        '../../default/app/views/index/clases/procedimientos.php',
        '../../default/public/javascript/AutoBuildSicap/index-chart.js',
    ],
    'filesMedia':[
        '../../default/public/imagenes/',
        '../../default/public/estilos/',
        '../../default/public/index/'
    ]
}

scaffoldConfig = {
    '%': 'AjaxScaffold',
    '*': 'AutoBuildSicap', #'AutoBuildSicap',        ##nombre del scaffold a usar dentro de un controlador basado en un scaffoldConroller
    '/': 'configuraciontabla',     ##nombre de la tabla para generar formularios automaticos
    'ow': True         ##habilitar la sobre escritura de controladores tipo scaffold
}

replacementRules = OrderedDict([
    ('$pdf->Open();','//$pdf->Open();'),
    ('mysql_','mysqli_'),
    ('mysqli_query(','mysqli_query($link,'),
    ('mysql_real_escape_string(','mysqli_real_escape_string($link,'),
    ('ereg(','preg_match('),
    ('mysqli_select_db','//mysqli_select_db'),
    ('echo"<script>parent.location.href=\"ValidaUsuario.php;','//'),
    ('exit(','//'),
    ('validacesion(','//'),
    ('function //','function validacesion('),
    ('estilos/imagenes/','/var/www/guadalupana-marte/default/app/views/estilos/imagenes/'),
    ('ardisoencabezado()','//'),
    ('or die("Error al seleccionar la base de datos")' , '//'),
    ('$link=mysqli_connect("localhost", $_SESSION["mibgUsuarioAct"], $_SESSION["mibgClaveUsuar"], $_SESSION["mibgBaseDatos"] )','$link=mysqli_connect($dbserver, $dbuser, $dbpwd,$dbase)'),
    ('mysqli_connect($dbserver, $dbuser, $dbpwd)','mysqli_connect($dbserver, $dbuser, $dbpwd,$dbase)'),
    ('mysqli_connect("localhost", $_SESSION["mibgUsuarioAct"], $_SESSION["mibgClaveUsuar"])','mysqli_connect("localhost", $_SESSION["mibgUsuarioAct"], $_SESSION["mibgClaveUsuar"], $_SESSION["mibgBaseDatos"] )'),
    ('$pdf->Table(','$pdf->Table($link,'),
    ('function Table(','function Table($link,'),
    ('$pdf->Tablet(','$pdf->Tablet($link,'),
    ('function Tablet(','function Tablet($link,'),
    ('$pdf->Tables(','$pdf->Tables($link,'),
    ('function Tables(','function Tables($link,')
    ]

)

modelConfig = {
    'ow': True
}


tablaConfiguracion = 'configuraciontabla'

classFormElement = ' class="form-control" '

identificadorEjecucion =  str(uuid.uuid4())

caracteresEspeciales  = ['_', '-', ',']

caracteresEspecialesTabla  = [ '-', ',']
