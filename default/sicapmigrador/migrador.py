import mysql.connector as cn
import config
import selects_relations
import os.path
from os.path import isfile, join
from os import listdir
import shutil

import os

def conectar(config):
  try:
    return cn.connect( **config  )
  except :
    config.log.write('[X]....Error al conectar revice config.py \n')
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
      contenidonuevoModelo = plantilla.replace('+', tabla)
      contenidonuevoModelo = contenidonuevoModelo.replace('-', clase)
      open(rutaNuevoModelo, 'w').write(contenidonuevoModelo)
      creados += 1
    else:
      omitidos += 1

  config.log.write('[OK].....Se han escrito '+ str( creados )+ ' modelos \n')
  config.log.write('[OK].....Se han omitido '+ str( omitidos )+ ' modelos \n')


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

  config.log.write('[OK].....Se han escrito '+ str( creados )+ ' controladores \n')
  config.log.write('[OK].....Se han omitido '+ str( omitidos )+ ' controladores \n')
  print('[OK].....Se han escrito '+ str( creados ) + ' controladores' )
  print('[OK].....Se han omitido '+ str( omitidos) + ' controladores' )

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
        config.log.write('[OK]....Trabjando la tabla '+ nombreTabla +'\n')


        datosFiltrados = str( datosCrudos[0] ).split('\\n')
        llavesForaneas = buscarForaneos(datosFiltrados)


        for index, dato in enumerate(datosFiltrados):
          if( index > 0  and index+1 <  len(datosFiltrados)  ):
            if( 'FOREIGN' not in dato and 'KEY' not in dato  and 'UNIQUE' not in dato and'PRIMARY' not in dato != 'CONSTRAINT'):
              campos = extraerCampos(nombreTabla, dato, llavesForaneas)
              campos.append(orden)
              if( campos != None  and config.globalConfig['cT'] ):
                creados += crearSQL(campos, conexion, nombreTablas)
                orden += config.globalConfig['stepsNum']

    except Exception as e:
      print(e)
      print('[X].....Error al leer la tabla '+ nombreTabla)
      config.log.write('[X].....Error al leer la tabla '+ nombreTabla +'\n')

  config.log.write('[OK].....' + str( creados ) + ' Registros Creados' +'\n')
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
  for chari in config.caracteresEspeciales:
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





  if(nulo == 'NOT' ):
    extras += 'required = "true"'
  else:
    extras += 'required = "false"'

  sql = ''
  try:
    sql = "INSERT INTO "+ tablaConf + " (`id`, `Name`, `TablaPropietaria`,  `Type`,  `VisibleEnForm`,  `VisibleEnTabla`, `Extras`,   `TablaForanea`, `CampoForaneo`, `Sentencias`, `IdentificadorEjecucion`, `Label`, `VisibleEnBusqueda`,`Orden`, `CampoForaneoValor`, `BusquedaSelect`, `DependeDe`  ) VALUES (NULL,'" + campo + "','" + tabla +"','"+ tipo +"',"+ "'1', '1','" + extras + "','" + tf +"','" + cf +"','" + sent + "','"+ config.identificadorEjecucion +  "','" +  label    +  "', '1',  "+ str(orden) +" , '" + campoObjetivo  +"','"+ cs+"', '"+ cd +"' ) "

    cursor.execute(sql)
    config.log.write('[OK].....Ejecutando '+sql+ '\n')
    conexion.commit()
    return 1
  except:
    config.log.write('[X].....Error al ejecutar ' + sql +' \n')
    print('[X].....Error al ejecutar**** ' + sql)

def alterMenuLink( menuTitle ):

    conexion = conectar(config.dbConfig)
    cursor = conexion.cursor()

    link = config.globalConfig['urlPath']+menuTitle
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

      config.log.write('[OK].....Ejecutando SQL ' + sql +' \n')
      print( '[OK].....Insertando Elemento ' + nombreElemento )

  except:
    print('[X].....Error al ejecutar ' + sql)
    config.log.write('[X].....Error al ejecutar ' + sql +' \n')

