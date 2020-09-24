import uuid



PATH_MODELS = '../app/models/'
PATH_CONTROLLERS = '../app/controllers/'
TARGET_FILE_EXTEN = '.php'


################################################################################
########################### INICIO DE SECCION EDITABLE #########################
nombreCaja = 'coroneo'
copiaCaja = '/home/juvencio/www/coroneo'
dbConfig = {
    'host' : 'localhost',
    'database': 'coroneo',
    'user' : 'alfredo',
    'password': 'Alfredo2020+'
}
globalConfig = {
    'oM': False,     ## realizar migracion de archivos y directorios SICAP
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
    'filesToEdit' : [
        '../../default/public/index.php',
        '../../default/public/javascript/AutoBuildSicap/index-chart.js',
        '../../default/app/views/index/ValidaUsuario.php',
        '../../default/app/views/index/clases/procedimientos.php',
        '../../default/public/javascript/AutoBuildSicap/index-chart.js',
    ],
    'filesMedia':[
        '../../default/public/imagenes/',
        '../../default/public/estilos/',
        '../../default/public/'
    ]
}

scaffoldConfig = {
    '*': 'AutoBuildSicap', #'AutoBuildSicap',        ##nombre del scaffold a usar dentro de un controlador basado en un scaffoldConroller
    '/': 'configuraciontabla',     ##nombre de la tabla para generar formularios automaticos
    'ow': True         ##habilitar la sobre escritura de controladores tipo scaffold
}

modelConfig = {
    'ow': True
}


tablaConfiguracion = 'configuraciontabla'

classFormElement = ' class="form-control" '

identificadorEjecucion =  str(uuid.uuid4())

caracteresEspeciales  = ['_', '-', ',']
