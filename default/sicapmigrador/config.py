import uuid
from datetime import datetime


PATH_MODELS = '../app/models/'
PATH_CONTROLLERS = '../app/controllers/'
TARGET_FILE_EXTEN = '.php'

fechaExec = datetime.utcnow().strftime('%Y-%m-%d%H:%M:%S:%f')
log = open('execlog'+fechaExec+'.log', 'w')

nombreCaja = 'coroneo'
fromCaja = 'yuriria'

globalConfig = {
    'oM': False,     ## realizar migracion de archivos de directorio
    'cM': True,     ## habilitar la creacion de modelos
    'cS': True,     ## habilitar la creacion de controladores basados en scaffolds
    'cC': True,     ## habilitar la creacion controladores estandar
    'cI': True,     ## habilitar la insercion en la tabla de configuracion
    'cL': True,     ## habilitar la insercion de elementos en la tabla menucompleto
    'cK': False,     ## detectar y corregir tablas sin llave primaria
    'cK': False,        
    'cT': True,     ## insertar en la tabla
    'prefix' : 'WflsTest',
    'startNum' : 50,
    'stepsNum' : 10,
    'readRelations': False,
    'fromDir': '/var/www/'+fromCaja+'/',
    'toDir': '/var/www/'+nombreCaja+'/default/app/views/index/',
    'ctrlFile': '/var/www/'+nombreCaja+'/default/app/controllers/index_controller.php',
    'ctrlDir':'/var/www/'+nombreCaja+'/default/app/controllers/',
    'urlPath': '/'+nombreCaja+'/default/public/'


}

dbConfig = {
    'host' : 'localhost',
    'database': 'coroneo',
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