def corregirTablaSinPrimaria(tablas):
    conexion = conectar(config.dbConfig)
    cursor = conexion.cursor()

    i = 0
    for tabla in tablas:

        try:
            cursor.execute("SHOW CREATE TABLE " + tabla)
            datosCrudos = cursor.fetchall()

            datosFiltrados = str( datosCrudos[0] ).join('\\n')
            if 'PRIMARY KEY' not in datosFiltrados :
                print('[NOT PK]....'+ tabla)
                config.log.write('[NOT PK]....'+ tabla +'\n')
                i = i + 1

        except Exception as e:
              print('[X NOT PK].....Error al leer la tabla '+ tabla)
              config.log.write('[X NOT PK].....Error al leer la tabla '+ tabla +'\n')

    print('[OK]....Total sin llave '+ str(i) )
    config.log.write('[OK]....Total sin llave '+ str(i) +'\n')

def migrate():
    #copiar directorios
    j = 0; k = 0
    for root, dirs, files in os.walk(config.globalConfig['fromDir'], topdown=False):
      for name in dirs:
        try:
          originDir = os.path.join(root, name)
          destinDir = config.globalConfig['toDir']+name

          if( not os.path.isdir(destinDir) ):
            shutil.copytree(originDir, destinDir)
            j += 1
          else:
            k += 1


          ##print originDir
          ##print destinDir
        except Exception as e:
          print '[X]....Error al inentar copiar el directorio ' + name
          print e

    print '[OK]....Directorios copiados '+ str(j)
    print '[OK]....Directorios omitidos '+ str(k)

      ## copiar ficheros externos
    externalFiles = listdir(config.globalConfig['fromDir'])
    reporte =  open(config.globalConfig['ctrlFile'], 'w')
    reporte.write( "<?php  \nclass IndexController extends AppController{ \n\n" )

    i = 0; m = 0; content = '//View::template(null);' ; nombres = []
    for fileName in externalFiles:
      if( '.php'  in fileName  and '~' not in fileName and '(copia).php' not in fileName ):

        try:
          detstiName = config.globalConfig['toDir'] + fileName

          for char in ['.php', '-', ' ']:
            fileName = fileName.replace( char, '')
          #methodName = fileName.replace('.php','')
          #methodName = methodName.replace('-','')

          methodName = fileName
          ctrlName = config.globalConfig['ctrlDir'] + fileName+'_controller.php';

          if methodName not in nombres:
            reporte.write( "\tpublic function "+ methodName +"() {\n\t\t"+ content +"\n\t}\n\n" )
            nombres.append(methodName)


          if( not os.path.isfile( ctrlName ) ):
              if( not os.path.isfile(detstiName) ):
                  originName = config.globalConfig['fromDir'] + fileName+'.php'
                  ## print fileName
                  shutil.copy(originName, detstiName)
                  i += 1
              else:
                  m += 1
          else:
            alterMenuLink(fileName)



        except Exception as e:
          print '[X].... Eror al copiar Archivo'
          print  e



    reporte.write( "}" )
    reporte.close()

    print "[OK]....Archivos copiados " + str(i)
    print "[OK]....Archivos omitidos " + str(m)












def init():


  if( not config.globalConfig['oM'] ):

    conexion = conectar(config.dbConfig)
    nombreTablas = conseguirTablas(conexion)
    plantillamodelos = platillaModelos()
    plantillascaffolds = plantillaScaffolds()

    if( config.globalConfig['cK'] ):
      corregirTablaSinPrimaria( nombreTablas )

    if( config.globalConfig['cS'] ):
      crearControladores(plantillascaffolds, nombreTablas)

    if( config.globalConfig['cM'] ):
      crearModelos(plantillamodelos, nombreTablas)

    if( config.globalConfig['cI'] ):
      describirTablas(nombreTablas)

    if( config.globalConfig['cL'] ):
      createMenuElements(nombreTablas)

  else:
    migrate()








if __name__ == "__main__":
    init()
