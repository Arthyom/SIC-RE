import mysql.connector as cn
import selects_relations
import os.path
from os.path import isfile, join
from os import listdir
import shutil
import shutil
import sys, getopt
import os
import config
from datetime import datetime
fechaExec = datetime.utcnow().strftime('%Y-%m-%d%H:%M:%S:%f')
log = open('execlog'+fechaExec+'.log', 'w')

marcadores = ['***nombre_caja***', '***plantilla_caja***', '***nombre_db***', '***nombre_host***', '***nombre_usuario***', '***nombre_pass***']

def createConfigTable():
    try:
        lines = open('configuraciontabla.sql', 'r').readlines(); sql = ''
        for line in lines:
            sql += line
        conexion = conectar(config.dbConfig)
        cursor = conexion.cursor()
        cursor.execute(sql)
        conexion.close()
    except Exception as e:
        print( '[X]....Error al crear la tabla de configuracion o ya ha sido creada\n' )

def conectar(config):
  try:
    return cn.connect( **config  )
  except :
    log.write('[X]....Error al conectar revice config.py \n')
    print( '[X]....Error al conectar revice config.py' )
    exit()

def conseguirTablas(dbConection):
  cursor = dbConection.cursor()

  cursor.execute('SHOW TABLES')
  tablas = cursor.fetchall()
  dbConection.close()

  tablasFiltradas = []

  for tabla in tablas:
    tablasFiltradas.append( str(tabla[0]) )

  return tablasFiltradas

def platillaModelos():
  return  open('kmodel.template', 'r').read()

def plantillaScaffolds():
  return  open('kscaffold.template', 'r').read()

def crearModelos( plantilla, nombreTablas ):
  creados = 0
  omitidos = 0
  for tabla in nombreTablas:
    clase = tabla
    tabla = tabla.lower()
    for char in config.caracteresEspeciales:
      tabla = tabla.replace(char, '')
    rutaNuevoModelo = config.PATH_MODELS + tabla + config.TARGET_FILE_EXTEN
    if( config.modelConfig['ow']):
      if not os.path.isfile(rutaNuevoModelo):
          contenidonuevoModelo = plantilla.replace('+', tabla)
          contenidonuevoModelo = contenidonuevoModelo.replace('-', clase)
          open(rutaNuevoModelo, 'w').write(contenidonuevoModelo)
      creados += 1
    else:
      omitidos += 1

  log.write('[OK].....Se han escrito '+ str( creados )+ ' modelos \n')
  log.write('[OK].....Se han omitido '+ str( omitidos )+ ' modelos \n')


  print('[OK].....Se han escrito '+ str( creados )+ ' modelos' )
  print('[OK].....Se han omitido '+ str( omitidos )+ ' modelos' )

def crearControladores( plantilla, nombreTablas ):
  omitidos = 0
  creados = 0
  for tabla in nombreTablas:
    for chari in config.caracteresEspeciales:
      tabla = tabla.replace( chari , '')

    rutaNuevoControlador = config.PATH_CONTROLLERS + tabla.lower() +'_controller' + config.TARGET_FILE_EXTEN

    if(  config.scaffoldConfig['ow'] ):
      contenidoNuevoControlador = plantilla.replace('+', tabla.capitalize() )
      contenidoNuevoControlador = contenidoNuevoControlador.replace('-', tabla.lower() )
      contenidoNuevoControlador = contenidoNuevoControlador.replace('*', config.scaffoldConfig['*'] )
      contenidoNuevoControlador = contenidoNuevoControlador.replace('/', config.scaffoldConfig['/'] )

      open(rutaNuevoControlador, 'w').write(contenidoNuevoControlador)
      creados += 1
    else:
      omitidos += 1

  log.write('[OK].....Se han escrito '+ str( creados )+ ' controladores \n')
  log.write('[OK].....Se han omitido '+ str( omitidos )+ ' controladores \n')
  print('[OK].....Se han escrito '+ str( creados ) + ' controladores' )
  print('[OK].....Se han omitido '+ str( omitidos) + ' controladores' )

def get_controller_content( nombreTabla ):
    exceptions = ['imprime', 'imprimir', ]
    for exception in exceptions:
        if exception in nombreTabla:
            return open('kblank.template','r').read().replace('+', nombreTabla)
    return open('kappcontroller.template','r').read().replace('+', nombreTabla)

