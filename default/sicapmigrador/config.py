import uuid



PATH_MODELS = '../app/models/'
PATH_VIEWS = '../app/views/'
PATH_INHERITS = '***plantilla_caja***'
PATH_CONTROLLERS = '../app/controllers/'
TARGET_FILE_EXTEN = '.php'


################################################################################
########################### INICIO DE SECCION EDITABLE #########################
nombreCaja = '***nombre_caja***'
copiaCaja = '***plantilla_caja***'
dbConfig = {
    'host' : '***nombre_host***',
    'database': '***nombre_db***',
    'user' : '***nombre_usuario***',
    'password': '***nombre_pass***'
}
globalConfig = {
    'oM': '***oM***',     ## realizar migracion de archivos y directorios SICAP
    'cM': '***cM***',     ## habilitar la creacion de modelos
    'cS': '***cS***',     ## habilitar la creacion de controladores SCAFFOLD
    'cC': '***cS***',     ## habilitar la creacion controladores APPCONTROLLER
    'cI': '***cI***',     ## habilitar la insercion en la tabla de configuracion
    'cL': '***cL***',     ## habilitar la insercion en la tabla mymenugenerador
    'cK': '***cK***',     ## detectar y corregir tablas sin llave primaria ************
    'startNum' : '***srtN***', ## inicio del indice para ordenar campos de la tabla de configuracion
    'stepsNum' : '***stpN***', ## incremento del indice para ordenar campos de la tabla de configuracion
    'readRelations': '***rL***',    ## habilitar la lectura de relaciones
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
        '../../default/public/index/'
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

caracteresEspecialesTabla  = [ '-', ',']
