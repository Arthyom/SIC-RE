import uuid
from datetime import datetime


PATH_MODELS = '../app/models/'
PATH_CONTROLLERS = '../app/controllers/'
TARGET_FILE_EXTEN = '.php'

fechaExec = datetime.utcnow().strftime('%Y-%m-%d%H:%M:%S:%f')
log = open('execlog'+fechaExec+'.log', 'w')


globalConfig = {
    'oM': True,      ## realizar migracion de archivos de directorio
    'cM': False,     ## habilitar la creacion de modelos
    'cS': False,     ## habilitar la creacion de controladores basados en scaffolds
    'cC': False,     ## habilitar la creacion controladores estandar
    'cI': False,      ## habilitar la insercion en la tabla de configuracion
    'cL': False,     ## habilitar la insercion de elementos en la tabla menucompleto
    'cK': False,     ## detectar y corregir tablas sin llave primaria
    'cK': False,
    'cT': False,     ## insertar en la tabla
    'prefix' : 'WflsTest',
    'startNum' : 50,
    'stepsNum' : 10,
    'readRelations': False,
    'fromDir': '/var/www/florencio/',
    'toDir': '/var/www/florencioK/default/app/views/index/',
    'ctrlFile': '/var/www/florencioK/default/app/controllers/index_controller.php',
    'ctrlDir':'/var/www/florencioK/default/app/controllers/',
    'urlPath': '/florencioK/default/public/'


}

dbConfig = {
    'host' : 'localhost',
    'database': 'florenciocopia',
    'user' : 'alfredo',
    'password': 'Alfredo2020+'
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