def describirTablas(nombreTablas):
  creados = 0

  conexion = conectar(config.dbConfig)
  cursor = conexion.cursor()
  for nombreTabla in nombreTablas:
    try:
      if( True ):
        orden = config.globalConfig['startNum']
        cursor.execute("SHOW CREATE TABLE " + nombreTabla)
        datosCrudos = cursor.fetchall()


        print('[OK]....Trabjando la tabla '+ nombreTabla)
        log.write('[OK]....Trabjando la tabla '+ nombreTabla +'\n')


        datosFiltrados = str( datosCrudos[0] ).split('\\n')
        llavesForaneas = buscarForaneos(datosFiltrados)


        for index, dato in enumerate(datosFiltrados):
          if( index > 0  and index+1 <  len(datosFiltrados)  ):
            if( 'FOREIGN' not in dato and 'KEY' not in dato  and 'UNIQUE' not in dato and'PRIMARY' not in dato != 'CONSTRAINT'):
              campos = extraerCampos(nombreTabla, dato, llavesForaneas)
              campos.append(orden)
              if( campos != None  ):
                creados += crearSQL(campos, conexion, nombreTablas)
                orden += config.globalConfig['stepsNum']

    except Exception as e:
      print(e)
      print('[X].....Error al leer la tabla '+ nombreTabla)
      log.write('[X].....Error al leer la tabla '+ nombreTabla +'\n')

  log.write('[OK].....' + str( creados ) + ' Registros Creados' +'\n')
  print('[OK].....' + str( creados ) + ' Registros Creados')

  conexion.close()

def extraerCampos( nombreTabla, campo, llaves ):
  cadanaSemgentada = campo.split(' ')
  cadanaSemgentada.remove('')
  nombreCampo = cadanaSemgentada[1].replace('`', '')
  if( nombreCampo != 'UNIQUE' and  nombreCampo != 'KEY' and nombreCampo != 'PRIMARY' and nombreCampo != 'CONSTRAINT'):
    key = cadanaSemgentada[0]
    tipoDato = cadanaSemgentada[2].replace('`', '')
    nulo = 'NULL'; tablaForanea = ''; campoForaneo =''
    if( 'NOT' in cadanaSemgentada ):
      nulo = 'NOT'
    if( 'unsigned' in cadanaSemgentada or 'UNSIGNED' in cadanaSemgentada ):
      tipoDato += ' UNSIGNED'
    if( len( llaves )  > 0 ):
      if( llaves[0][0] == nombreCampo ):
        tablaForanea = llaves[0][1]
        campoForaneo = llaves[0][2]
    return [nombreTabla, nombreCampo, tipoDato, nulo, tablaForanea, campoForaneo]
  else:
    return None

def buscarForaneos( tablaFiltrada ):
  llaves = []
  for linea in tablaFiltrada:
    lineaSegmentada = linea.split(' ')
    #print lineaSegmentada
    if( 'FOREIGN' in lineaSegmentada ):
      campoOrigen = lineaSegmentada[6].replace('(`', '').replace( '`)', '')
      tabla = lineaSegmentada[8].replace('`', '')
      campo = lineaSegmentada[9].replace('(`', '').replace( '`)', '')
      llaves.append( (campoOrigen, tabla, campo) )
  return (llaves)

def pluralizar (singular):
    if( singular.endswith(('a','e','i','o','u')) ):
        singular += 's'
    elif( singular.endswith(('z')) ):
        singular += 'ces'
    elif( singular.endswith(('s','0','1','2','3','4','5','6','7','8','9')) ):
        pass
    else:
        singular += 'es'

    return singular.lower()

def buscarDependencia( campoHijo ):
    lineas =  open('selects.dependent', 'r').readlines()
    for linea in lineas:
        linea = linea.rstrip()
        lineaSegmentada = linea.split(':')
        if( campoHijo in lineaSegmentada ):
            indicePadre = lineaSegmentada.index(campoHijo) - 1
            if( indicePadre > 0 ):
                return lineaSegmentada[indicePadre]
    return ''

def reportarSelect( tablaPropietaria, campo, tablaForanea, tipo ):
        reporte =  open('selects.reported', 'a')
        texto = '[ '+tipo+' ] CREADO:['+tablaPropietaria +'] \t CAMPO:['+ campo+"] \t REFENCIA:[ "+tablaForanea+" ] \n";
        reporte.write( texto )
        reporte.close()

def buscarRelaciones( tablaPropietaria, campoPropietario  ):
    key = tablaPropietaria +'.'+campoPropietario
    values = selects_relations.relations.get(key)

    if( values ):
        values = values.split('.')
        return values
    else:
        return None

def crearSQL( parametros, conexion, nombreTablas ):
  cursor = conexion.cursor()
  extras = config.classFormElement
  tablaConf = config.tablaConfiguracion
  tipoSelect = ''

  tabla = parametros[0]
  campo = parametros[1]
  tipo  = parametros[2]
  nulo  = parametros[3]
  tf    = parametros[4]
  cf    = parametros[5]
  orden = parametros[6]
  label = campo
  cs    = ''
  cd    = ''
  campoObjetivo = ''
  sent = ""
  for chari in config.caracteresEspecialesTabla:
      tabla = tabla.replace( chari , '')
      campo = campo.replace( chari , '')
      tipo = tipo.replace( chari , '')
      nulo = nulo.replace( chari , '')
      tf = tf.replace( chari , '')
      cf = cf.replace( chari , '')

  if( campo.startswith('Id') ):
      conexion = conectar(config.dbConfig)
      cursor = conexion.cursor()
      cursor.execute("SHOW INDEX FROM " + tabla)
      indices = cursor.fetchall()
      for indice in indices:
          if( 'PRIMARY' != indice[2] and  indice[4].startswith('Id') ):
              singular = campo
              singular = singular[2:]
              tf = pluralizar(singular)
              infoForanea = ''
              # si el plural existe en las tablas
              if( tf not in nombreTablas ):
                  # leerlo desde el archivo de relaciones
                  infoForanea = buscarRelaciones( tabla, campo )
                  tipoSelect = 'READ'
                  if( infoForanea != None):
                      tf = infoForanea[0]
                      campoObjetivo = infoForanea[1]
                      cs = infoForanea[2]
                  else:
                      tf = singular.lower()
                      campoObjetivo = campo
                      cs = campo
              else:
                  # si existe generar valores automaticos
                  tipoSelect = 'AUTO'
                  campoObjetivo = campo
                  cs = campo

              sent =  "SELECT * FROM " +tf
              tipo = 'select'
              cf = campo
              cd = buscarDependencia(campo)
              ### generar etiqueta con nombre pluralizado  para la tabla de referencia
              label = tf


  if(tipo == 'select'):
    reportarSelect(tabla, campo, tf, tipoSelect)

  if( 'enum' in tipo or tf != '' or 'set' in tipo):
    tipo = 'select'
  elif( 'date' in tipo  ):
    tipo = 'date'
  elif( 'varchar' in tipo ):
    tipo = 'text'
  elif( 'int' in tipo or 'decima' in tipo ):
    tipo = 'number'
    if( 'UNSIGNED' in tipo or 'unsigned' in tipo ):
      extras += ' min = "0" '
  if( tf != '' ):
    sent  = "SELECT * FROM " +tf

  if(nulo == 'NOT' or 'NOT' in parametros ):
    extras += 'required'
  else:
    extras += ''

  if( tabla == config.tablaConfiguracion ):
    extras = config.classFormElement

  sql = ''
  try:
    sql = "INSERT INTO "+ tablaConf + " (`id`, `Name`, `TablaPropietaria`,  `Type`,  `VisibleEnForm`,  `VisibleEnTabla`, `Extras`,   `TablaForanea`, `CampoForaneo`, `Sentencias`, `IdentificadorEjecucion`, `Label`, `VisibleEnBusqueda`,`Orden`, `CampoForaneoValor`, `BusquedaSelect`, `DependeDe`  ) VALUES (NULL,'" + campo + "','" + tabla +"','"+ tipo +"',"+ "'1', '1','" + extras + "','" + tf +"','" + cf +"','" + sent + "','"+ config.identificadorEjecucion +  "','" +  label    +  "', '1',  "+ str(orden) +" , '" + campoObjetivo  +"','"+ cs+"', '"+ cd +"' ) "

    cursor.execute(sql)
    log.write('[OK].....Ejecutando '+sql+ '\n')
    conexion.commit()
    return 1
  except:
    log.write('[X].....Error al ejecutar ' + sql +' \n')
    print('[X].....Error al ejecutar**** ' + sql)

def alterMenuLink( menuTitle ):

    conexion = conectar(config.dbConfig)
    cursor = conexion.cursor()

#    link = config.globalConfig['urlPath']+menuTitle
    link = menuTitle

    sql = "UPDATE mymenugenerador SET external_link = '" + link + "' WHERE menu_title ='"+ menuTitle.capitalize() +"'"
    cursor.execute(sql)
    conexion.commit()

def createMenuElements(elementosTabla):
  try:
    sql = ''
    for elementoTabla in elementosTabla:
      conexion = cn.connect( **config.dbConfig  )
      cursor = conexion.cursor()
      nombreElemento = config.globalConfig['prefix']+elementoTabla

      sql = "INSERT INTO `menucompleto` (`Grupo`, `Id`, `Nombre`, `Posicion`, `IdPadre`, `Mensaje`) VALUES ('1', 0 , '"+ nombreElemento +"', '0', '0', '0')"
      cursor.execute( sql )
      conexion.commit()

      log.write('[OK].....Ejecutando SQL ' + sql +' \n')
    ##  print( '[OK].....Insertando Elemento ' + nombreElemento )

  except:
    print('[X].....Error al ejecutar ' + sql)
    log.write('[X].....Error al ejecutar ' + sql +' \n')

def corregirTablaSinPrimaria(tablas):
    conexion = conectar(config.dbConfig)
    cursor = conexion.cursor()

    i = 0
    for tabla in tablas:

        try:
            cursor.execute("SHOW CREATE TABLE " + tabla)
            datosCrudos = cursor.fetchall()

            datosFiltrados = str( datosCrudos[0] ).join('\\n')
            first_column = datosFiltrados.split(',')[1].split(' ')[6]
            if 'PRIMARY KEY' not in datosFiltrados :
                print('[NOT PK]....'+ tabla + ' [CORRECTING]')
                sql_add_pk = 'ALTER TABLE '+tabla + ' ADD  CONSTRAINT PK_'+tabla +' PRIMARY KEY (' + first_column  +' );'
                cursor.execute( sql_add_pk )
                print( sql_add_pk )
                log.write('[NOT PK]....'+ tabla +'\n')
                i = i + 1

        except Exception as e:
              i = i + 1
              print('[X].....Error al leer la tabla '+ tabla + ' ' + e)
              log.write('[X].....Error al leer la tabla '+ tabla +' e \n')

    print('[OK]....Total sin llave '+ str(i) )
    log.write('[OK]....Total sin llave '+ str(i) +'\n')

def truncate():
    try:
        conexion = conectar(config.dbConfig)
        cursor = conexion.cursor()
        tabla = config.tablaConfiguracion
        sql = "USE "+ config.dbConfig['database']
        cursor.execute(sql)
        sql = "TRUNCATE TABLE "+ tabla
        cursor.execute(sql)
        conexion.commit()

        print("[OK]....Tabla '" + tabla + "'' truncada correctamente" )

    except Exception as e:
        raise

def migrate():
    #copiar directorios
    j = 0; k = 0
    for root, dirs, files in os.walk(config.globalConfig['fromDir'], topdown=False):
      for name in dirs:
        try:
          originDir = os.path.join(root, name)
          destinDir = config.globalConfig['toDir']+name

          fileDir = os.path.dirname(os.path.realpath('__file__'))
          destinDir = os.path.join(fileDir, destinDir)
          destinDir = os.path.abspath(os.path.realpath(destinDir))

          mediaTarget = os.path.join(fileDir, config.globalConfig['filesMedia'][2] )
          mediaTarget = os.path.abspath(os.path.realpath(mediaTarget))

          if( not os.path.isdir(destinDir) ):
              if name != 'estilos' and name != 'imagenes':
                  shutil.copytree(originDir, destinDir)
              elif name == 'estilos':
                  shutil.copytree(originDir, mediaTarget+'/estilos')
              elif name == 'imagenes':
                  shutil.copytree(originDir, mediaTarget+'/imagenes')
              j += 1
          else:
            k += 1


          ##print originDir
          ##print destinDir
        except Exception as e:
          print '[X]....Error al inentar copiar el directorio ' + name + 'dddd'+ destinDir
          print e

    print '[OK]....Directorios copiados '+ str(j)
    print '[OK]....Directorios omitidos '+ str(k)

      ## copiar ficheros externos
    i = 0; m = 0; content = 'View::template(null);' ; nombres = []
    externalFiles = listdir(config.globalConfig['fromDir'])
    reporte =  open(config.globalConfig['ctrlFile'], 'w')
    reporte.write( "<?php  \nclass IndexController extends AppController{ \n\n" )
    reporte.write( "public function index(){\n\t\t"+ '///'+content +"\n\t}\n\n" )
    for fileName in externalFiles:
      if( '.php'  in fileName  and '~' not in fileName and '(copia).php' not in fileName ):

        try:

          detstiName = config.globalConfig['toDir'] + fileName
          fileDir = os.path.dirname(os.path.realpath('__file__'))
          detstiName = os.path.join(fileDir, detstiName)
          detstiName = os.path.abspath(os.path.realpath(detstiName))

          for char in ['.php', '-', ' ','.']:
            fileName = fileName.replace( char, '')
          #methodName = fileName.replace('.php','')
          #methodName = methodName.replace('-','')

          methodName = fileName
          ctrlName = config.globalConfig['ctrlDir'] + fileName+'_controller.php';
          fileDir = os.path.dirname(os.path.realpath('__file__'))
          ctrlName = os.path.join(fileDir, ctrlName)
          ctrlName = os.path.abspath(os.path.realpath(ctrlName))

          if methodName not in nombres:
            reporte.write( "\tpublic function "+ methodName +"() {\n\t\t"+ content +"\n\t}\n\n" )
            nombres.append(methodName)


          if( not os.path.isfile( ctrlName ) and fileName != 'ValidaUsuario' and fileName != 'procedimientos'):
              if( not os.path.isfile(detstiName) ):
                  originName = config.globalConfig['fromDir'] + fileName+'.php'
                  ## print fileName
                  shutil.copy(originName, detstiName)
                  i += 1
              else:
                  m += 1
          else:
              try:
                  alterMenuLink(fileName)
              except Exception as e:
                  print '[X].... Eror al intentar modificar la tabla de menus'
                  print  e




        except Exception as e:
          print '[X].... Eror al copiar Archivo'
          print  e



    reporte.write( "}" )
    reporte.close()

    print "[OK]....Archivos copiados " + str(i)
    print "[OK]....Archivos omitidos " + str(m)

def inject_styles_marto_to_inherited( inherited_content ):
    replaces = {
        '<input type=\\"submit\\"': '<input type=\\"submit\\" class=\'btn btn-block btn-primary\'',
        '<button type="submit"': '<button type="submit" class="btn btn-block btn-primary"',
        '<button type=\\"submit\\"' : '<button type=\\"submit\\" class=\\"btn btn-block btn-primary\\"',
        '<input type=\\"button\\"': '<input type=\\"button\\" class=\\"btn btn-block btn-primary\\"',
        '"clases' : 'APP_PATH."views/clases',
        'table': 'table class=\'table\'',
        '"clases': 'APP_PATH."/views/clases',
        '<select': '<select class=\'form-control\'',
        'action=\\"".$_SERVER[\'PHP_SELF\']."\\"' : 'action=\\"$controller_name\\"',
        'require(\'imprimir/': 'require(APP_PATH.\'/views/imprimir/'

    }

    for key in replaces.keys():
        inherited_content = inherited_content.replace(key, replaces[key])

    return inherited_content

def get_inherited_content( file_name ):
    if os.path.isfile(config.PATH_INHERITS + file_name +'.php'):
        content_text = open( config.PATH_INHERITS + file_name +'.php', 'r').read()
        content_text = inject_styles_marto_to_inherited(content_text)
        return content_text
    else:
        return "<?php \t\t * "

def convert_inherited():
    ## get files from controllers dir
    inherited_files = [file.replace('.php', '') for file in os.listdir(config.PATH_INHERITS) ]
    controllers_files = [file.replace('.php', '') for file in os.listdir(config.PATH_MODELS) ]
    inherited_notin_controllers = [ file_inherited for file_inherited in inherited_files if file_inherited not in controllers_files ]
    inherited_dirs = []

    for file_name in inherited_notin_controllers:
        dir_path = config.PATH_VIEWS + file_name
        ctrl_path = config.PATH_CONTROLLERS + file_name + '_controller.php'

        if file_name not in ['clases','imprimir']:
            if  os.path.isdir( dir_path ):
                shutil.rmtree(dir_path)

            os.mkdir( dir_path )
            open( dir_path + '/index.phtml', 'w' ).write( get_inherited_content(file_name))

            ##if not os.path.isfile(ctrl_path):
            open( ctrl_path , 'w').write( get_controller_content(file_name) )







def printCriticals():
    for file in config.globalConfig['filesToEdit']:
        fileDir = os.path.dirname(os.path.realpath('__file__'))
        file = os.path.join(fileDir, file)
        file = os.path.abspath(os.path.realpath(file))

        lector = open( file  ,'r')
        lines = lector.readlines()
        content = ''
        for line in lines:
            content += line
        lector.close()


        content = content.replace("***nombre_caja***",   config.nombreCaja)
        content = content.replace("***nombre_host***",   config.dbConfig['host'])
        content = content.replace("***nombre_db***",     config.dbConfig['database'])
        content = content.replace("***nombre_usuario***",config.dbConfig['user'] )
        content = content.replace("***nombre_pass***",   config.dbConfig['password'])

        escrior = open(file,'w')
        escrior.write(content)
        escrior.close()

def readConfParams():
    ## solo para las configuraciones criticas
    for param in sys.argv[1:]:
         pi = param.split('=')
         if '--nombre_caja' in pi:
             config.nombreCaja = pi[1]
         if '--plantilla_caja' in pi:
             config.plantillaCaja = pi[1]
         if '--orden_iniciar_en' in pi:
             config.globalConfig['startNum']  = pi[1]
         if '--orden_saltos_de' in pi:
             config.globalConfig['stepNum']  = pi[1]
         if '--crear_modelos' in pi:
             config.globalConfig['cM']  = True
         if '--crear_controls' in pi:
             config.globalConfig['cS']  = True
         if '--crear_tabla_conf' in pi:
             config.globalConfig['cI']  = True
         if '--solo_migrar' in pi:
              config.globalConfig['oM']  = True
         if '--crear_tabla_menu' in pi:
             config.globalConfig['cL']  = True
         if '--nombre_host' in pi:
             config.dbConfig['host']  = pi[1]
         if '--nombre_user' in pi:
             config.dbConfig['usuario']  = pi[1]
         if '--nombre_db' in pi:
             config.dbConfig['database']  = pi[1]
         if '--nombre_pass' in pi:
             config.dbConfig['password']  = pi[1]

def readParams():
    ## para cualquier configuracion que se desee realizar
     parametros = sys.argv[1:]
     nombreCaja = ''; plantillaCaja = ''; startNum = 50; stepNum = 10
     crearModelos = True; crearControladores = True;
     insertarTablaConf = True; insertarTablaMenu = True; soloCopiar = False
     solorMigrar = True; nombreHost = '';  nombreDb = ''; nombreUser = '';
     nombrePass = ''; truncar = False
     corregirKeys = False; readRelations = False
     escritor = open( 'config.py'  ,'r');
     lines = escritor.readlines(); content = ''

     for line in lines:
         content += line
     escritor.close()

     for param in sys.argv[1:]:
          pi = param.split('=')
          if '--nombre_caja' in pi:
               nombreCaja = pi[1]
          if '--plantilla_caja' in pi:
                plantillaCaja = pi[1]
          if '--orden_iniciar_en' in pi:
               startNum  = pi[1]
          if '--orden_saltos_de' in pi:
               stepNum = pi[1]
          if '--crear_modelos' in pi:
               crearModelos = pi[1]
          if '--crear_controls' in pi:
               crearControladores = pi[1]
          if '--crear_tabla_conf' in pi:
               insertarTablaConf = pi[1]
          if '--crear_tabla_menu' in pi:
               insertarTablaMenu = pi[1]
          if '--solo_migrar' in pi:
               solorMigrar  = pi[1]
          if '--nombre_host' in pi:
               nombreHost = pi[1]
          if '--nombre_db' in pi:
               nombreDb = pi[1]
          if '--nombre_user' in pi:
               nombreUser = pi[1]
          if '--nombre_pass' in pi:
               nombrePass = pi[1]
          if '--truncar_tabla' in pi:
               truncar = pi[1]


     if nombreCaja != '' and plantillaCaja != '':
       content = content.replace('***nombre_caja***', nombreCaja)
       content = content.replace("***plantilla_caja***", plantillaCaja)
       content = content.replace("'***srtN***'", str(startNum) )
       content = content.replace("'***stpN***'", str(stepNum) )
       content = content.replace("'***oM***'", str(solorMigrar) )
       content = content.replace("'***cM***'", str(crearModelos))
       content = content.replace("'***cS***'", str(crearControladores))
       content = content.replace("'***cI***'", str(insertarTablaConf))
       content = content.replace("'***cL***'", str(insertarTablaMenu))
       content = content.replace("'***cK***'", str(corregirKeys))
       content = content.replace("'***rL***'", str(readRelations))
       content = content.replace("***nombre_host***", str(nombreHost))
       content = content.replace("***nombre_db***", str(nombreDb))
       content = content.replace("***nombre_usuario***", str(nombreUser))
       content = content.replace("***nombre_pass***", str(nombrePass))
       content = content.replace("'***tT***'", str(truncar) )




       vacio = False
       for par in [ nombreCaja, plantillaCaja, nombreHost, nombreDb, nombreUser, nombrePass]:
           if par == '':
               vacio = True

       if not vacio :
            escrior = open( 'config.py'  ,'w')
            escrior.write(content)
            escrior.close()
            reload(config)
            printCriticals()

     if nombreCaja == '':
         print("Por favor indique el nombre de la caja a migrar con el parametro --nombre_caja=caja ")
     if plantillaCaja == '':
         print("Por favor indique el path de la caja a usar como plantilla con el parametro --plantilla_caja=path  ")
     if nombreHost == '':
         print("Por favor indique el host que almacena la base de datos con el parametro --nombre_host=host  ")
     if nombreUser == '':
         print("Por favor indique el usuario para acceder a la base de datos con el parametro --nombre_user=user  ")
     if nombreDb == '':
         print("Por favor indique el nombre de la base de datos a usar con el parametro --nombre_db=db  ")
     if nombrePass == '':
         print("Por favor indique el pass para acceder a la base de datos con el parametro --nombre_pass=pass  ")

def checkCriticalConf():
    lector = open( 'config.py'  ,'r')
    lines = lector.readlines(); content = ''
    for line in lines:
        content += line
    lector.close()
    for marcador in marcadores:
        return marcador in content

def executeMigrator( primeraVez = False ):
    conexion = conectar(config.dbConfig)
    nombreTablas = conseguirTablas(conexion)
    plantillamodelos = platillaModelos()
    plantillascaffolds = plantillaScaffolds()
    parametros = sys.argv[1:]
    if( primeraVez ):
        crearControladores(plantillascaffolds, nombreTablas)
        crearModelos(plantillamodelos, nombreTablas)
        describirTablas(nombreTablas)
        createMenuElements(nombreTablas)
        migrate()



    if( not config.globalConfig['oM'] ):
      for param in sys.argv[1:]:

          pi = param.split('=')

          if '--truncar_tabla' in pi:
              truncate()

          if( config.globalConfig['cK'] ):
              corregirTablaSinPrimaria( nombreTablas )

          if( '--crear_controls' in pi ):
              crearControladores(plantillascaffolds, nombreTablas)

          if( '--crear_modelos' in pi ):
              crearModelos(plantillamodelos, nombreTablas)

              #### necesitan mejora
          if( '--crear_tabla_conf' in pi):
              describirTablas(nombreTablas)

              ### necesitan mejora
          if( '--crear_tabla_menu' in pi):
              createMenuElements(nombreTablas)


          if( '--convertir_heredados' in pi):
              convert_inherited()


          if( '--migrar' in pi):
              migrate()
    else:
     migrate()



def init():
    primera = False
    if checkCriticalConf():
        readParams()
        primera = True

    else:
        ### no se indican para metros
        if 1 < len( sys.argv )    :
            readConfParams()

    ### comenzar la migracion
    createConfigTable()
    executeMigrator(primera)













if __name__ == "__main__":
    init()
