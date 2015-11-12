/* ----------------------------------------------------------------------------------
    KEME-Contabilidad; aplicación para llevar contabilidades

    Copyright (C)

    Este programa es software libre: usted puede redistribuirlo y/o modificarlo 
    bajo los términos de la Licencia Pública General GNU publicada 
    por la Fundación para el Software Libre, ya sea la versión 3 
    de la Licencia, o (a su elección) cualquier versión posterior.

    Este programa se distribuye con la esperanza de que sea útil, pero 
    SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita 
    MERCANTIL o de APTITUD PARA UN PROPÓSITO DETERMINADO. 
    Consulte los detalles de la Licencia Pública General GNU para obtener 
    una información más detallada. 

    Debería haber recibido una copia de la Licencia Pública General GNU 
    junto a este programa. 
    En caso contrario, consulte <http://www.gnu.org/licenses/>.
  ----------------------------------------------------------------------------------*/

#include "basedatos.h"

#include "funciones.h"

#define VERSION "2.9.8.5"

#include <QMessageBox>
#include <QApplication>
#include <QObject>
#include <QProgressDialog>
#include <QDir>
#include <QFile>

//////////////////////////////
// Constantes
//////////////////////////////

basedatos* basedatos::base = NULL;
static bool error_consulta=false;

const char basedatos::NINGUNA = 0;
const char basedatos::MYSQL = 1;
const char basedatos::POSTGRES = 2;
const char basedatos::SQLITE = 3;

//////////////////////////////
// Constructores y destructores
//////////////////////////////

basedatos::basedatos () {
    comadecimal=haycomadecimal();
    decimales=haydecimales();
}

basedatos::~basedatos () {

}

//////////////////////////////
// Públicas
//////////////////////////////

// Devuelve un puntero a la instancia única de la base de datos
basedatos* basedatos::instancia () {
    if( base == NULL ) {
        base = new basedatos();
    }
    return base;
}

// Devuelve si la base de datos está abierta o no
bool basedatos::abierta () {
    return QSqlDatabase::database().open();
}


// Devuelve el tipo de base de datos que está siendo usada: MYSQL, POSTGRES, SQLITE
char basedatos::cualControlador (QString db) {
    QSqlDatabase b=QSqlDatabase::database(db);
    if ( b.driverName().indexOf("QMYSQL") > -1 ) { return MYSQL; }
    else if ( b.driverName().indexOf("QPSQL") > -1 ) { return POSTGRES; }
    else if ( b.driverName().indexOf("QSQLITE") > -1 ) { return SQLITE; }
    else return NINGUNA;
}

// Devuelve el tipo de base de datos que está siendo usada: MYSQL, POSTGRES, SQLITE
char basedatos::cualControlador (void) {
    if ( QSqlDatabase::database().driverName().indexOf("QMYSQL") > -1 ) { return MYSQL; }
    else if ( QSqlDatabase::database().driverName().indexOf("QPSQL") > -1 ) { return POSTGRES; }
    else if ( QSqlDatabase::database().driverName().indexOf("QSQLITE") > -1 ) { return SQLITE; }
    else return NINGUNA;
}

char basedatos::codigoControlador (QString controlador) {
    if ( controlador.indexOf("QMYSQL") > -1 ) { return MYSQL; }
    else if ( controlador.indexOf("QPSQL") > -1 ) { return POSTGRES; }
    else if ( controlador.indexOf("QSQLITE") > -1 ) { return SQLITE; }
    else return NINGUNA;
}

// Devuelve el nombre del controlador
QString basedatos::controlador () {
    return QSqlDatabase::database().driverName();
}

// Devuelve el nombre de la base de datos
QString basedatos::nombre () {
    return QSqlDatabase::database().databaseName();
}

// Devuelve el nombre del usuario
QString basedatos::usuario () {
    return QSqlDatabase::database().userName();
}

// Devuelve la clave
QString basedatos::clave () {
    return QSqlDatabase::database().password();
}

// Devuelve el host de conexión
QString basedatos::host () {
    return QSqlDatabase::database().hostName();
}

// Devuelve el puerto de conexión
int basedatos::puerto() {
    return QSqlDatabase::database().port();
}

// Versión de la base de datos
QString basedatos::selectVersion() {
    QString laversion;
    if (cualControlador() == SQLITE) {
        laversion = "SQLite ";
        QSqlQuery query = ejecutar("SELECT sqlite_version()");
        if ( (query.isActive()) && (query.first()) ) {
            laversion += query.value(0).toString();
        }
    }
    else {
        QSqlQuery query = ejecutar("select version()");
        if ( (query.isActive()) && (query.first()) ) {
            if (cualControlador() == MYSQL) {
                laversion = "MySQL ";
            }
            laversion += query.value(0).toString();
        }
    }
    return laversion;
}

// Crea una nueva base de datos
bool basedatos::crearBase (QString controlador, QString nombre, QString usuario, QString clave,
                           QString host, QString puerto, QString segunda) {
    bool segundadb=false;
    if (!segunda.isEmpty()) segundadb=true;
    QSqlDatabase database;
    if (segundadb) database = QSqlDatabase::addDatabase( controlador,segunda );
       else database = QSqlDatabase::addDatabase( controlador );
    if ( controlador.contains("SQLITE") )
    {
        // intentamos abrir la base de datos
        // si no existe se crea sola
        // habría que comprobar la existencia de la misma a través del fichero que la representa
        // tanto si se crea o no, se debe de salir de la función, ya que el resto
        // sólo sirve para mysql o postgres
        QString nombrebd;
        if ( database.isDriverAvailable (controlador) )
        {
            // construimos la ruta completa del nombre de la base de datos
            // basada en el fichero que se guardará en el directorio 'keme'
            nombrebd = dirtrabajobd();
            nombrebd.append(QDir::separator());
            nombrebd.append(nombre);
            QFile fichero( adapta(nombrebd) );
            if (fichero.exists())
            {
                QMessageBox::warning( 0, QObject::tr("Crear base de datos"),
                QObject::tr("ERROR; LA BASE DE DATOS ")+nombre+" YA EXISTE");
                return false;
            }
        }
        else
        {
            QMessageBox::warning( 0, QObject::tr("Apertura de base de datos"),
            QObject::tr("IMPOSIBLE OPERAR BASE DE DATOS CON ")+controlador);
	        return false;
        }
        database.setDatabaseName( adapta(nombrebd) );
         // QMessageBox::information( 0, tr("Apertura de base de datos"),nombrebd);
        if (!database.open())
        {
            QMessageBox::warning( 0, QObject::tr("Apertura de base de datos"),
            QObject::tr("No se puede abrir la base de datos creada"));
            return false;
        }
        else
	    {
	        // llamanos a crear la estructura de la base de datos
	        /**///crearTablas();
	        /**///importaplan();
	        return true;
	    }
        // final del procesado para sqlite, ahora sigue para mysql y postgres
    }

    if ( database.isDriverAvailable (controlador) )
    {
        if (controlador=="QPSQL7"
        || controlador=="QPSQL") database.setDatabaseName("template1");
        else if (controlador=="QMYSQL3"
        || controlador=="QMYSQL") database.setDatabaseName("mysql");
        else return false;

        database.setUserName( usuario );
        database.setPassword( clave );
        database.setHostName( host );

        if (puerto.length()>0)
        {
            database.setPort(puerto.toInt());
        }
        if ( !database.open() ) 
        {
            QMessageBox::warning( 0, QObject::tr("Apertura de base de datos"),
            QObject::tr("IMPOSIBLE ABRIR BASE DE DATOS ")+database.databaseName());
            return false;
        }
        // La base de datos se ha abierto, ya podemos executar comandos
        // Se crea la base de datos
        if (crearDatabase(controlador,nombre, segunda)) {
            database.close();

            database.setDatabaseName( nombre );
            database.setUserName( usuario );
            database.setPassword( clave );
            database.setHostName( host );

            if (puerto.length()>0)
            {
                database.setPort(puerto.toInt());
            }

            if (!database.open())
            {
                QMessageBox::warning( 0, QObject::tr("Apertura de base de datos"),
                QObject::tr("No se puede abrir la base de datos creada"));
                return false;
            }
            else
	        {
	            return true;
	        }
        }
        else
        { 
        QMessageBox::warning( 0, QObject::tr("Creación de base de datos"),
        QObject::tr("No se puede crear la base de datos porque ya existe una con el nombre \"%1\"").arg(nombre) );
        return false;
        }
    }
    else
    {
        QMessageBox::warning( 0, QObject::tr("Apertura de base de datos"),
        QObject::tr("IMPOSIBLE OPERAR BASE DE DATOS CON ")+controlador);
	    return false;
    }
}

// Abre base de datos
bool basedatos::abrirBase (QString controlador, QString nombre, QString usuario, QString clave, QString host, QString puerto) {
    QSqlDatabase database = QSqlDatabase::addDatabase( controlador );
    if ( database.isDriverAvailable (controlador) ) {
        // Abre la base de datos para el controlador sqlite
        if (codigoControlador(controlador) == SQLITE) {
            database.setDatabaseName(nombre);
        }
        // Abre la base de datos para el controlador mysql y postgres
        else if ( (codigoControlador(controlador) == MYSQL) ||
                   (codigoControlador(controlador) == POSTGRES) ) {
            if (!nombre.isEmpty())  database.setDatabaseName( nombre );
            if (!usuario.isEmpty()) database.setUserName( usuario );
            if (!clave.isEmpty())   database.setPassword( clave );
            if (!host.isEmpty())    database.setHostName( host );
            if (!puerto.isEmpty())  database.setPort(puerto.toInt());
        }

        if (!abierta()) { return false; }
        // La base de datos se ha abierto, ya podemos executar comandos
    }
    else {
        QMessageBox::warning( 0 , QObject::tr("Apertura de base de datos"),
        QObject::tr("IMPOSIBLE CARGAR BASE DE DATOS CON ")+controlador );
		return false;
    }

    return comprobarVersion();
}

// abre base datos sin comprobar versión
bool basedatos::abrirBase_s (QString controlador, QString nombre, QString usuario, QString clave, QString host, QString puerto) {
    QSqlDatabase database = QSqlDatabase::addDatabase( controlador );
    if ( database.isDriverAvailable (controlador) ) {
        // Abre la base de datos para el controlador sqlite
        if (codigoControlador(controlador) == SQLITE) {
            database.setDatabaseName(nombre);
        }
        // Abre la base de datos para el controlador mysql y postgres
        else if ( (codigoControlador(controlador) == MYSQL) ||
                   (codigoControlador(controlador) == POSTGRES) ) {
            if (!nombre.isEmpty())  database.setDatabaseName( nombre );
            if (!usuario.isEmpty()) database.setUserName( usuario );
            if (!clave.isEmpty())   database.setPassword( clave );
            if (!host.isEmpty())    database.setHostName( host );
            if (!puerto.isEmpty())  database.setPort(puerto.toInt());
        }

        if (!abierta()) { return false; }
        // La base de datos se ha abierto, ya podemos executar comandos
    }
    else {
        QMessageBox::warning( 0 , QObject::tr("Apertura de base de datos"),
        QObject::tr("IMPOSIBLE CARGAR BASE DE DATOS CON ")+controlador );
		return false;
    }

    return true;
}

// Cierra la base de datos
void basedatos::cerrar () {

   QSqlDatabase bd = QSqlDatabase::database();
   bd.close(); //linea nueva

}

// Crea solo las tablas
void basedatos::solotablas(bool segunda, QString qbase)
{
    QString cadena;

    QProgressDialog progreso(QObject::tr("Creando tablas ..."), 0, 0, 30);
    progreso.setWindowModality(Qt::WindowModal);

    // amortcontable
    cadena = "CREATE TABLE amortcontable ("
             "ejercicio     varchar(40),"
             "asiento       bigint default 0,"
             "as_enero       bigint default 0,"
             "as_febrero     bigint default 0,"
             "as_marzo       bigint default 0,"
             "as_abril       bigint default 0,"
             "as_mayo        bigint default 0,"
             "as_junio       bigint default 0,"
             "as_julio       bigint default 0,"
             "as_agosto      bigint default 0,"
             "as_septiembre  bigint default 0,"
             "as_octubre     bigint default 0,"
             "as_noviembre   bigint default 0,"
             "as_diciembre   bigint default 0,"
             "PRIMARY KEY (ejercicio) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // amortfiscalycontable
    cadena = "CREATE TABLE amortfiscalycontable ("
             "ejercicio         varchar(40),"
             "mes               int default 0, "
             "cuenta_activo     varchar(40),"
             "codigo_activo     varchar(40),"
             "cuenta_am_acum    varchar(40),"
             "cuenta_gasto      varchar(40),"
             "dotacion_contable numeric(14,2),"
             "dotacion_fiscal   numeric(14,2),"
             "PRIMARY KEY (ejercicio, cuenta_activo, mes) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // borrador
    cadena = "CREATE TABLE borrador ("
             "asiento       bigint,"
             "usuario       varchar(80),"
             "cuenta        varchar(40),"
             "concepto      varchar(255),"
             "debe          varchar(40),"
             "haber         varchar(40),"
             "documento     varchar(255),"
             "ctabaseiva    varchar(40),"
             "baseiva       varchar(40),"
             "claveiva      varchar(40),"
             "tipoiva       varchar(40),"
             "tipore        varchar(40),"
             "cuotaiva      varchar(40),"
             "cuentafra     varchar(40),"
             "diafra        varchar(40),"
             "mesfra        varchar(40),"
             "anyofra       varchar(40),"
             "soportado     varchar(40),"
             "ci            varchar(40),"
             "prorrata      varchar(40) default '',"
             "rectificativa varchar(40) default '',"
             "autofactura   varchar(40) default '',"
             "fecha_operacion varchar(40) default '',"
             "clave_operacion varchar(40) default '',"
             "biacoste        varchar(40) default '',"
             "rectificada     varchar(40) default '',"
             "nfacturas       varchar(40) default '',"
             "finicial        varchar(40) default '',"
             "ffinal          varchar(40) default '',"
             "pr_serv_ue      varchar(40) default '',"
             "autofactura_noue varchar(40) default '',"
             "bien_inversion   varchar(40) default '',"
             "sujeto           varchar(40) default '',"
             "afecto           varchar(40) default '',"
             "col32            varchar(40) default '',"
             "col33            varchar(255) default '',"
             "col34            varchar(40) default '',"
             "col35            varchar(40) default '',"
             "col36            varchar(40) default '',"
             "col37            varchar(40) default '',"
             "col38            varchar(40) default '',"
             "col39            varchar(40) default '',"
             "col40            varchar(40) default '',"
             "col41            varchar(40) default '',"
             "col42            varchar(40) default '',"
             "col43            varchar(255) default '',"
             "col44            varchar(40) default '',"
             "col45            varchar(255) default '',"
             "col46            varchar(40) default '',"
             "col47            varchar(40) default '',"
             "col48            varchar(40) default '',"
             "col49            varchar(40) default '',"
             "col50            varchar(40) default '',"
             "fecha            date"
             ")"; // no primary key
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // cab_as_modelo
    cadena = "CREATE TABLE cab_as_modelo ("
             "asientomodelo         varchar(255),"
             "fecha                 varchar(255),"
             "aib                   bool,"
             "autofactura_ue        bool,"
             "autofactura_no_ue     bool,"
             "eib                   bool,"
             "eib_servicios         bool,";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                 cadena += "isp_op_interiores bool default 0,";
                else cadena += "isp_op_interiores bool default false,";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                 cadena += "importacion bool default 0,";
                else cadena += "importacion bool default false,";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                cadena += "exportacion bool default 0,";
               else cadena += "exportacion bool default false,";
     cadena+="diario                varchar(255) default '',"
             "tipo                  varchar(80) default '',"
             "PRIMARY KEY (asientomodelo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // cabeceraestados
    cadena = "CREATE TABLE cabeceraestados ("
             "titulo                  varchar(255),"
             "cabecera                varchar(255),"
             "parte1                  varchar(255),"
             "parte2                  varchar(255),"
             "observaciones           varchar(255),"
             "formulabasepor          varchar(255),"
             "fechacalculo            date,"
             "horacalculo             varchar(10),"
             "ejercicio1              varchar(255),"
             "ejercicio2              varchar(255),"
             "analitica               bool,"
             "grafico                 bool,"
             "haycolref               bool,"
             "desglose_ctas           bool,"
             "colref                  varchar(80),"
             "ci                      varchar(80),"
             "desglose                bool,"
             "des_cabecera            varchar(255),"
             "des_pie                 varchar(255),"
             "diarios                 varchar(255),"
             "estadosmedios           bool,"
             "valorbasepor1           numeric(14,2),"
             "valorbasepor2           numeric(14,2),"
             "PRIMARY KEY (titulo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // conceptos
    cadena = "CREATE TABLE conceptos ("
             "clave        varchar(10),"
             "descripcion  varchar(255),"
             "PRIMARY KEY (clave) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // configuracion
    cadena = "CREATE TABLE configuracion ("
             "empresa        varchar(255),"
             "nif            varchar(255),"
             "domicilio      varchar(255),"
             "poblacion      varchar(255),"
             "cpostal        varchar(255),"
             "provincia      varchar(255),"
             "email          varchar(255),"
             "web            varchar(255),"
             "anchocuentas   integer,"
             "cuenta_iva_soportado      varchar(255),"
             "cuenta_iva_repercutido    varchar(255),"
             "cuenta_ret_irpf   varchar(255),"
             "cuenta_ret_ing    varchar(255),"
             "tipo_ret_irpf     numeric(7,4) default 0,"
             "cuentasapagar     varchar(255),"
             "cuentasacobrar    varchar(255),"
             "cuenta_tesoreria  varchar(40),"
             "cuenta_gasto_vto  varchar(40),"
             "clave_iva_defecto varchar(255),"
             "clave_gastos      varchar(255),"
             "clave_ingresos    varchar(255),"
             "cuentas_aa        varchar(255),"
             "cuentas_ag        varchar(255),";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) cadena += "amoinv bool default 0,";
             else cadena += "amoinv bool default false,";
             cadena+="cuenta_pyg        varchar(255),"
             "prox_pase         bigint,"
             "prox_vencimiento  bigint,"
             "prox_domiciliacion  bigint default 1,"
             "id_registral      varchar(255),"
             "prorrata_iva      numeric(5,2) default 0,";
             // "prox_asiento      bigint," eliminado
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) cadena += "prorrata_especial bool default 0,";
    else cadena += "prorrata_especial bool default false,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) cadena += "analitica_tabla bool default 0,";
    else cadena += "analitica_tabla bool default false,";
    // cadena += "numrel       bool,"
    cadena += "igic         bool,"
              "analitica    bool,"
              "analitica_parc    bool,"
              "ctas_analitica    text,"
              "notas             text,"
              "version      varchar(40),"
              "fecha_constitucion     date,"
              "objeto       varchar(255) default '',"
              "actividad     varchar(255) default '',"
              "cod_ine_plaza varchar(40) default '',"
              "provincia_def  varchar(255) default '',"
              "prox_documento bigint default 1,"
              "prox_num_ci bigint default 1,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) cadena += "gestion_usuarios bool default 0,";
      else cadena += "gestion_usuarios bool default false,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "sec_recibidas bool default 0,";
         else cadena += "sec_recibidas bool default false,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "caja_iva bool default 0,";
          else cadena += "caja_iva bool default false,";
    cadena+="bloqsaldosmedios bool,";

    cadena+="cabecera347 varchar(255),";
    cadena+="cuerpo347 text,";
    cadena+="desglose347 bool,";
    cadena+="cuerpo2_347 text,";
    cadena+="pie347 text,";
    cadena+="saludo347 text,";
    cadena+="firma347 varchar(255),";

    cadena+="imagen text )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // datossubcuenta
    cadena = "CREATE TABLE datossubcuenta ("
             "cuenta         varchar(40),"
             "razon          varchar(80),"
             "nombrecomercial varchar(80),"
             "cif            varchar(80),"
             "nifrprlegal    varchar(80),"
             "web            varchar(80),"
             "claveidfiscal  varchar(80),"
             "domicilio      varchar(255),"
             "poblacion      varchar(80),"
             "codigopostal   varchar(40),"
             "provincia      varchar(80),"
             "pais           varchar(80),"
             "tfno           varchar(80),"
             "fax            varchar(80),"
             "email          varchar(80),"
             "ccc            varchar(80),"
             "cuota          varchar(40),"
             "observaciones  varchar(255),"
             "pais_dat       varchar(80),"
             "tipo_ret       numeric(5,2) default 0,"
             "tipo_operacion_ret varchar(80),";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
         cadena += "ret_arrendamiento bool default 0,"; // retención de arrendamiento
       else cadena += "ret_arrendamiento bool default false,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) cadena += "venciasoc bool default 0,"; // vencimiento asociado
       else cadena += "venciasoc bool default false,";
     cadena+="codvenci       varchar(40),"
             "vto_dia_fijo   int default 0, "
             "tesoreria      varchar(40),";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) cadena += "ivaasoc bool default 0,"; // vencimiento asociado
       else cadena += "ivaasoc bool default false,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) cadena += "procesavenci bool default 0,"; // procesa vencimiento
       else cadena += "procesavenci bool default false,";
     cadena+="cuentaiva      varchar(40),"
             "tipoiva        varchar(20),"
             "conceptodiario varchar(155),"
             "cuenta_ret_asig varchar(40) default '',";
     if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
             cadena += "es_ret_asig_arrend bool default 0,"; // vencimiento asociado
        else cadena += "es_ret_asig_arrend bool default false,";
     cadena+="tipo_ret_asig numeric(5,2) default 0,"
             "tipo_oper_ret_asig varchar(80),";
     if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
             cadena += "dom_bancaria bool default 0,"; // domiciliable
        else cadena += "dom_bancaria bool default false,";
     cadena+="IBAN     varchar(80),";
     cadena+="BIC     varchar(80),";
     cadena+="nriesgo  varchar(80),";
     cadena+="cedente1 varchar(80),";
     cadena+="cedente2 varchar(80),";
     cadena+="sufijo   varchar(80),";
     cadena+="ref_mandato varchar(80),";
     cadena+="firma_mandato date,";
     if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
             cadena += "caja_iva bool default 0,"; // criterio de caja en IVA
        else cadena += "caja_iva bool default false,";

     cadena+="imagen         text,"
             "PRIMARY KEY (cuenta) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();

    // codigopais
    cadena = "CREATE TABLE codigopais ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // claveidpais
    cadena = "CREATE TABLE claveidfiscal ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // ci
    cadena = "CREATE TABLE ci ("
             "codigo         varchar(40),"
             "descripcion    varchar(80),"
             "nivel          integer,"
             "PRIMARY KEY (codigo, nivel) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // det_as_modelo
    cadena = "CREATE TABLE det_as_modelo ("
             "asientomodelo     varchar(255),"
             "cuenta            varchar(255),"
             "concepto          varchar(255),"
             "expresion         varchar(255),"
             "d_h               varchar(255),"
             "ci                varchar(255),"
             "baseiva           varchar(255),"
             "cuentafra         varchar(255),"
             "cuentabaseiva     varchar(255),"
             "claveiva          varchar(255),"
             "documento         varchar(255),"
             "fecha_factura     varchar(255) default '',"
             "prorrata_e        varchar(255) default '',";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                 cadena += "rectificativa bool default 0,";
                else cadena += "rectificativa bool default false,";

             /*if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) cadena += "autofactura bool default 0,";
                else cadena += "autofactura bool default false,";*/

             cadena+="fecha_op     varchar(255) default '',"
             "clave_op     varchar(255) default '',"
             "bicoste      varchar(255) default '',"
             "rectificada  varchar(255) default '',"
             "numfacts     varchar(255) default '',"
             "facini       varchar(255) default '',"
             "facfinal     varchar(255) default '',"
             "afecto       varchar(255) default '',";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
               cadena += "bien_inversion        bool default 0,";
              else
                  cadena += "bien_inversion     bool default false,";
              if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                cadena += "agrario        bool default 0,";
               else
                   cadena += "agrario     bool default false,";
              cadena+="nombre       varchar(255) default '',";
              cadena+="cif       varchar(255) default '',";
              cadena+="cta_retenido varchar(255) default '',";
              if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                  cadena += "arrendamiento bool default 0,";
                 else cadena += "arrendamiento bool default false,";
              cadena+="clave       varchar(255) default '',";
              cadena+="base_ret    varchar(255) default '',";
              cadena+="tipo_ret    varchar(255) default '',";
              cadena+="retencion   varchar(255) default '',";
              cadena+="ing_a_cta   varchar(255) default '',";
              cadena+="ing_a_cta_rep varchar(255) default '',";
              cadena+="nombre_ret    varchar(255) default '',";
              cadena+="cif_ret      varchar(255) default '',";
              cadena+="provincia    varchar(255) default '',";
              cadena+="orden        integer )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // diario
    cadena = "CREATE TABLE diario ("
             "asiento       bigint,"
             "pase          bigint,"
             "fecha         date,"
             "cuenta        varchar(40),"
             "debe          numeric(14,2),"
             "haber         numeric(14,2),"
             "concepto      varchar(255),"
             "documento     varchar(255),"
             "diario        varchar(40),"
             "ci            varchar(40),"
             "usuario       varchar(80),";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "conciliado       bool default 0,";
    else
        cadena += "conciliado       bool default false,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "enlace       bool default 0,";
    else
        cadena += "enlace       bool default false,";
    cadena += "dif_conciliacion varchar(40) default '',"
              "copia_doc        varchar(255) default '',"
              "clave_ci         bigint default 0,"
              "ejercicio        varchar(80),"
              "apunte_origen    bigint,"
              "codfactura       varchar(80) default '',"
              "nrecepcion       bigint default 0,";
              if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                  cadena += "revisado       bool default 0,";
              else
                  cadena += "revisado       bool default false,";
              cadena+="codigo_var_evpn_pymes varchar(160) default '',";
              cadena+="fecha_factura        date,"
              "PRIMARY KEY (pase) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();

    cadena = "create index cuenta on diario (cuenta)";
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // dif_conciliacion
    cadena = "CREATE TABLE dif_conciliacion ("
             "codigo        varchar(40),"
             "descripcion   varchar(80) default '',"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // ejercicios
    cadena = "CREATE TABLE ejercicios ("
             "codigo        varchar(40),"
             "apertura      date,"
             "cierre        date,"
             "cerrado       bool,"
             "cerrando      bool,"
             "prox_asiento  bigint default 2,"
             "prox_nrecepcion bigint default 1,";

    // "primerasiento bigint default 0," // a eliminar
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                 cadena += "primero_apertura bool default 1,";
             else
                 cadena += "primero_apertura bool default true,";
             cadena+="base_ejercicio varchar(80) default '',"
             "base_presupuesto varchar(80) default '',";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "presupuesto_base_cero bool default 1,";
    else
        cadena += "presupuesto_base_cero bool default true,";
    cadena += "incremento numeric(14,4) default 0,"
              "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // estados
    cadena = "CREATE TABLE estados ("
             "titulo      varchar(166),"
             "apartado    varchar(255),"
             "parte1      bool,"
             "clave       varchar(255),"
             "nodo        varchar(166),"
             "formula     varchar(2000),"
             "referencia  varchar(80),"
             "importe1    numeric(14,2),"
             "importe2    numeric(14,2),"
             "m1         numeric(14,2),"
             "m2         numeric(14,2),"
             "m3         numeric(14,2),"
             "m4         numeric(14,2),"
             "m5         numeric(14,2),"
             "m6         numeric(14,2),"
             "m7         numeric(14,2),"
             "m8         numeric(14,2),"
             "m9         numeric(14,2),"
             "m10         numeric(14,2),"
             "m11         numeric(14,2),"
             "m12         numeric(14,2),"
             "calculado   bool,"
             "PRIMARY KEY (titulo, nodo, parte1) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // libroiva
    cadena = "CREATE TABLE libroiva ("
             "pase          bigint,"
             "cta_base_iva  varchar(40),"
             "base_iva      numeric(14,2),"
             "clave_iva     varchar(40),"
             "tipo_iva      numeric(5,2),"
             "tipo_re       numeric(5,2),"
             "cuota_iva     numeric(14,2),"
             "cuenta_fra    varchar(40),"
             "fecha_fra     date,"
             "soportado     bool,"
             "prorrata      numeric(5,2) default 1,"
             "afecto        numeric(7,4) default 1,"
             "aib           bool,"
             "eib           bool,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "rectificativa        bool default 0,"
                  "autofactura          bool default 0,"
                  "agrario              bool default 0,";
    }
    else {
        cadena += "rectificativa    bool default false,"
                  "autofactura      bool default false,"
                  "agrario          bool default false,";
    }
    cadena+="fecha_operacion     date,";
    cadena+="clave_operacion     varchar(40),";
    cadena+="bi_costo            numeric(14,2),";
    cadena+="rectificada         varchar(80) default '',";
    cadena+="nfacturas           int default 0,";
    cadena+="finicial            varchar(80) default '',";
    cadena+="ffinal              varchar(80) default '',";
    cadena+="nombre              varchar(80) default '',";
    cadena+="cif                 varchar(80) default '',";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "autofactura_no_ue        bool default 0,"
                  "pr_servicios_ue          bool default 0,";
    }
    else {
        cadena += "autofactura_no_ue    bool default false,"
                  "pr_servicios_ue      bool default false,";
    }
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "bien_inversion        bool default 0,";
    else
        cadena += "bien_inversion        bool default false,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "op_no_sujeta          bool default 0,";
    else
        cadena += "op_no_sujeta          bool default false,";

    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "exento_dcho_ded       bool default 1,";
    else
        cadena += "exento_dcho_ded       bool default true,";

    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "isp_op_interiores     bool default 0,";
    else
        cadena += "isp_op_interiores     bool default false,";

    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "importacion           bool default 0,";
    else
        cadena += "importacion           bool default false,";

    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "exportacion           bool default 0,";
    else
        cadena += "exportacion           bool default false,";

    // ----------------------------------------------------------------------
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "caja_iva              bool default 0,";
    else
        cadena += "caja_iva              bool default false,";

    cadena+="apunte_tot_factura          bigint default 0,";
    cadena+="total_factura            numeric(14,2),";

    // -------------------------------------------------------------------------------------
    cadena+="PRIMARY KEY (pase) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // planamortizaciones
    cadena = "CREATE TABLE planamortizaciones ("
             "cuenta_activo         varchar(40),"
             "fecha_func            date,"
             "codigo_activo         varchar(40),"
             "cuenta_am_acum        varchar(40),"
             "cuenta_gasto          varchar(40),"
             "valor_residual        numeric(14,2),"
             "amort_personalizada   bool,"
             "coef_amort_contable   float8,"
             "coef_amort_fiscal     float8,"
             "amort_fiscal_1ejer    bool,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "baja           bool default 0,";
    else
        cadena += "baja           bool default false,";
    cadena+= "fecha_baja            date,";
    cadena+= "motivo_baja        varchar(255),";
    cadena+= "cuenta_proveedor   varchar(40),";

    cadena+= "PRIMARY KEY (cuenta_activo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // amortpers
    cadena = "CREATE TABLE amortpers ("
             "cuenta     varchar(40),"
             "ejercicio  varchar(40),"
             "importe    numeric(14,2),"
             "PRIMARY KEY (cuenta, ejercicio) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // plancontable
    cadena = "CREATE TABLE plancontable ("
             "codigo       varchar(40),"
             "descripcion  varchar(255),";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "auxiliar     bool default 0,";
    }
    else {
        cadena += "auxiliar     bool default false,";
    }
    cadena += "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // nuevoplan
    cadena = "CREATE TABLE nuevoplan ("
             "codigo       varchar(40),"
             "descripcion  varchar(255),";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "auxiliar     bool default 0,";
    }
    else {
        cadena += "auxiliar     bool default false,";
    }
    cadena += "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // equivalenciasplan
    cadena = "CREATE TABLE equivalenciasplan ("
             "cod       varchar(40),"
             "equiv     varchar(40),"
             "PRIMARY KEY (cod) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // saldossubcuenta
    cadena = "CREATE TABLE saldossubcuenta ("
             "codigo       varchar(40),"
             "saldo        numeric(14,2),"
             "saldomedio1  numeric(14,2),"
             "saldomedio2  numeric(14,2),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // tiposiva
    cadena = "CREATE TABLE tiposiva ("
             "clave        varchar(4),"
             "tipo         numeric(5,2),"
             "re           numeric(5,2),"
             "descripcion  varchar(80),"
             "cuenta       varchar(80),"
             "cuenta_sop   varchar(80),"
             "PRIMARY KEY (clave) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // var_as_modelo
    cadena = "CREATE TABLE var_as_modelo ("
             "asientomodelo         varchar(166),"
             "variable              varchar(166),"
             "tipo                  varchar(255),"
             "descripcion           varchar(255),"
             "valor                 varchar(160) default '',"
             "orden                 integer,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "guardar bool default 0,";
    }
    else {
        cadena += "guardar bool default false,";
    }
    cadena+="PRIMARY KEY (asientomodelo, variable) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // vencimientos
    cadena = "CREATE TABLE vencimientos ("
             "num                     bigint,"
             "cta_ordenante           varchar(40),"
             "fecha_operacion         date,"
             "importe                 numeric(14,2),"
             "cuenta_tesoreria        varchar(40),"
             "fecha_vencimiento       date,"
             "derecho                 bool,"
             "pendiente               bool,"
             "anulado                 bool,"
             "pase_diario_operacion   bigint,"
             "pase_diario_vencimiento bigint,"
             "concepto                varchar(160),"
             "usuario                 varchar(80),"
             "fecha_realizacion       date,"
             "medio_realizacion       varchar(255) default '',"
             "cuenta_bancaria         varchar(255) default '',"
             "domiciliacion           bigint default 0,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "impagado bool default 0,";
    }
    else {
        cadena += "impagado bool default false,";
    }
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "domiciliable bool default 0,";
    }
    else {
        cadena += "domiciliable bool default false,";
    }
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "forzar_liq_iva bool default 0,";
    }
    else {
        cadena += "forzar_liq_iva bool default false,";
    }
    cadena+="liq_iva_fecha       date,";

    cadena+=  "PRIMARY KEY (num) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();

    // domiciliaciones
    cadena = "CREATE TABLE domiciliaciones ("
             "id_remesa    bigint,"
             "aux_banco    varchar(80),"  // Cuenta auxiliar correspondiente al banco
             "descrip      varchar(255)," // Campo con descripción de la remesa
             "ccc          varchar(80),"  // Código cuenta cliente
             "iban         varchar(80),"  // IBAN
             "sufijo       varchar(80),"  // Campo sufijo
             "cedente1     varchar(80),"  // Campo cedente1
             "cedente2     varchar(80),"  // Campo cedente2
             "riesgo       varchar(80),"  //  Número de riesgo
             "fecha_conf   date       ,"  // Fecha de confección de la remesa
             "fecha_cargo  date       ,";  // Fecha de cargo
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                 cadena += "emitido19 bool default 0,";
             else
                 cadena += "emitido19 bool default false,";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                 cadena += "emitido58 bool default 0,";
             else
                 cadena += "emitido58 bool default false,";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                 cadena += "emitidosepa1914 bool default 0,";
             else
                 cadena += "emitidosepa1914 bool default false,";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
                 cadena += "procesada bool default 0,";
             else
                 cadena += "procesada bool default false,";

             cadena+=  "PRIMARY KEY (id_remesa) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


    // diarios
    cadena = "CREATE TABLE diarios ("
             "codigo       varchar(40),"
             "descripcion  varchar(255),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // presupuesto
    cadena = "CREATE TABLE presupuesto ("
             "ejercicio         varchar(40),"
             "cuenta            varchar(40),"
             "ejerciciobase     varchar(40) default '',"
             "presupuestobase   varchar(40) default '',"
             "base              numeric(14,2) default 0,"
             "base1             numeric(14,2) default 0,"
             "base2             numeric(14,2) default 0,"
             "base3             numeric(14,2) default 0,"
             "base4             numeric(14,2) default 0,"
             "base5             numeric(14,2) default 0,"
             "base6             numeric(14,2) default 0,"
             "base7             numeric(14,2) default 0,"
             "base8             numeric(14,2) default 0,"
             "base9             numeric(14,2) default 0,"
             "base10            numeric(14,2) default 0,"
             "base11            numeric(14,2) default 0,"
             "base12            numeric(14,2) default 0,"
             "base13            numeric(14,2) default 0,"
             "base14            numeric(14,2) default 0,"
             "base15            numeric(14,2) default 0,"
             "presupuesto       numeric(14,2) default 0,"
             "presupuesto1      numeric(14,2) default 0,"
             "presupuesto2      numeric(14,2) default 0,"
             "presupuesto3      numeric(14,2) default 0,"
             "presupuesto4      numeric(14,2) default 0,"
             "presupuesto5      numeric(14,2) default 0,"
             "presupuesto6      numeric(14,2) default 0,"
             "presupuesto7      numeric(14,2) default 0,"
             "presupuesto8      numeric(14,2) default 0,"
             "presupuesto9      numeric(14,2) default 0,"
             "presupuesto10      numeric(14,2) default 0,"
             "presupuesto11      numeric(14,2) default 0,"
             "presupuesto12      numeric(14,2) default 0,"
             "presupuesto13      numeric(14,2) default 0,"
             "presupuesto14      numeric(14,2) default 0,"
             "presupuesto15      numeric(14,2)  default 0,"
             "PRIMARY KEY (ejercicio, cuenta) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // periodos
    cadena = "CREATE TABLE periodos ("
             "ejercicio        varchar(40),"
             "codigo           varchar(40),"
             "inicio           date,"
             "fin              date,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "deshabilitado bool default 0, ";
    }
    else {
        cadena += "deshabilitado bool default false, ";
    }
    cadena+="PRIMARY KEY (ejercicio, codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // ci_amort
    cadena = "CREATE TABLE ci_amort ("
             "cuenta        varchar(40),"
             "ci            varchar(40),"
             "asignacion    float8,"
             "PRIMARY KEY (cuenta, ci) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // ajustes_conci
    cadena = "create table ajustes_conci ("
             "cuenta varchar(40),"
             "fecha  date,"
             "debe   numeric(14,2),"
             "haber  numeric(14,2),"
             "concepto varchar(255),"
             "dif_conciliacion varchar(40) default '',";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "conciliado bool default 0)";
    }
    else {
        cadena += "conciliado bool default false)";
    }
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // cuenta_ext_conci
    cadena = "create table cuenta_ext_conci (";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) {
        cadena += "numero integer primary key autoincrement,";
    }
    else {
        cadena += "numero   serial,";
    }
    cadena += "cuenta   varchar(40),"
              "fecha    date,"
              "debe     numeric(14,2),"
              "haber    numeric(14,2),"
              "saldo    numeric(14,2),"
              "concepto varchar(255),"
              "dif_conciliacion varchar(40) default '',";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "conciliado bool default 0)";
    }
    else {
        cadena += "conciliado bool default false,"
                  "PRIMARY KEY (numero) )";
    }
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();

    // cab_regulariz
    cadena = "CREATE TABLE cab_regulariz ("
             "codigo        varchar(40),"
             "descripcion   varchar(255),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // regul_sec
    cadena = "CREATE TABLE regul_sec ("
             "codigo            varchar(40),"
             "orden             int,"
             "descripcion       varchar(255),"
             "cuenta_deudora    varchar(40),"
             "cuenta_acreedora  varchar(40),"
             "cuentas           varchar(40),"
             "condicion         varchar(255),"
             "diario            varchar(80) default '',"
             "PRIMARY KEY (codigo,orden) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // tiposvenci
    cadena = "CREATE TABLE tiposvenci ("
             "codigo            varchar(40),"
             "descripcion       varchar(255),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // bloqueos_cta
    cadena = "CREATE TABLE bloqueos_cta ("
             "codigo        varchar(80),";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "enero bool default 0,";
        cadena += "febrero bool default 0,";
        cadena += "marzo bool default 0,";
        cadena += "abril bool default 0,";
        cadena += "mayo bool default 0,";
        cadena += "junio bool default 0,";
        cadena += "julio bool default 0,";
        cadena += "agosto bool default 0,";
        cadena += "septiembre bool default 0,";
        cadena += "octubre bool default 0,";
        cadena += "noviembre bool default 0,";
        cadena += "diciembre bool default 0,";
    }
    else {
        cadena += "enero bool default false,";
        cadena += "febrero bool default false,";
        cadena += "marzo bool default false,";
        cadena += "abril bool default false,";
        cadena += "mayo bool default false,";
        cadena += "junio bool default false,";
        cadena += "julio bool default false,";
        cadena += "agosto bool default false,";
        cadena += "septiembre bool default false,";
        cadena += "octubre bool default false,";
        cadena += "noviembre bool default false,";
        cadena += "diciembre bool default false,";
    }

    cadena+="PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


    // clave_op_expedidas
    cadena = "CREATE TABLE clave_op_expedidas ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // clave_op_recibidas
    cadena = "CREATE TABLE clave_op_recibidas ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // provincias
    cadena = "CREATE TABLE provincias ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // clave_op_retenciones
    cadena = "CREATE TABLE clave_op_retenciones ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);



    // usuarios
    cadena = "CREATE TABLE usuarios ("
             "codigo         varchar(40) default '',"
             "nombre         varchar(254) default '',"
             "clave          varchar(40) default '',"
             "nif            varchar(40) default '',"
             "domicilio      varchar(80) default '',"
             "poblacion      varchar(80) default '',"
             "codigopostal   varchar(40) default '',"
             "provincia      varchar(80) default '',"
             "pais           varchar(80) default '',"
             "tfno           varchar(80) default '',"
             "email          varchar(80) default '',"
             "observaciones  varchar(255) default '',"
             "privilegios    varchar(255) default '',"
             "imagen         text,"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    cadena="insert into usuarios (codigo, nombre, clave, privilegios) "
           "values ('admin', 'Administrador', 'admin',"
           "'111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111')";
          // 1234567890123456789012345678901234567890123456789012345678901234567890123
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // inputaciones automáticas para las cuentas auxiliares

    cadena = "CREATE TABLE input_cta (";
    cadena+="auxiliar    varchar(80),"
            "ci          varchar(40),"
          "imputacion  numeric(10,4),";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) {
    cadena += "registro integer primary key autoincrement";
    }
     else {
            cadena += "registro   serial";
          }
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) cadena+=")";
       else cadena+=", primary key (registro))";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);

    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


    // diario_ci
    cadena = "CREATE TABLE diario_ci ("
             "clave_ci       bigint,"
             "ci             varchar(80),"
             "importe        numeric(19,4),";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) {
    cadena += "registro integer primary key autoincrement";
    }
     else {
            cadena += "registro   serial";
          }
     if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) cadena+=")";
        else cadena+=", primary key (registro))";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);

    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    cadena="create index clave_ci_idx on diario_ci(clave_ci)";
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // series de facturas
    cadena = "CREATE TABLE series_fact ("
             "codigo  varchar(80),"
             "descrip varchar(254),"
             "proxnum bigint default 0,"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // tipos documentos a emitir
    cadena = "CREATE TABLE tipos_doc ("
             "codigo  varchar(80),"
             "descrip varchar(254) default '',"
             "serie   varchar(80) default '',"
             "pie1     varchar(254) default '',"
             "pie2     varchar(254) default '',"
             "moneda          varchar(10) default '',"
             "codigo_moneda   varchar(10) default '',";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
                 cadena += "contabilizable bool default 0,";
             }
             else {
                 cadena += "contabilizable bool default false,";
             }
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
                 cadena += "rectificativo bool default 0,";
             }
             else {
                 cadena += "rectificativo bool default false,";
             }
             cadena+="tipo_operacion int,"
             "cif_empresa varchar(254) default '', "
             "cif_cliente varchar(254) default '', "
             "documento   varchar(254) default '', "
             "cantidad    varchar(254) default '', "
             "referencia  varchar(254) default '', "
             "descripref  varchar(254) default '', "
             "precio      varchar(254) default '', "
             "totallin    varchar(254) default '', "
             "bi          varchar(254) default '', "
             "descuento   varchar(254) default '', "
             "tipoiva     varchar(254) default '', "
             "tipore      varchar(254) default '', "
             "cuota       varchar(254) default '', "
             "cuotare     varchar(254) default '', "
             "totalfac    varchar(254) default '', "
             "totallineas varchar(254) default '', "  // nuevo **************
             "lineas_doc  int, "                      // nuevo **************
             "notas       varchar(254) default '', "
             "venci       varchar(254) default '', "
             "numero      varchar(254) default '', "
             "fecha       varchar(254) default '', "
             "cliente     varchar(254) default '', "
             "nombre_emisor     varchar(254) default '', " // nuevo ***************
             "domicilio_emisor  varchar(254) default '', " // nuevo ***************
             "cp_emisor         varchar(254) default '', " // nuevo ***************
             "poblacion_emisor  varchar(254) default '', " // nuevo ***************
             "provincia_emisor  varchar(254) default '', " // nuevo ***************
             "pais_emisor       varchar(254) default '', " // nuevo ***************
             "cif_emisor        varchar(254) default '', " // nuevo ***************
             "id_registral      varchar(254) default '', " // nuevo ***************
             "notastex    text , ";
             cadena+="imagen         text,";
             cadena+="PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // referencias (facturación)
    cadena = "CREATE TABLE referencias ("
             "codigo  varchar(80),"
             "descrip varchar(254) default '',"
             "precio numeric(19,4) default 0,"
             "iva varchar(20) default '',"
             "ivare varchar(20) default '',"
             "cuenta varchar(80) default '',"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


    // facturas
    cadena = "create table facturas (";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) {
        cadena += "clave integer primary key autoincrement,";
    }
    else {
        cadena += "clave   serial,";
    }
    cadena += "serie   varchar(80),"
              "numero  bigint,"
              "cuenta  varchar(80),"
              "fecha     date,"
              "fecha_fac date,"
              "fecha_op  date,";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "contabilizado bool default 0,";
       }
    else {
          cadena += "contabilizado bool default false,";
         }
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "contabilizable bool default 0,";
       }
    else {
          cadena += "contabilizable bool default false,";
         }
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "con_ret bool default 0,";
       }
    else {
          cadena += "con_ret bool default false,";
         }
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "con_re bool default 0,";
       }
    else {
          cadena += "con_re bool default false,";
         }
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "cerrado bool default 0,";
       }
    else {
          cadena += "cerrado bool default false,";
         }
    cadena+="tipo_ret  numeric(5,2),"
            "retencion numeric(14,2), "
            "tipo_doc varchar(80), "
            "notas text, "
            "pie1 varchar(254),"
            "pie2 varchar(254),"
            "pase_diario_cta bigint";
    cadena+=")";

    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


    // línea de facturas
    cadena = "create table lin_fact ( ";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) {
        cadena += "linea integer primary key autoincrement,";
    }
    else {
        cadena += "linea   serial,"; }
    cadena += "clave bigint, "  // por este campo se relaciona con cabecera
              "codigo       varchar(80),"
              "descripcion  varchar(254),"
              "cantidad     numeric(19,3),"
              "precio       numeric(19,4),"
              "clave_iva    varchar(40),"
              "tipo_iva     numeric(8,4),"
              "tipo_re      numeric(8,4),"
              "dto          numeric(8,4)";
    if (( segunda ? cualControlador(qbase) : cualControlador())!=SQLITE)
        cadena+=",PRIMARY KEY (linea) )";
       else cadena+=")";

    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // tipos_automaticos
    cadena = "CREATE TABLE tipos_automaticos ("
             "codigo            varchar(40),"
             "descripcion       varchar(255),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) {
       cadena="insert into tipos_automaticos ( codigo,descripcion) "
             "values ('";
       cadena += tipo_asiento_general();
       cadena += "','";
       cadena += QObject::tr("TIPO GENERAL");
       cadena += "')";
       if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
      }

    // facturas predefinidas
    cadena = "create table facturas_predef (";
    cadena += "codigo  varchar(40),";
    cadena += "descrip  varchar(254),";
    cadena += "serie   varchar(80),";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "contabilizable bool default 0,";
       }
    else {
          cadena += "contabilizable bool default false,";
         }
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "con_ret bool default 0,";
       }
    else {
          cadena += "con_ret bool default false,";
         }
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "con_re bool default 0,";
       }
    else {
          cadena += "con_re bool default false,";
         }
    cadena+="tipo_ret  numeric(5,2),"
            "tipo_doc varchar(80), "
            "notas text, "
            "pie1 varchar(254),"
            "pie2 varchar(254), ";
    cadena+="primary key (codigo))";

    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


    // línea de facturas predefinidas
    cadena = "create table lin_fact_predef ( ";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) {
        cadena += "linea integer primary key autoincrement,";
    }
    else {
        cadena += "linea   serial,"; }
    cadena += "cod_fact     varchar(40), "  // por este campo se relaciona con cabecera
              "codigo       varchar(80),"
              "descripcion  varchar(254),"
              "cantidad     numeric(19,3),"
              "precio       numeric(19,4),"
              "clave_iva    varchar(40),"
              "tipo_iva     numeric(8,4),"
              "tipo_re      numeric(8,4),"
              "dto          numeric(8,4)";
    if (( segunda ? cualControlador(qbase) : cualControlador())!=SQLITE)
        cadena+=",PRIMARY KEY (linea) )";
       else cadena+=")";

    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);



    // lotes facturas predefinidas
    cadena = "create table lote_fact ( "
              "codigo       varchar(80),"
              "descripcion  varchar(255),";
              if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
                  cadena += "cuotas bool default 0,";
                 }
                 else {
                       cadena += "cuotas bool default false,";
                      }
    cadena += "cod_factura_predef  varchar(80), ";
    cadena += "presupuesto numeric(19,2) default 0, ";
    cadena+=" PRIMARY KEY (codigo) )";

    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);



    // línea de lotes facturas predefinidas
    cadena = "create table lin_lote_predef ( ";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) {
        cadena += "linea integer primary key autoincrement,";
    }
    else {
        cadena += "linea   serial,"; }
    cadena += "codigo       varchar(80), "  // por este campo se relaciona con cabecera
              "cuenta       varchar(80), "
              "importe      varchar(80), "
              "cuota        varchar(80) ";
    if (( segunda ? cualControlador(qbase) : cualControlador())!=SQLITE)
        cadena+=", PRIMARY KEY (linea) )";
       else cadena+=")";

    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);



    // retenciones
    cadena = "CREATE TABLE retenciones ("
             "pase          bigint,"
             "cta_retenido  varchar(80),";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
                 cadena += "arrendamiento        bool default 0,";
             }
             else {
                 cadena += "arrendamiento    bool default false,";
             }
             cadena+="clave_ret varchar(255),"

             "base_ret      numeric(14,2),"
             "tipo_ret      numeric(5,2),"
             "retencion     numeric(14,2),"
             "ing_cta       numeric(14,2),"
             "ing_cta_rep   numeric(14,2),"
             "nombre        varchar(255),"
             "cif           varchar(80),"
             "provincia     varchar(255),";
    cadena+="PRIMARY KEY (pase) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


    // apuntes borrados
    cadena = "CREATE TABLE apuntes_borrados ("
             "pase          bigint,"
             "fecha_reg     date, "
             "hora_reg      varchar(10),";
    cadena+="PRIMARY KEY (pase) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


    // empresas consolidables
    cadena = "CREATE TABLE consolidables (";
    cadena += "codigo         int, ";       //código de la empresa 1 al 9
    cadena += "nombrebd       varchar(80),"; //nombre base de datos
    cadena += "descrip        varchar(80),"; // descripción de la conexión
    cadena += "usuario        varchar(80),"; //usuario base de datos
    cadena += "clave          varchar(80),"; //clave conexión
    cadena += "host           varchar(160),"; //host donde se encuentra la base de datos
    cadena += "puerto         varchar(80),"; //puerto de la conexión
    cadena += "controlador    varchar(80),"; //controlador de la conexión
    cadena += "ultimo_apunte  bigint,";      // último apunte conexión origen
    cadena += "fecha_actu     date       ,";    // fecha última actualización
    cadena += "hora_actu      varchar(10),";  //hora última actualización hhmmss
    cadena += "import_tipos_diario  bool,";  // importa tipos de diario no estándar
    cadena+="PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // vencimientos asignados a cuentas (no auxiliares)
    cadena ="CREATE TABLE venci_cta (";
    cadena += "codigo varchar(40), ";
    cadena += "venci  varchar(40), ";
    cadena += "tesoreria  varchar(80) default '', ";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
        cadena += "procesavenci bool default 0,";
       }
    else {
          cadena += "procesavenci bool default false,";
         }
    cadena += "vto_dia_fijo  int default 0, ";
    cadena+="PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // iva, retenciones asignados a cuentas (no auxiliares)
    cadena ="CREATE TABLE iva_ret_cta (";
    cadena += "codigo varchar(40), ";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "ivaasoc bool default 0,"; // vencimiento asociado
     else cadena += "ivaasoc bool default false,";
    cadena+="cuentaiva      varchar(40),"
            "tipoiva        varchar(20),"
            "conceptodiario varchar(155),"
            "cuenta_ret_asig varchar(40) default '',";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
      cadena += "es_ret_asig_arrend bool default 0,"; // vencimiento asociado
     else cadena += "es_ret_asig_arrend bool default false,";
    cadena+="tipo_ret_asig numeric(5,2) default 0,"
            "tipo_oper_ret_asig varchar(80),";
    cadena+="PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // clave_concepto_evpn
    cadena = "CREATE TABLE concepto_evpn_pymes ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


    // clave_columnas_evpn
    cadena = "CREATE TABLE cols_evpn_pymes ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "cuentas    varchar(254),"
             "PRIMARY KEY (codigo) )";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


    // tabla amoinv
    cadena = "create table amoinv (";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) {
        cadena += "numero integer primary key autoincrement,";
    }
    else {
        cadena += "numero   serial,";
    }
    cadena += "cta_aa       varchar(40),"
              "cta_ag       varchar(40),"
              "concepto     varchar(255),"
              "fecha_inicio date,"
              "fecha_compra date,"
              "fecha_ult_amort date,"
              "coef         float8,"
              "valor_adq    numeric(14,2),"
              "valor_amortizado numeric(14,2),"
              "factura      varchar(255),"
              "proveedor    varchar(255))";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    cadena = "create table condiciones(";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) {
        cadena += "registro integer primary key autoincrement,";
    }
    else {
        cadena += "registro   serial,";
    }
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
        cadena += "mostrar bool default 0, "; // vencimiento asociado
       else cadena += "mostrar bool default false, ";
    cadena+= " mensaje text,"
             " fecha   date,"
             " hora    varchar(6),"
             " nombre varchar(255),"
             " cargo  varchar(255),"
             " organizacion varchar(255),";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
      cadena += "acepta bool default 0 ";
     else cadena += "acepta bool default false ";
    cadena+=");";
    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


}


// Crea claves foráneas
void basedatos::crearForaneas(bool segunda, QString qbase)
{
 if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) return; // SQLITE no soporta foráneas
 QString cadena;
 cadena="alter table amortcontable add foreign key(ejercicio) references ejercicios(codigo)";
 if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

 // ejecutar("alter table amortcontable add foreign key(asiento) references diario(asiento)");
 cadena="create index ac_ejercicio_idx on amortcontable(ejercicio)";
 if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

 //ejecutar("create index ac_asiento_idx on amortcontable(asiento)");

 cadena="alter table amortfiscalycontable add foreign key(ejercicio) references ejercicios(codigo)";
 if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

 cadena="alter table amortfiscalycontable add foreign key(cuenta_activo) references plancontable(codigo)";
 if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

 cadena="alter table amortfiscalycontable add foreign key(cuenta_am_acum) references plancontable(codigo)";
 if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

 cadena="alter table amortfiscalycontable add foreign key(cuenta_gasto) references plancontable(codigo)";
 if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

 cadena="create index afc_ejercicio_idx on amortfiscalycontable(ejercicio)";
 if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

 cadena="create index afc_cuenta_activo_idx on amortfiscalycontable(cuenta_activo)";
 if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

 cadena="create index afc_cuenta_am_acum_idx on amortfiscalycontable(cuenta_am_acum)";
 if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

 cadena="create index afc_cuenta_gasto_idx on amortfiscalycontable(cuenta_gasto)";
 if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


// suponemos que esta tabla va a contener pocos registros, por lo que no indexamos el campo cuenta
cadena="alter table borrador add foreign key(cuenta) references plancontable(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// cuenta es clave primaria, por lo que ya está indexada
cadena="alter table datossubcuenta add foreign key(cuenta) references plancontable(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// esta tabla tiene relativamente pocas filas, por lo que no indexamos
cadena="alter table det_as_modelo add foreign key(asientomodelo) references cab_as_modelo(asientomodelo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


// esta tabla tiene relativamente pocas filas, por lo que no indexamos
cadena="alter table var_as_modelo add foreign key(asientomodelo) references cab_as_modelo(asientomodelo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// el índice cuenta ya se ha creado
cadena="alter table diario add foreign key(cuenta) references plancontable(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// tabla con relativamente pocas filas; no indexamos
cadena="alter table estados add foreign key(titulo) references cabeceraestados(titulo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// pase forma parte la clave primaria, por lo que hay índice implícito
cadena="alter table libroiva add foreign key(pase) references diario(pase)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// cuenta_activo forma parte la clave primaria, por lo que hay índice implícito
cadena="alter table planamortizaciones add foreign key(cuenta_activo) references plancontable(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="alter table planamortizaciones add foreign key(cuenta_gasto) references plancontable(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="create index plam_cuenta_gasto_idx on planamortizaciones(cuenta_gasto)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// tabla con relativamente pocas filas; no indexamos
cadena="alter table amortpers add foreign key(cuenta) references plancontable(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="alter table amortpers add foreign key(ejercicio) references ejercicios(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// codigo es clave primaria, por lo que hay índice implícito
cadena="alter table saldossubcuenta add foreign key(codigo) references plancontable(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// pase_diario_vencimiento puede ser nulo
cadena="alter table vencimientos add foreign key(cta_ordenante) references plancontable(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
// ejecutar("alter table vencimientos add foreign key(cuenta_tesoreria) references plancontable(codigo)");
cadena="alter table vencimientos add foreign key(pase_diario_operacion) references diario(pase)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="create index vtos_cta_ordenante_idx on vencimientos(cta_ordenante)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="create index vtos_cta_tesoreria_idx on vencimientos(cuenta_tesoreria)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="create index vtos_cta_pase_operacion_idx on vencimientos(pase_diario_operacion)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// en presupuesto, ejerciciobase y presupuestobase pueden no tener contenido
cadena="alter table presupuesto add foreign key(ejercicio) references ejercicios(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="alter table presupuesto add foreign key(cuenta) references plancontable(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="create index pres_ejercicio_idx on presupuesto(ejercicio)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="create index pres_cuenta_idx on presupuesto(cuenta)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


// esta tabla va a contener relativamente pocos registros (conciliación a mano)
// no vamos a indexar por ningún campo para clave foránea
cadena="alter table ajustes_conci add foreign key(cuenta) references plancontable(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
// ejecutar("alter table ajustes_conci add foreign key(dif_conciliacion) references dif_conciliacion(codigo)");

cadena="alter table cuenta_ext_conci add foreign key(cuenta) references plancontable(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
// ejecutar("alter table cuenta_ext_conci add foreign key(dif_conciliacion) references dif_conciliacion(codigo)");
cadena="create index exconc_cuenta_idx on cuenta_ext_conci(cuenta)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="create index exconc_dif_conc_idx on cuenta_ext_conci(dif_conciliacion)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// tabla con pocos registros, no vamos a crear índice
cadena="alter table regul_sec add foreign key(codigo) references cab_regulariz(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// NO es posible esta restricción ya que no hay restricción a unique en la tabla
// diario para el campo clave_ci
/* if ( cualControlador() != MYSQL )
   {
    cadena="alter table diario_ci add foreign key(clave_ci) references diario(clave_ci)";
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
   } */

/*// claves foráneas para tipos_automaticos y facturación
cadena="create index tipos_doc_serie_idx on tipos_doc(serie)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="alter table tipos_doc add foreign key(serie) references series_fact(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
*/

cadena="create index tipo_doc_factura_idx on facturas(tipo_doc)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="alter table facturas add foreign key(tipo_doc) references tipos_doc(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


cadena="create index codigo_lin_fact_idx on lin_fact(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);
cadena="alter table lin_fact add foreign key(codigo) references referencias(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

cadena="alter table cab_as_modelo add foreign key(tipo) references tipos_automaticos(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


// insertamos el tipo general de automáticos después de haber creado la clave foránea
cadena="insert into tipos_automaticos ( codigo,descripcion) "
      "values ('";
cadena += tipo_asiento_general();
cadena += "','";
cadena += QObject::tr("TIPO GENERAL");
cadena += "')";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


// pase forma parte la clave primaria, por lo que hay índice implícito
cadena="alter table retenciones add foreign key(pase) references diario(pase)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

// lineas con cabecera
cadena="alter table lin_lote_predef add foreign key(codigo) "
       "references lote_fact(codigo)";
if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);


}


void basedatos::copiatablas(QString base)
{
    QProgressDialog progress(QObject::tr("COPIANDO TABLAS ..."), 0, 0, 22);

    progress.setMinimumDuration(0);
    // progress.setWindowModality(Qt::WindowModal);
    QApplication::processEvents();

 copiatabla_ejercicios(base);
 progress.setValue(1);
 QApplication::processEvents();

 copiatabla_plancontable(base);
 progress.setValue(2);
 QApplication::processEvents();

 copiatabla_datos_subcuenta(base);
 progress.setValue(3);
 QApplication::processEvents();

 copiatabla_tipos_venci(base);
 progress.setValue(4);
 QApplication::processEvents();

 copiatabla_diario(base);
 progress.setValue(5);
 QApplication::processEvents();

 copiatabla_amortcontable(base); // ********
 progress.setValue(6);
 QApplication::processEvents();

 copiatabla_amortfiscalycontable(base);
 progress.setValue(7);
 QApplication::processEvents();

 copiatabla_libroiva(base);
 progress.setValue(8);
 QApplication::processEvents();

 copiatabla_configuracion(base);
 progress.setValue(9);
 QApplication::processEvents();

 copiatabla_vencimientos(base);
 progress.setValue(10);
 QApplication::processEvents();

 copiatabla_tiposiva(base);
 progress.setValue(11);
 QApplication::processEvents();

 copiatabla_diarios(base);
 progress.setValue(12);
 QApplication::processEvents();

 copiatabla_ci(base);
 progress.setValue(13);
 QApplication::processEvents();

 copiatabla_diario_ci(base);
 progress.setValue(14);
 QApplication::processEvents();

 copiatabla_planamortizaciones(base);
 copiatabla_ci_amort(base);
 progress.setValue(16);
 QApplication::processEvents();

 copiatabla_amortpers(base);
 progress.setValue(17);
 QApplication::processEvents();

 copiatabla_paises(base);
 progress.setValue(18);
 QApplication::processEvents();

 copiatabla_claveidfiscal(base);
 progress.setValue(19);
 QApplication::processEvents();

 copiatabla_claveexpedidas(base);
 progress.setValue(20);
 QApplication::processEvents();

 copiatabla_claverecibidas(base);
 QApplication::processEvents();
 copiatabla_clave_op_retenciones(base);
 QApplication::processEvents();

 copiatabla_retenciones(base);
 QApplication::processEvents();
 copiatabla_provincias(base);
 QApplication::processEvents();
 copiatabla_concepto_evpn_pymes(base);
 QApplication::processEvents();
 copiatabla_cols_evpn_pymes(base);
 QApplication::processEvents();

 progress.setValue(21);
 QApplication::processEvents();

 copiatabla_saldosubcuenta(base);
 QApplication::processEvents();
 copiatabla_input_cta(base);
 progress.setValue(22);
 QApplication::processEvents();

 // progress.close();

 //QMessageBox::information( 0, QObject::tr("COPIAR EMPRESA"),
 //                       QObject::tr("El proceso ha concluido"));
}


void basedatos::copiatabla_ejercicios(QString base)
{
  QString cad = "select codigo,apertura,cierre, cerrado,cerrando, prox_asiento,"
                "primero_apertura, "
                "base_ejercicio, base_presupuesto, presupuesto_base_cero, prox_nrecepcion, incremento "
                "from ejercicios";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     while (query.next())
      {
       QString cad2="insert into ejercicios ( codigo,apertura,cierre, cerrado,cerrando, "
                    "prox_asiento, primero_apertura, "
                "base_ejercicio, base_presupuesto, presupuesto_base_cero, prox_nrecepcion, incremento) "
                "values ('";
                cad2+=query.value(0).toString().left(-1).replace("'","''"); // codigo
                cad2+="', '";
                cad2+=query.value(1).toDate().toString("yyyy-MM-dd"); // apertura
                cad2+="', '";
                cad2+=query.value(2).toDate().toString("yyyy-MM-dd"); // cierre
                cad2+="', ";
                if ( cualControlador(base) == SQLITE ) { // cerrado
                   cad2 += query.value(3).toBool() ? "1": "0" ;
                  }
                else {
                       cad2 += query.value(3).toBool() ? "true": "false" ;
                     }
                cad2+=", ";

                if ( cualControlador(base) == SQLITE ) { // cerrando
                   cad2 += query.value(4).toBool() ? "1": "0" ;
                  }
                else {
                       cad2 += query.value(4).toBool() ? "true": "false" ;
                     }
                cad2+=", ";
                cad2+=query.value(5).toString(); // prox_asiento
                cad2+=", ";

                if ( cualControlador(base) == SQLITE ) { // primero_apertura
                   cad2 += query.value(6).toBool() ? "1": "0" ;
                  }
                else {
                       cad2 += query.value(6).toBool() ? "true": "false" ;
                     }

                cad2+=", '";
                cad2+=query.value(7).toString().left(-1).replace("'","''"); // base_ejercicio
                cad2+="', '";
                cad2+=query.value(8).toString().left(-1).replace("'","''"); // base_presupuesto
                cad2+="', ";
                if ( cualControlador(base) == SQLITE ) { // presupuesto_base_cero
                   cad2 += query.value(9).toBool() ? "1": "0" ;
                  }
                else {
                       cad2 += query.value(9).toBool() ? "true": "false" ;
                     }
                cad2+=",";
                if (query.value(10).toString().isEmpty()) cad2+="0";
                  else cad2+=query.value(10).toString();
                cad2+=",";
                cad2+=query.value(11).toString();
                cad2+=")";
                ejecutar(cad2,base);
      }
}


void basedatos::copiatabla_plancontable(QString base)
{
  QString cad = "select codigo, descripcion, auxiliar from plancontable ";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     while (query.next())
      {
       QString cad2="insert into plancontable ( codigo,descripcion, auxiliar) "
                "values ('";
                cad2+=query.value(0).toString().left(-1).replace("'","''"); // codigo
                cad2+="', '";
                cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
                cad2+="', ";
                if ( cualControlador(base) == SQLITE ) { // auxiliar
                   cad2 += query.value(2).toBool() ? "1": "0" ;
                  }
                else {
                       cad2 += query.value(2).toBool() ? "true": "false" ;
                     }
                cad2+=")";
                ejecutar(cad2,base);
      }
}

void basedatos::copiatabla_diario(QString base)
{
  QString cad = "select asiento, pase, fecha, cuenta, debe, haber, concepto, documento, "
                "diario, ci, usuario, conciliado, dif_conciliacion, copia_doc, "
                "clave_ci, ejercicio, "
                "apunte_origen,"
                "codfactura,"
                "nrecepcion,"
                "revisado,"
                "codigo_var_evpn_pymes,"
                "fecha_factura "
                "from diario";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     while (query.next())
      {
       QString cad2="insert into diario (asiento, pase, fecha, cuenta, debe, haber,"
                    " concepto, documento, diario, ci, usuario, conciliado, dif_conciliacion,"
                    "copia_doc, clave_ci, ejercicio, "
               "apunte_origen,"
               "codfactura,"
               "nrecepcion,"
               "revisado,"
               "codigo_var_evpn_pymes,"
               "fecha_factura "
                ")values (";
                cad2+=query.value(0).toString(); // asiento
                cad2+=", ";
                cad2+=query.value(1).toString(); // pase
                cad2+=", '";
                cad2+=query.value(2).toDate().toString("yyyy-MM-dd"); // fecha
                cad2+="', '";
                cad2+=query.value(3).toString().left(-1).replace("'","''"); // cuenta
                cad2+="', ";
                cad2+=query.value(4).toString(); // debe
                cad2+=", ";
                cad2+=query.value(5).toString(); // haber
                cad2+=", '";
                cad2+=query.value(6).toString().left(-1).replace("'","''"); // concepto
                cad2+="', '";
                cad2+=query.value(7).toString().left(-1).replace("'","''"); // documento
                cad2+="', '";
                cad2+=query.value(8).toString().left(-1).replace("'","''"); // diario
                cad2+="', '";
                cad2+=query.value(9).toString().left(-1).replace("'","''"); // ci
                cad2+="', '";
                cad2+=query.value(10).toString().left(-1).replace("'","''"); // usuario
                cad2+="', ";
                if ( cualControlador(base) == SQLITE ) { // conciliado
                   cad2 += query.value(11).toBool() ? "1": "0";
                  }
                else {
                       cad2 += query.value(11).toBool() ? "true": "false" ;
                     }
                cad2+=", '";
                cad2+=query.value(12).toString().left(-1).replace("'","''"); // dif_conciliacion
                cad2+="','";
                cad2+=query.value(13).toString().left(-1).replace("'","''"); // copia_doc
                cad2+="',";
                cad2+=query.value(14).toString().left(-1).replace("'","''"); // clave_ci
                cad2+=", '";
                cad2+=query.value(15).toString().left(-1).replace("'","''"); // ejercicio
                cad2+="',";
                cad2+=query.value(16).toString(); // "apunte_origen,"
                cad2+=", '";
                cad2+=query.value(17).toString().left(-1).replace("'","''"); // "codfactura,"
                cad2+="',";
                cad2+=query.value(18).toString(); // "nrecepcion,"
                cad2+=", ";
                if ( cualControlador(base) == SQLITE ) { // revisado
                   cad2 += query.value(19).toBool() ? "1": "0";
                  }
                else {
                       cad2 += query.value(19).toBool() ? "true": "false" ;
                     }
                cad2+=", '";
                cad2+=query.value(20).toString().left(-1).replace("'","''"); // "codigo_var_evpn_pymes,"
                cad2+="', '";
                if (query.value(21).toString().isEmpty()) cad2+="2000-01-01";
                  else cad2+=query.value(21).toDate().toString("yyyy-MM-dd"); //"fecha_factura "
                cad2+="')";
                ejecutar(cad2,base);
      }
}

void basedatos::copiatabla_libroiva(QString base)
{
    QString cad = "select pase, cta_base_iva, base_iva, clave_iva, tipo_iva, tipo_re, "
                  "cuota_iva, cuenta_fra, fecha_fra, soportado, prorrata, "
                  "afecto, aib, eib, rectificativa, autofactura, agrario, fecha_operacion,"
                  "clave_operacion, bi_costo, rectificada, nfacturas, finicial,"
                  "ffinal, nombre, cif, autofactura_no_ue, pr_servicios_ue, bien_inversion, "
                  "op_no_sujeta, exento_dcho_ded, isp_op_interiores, importacion, exportacion, "
                  "caja_iva, apunte_tot_factura, total_factura"
                  " from libroiva";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into libroiva ("
                  "pase, cta_base_iva, base_iva, clave_iva, tipo_iva, tipo_re, "
                  "cuota_iva, cuenta_fra, fecha_fra, soportado, prorrata, "
                  "afecto, aib, eib, rectificativa, autofactura, agrario, fecha_operacion,"
                  "clave_operacion, bi_costo, rectificada, nfacturas, finicial,"
                  "ffinal, nombre, cif, autofactura_no_ue, pr_servicios_ue, bien_inversion, "
                  "op_no_sujeta, exento_dcho_ded, isp_op_interiores, importacion, exportacion, "
                  "caja_iva, apunte_tot_factura, total_factura"
                  ") values (";
         cad2+=query.value(0).toString(); // pase
         cad2+=",'";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // cta_base_iva
         cad2+="',";
         cad2+=query.value(2).toString(); // base_iva
         cad2+=",'";
         cad2+=query.value(3).toString().left(-1).replace("'","''"); // clave_iva
         cad2+="',";
         cad2+=query.value(4).toString(); // tipo_iva
         cad2+=",";
         cad2+=query.value(5).toString(); // tipo_re
         cad2+=",";
         cad2+=query.value(6).toString(); // cuota_iva
         cad2+=",'";
         cad2+=query.value(7).toString().left(-1).replace("'","''"); // cuenta_fra
         cad2+="','";
         cad2+=query.value(8).toDate().toString("yyyy-MM-dd"); // fecha_fra
         cad2+="',";
         if ( cualControlador(base) == SQLITE ) { // soportado
            cad2 += query.value(9).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(9).toBool() ? "true": "false" ;
              }
         cad2+=",";
         cad2+=query.value(10).toString(); // prorrata
         cad2+=",";
         cad2+=query.value(11).toString(); // afecto (porcentaje)
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // aib
            cad2 += query.value(12).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(12).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // eib
            cad2 += query.value(13).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(13).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // rectificativa
            cad2 += query.value(14).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(14).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // autofactura
            cad2 += query.value(15).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(15).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // agrario
            cad2 += query.value(16).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(16).toBool() ? "true": "false" ;
              }
         cad2+=",'";
         cad2+=query.value(17).toDate().toString("yyyy-MM-dd"); // fecha_operacion
         cad2+="','";
         cad2+=query.value(18).toString().left(-1).replace("'","''"); // clave operación
         cad2+="',";
         cad2+=query.value(19).toString().isEmpty() ? "0" : query.value(19).toString(); // bi_costo
         cad2+=",'";
         cad2+=query.value(20).toString(); // rectificada
         cad2+="',";
         cad2+=query.value(21).toString(); // nfacturas
         cad2+=",'";
         cad2+=query.value(22).toString().left(-1).replace("'","''"); // finicial
         cad2+="','";
         cad2+=query.value(23).toString().left(-1).replace("'","''"); // ffinal
         cad2+="','";
         cad2+=query.value(24).toString().left(-1).replace("'","''"); // nombre
         cad2+="','";
         cad2+=query.value(25).toString().left(-1).replace("'","''"); // cif
         cad2+="',";
         if ( cualControlador(base) == SQLITE ) { // autofactura_no_e
            cad2 += query.value(26).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(26).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // pr_servicios_ue
            cad2 += query.value(27).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(27).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // bien_inversion
            cad2 += query.value(28).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(28).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // op_no_sujeta
            cad2 += query.value(29).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(29).toBool() ? "true": "false" ;
              }
         //exento_dcho_ded, isp_op_interiores, importacion, exportacion, "
         //"caja_iva, apunte_tot_factura, total_factura
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // exento_dcho_ded
            cad2 += query.value(30).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(30).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // isp_op_interiores
            cad2 += query.value(31).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(31).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // importacion
            cad2 += query.value(32).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(32).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // exportación
            cad2 += query.value(33).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(33).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // caja_iva
            cad2 += query.value(34).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(34).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if (query.value(35).toString().isEmpty()) cad2+="0";
            else cad2+=query.value(35).toString(); // apunte_tot_factura
         cad2+=",";

         if (query.value(36).toString().isEmpty()) cad2+="0";
            else cad2+=query.value(36).toString(); // total_factura
         cad2+=")";
         ejecutar(cad2,base);
        }

}


void basedatos::copiatabla_planamortizaciones(QString base)
{
    // planamortizaciones
    /*  "CREATE TABLE planamortizaciones ("
             "cuenta_activo         varchar(40),"
             "fecha_func            date,"
             "codigo_activo         varchar(40),"
             "cuenta_am_acum        varchar(40),"
             "cuenta_gasto          varchar(40),"
             "valor_residual        numeric(14,2),"
             "amort_personalizada   bool,"
             "coef_amort_contable   float8,"
             "coef_amort_fiscal     float8,"
             "amort_fiscal_1ejer    bool,"
             "baja` tinyint(1) DEFAULT '0',
             `fecha_baja` date DEFAULT NULL,
             `motivo_baja` varchar(255) DEFAULT NULL,
             `cuenta_proveedor` varchar(40) DEFAULT NULL,
             "PRIMARY KEY (cuenta_activo) )"; */

 QString cad = "select "
                "cuenta_activo, "
                "fecha_func, "
                "codigo_activo, "
                "cuenta_am_acum, "
                "cuenta_gasto, "
                "valor_residual, "
                "amort_personalizada, "
                "coef_amort_contable, "
                "coef_amort_fiscal, "
                "amort_fiscal_1ejer,"
                "baja, "
                "fecha_baja, "
                "motivo_baja, "
                "cuenta_proveedor "
                "from planamortizaciones ";

  QSqlQuery query=ejecutar(cad);
      if (query.isActive())
                   while (query.next())
                    {
                     QString cad2="insert into planamortizaciones ( "
                                  "cuenta_activo, "
                                  "fecha_func, "
                                  "codigo_activo, "
                                  "cuenta_am_acum, "
                                  "cuenta_gasto, "
                                  "valor_residual, "
                                  "amort_personalizada, "
                                  "coef_amort_contable, "
                                  "coef_amort_fiscal, "
                                  "amort_fiscal_1ejer, "
                                  "baja, "
                                  "fecha_baja, "
                                  "motivo_baja, "
                                  "cuenta_proveedor) "
                                  "values ('";
                     cad2+=query.value(0).toString().left(-1).replace("'","''"); // cuenta_activo
                     cad2+="','";
                     cad2+=query.value(1).toDate().toString("yyyy-MM-dd"); // fecha_func
                     cad2+="','";
                     cad2+=query.value(2).toString().left(-1).replace("'","''"); // codigo_activo
                     cad2+="','";
                     cad2+=query.value(3).toString().left(-1).replace("'","''"); // cuenta_am_acum
                     cad2+="','";
                     cad2+=query.value(4).toString().left(-1).replace("'","''"); // cuenta_gasto
                     cad2+="',";
                     cad2+=query.value(5).toString(); // valor_residual
                     cad2+=",";
                     if ( cualControlador(base) == SQLITE ) { // amort_personalizada
                        cad2 += query.value(6).toBool() ? "1": "0";
                       }
                     else {
                            cad2 += query.value(6).toBool() ? "true": "false" ;
                          }
                     cad2+=",";
                     cad2+=query.value(7).toString(); // coef_amort_contable
                     cad2+=",";
                     cad2+=query.value(8).toString(); // coef_amort_fiscal
                     cad2+=",";
                     if ( cualControlador(base) == SQLITE ) { // amort_fiscal_1ejer
                        cad2 += query.value(9).toBool() ? "1": "0";
                       }
                     else {
                            cad2 += query.value(9).toBool() ? "true": "false" ;
                          }
                     cad2+=",";
                     if ( cualControlador(base) == SQLITE ) { // baja
                        cad2 += query.value(10).toBool() ? "1": "0";
                       }
                     else {
                            cad2 += query.value(10).toBool() ? "true": "false" ;
                          }
                     cad2+=",'";
                     if (query.value(11).toString().isEmpty()) cad2+="2000-01-01";
                        else cad2+=query.value(11).toDate().toString("yyyy-MM-dd"); // fecha_baja
                     cad2+="','";
                     cad2+=query.value(12).toString().left(-1).replace("'","''"); // motivo_baja
                     cad2+="','";
                     cad2+=query.value(13).toString().left(-1).replace("'","''"); // cuenta_proveedor
                     cad2+="')";
                     ejecutar(cad2,base);
                    }

}

void basedatos::copiatabla_amortcontable(QString base)
{
  QString cad = "select ejercicio,asiento, "
                "as_enero, as_febrero, as_marzo, as_abril, as_mayo, as_junio, "
                "as_julio, as_agosto, as_septiembre, as_octubre, as_noviembre, as_diciembre "
                "from amortcontable";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     while (query.next())
      {
       QString cad2="insert into amortcontable (ejercicio, asiento, "
               "as_enero, as_febrero, as_marzo, as_abril, as_mayo, as_junio, "
               "as_julio, as_agosto, as_septiembre, as_octubre, as_noviembre, as_diciembre "
                    ") "
                "values ('";
       cad2+=query.value(0).toString().left(-1).replace("'","''");
       cad2+="',";
       cad2+=query.value(1).toString();
       cad2+=", ";
       cad2+=query.value(2).toString();
       cad2+=", ";
       cad2+=query.value(3).toString();
       cad2+=", ";
       cad2+=query.value(4).toString();
       cad2+=", ";
       cad2+=query.value(5).toString();
       cad2+=", ";
       cad2+=query.value(6).toString();
       cad2+=", ";
       cad2+=query.value(7).toString();
       cad2+=", ";
       cad2+=query.value(8).toString();
       cad2+=", ";
       cad2+=query.value(9).toString();
       cad2+=", ";
       cad2+=query.value(10).toString();
       cad2+=", ";
       cad2+=query.value(11).toString();
       cad2+=", ";
       cad2+=query.value(12).toString();
       cad2+=", ";
       cad2+=query.value(13).toString();
       cad2+=")";
       ejecutar(cad2,base);
      }
}


void basedatos::copiatabla_vencimientos(QString base)
{
  /*  cadena = "CREATE TABLE vencimientos ("
             "num                     bigint,"
             "cta_ordenante           varchar(40),"
             "fecha_operacion         date,"
             "importe                 numeric(14,2),"
             "cuenta_tesoreria        varchar(40),"
             "fecha_vencimiento       date,"
             "derecho                 bool,"
             "pendiente               bool,"
             "anulado                 bool,"
             "pase_diario_operacion   bigint,"
             "pase_diario_vencimiento bigint,"
             "concepto                varchar(160),"
             "usuario                 varchar(80),"
             "PRIMARY KEY (num) )"; */

    QString cad = "select "
                  "num, "
                  "cta_ordenante, "
                  "fecha_operacion, "
                  "importe, "
                  "cuenta_tesoreria, "
                  "fecha_vencimiento, "
                  "derecho, "
                  "pendiente, "
                  "anulado, "
                  "pase_diario_operacion, "
                  "pase_diario_vencimiento, "
                  "concepto, "
                  "usuario, "
                  "domiciliacion, "
                  "impagado, "
                  "domiciliable,"
                  "fecha_realizacion, "
                  "medio_realizacion, "
                  "cuenta_bancaria, "
                  "forzar_liq_iva, "
                  "liq_iva_fecha "
                  "from vencimientos";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into vencimientos ("
                      "num, "
                      "cta_ordenante, "
                      "fecha_operacion, "
                      "importe, "
                      "cuenta_tesoreria, "
                      "fecha_vencimiento, "
                      "derecho, "
                      "pendiente, "
                      "anulado, "
                      "pase_diario_operacion, "
                      "pase_diario_vencimiento, "
                      "concepto, "
                      "usuario, "
                      "domiciliacion, "
                      "impagado, "
                      "domiciliable,"
                      "fecha_realizacion, "
                      "medio_realizacion, "
                      "cuenta_bancaria, "
                      "forzar_liq_iva, "
                      "liq_iva_fecha "
                      ") values (";
         cad2+=query.value(0).toString(); // num
         cad2+=",'";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // cuenta_ordenante
         cad2+="','";
         cad2+=query.value(2).toDate().toString("yyyy-MM-dd"); // fecha_operacion
         cad2+="',";
         cad2+=query.value(3).toString(); // importe
         cad2+=",'";
         cad2+=query.value(4).toString().left(-1).replace("'","''"); // cuenta_tesoreria
         cad2+="','";
         cad2+=query.value(5).toDate().toString("yyyy-MM-dd"); // fecha_vencimiento
         cad2+="',";
         if ( cualControlador(base) == SQLITE ) { // derecho
            cad2 += query.value(6).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(6).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // pendiente
            cad2 += query.value(7).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(7).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // anulado
            cad2 += query.value(8).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(8).toBool() ? "true": "false" ;
              }
         cad2+=",";
         cad2+=query.value(9).toString(); // pase_diario_operacion
         cad2+=",";
         cad2+=query.value(10).toString(); // pase_diario_vencimiento
         cad2+=",'";
         cad2+=query.value(11).toString().left(-1).replace("'","''"); // concepto
         cad2+="','";
         cad2+=query.value(12).toString().left(-1).replace("'","''"); // usuario
         cad2+="',";
         cad2+=query.value(13).toString(); // `domiciliacion` bigint(20) DEFAULT '0',
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // impagado
            cad2 += query.value(14).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(14).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // domiciliable
            cad2 += query.value(15).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(15).toBool() ? "true": "false" ;
              }
         cad2+=",'";
         if (query.value(16).toString().isEmpty()) cad2+="2000-01-01";
           else cad2+=query.value(16).toDate().toString("yyyy-MM-dd"); // `fecha_realizacion` date DEFAULT NULL,
         cad2+="','";
         cad2+=query.value(17).toString().left(-1).replace("'","''"); // `medio_realizacion` varchar(255) DEFAULT '',
         cad2+="','";
         cad2+=query.value(18).toString().left(-1).replace("'","''"); // `cuenta_bancaria` varchar(255) DEFAULT '',
         cad2+="',";
         if ( cualControlador(base) == SQLITE ) { // `forzar_liq_iva` tinyint(1) DEFAULT '0',
            cad2 += query.value(19).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(19).toBool() ? "true": "false" ;
              }
         cad2+=",'";
         if (query.value(20).toString().isEmpty()) cad2+="2000-01-01";
            else cad2+=query.value(20).toDate().toString("yyyy-MM-dd"); // `liq_iva_fecha` date DEFAULT NULL
         cad2+="')";
         ejecutar(cad2,base);
        }
}

void basedatos::copiatabla_tiposiva(QString base)
{
         /*  "CREATE TABLE tiposiva ("
             "clave        varchar(4),"
             "tipo         numeric(5,2),"
             "re           numeric(5,2),"
             "descripcion  varchar(80),"
             "cuenta      varchar(80),"
             "cuenta_sop    varchar(80),"
             "PRIMARY KEY (clave) )"; */
    QString cad = "select "
                  "clave, "
                  "tipo, "
                  "re, "
                  "descripcion, "
                  "cuenta, "
                  "cuenta_sop "
                  "from tiposiva";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into tiposiva ("
                      "clave, "
                      "tipo, "
                      "re, "
                      "descripcion, "
                      "cuenta, "
                      "cuenta_sop "
                      ") values ('";
         cad2+=query.value(0).toString(); // clave
         cad2+="',";
         cad2+=query.value(1).toString(); // tipo
         cad2+=", ";
         cad2+=query.value(2).toString(); // re
         cad2+=",'";
         cad2+=query.value(3).toString().left(-1).replace("'","''"); // descripcion
         cad2+="','";
         cad2+=query.value(4).toString().left(-1).replace("'","''"); // cuenta
         cad2+="','";
         cad2+=query.value(5).toString().left(-1).replace("'","''"); // cuenta_sop
         cad2+="')";
         ejecutar(cad2,base);
        }
}

void basedatos::copiatabla_diarios(QString base)
{
           /* "CREATE TABLE diarios ("
             "codigo       varchar(40),"
             "descripcion  varchar(255),"
             "PRIMARY KEY (codigo) )"; */
    QString cad = "select codigo, descripcion from diarios";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into diarios (codigo, descripcion) "
                  "values ('";
         cad2+=query.value(0).toString().left(-1).replace("'","''"); // codigo
         cad2+="','";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
         cad2+="')";
         ejecutar(cad2,base);
        }


}

void basedatos::copiatabla_datos_subcuenta(QString base)
{
  /*  cadena = "CREATE TABLE datossubcuenta ("
             "cuenta         varchar(40),"
             "razon          varchar(80),"
             "nombrecomercial varchar(80),"
             "cif            varchar(80),"
             "nifrprlegal    varchar(80),"
             "web            varchar(80),"
             "claveidfiscal  varchar(80),"
             "domicilio      varchar(80),"
             "poblacion      varchar(80),"
             "codigopostal   varchar(40),"
             "provincia      varchar(80),"
             "pais           varchar(80),"
             "tfno           varchar(80),"
             "fax            varchar(80),"
             "email          varchar(80),"
             "ccc            varchar(80),"
             "cuota          varchar(40),"
             "observaciones  varchar(255),";
    if ( cualControlador() == SQLITE ) cadena += "venciasoc bool default 0,"; // vencimiento asociado
       else cadena += "venciasoc bool default false,";
     cadena+="codvenci       varchar(40),"
             "tesoreria      varchar(40),";
    if ( cualControlador() == SQLITE ) cadena += "ivaasoc bool default 0,"; // vencimiento asociado
       else cadena += "ivaasoc bool default false,";
    if ( cualControlador() == SQLITE ) cadena += "procesavenci bool default 0,"; // procesa vencimiento
       else cadena += "procesavenci bool default false,";
     cadena+="cuentaiva      varchar(40),"
             "tipoiva        varchar(20),"
             "conceptodiario varchar(155),"
             "imagen         text,"
             "PRIMARY KEY (cuenta) )"; */
    QString cad = "select "
           "cuenta, "
           "razon, "
           "nombrecomercial, "
           "cif, "
           "nifrprlegal, "
           "web, "
           "claveidfiscal, "
           "domicilio, "
           "poblacion, "
           "codigopostal, "
           "provincia, "
           "pais, "
           "tfno, "
           "fax, "
           "email, "
           "ccc, "
           "cuota, "
           "observaciones, "
           "pais_dat, "
           "tipo_ret, "
           "tipo_operacion_ret, "
           "ret_arrendamiento, "
           "venciasoc, "
           "codvenci, "
           "tesoreria, "
           "ivaasoc, "
           "procesavenci, "
           "cuentaiva, "
           "tipoiva, "
           "conceptodiario, "
            "vto_dia_fijo, "
            "cuenta_ret_asig, "
            "es_ret_asig_arrend, "
            "tipo_ret_asig, "
            "tipo_oper_ret_asig, "
            "dom_bancaria, "
            "IBAN, "
            "BIC, "
            "ref_mandato, "
            "firma_mandato, "
            "caja_iva, "
           "imagen "
           "from datossubcuenta ";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into datossubcuenta ("
                      "cuenta, "
                      "razon, "
                      "nombrecomercial, "
                      "cif, "
                      "nifrprlegal, "
                      "web, "
                      "claveidfiscal, "
                      "domicilio, "
                      "poblacion, "
                      "codigopostal, "
                      "provincia, "
                      "pais, "
                      "tfno, "
                      "fax, "
                      "email, "
                      "ccc, "
                      "cuota, "
                      "observaciones, "
                      "pais_dat, "
                      "tipo_ret, "
                      "tipo_operacion_ret, "
                      "ret_arrendamiento, "
                      "venciasoc, "
                      "codvenci, "
                      "tesoreria, "
                      "ivaasoc, "
                      "procesavenci, "
                      "cuentaiva, "
                      "tipoiva, "
                      "conceptodiario, "
                 "vto_dia_fijo, "
                 "cuenta_ret_asig, "
                 "es_ret_asig_arrend, "
                 "tipo_ret_asig, "
                 "tipo_oper_ret_asig, "
                 "dom_bancaria, "
                 "IBAN, "
                 "BIC, "
                 "ref_mandato, "
                 "firma_mandato, "
                 "caja_iva, "
                      "imagen ) "
                      "values ('";
         cad2+=query.value(0).toString().left(-1).replace("'","''"); // cuenta
         cad2+="','";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // razon
         cad2+="','";
         cad2+=query.value(2).toString().left(-1).replace("'","''"); // nombrecomercial
         cad2+="','";
         cad2+=query.value(3).toString().left(-1).replace("'","''"); // cif
         cad2+="','";
         cad2+=query.value(4).toString().left(-1).replace("'","''"); // nifrprlegal
         cad2+="','";
         cad2+=query.value(5).toString().left(-1).replace("'","''"); // web
         cad2+="','";
         cad2+=query.value(6).toString().left(-1).replace("'","''"); // claveidfiscal
         cad2+="','";
         cad2+=query.value(7).toString().left(-1).replace("'","''"); // domicilio
         cad2+="','";
         cad2+=query.value(8).toString().left(-1).replace("'","''"); // poblacion
         cad2+="','";
         cad2+=query.value(9).toString().left(-1).replace("'","''"); // codigopostal
         cad2+="','";
         cad2+=query.value(10).toString().left(-1).replace("'","''"); // provincia
         cad2+="','";
         cad2+=query.value(11).toString().left(-1).replace("'","''"); // pais
         cad2+="','";
         cad2+=query.value(12).toString().left(-1).replace("'","''"); // tfno
         cad2+="','";
         cad2+=query.value(13).toString().left(-1).replace("'","''"); // fax
         cad2+="','";
         cad2+=query.value(14).toString().left(-1).replace("'","''"); // email
         cad2+="','";
         cad2+=query.value(15).toString().left(-1).replace("'","''"); // ccc
         cad2+="','";
         cad2+=query.value(16).toString().left(-1).replace("'","''"); // cuota
         cad2+="','";
         cad2+=query.value(17).toString().left(-1).replace("'","''"); // observaciones
         cad2+="','";
         cad2+=query.value(18).toString().left(-1).replace("'","''"); // pais_dat
         cad2+="',";
         QString qtiporet=query.value(19).toString().isEmpty() ? "0" :
                          query.value(19).toString().left(-1).replace("'","''");
         cad2+=qtiporet; // tipo_ret
         cad2+=",'";
         cad2+=query.value(20).toString().left(-1).replace("'","''"); // tipo_operacion_ret
         cad2+="',";
         if ( cualControlador(base) == SQLITE ) { // ret_arrendamiento
            cad2 += query.value(21).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(21).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // venciasoc
            cad2 += query.value(22).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(22).toBool() ? "true": "false" ;
              }
         cad2+=",'";
         cad2+=query.value(23).toString().left(-1).replace("'","''"); // codvenci
         cad2+="','";
         cad2+=query.value(24).toString().left(-1).replace("'","''"); // tesoreria
         cad2+="',";
         if ( cualControlador(base) == SQLITE ) { // ivaasoc
            cad2 += query.value(25).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(25).toBool() ? "true": "false" ;
              }
         cad2+=",";
         if ( cualControlador(base) == SQLITE ) { // procesavenci
            cad2 += query.value(26).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(26).toBool() ? "true": "false" ;
              }
         cad2+=",'";
         cad2+=query.value(27).toString().left(-1).replace("'","''"); // cuentaiva
         cad2+="','";
         cad2+=query.value(28).toString().left(-1).replace("'","''"); // tipoiva
         cad2+="','";
         cad2+=query.value(29).toString().left(-1).replace("'","''"); // conceptodiario
         cad2+="',";
         if (query.value(30).toString().isEmpty()) cad2+="0";
           else cad2+=query.value(30).toString(); // "vto_dia_fijo, "
         cad2+=",'";
         cad2+=query.value(31).toString().left(-1).replace("'","''"); // "cuenta_ret_asig, "
         cad2+="',";
         if ( cualControlador(base) == SQLITE ) { // es_ret_asig_arrend
            cad2 += query.value(32).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(32).toBool() ? "true": "false" ;
              }
         cad2+=", ";
         cad2+=query.value(33).toString(); //"tipo_ret_asig, "
         cad2+=",'";
         cad2+=query.value(34).toString().left(-1).replace("'","''"); //"tipo_oper_ret_asig, "
         cad2+="',";
         if ( cualControlador(base) == SQLITE ) { // "dom_bancaria, "
            cad2 += query.value(35).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(35).toBool() ? "true": "false" ;
              }
         cad2+=",'";
         cad2+=query.value(36).toString().left(-1).replace("'","''"); //"IBAN, "
         cad2+="','";
         cad2+=query.value(37).toString().left(-1).replace("'","''"); //"BIC, "
          cad2+="','";
         cad2+=query.value(38).toString().left(-1).replace("'","''"); // "ref_mandato, "
          cad2+="','";
          if (query.value(39).toString().isEmpty())
              cad2+="2000-01-01"; // "firma_mandato, "
            else
               cad2+=query.value(39).toDate().toString("yyyy-MM-dd"); // "firma_mandato, "
          cad2+="',";
          if ( cualControlador(base) == SQLITE ) { // "caja_iva, "
             cad2 += query.value(40).toBool() ? "1": "0";
            }
          else {
                 cad2 += query.value(40).toBool() ? "true": "false" ;
               }
         cad2+=",'";
         cad2+=query.value(41).toString(); // imagen
         cad2+="')";
         ejecutar(cad2,base);
        }
}

void basedatos::copiatabla_ci(QString base)
{
          /* "CREATE TABLE ci ("
             "codigo         varchar(40),"
             "descripcion    varchar(80),"
             "nivel          integer,"
             "PRIMARY KEY (codigo, nivel) )"; */
    QString cad = "select codigo, descripcion, nivel from ci";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into ci (codigo, descripcion, nivel) "
                  "values ('";
         cad2+=query.value(0).toString().left(-1).replace("'","''"); // codigo
         cad2+="','";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
         cad2+="',";
         cad2+=query.value(2).toString(); // nivel
         cad2+=")";
         ejecutar(cad2,base);
        }

}

void basedatos::copiatabla_diario_ci(QString base)
{
    /* "CREATE TABLE diario_ci ("
             "clave_ci       bigint,"
             "ci             varchar(80),"
             "importe        numeric(19,4),";
    if ( cualControlador() == SQLITE) {
    cadena += "registro integer primary key autoincrement";
    }
     else {
            cadena += "registro   serial";
          }
     if ( cualControlador() == SQLITE) cadena+=")";
        else cadena+=", primary key (registro))"; */

    QString cad = "select clave_ci, ci, importe from diario_ci order by registro";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into diario_ci (clave_ci, ci, importe) "
                  "values (";
         cad2+=query.value(0).toString().left(-1).replace("'","''"); // clave_ci
         cad2+=",'";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // ci
         cad2+="',";
         cad2+=query.value(2).toString(); // importe
         cad2+=")";
         ejecutar(cad2,base);
        }
}

void basedatos::copiatabla_ci_amort(QString base)
{
          /* "CREATE TABLE ci_amort ("
             "cuenta        varchar(40),"
             "ci            varchar(40),"
             "asignacion    float8,"
             "PRIMARY KEY (cuenta, ci) )"; */

    QString cad = "select cuenta, ci, asignacion from ci_amort";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into ci_amort (cuenta, ci, asignacion) "
                  "values ('";
         cad2+=query.value(0).toString().left(-1).replace("'","''"); // cuenta
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // ci
         cad2+="', ";
         cad2+=query.value(2).toString(); // asignacion
         cad2+=")";
         ejecutar(cad2,base);
        }
}


void basedatos::copiatabla_amortpers(QString base)
{
          /* "CREATE TABLE amortpers ("
             "cuenta     varchar(40),"
             "ejercicio  varchar(40),"
             "importe    numeric(14,2),"
             "PRIMARY KEY (cuenta, ejercicio) )"; */

    QString cad = "select cuenta, ejercicio, importe from amortpers";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into amortpers (cuenta, ejercicio, importe) "
                  "values ('";
         cad2+=query.value(0).toString().left(-1).replace("'","''"); // cuenta
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // ejercicio
         cad2+="', ";
         cad2+=query.value(2).toString(); // importe
         cad2+=")";
         ejecutar(cad2,base);
        }
}


void basedatos::copiatabla_tipos_venci(QString base)
{
         /*  "CREATE TABLE tiposvenci ("
             "codigo            varchar(40),"
             "descripcion       varchar(255),"
             "PRIMARY KEY (codigo) )"; */

    QString cad = "select codigo, descripcion from tiposvenci";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into tiposvenci (codigo, descripcion) "
                  "values ('";
         cad2+=query.value(0).toString().left(-1).replace("'","''"); // codigo
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
         cad2+="')";
         ejecutar(cad2,base);
        }

}



void basedatos::copiatabla_configuracion(QString base)
{
  QString cad = "select "

                "empresa, nif, domicilio, "
                "poblacion, "
                "cpostal,   "
                "provincia ,"
                "email,"
                "web,"
                "anchocuentas,"
                "cuenta_iva_soportado, "
                "cuenta_iva_repercutido,"
                "cuenta_ret_irpf,"
                "tipo_ret_irpf,"
                "cuentasapagar,"
                "cuentasacobrar,"
                "clave_iva_defecto,"
                "clave_gastos,"
                "clave_ingresos,"
                "cuenta_pyg,"
                "prox_pase,"
                "prox_vencimiento,"
                "id_registral,"
                "prorrata_iva,"
                "prorrata_especial,"
                "analitica_tabla,"
                "igic       ,"
                "analitica  ,"
                "analitica_parc,"
                "ctas_analitica,"
                "notas ,"
                "version,"
                "fecha_constitucion,"
                "objeto,"
                "actividad,"
                 "prox_documento,"
                 "prox_num_ci,"
                 "gestion_usuarios,"
                 "bloqsaldosmedios, cabecera347, cuerpo347, desglose347, cuerpo2_347, pie347, saludo347, firma347 "
                " from configuracion";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     while (query.next())
      {
       QString cad2="insert into configuracion ("
                    "empresa, nif, domicilio, "
                    "poblacion, "
                    "cpostal,   "
                    "provincia ,"
                    "email,"
                    "web,"
                    "anchocuentas,"
                    "cuenta_iva_soportado, "
                    "cuenta_iva_repercutido,"
                    "cuenta_ret_irpf,"
                    "tipo_ret_irpf,"
                    "cuentasapagar,"
                    "cuentasacobrar,"
                    "clave_iva_defecto,"
                    "clave_gastos,"
                    "clave_ingresos,"
                    "cuenta_pyg,"
                    "prox_pase,"
                    "prox_vencimiento,"
                    "id_registral,"
                    "prorrata_iva,"
                    "prorrata_especial,"
                    "analitica_tabla,"
                    "igic       ,"
                    "analitica  ,"
                    "analitica_parc,"
                    "ctas_analitica,"
                    "notas ,"
                    "version,"
                    "fecha_constitucion,"
                    "objeto,"
                    "actividad,"
                     "prox_documento,"
                     "prox_num_ci,"
                     "gestion_usuarios,"
                     "bloqsaldosmedios, cabecera347, cuerpo347, desglose347, cuerpo2_347, pie347, saludo347, firma347 "
                    ") "
                    "values ('";
       cad2+=query.value(0).toString().left(-1).replace("'","''"); // empresa
       cad2+="','";
       cad2+=query.value(1).toString().left(-1).replace("'","''"); // nif
       cad2+="','";
       cad2+=query.value(2).toString().left(-1).replace("'","''"); // domicilio
       cad2+="','";
       cad2+=query.value(3).toString().left(-1).replace("'","''"); // población
       cad2+="','";
       cad2+=query.value(4).toString().left(-1).replace("'","''"); // cpostal
       cad2+="','";
       cad2+=query.value(5).toString().left(-1).replace("'","''"); // provincia
       cad2+="','";
       cad2+=query.value(6).toString().left(-1).replace("'","''"); // email
       cad2+="','";
       cad2+=query.value(7).toString().left(-1).replace("'","''"); // web
       cad2+="',";
       cad2+=query.value(8).toString(); // anchocuentas
       cad2+=",'";
       cad2+=query.value(9).toString().left(-1).replace("'","''"); // cuenta iva soportado
       cad2+="','";
       cad2+=query.value(10).toString().left(-1).replace("'","''"); // cuenta iva repercutido
       cad2+="','";
       cad2+=query.value(11).toString().left(-1).replace("'","''"); // cuenta ret irpf
       cad2+="',";
       cad2+=query.value(12).toString().left(-1).replace("'","''"); // tipo ret irpf
       cad2+=",'";
       cad2+=query.value(13).toString().left(-1).replace("'","''"); // cuentas a pagar
       cad2+="','";
       cad2+=query.value(14).toString().left(-1).replace("'","''"); // cuentas a cobrar
       cad2+="','";
       cad2+=query.value(15).toString().left(-1).replace("'","''"); // clave iva defecto
       cad2+="','";
       cad2+=query.value(16).toString().left(-1).replace("'","''"); // clave gastos
       cad2+="','";
       cad2+=query.value(17).toString().left(-1).replace("'","''"); // clave ingresos
       cad2+="','";
       cad2+=query.value(18).toString().left(-1).replace("'","''"); // cuenta pyg
       cad2+="',";
       cad2+=query.value(19).toString(); // proxpase
       cad2+=",";
       cad2+=query.value(20).toString(); // proxvencimiento
       cad2+=",'";
       cad2+=query.value(21).toString().left(-1).replace("'","''"); // id registral
       cad2+="',";
       cad2+=query.value(22).toString(); // prorrata iva
       cad2+=",";
       if ( cualControlador(base) == SQLITE ) { // prorrata especial
          cad2 += query.value(23).toBool() ? "1": "0";
         }
       else {
              cad2 += query.value(23).toBool() ? "true": "false" ;
            }
       cad2+=",";
       if ( cualControlador(base) == SQLITE ) { // analitica tabla
          cad2 += query.value(24).toBool() ? "1": "0";
         }
       else {
              cad2 += query.value(24).toBool() ? "true": "false" ;
            }
       cad2+=",";
       if ( cualControlador(base) == SQLITE ) { // igic
          cad2 += query.value(25).toBool() ? "1": "0";
         }
       else {
              cad2 += query.value(25).toBool() ? "true": "false" ;
            }
       cad2+=",";
       if ( cualControlador(base) == SQLITE ) { // analitica
          cad2 += query.value(26).toBool() ? "1": "0";
         }
       else {
              cad2 += query.value(26).toBool() ? "true": "false" ;
            }
       cad2+=",";
       if ( cualControlador(base) == SQLITE ) { // analitica_parc
          cad2 += query.value(27).toBool() ? "1": "0";
         }
       else {
              cad2 += query.value(27).toBool() ? "true": "false" ;
            }
       cad2+=",'";
       cad2+=query.value(28).toString().left(-1).replace("'","''"); // ctas_analitica
       cad2+="','";
       cad2+=query.value(29).toString().left(-1).replace("'","''"); // notas
       cad2+="','";
       cad2+=query.value(30).toString().left(-1).replace("'","''"); // version
       cad2+="','";
       cad2+=query.value(31).toDate().toString("yyyy-MM-dd"); // fecha constitucion
       cad2+="','";
       cad2+=query.value(32).toString().left(-1).replace("'","''"); // objeto
       cad2+="','";
       cad2+=query.value(33).toString().left(-1).replace("'","''"); // actividad
       cad2+="',";
       cad2+=query.value(34).toString().left(-1).replace("'","''"); // prox_documento
       cad2+=",";
       cad2+=query.value(35).toString(); // prox_num_ci
       cad2+=",";
       if ( cualControlador(base) == SQLITE ) { // gestión usuarios
          cad2 += query.value(36).toBool() ? "1": "0";
         }
       else {
              cad2 += query.value(36).toBool() ? "true": "false" ;
            }
       cad2+=",";
       if ( cualControlador(base) == SQLITE ) { //bloqsaldosmedios
          cad2 += query.value(37).toBool() ? "1": "0";
         }
       else {
              cad2 += query.value(37).toBool() ? "true": "false" ;
            }
       cad2+=",'";
       cad2+=query.value(38).toString().left(-1).replace("'","''"); //
       cad2+="','";
       cad2+=query.value(39).toString().left(-1).replace("'","''"); //
       cad2+="','";
       cad2+=query.value(40).toString().left(-1).replace("'","''"); //
       cad2+="','";
       cad2+=query.value(41).toString().left(-1).replace("'","''"); //
       cad2+="','";
       cad2+=query.value(42).toString().left(-1).replace("'","''"); //
       cad2+="','";
       cad2+=query.value(43).toString().left(-1).replace("'","''"); //
       cad2+="','";
       cad2+=query.value(44).toString().left(-1).replace("'","''"); //
       cad2+="')";
       ejecutar(cad2,base);
      }
}

/*
// configuracion
cadena = "CREATE TABLE configuracion ("
         "empresa        varchar(255),"
         "nif            varchar(255),"
         "domicilio      varchar(255),"
         "poblacion      varchar(255),"
         "cpostal        varchar(255),"
         "provincia      varchar(255),"
         "email          varchar(255),"
         "web            varchar(255),"
         "anchocuentas   integer,"
         "cuenta_iva_soportado      varchar(255),"
         "cuenta_iva_repercutido    varchar(255),"
         "cuenta_ret_irpf   varchar(255),"
         "tipo_ret_irpf     numeric(7,4) default 0,"
         "cuentasapagar     varchar(255),"
         "cuentasacobrar    varchar(255),"
         "clave_iva_defecto varchar(255),"
         "clave_gastos      varchar(255),"
         "clave_ingresos    varchar(255),"
         "cuenta_pyg        varchar(255),"
         "prox_pase         bigint,"
         "prox_vencimiento  bigint,"
         "id_registral      varchar(255),"
         "prorrata_iva      numeric(5,2) default 0,";
         // "prox_asiento      bigint," eliminado
if ( cualControlador() == SQLITE ) cadena += "prorrata_especial bool default 0,";
else cadena += "prorrata_especial bool default false,";
if ( cualControlador() == SQLITE ) cadena += "analitica_tabla bool default 0,";
else cadena += "analitica_tabla bool default false,";
// cadena += "numrel       bool,"
cadena += "igic         bool,"
          "analitica    bool,"
          "analitica_parc    bool,"
          "ctas_analitica    text,"
          "notas             text,"
          "version      varchar(40),"
          "fecha_constitucion     date,"
          "objeto       varchar(255) default '',"
          "actividad    varchar(255) default '',"
          "prox_documento bigint default 1,"
          "prox_num_ci bigint default 1,";
if ( cualControlador() == SQLITE ) cadena += "gestion_usuarios bool default 0,";
  else cadena += "gestion_usuarios bool default false,";
cadena+="bloqsaldosmedios bool )";
*/



void basedatos::copiatabla_amortfiscalycontable(QString base)
{
  QString cad = "select ejercicio, cuenta_activo, codigo_activo, cuenta_am_acum, "
                "cuenta_gasto, dotacion_contable, dotacion_fiscal, mes from amortfiscalycontable";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     while (query.next())
      {
       QString cad2="insert into amortfiscalycontable (ejercicio, cuenta_activo, codigo_activo, "
                    "cuenta_am_acum, cuenta_gasto, dotacion_contable, dotacion_fiscal,mes) "
                    "values ('";
       cad2+=query.value(0).toString().left(-1).replace("'","''"); // ejercicio
       cad2+="','";
       cad2+=query.value(1).toString().left(-1).replace("'","''"); // cuenta_activo
       cad2+="','";
       cad2+=query.value(2).toString().left(-1).replace("'","''"); // codigo_activo
       cad2+="','";
       cad2+=query.value(3).toString().left(-1).replace("'","''"); // cuenta_am_acum
       cad2+="','";
       cad2+=query.value(4).toString().left(-1).replace("'","''"); // cuenta_gasto
       cad2+="',";
       cad2+=query.value(5).toString().left(-1).replace("'","''"); // dotacion_contable
       cad2+=",";
       cad2+=query.value(6).toString().left(-1).replace("'","''"); // dotacion_fiscal
       cad2+=",";
       cad2+=query.value(7).toString().left(-1).replace("'","''"); // mes
       cad2+=")";
       ejecutar(cad2,base);
      }
}

void basedatos::copiatabla_paises(QString base)
{
          /* "CREATE TABLE codigopais ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )"; */
    QString cad = "select codigo, descripcion from codigopais";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into codigopais (codigo, descripcion) "
                  "values ('";
         cad2+=query.value(0).toString(); // codigo
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
         cad2+="')";
         ejecutar(cad2,base);
        }
}


void basedatos::copiatabla_provincias(QString base)
{
          /*
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )"; */
    QString cad = "select codigo, descripcion from provincias";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into provincias (codigo, descripcion) "
                  "values ('";
         cad2+=query.value(0).toString(); // codigo
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
         cad2+="')";
         ejecutar(cad2,base);
        }
}

void basedatos::copiatabla_concepto_evpn_pymes(QString base)
{
          /*
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )"; */
    QString cad = "select codigo, descripcion from concepto_evpn_pymes";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into concepto_evpn_pymes (codigo, descripcion) "
                  "values ('";
         cad2+=query.value(0).toString(); // codigo
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
         cad2+="')";
         ejecutar(cad2,base);
        }
}


void basedatos::copiatabla_cols_evpn_pymes(QString base)
{
          /*
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )"; */
    QString cad = "select codigo, descripcion, cuentas from cols_evpn_pymes";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into cols_evpn_pymes (codigo, descripcion, cuentas) "
                  "values ('";
         cad2+=query.value(0).toString(); // codigo
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
         cad2+="', '";
         cad2+=query.value(2).toString().left(-1).replace("'","''"); // cuentas
         cad2+="')";
         ejecutar(cad2,base);
        }
}



void basedatos::copiatabla_claveidfiscal(QString base)
{
          /* "CREATE TABLE claveidfiscal ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )"; */
    QString cad = "select codigo, descripcion from claveidfiscal";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into claveidfiscal (codigo, descripcion) "
                  "values ('";
         cad2+=query.value(0).toString(); // codigo
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
         cad2+="')";
         ejecutar(cad2,base);
        }
}

void basedatos::copiatabla_claveexpedidas(QString base)
{
          /* "CREATE TABLE clave_op_expedidas ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )"; */
    QString cad = "select codigo, descripcion from clave_op_expedidas";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into clave_op_expedidas (codigo, descripcion) "
                  "values ('";
         cad2+=query.value(0).toString(); // codigo
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
         cad2+="')";
         ejecutar(cad2,base);
        }

}

void basedatos::copiatabla_claverecibidas(QString base)
{
    // clave_op_recibidas
         /* "CREATE TABLE clave_op_recibidas ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )"; */
    QString cad = "select codigo, descripcion from clave_op_recibidas";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into clave_op_recibidas (codigo, descripcion) "
                  "values ('";
         cad2+=query.value(0).toString(); // codigo
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
         cad2+="')";
         ejecutar(cad2,base);
        }
}

void basedatos::copiatabla_retenciones(QString base)
{
    /*
CREATE TABLE `retenciones` (
  `pase` bigint(20) NOT NULL DEFAULT '0',
  `cta_retenido` varchar(80) DEFAULT NULL,
  `arrendamiento` tinyint(1) DEFAULT '0',
  `clave_ret` varchar(255) DEFAULT NULL,
  `base_ret` decimal(14,2) DEFAULT NULL,
  `tipo_ret` decimal(5,2) DEFAULT NULL,
  `retencion` decimal(14,2) DEFAULT NULL,
  `ing_cta` decimal(14,2) DEFAULT NULL,
  `ing_cta_rep` decimal(14,2) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `cif` varchar(80) DEFAULT NULL,
  `provincia` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pase`)
     */
    QString cad="select "
          "pase, "
          "cta_retenido, "
          "arrendamiento, "
          "clave_ret, "
          "base_ret, "
          "tipo_ret, "
          "retencion, "
          "ing_cta, "
          "ing_cta_rep, "
          "nombre, "
          "cif, "
          "provincia "
          "from retenciones ";

    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into retenciones ( "
                 "pase, "
                 "cta_retenido, "
                 "arrendamiento, "
                 "clave_ret, "
                 "base_ret, "
                 "tipo_ret, "
                 "retencion, "
                 "ing_cta, "
                 "ing_cta_rep, "
                 "nombre, "
                 "cif, "
                 "provincia "
                  ") values (";
         cad2+=query.value(0).toString(); // pase
         cad2+=", '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // cta_retenido
         cad2+="', ";
         if ( cualControlador(base) == SQLITE ) { // arrendamiento
            cad2 += query.value(2).toBool() ? "1": "0";
           }
         else {
                cad2 += query.value(2).toBool() ? "true": "false" ;
              }
         cad2+=",'";
         cad2+=query.value(3).toString().left(-1).replace("'","''"); // clave_ret
         cad2+="', ";
         cad2+=query.value(4).toString(); // base_ret
         cad2+=", ";
         cad2+=query.value(5).toString(); // tipo_ret
         cad2+=", ";
         cad2+=query.value(6).toString(); // retencion
         cad2+=", ";
         cad2+=query.value(7).toString(); // ing_cta
         cad2+=", ";
         cad2+=query.value(8).toString(); // ing_cta_rep
         cad2+=",'";
         cad2+=query.value(9).toString().left(-1).replace("'","''"); // nombre
         cad2+="','";
         cad2+=query.value(10).toString().left(-1).replace("'","''"); // cif
         cad2+="','";
         cad2+=query.value(11).toString().left(-1).replace("'","''"); // provincia
         cad2+="')";
         ejecutar(cad2,base);
        }
}


void basedatos::copiatabla_clave_op_retenciones(QString base)
{
    // clave_op_recibidas
         /* "CREATE TABLE clave_op_retenciones ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )"; */
    QString cad = "select codigo, descripcion from clave_op_retenciones";
    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into clave_op_retenciones (codigo, descripcion) "
                  "values ('";
         cad2+=query.value(0).toString(); // codigo
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // descripcion
         cad2+="')";
         ejecutar(cad2,base);
        }
}



// saldossubcuenta
/*cadena = "CREATE TABLE saldossubcuenta ("
         "codigo       varchar(40),"
         "saldo        numeric(14,2),"
         "saldomedio1  numeric(14,2),"
         "saldomedio2  numeric(14,2),"
         "PRIMARY KEY (codigo) )"; */

void basedatos::copiatabla_saldosubcuenta(QString base)
{
    QString cad = "select codigo, saldo from saldossubcuenta";

    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into saldossubcuenta (codigo, saldo) "
                      "values ( '";
         cad2+=query.value(0).toString().left(-1).replace("'","''"); // codigo
         cad2+="', ";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // saldo
         cad2+=")";
         ejecutar(cad2,base);
       }
}

void basedatos::copiatabla_input_cta(QString base)
{
    /*
     *   `auxiliar` varchar(80) DEFAULT NULL,
         `ci` varchar(40) DEFAULT NULL,
         `imputacion` decimal(10,4) DEFAULT NULL,
         `registro` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
         PRIMARY KEY (`registro`),
         UNIQUE KEY `registro` (`registro`)
     * */
    QString cad = "select auxiliar, ci, imputacion from input_cta";

    QSqlQuery query=ejecutar(cad);
    if (query.isActive())
       while (query.next())
        {
         QString cad2="insert into input_cta (auxiliar, ci, imputacion) "
                      "values ( '";
         cad2+=query.value(0).toString().left(-1).replace("'","''"); // auxiliar
         cad2+="', '";
         cad2+=query.value(1).toString().left(-1).replace("'","''"); // ci
         cad2+="', ";
         if (query.value(2).toString().isEmpty()) cad2+="0";
           else cad2+=query.value(2).toString().left(-1).replace("'","''"); // imputación
         cad2+=")";
         ejecutar(cad2,base);
       }
}


void basedatos::copiatabla_borrador(QString base)
{
  QString cad = "select asiento, usuario, cuenta, concepto, debe, haber, documento, ctabaseiva, "
             "baseiva, claveiva, tipoiva, tipore, cuotaiva, cuentafra, diafra, mesfra, "
             "anyofra, soportado, ci, prorrata, rectificativa, autofactura  from borrador";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     while (query.next())
      {
       QString cad2="insert into borrador (asiento, usuario, cuenta, concepto, debe, "
                    "haber, documento, ctabaseiva, "
                    "baseiva, claveiva, tipoiva, tipore, cuotaiva, cuentafra, diafra, mesfra, "
                    "anyofra, soportado, ci, prorrata, rectificativa, autofactura) "
                    "values (";
       cad2+=query.value(0).toString().left(-1).replace("'","''"); // asiento
       cad2+="','";
       cad2+=query.value(1).toString().left(-1).replace("'","''"); // usuario
       cad2+="','";
       cad2+=query.value(2).toString().left(-1).replace("'","''"); // cuenta
       cad2+="','";
       cad2+=query.value(3).toString().left(-1).replace("'","''"); // concepto
       cad2+="','";
       cad2+=query.value(4).toString().left(-1).replace("'","''"); // debe
       cad2+="',";
       cad2+=query.value(5).toString().left(-1).replace("'","''"); // haber
       cad2+="',";
       cad2+=query.value(6).toString().left(-1).replace("'","''"); // documento
       cad2+=",";
       cad2+=query.value(7).toString().left(-1).replace("'","''"); // ctabaseiva
       cad2+=",";
       cad2+=query.value(8).toString(); // baseiva
       cad2+=",";
       cad2+=query.value(9).toString().left(-1).replace("'","''"); // claveiva
       cad2+=",";
       cad2+=query.value(10).toString(); // tipoiva
       cad2+=",";
       cad2+=query.value(11).toString(); // tipore
       cad2+=",";
       cad2+=query.value(12).toString(); // cuotaiva
       cad2+=",";
       cad2+=query.value(13).toString().left(-1).replace("'","''"); // cuentafra
       cad2+=",";
       cad2+=query.value(14).toString(); // diafra
       cad2+=",";
       cad2+=query.value(15).toString(); // mesfra
       cad2+=",";
       cad2+=query.value(16).toString(); // anyofra
       cad2+=",";
       cad2+=query.value(17).toString(); // soportado
       cad2+=",";
       cad2+=query.value(18).toString(); // ci
       cad2+=",";
       cad2+=query.value(19).toString(); // prorrata
       cad2+=",";
       cad2+=query.value(20).toString(); // rectificativa
       cad2+=",";
       cad2+=query.value(21).toString(); // autofactura
       cad2+=")";
       ejecutar(cad2,base);
      }
}

QString basedatos::mensajes_iniciales_347()
{
    QString cadena= "update configuracion ";
    cadena+="set cabecera347='";
    cadena+=QObject::tr("Estimad@s Señores/as");
    cadena+="', cuerpo347='";
    cadena+=QObject::tr("Al objeto de cumplimentar la Declaración Anual de Operaciones con Terceros superiores a "
            "3.005,06 Euros que establece el Real Decreto 2027/95 del 22 de diciembre de 1995 le(s) comunicamos "
            "que, según nuestros apuntes el importe total (IVA incluido) de dichas operaciones realizadas por "
            "nuestra firma a Vds. durante el ejercicio 2014 asciende a la cantidad de {cifra} Euros.");
    cadena+="', desglose347=";
    cadena+= cualControlador() == SQLITE ? "1" : "true";
    cadena+= ", cuerpo2_347='";
    cadena+=QObject::tr("Cifra que declararemos junto con el NIF: {nif}, salvo disconformidad por su parte, ante la Administración correspondiente "
            "en marzo del presente año.");
    cadena+= "', pie347='";
    cadena+=QObject::tr("Rogando que comprueben dicho dato, se despide");
    cadena+="', saludo347='";
    cadena+=QObject::tr("atentamente,");
    cadena+="' , firma347='";
    cadena+=QObject::tr("DPTO. CONTABILIDAD");
    cadena+="'";

    return cadena;
}

// Crea las tablas y datos por defecto para los diferentes sistemas
// Recibe los datos de si es de longitud variable y el número de dígitos de las cuentas
void basedatos::crearTablas (bool checked, QString digitos) {
    if (!abierta()) return;

    solotablas();

    // Barra de progreso
    QProgressDialog progreso(QObject::tr("Creando base de datos ..."), 0, 0, 4);
    progreso.setWindowModality(Qt::WindowModal);


    // Inserción de datos en configuración
    QString cadena = "insert into configuracion (empresa,nif,domicilio,poblacion,cpostal,provincia,"
             "email,web,anchocuentas,Cuenta_IVA_Soportado,Cuenta_IVA_Repercutido,"
             "cuentasapagar,cuentasacobrar,Clave_IVA_defecto,Clave_gastos,Clave_ingresos,id_registral,"
             "prox_pase,prox_vencimiento,bloqsaldosmedios,cuenta_pyg,igic,analitica)"
             "values (''     ,'' ,''       ,''       ,''     ,''       ,''   ,'' ,0 ,'',"
             "     ''   ,''           ,''            ,''    ,'' ,'' ,'',"
             "   1        ,1                ,";
    if ( cualControlador() == SQLITE ) {
        cadena += "0,'',0,0)";
    }
    else {
        cadena += "FALSE,'',FALSE,FALSE)";
    }
    ejecutar(cadena);


    ejecutar(mensajes_iniciales_347());

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // Inserción de datos en configuración
    cadena = "update configuracion set anchocuentas=";
    cadena += checked ? "0" : digitos.left(-1).replace("'","''");
    ejecutar(cadena);

    // Inserción de datos en configuración
    cadena = "update configuracion set clave_iva_defecto='GN'";
    ejecutar(cadena);

    // Inserción de datos en configuración
    cadena = "update configuracion set version='"VERSION"'";
    ejecutar(cadena);

    // Inserción de datos en configuración
    cadena = "insert into tiposiva (clave,tipo,re,descripcion) values ('GN',21,0,'TIPO GENERAL')";
    ejecutar(cadena);

    // Inserción de datos en configuración
    cadena = "insert into tiposiva (clave,tipo,re,descripcion) values ('GNR',21,5.20,'TIPO GENERAL CON RECARGO DE EQUIVALENCIA')";
    ejecutar(cadena);

    // Inserción de datos en configuración
    cadena = "insert into tiposiva (clave,tipo,re,descripcion) values ('RD',10,0,'TIPO REDUCIDO')";
    ejecutar(cadena);

    // Inserción de datos en configuración
    cadena = "insert into tiposiva (clave,tipo,re,descripcion) values ('RD2',10,1.40,'TIPO REDUCIDO CON RECARGO DE EQUIVALENCIA')";
    ejecutar(cadena);

    // Inserción de datos en configuración
    cadena = "insert into tiposiva (clave,tipo,re,descripcion) values ('SRD',4,0,'TIPO SUPERREDUCIDO')";
    ejecutar(cadena);

    // Inserción de datos en configuración
    cadena = "insert into tiposiva (clave,tipo,re,descripcion) values ('SRD2',4,0.5,'TIPO SUPERREDUCIDO CON RECARGO DE EQUIVALENCIA');";
    ejecutar(cadena);

    // Inserción de datos en configuración
    cadena = "update configuracion set clave_gastos='6',clave_ingresos='7'";
    ejecutar(cadena);

    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();


    // Inserción de datos en configuración

    intropaises();
    introclaveidfiscal();
    introclave_op_retenciones();
    intro_provincias();
    introclaveexpedidas();
    introclaverecibidas();
    intro_conceptos_evpn();
    intro_cols_evpn_pymes();


 // lo que sigue sirve para implementar claves foráneas; no procede para SQLITE
 if ( cualControlador() == SQLITE)
   {
    progreso.setValue(progreso.value()+1);
    QApplication::processEvents();
    return;
   }

 crearForaneas();

progreso.setValue(progreso.value()+1);
QApplication::processEvents();
return;

}

// 
void basedatos::borrarDatabase (QString database) {
    QString cadena="DROP DATABASE ";
    cadena += database.left(-1).replace("'","''");
    ejecutar(cadena);
}

// Borra de la tabla libroiva a partir de un pase
void basedatos::deletelibroivapase (QString pase) {
    ejecutar("delete from libroiva where pase="+ pase.left(-1).replace("'","''"));
}


// Borra de la tabla retenciones a partir de un pase
void basedatos::deleteretencionespase (QString pase) {
    ejecutar("delete from retenciones where pase="+ pase.left(-1).replace("'","''"));
}


// Borra de la tabla vencimientos a partir de un pase
void basedatos::deletevencimientospase_diario_operacion (QString pase) {
    ejecutar("delete from vencimientos where pase_diario_operacion="+ pase.left(-1).replace("'","''"));
}

// Borra del diario asientos que sean mayores o iguales
void basedatos::deleteDiarioasientomayorigualmenorigual(QString inicial, QString final,
                                                        QString ejercicio) {
    QString cadena = "delete from diario where asiento>=";
    cadena += inicial.left(-1).replace("'","''");
    cadena += " and asiento<=";
    cadena += final.left(-1).replace("'","''");
    cadena += " and ejercicio='";
    cadena += ejercicio;
    cadena += "'";
    ejecutar(cadena);
}

// Borra de plancontable una cuenta
void basedatos::deletePlancontablecodigo (QString qcodigo) {
    ejecutar("delete from plancontable where codigo = '"+ qcodigo.left(-1).replace("'","''") +"'");
}

// Borra de datossubcuenta una cuenta
void basedatos::deleteDatossubcuentacuenta (QString qcodigo) {
    ejecutar("delete from datossubcuenta where cuenta = '"+ qcodigo.left(-1).replace("'","''") +"'");
}

// Borra de saldossubcuenta una cuenta
void basedatos::deleteSaldossubcuentacodigo (QString qcodigo) {
    ejecutar("delete from saldossubcuenta where codigo = '"+ qcodigo.left(-1).replace("'","''") +"'");
}

// Borra de conceptos una clave
void basedatos::deleteConceptosclave (QString qcodigo) {
    ejecutar("delete from conceptos where clave = '"+ qcodigo.left(-1).replace("'","''") +"'");
}

// Borra de tabla una clave
void basedatos::deleteclavetabla (QString tabla, QString qcodigo) {
    ejecutar("delete from "+tabla+" where codigo = '"+ qcodigo.left(-1).replace("'","''") +"'");
}

// Borra de ci un codigo
void basedatos::deleteCicodigo (QString codigo) {
    ejecutar("delete from ci where codigo='"+ codigo.left(-1).replace("'","''") +"'");
}

// Borra de vencimientos un num
void basedatos::deleteVencimientosnum (QString vto) {
    ejecutar("delete from vencimientos where num="+ vto.left(-1).replace("'","''"));
}

// Borra de cab_as_modelo un asientomodelo
void basedatos::deleteCab_as_modeloasientomodelo (QString asiento) {
    ejecutar("delete from cab_as_modelo where asientomodelo='"+ asiento.left(-1).replace("'","''") +"'");
}

// Borra de det_as_modelo un asientomodelo
void basedatos::deleteDet_as_modeloasientomodelo (QString asiento) {
    ejecutar("delete from det_as_modelo where asientomodelo='"+ asiento.left(-1).replace("'","''") +"'");
}

// Borra de var_as_modelo un asientomodelo
void basedatos::deleteVar_as_modeloasientomodelo (QString asiento) {
    ejecutar("delete from var_as_modelo where asientomodelo='"+ asiento.left(-1).replace("'","''") +"'");
}

// Borra de tiposiva una clave
void basedatos::deleteTiposivaclave (QString qclave) {
    ejecutar("delete from tiposiva where clave = '"+ qclave.left(-1).replace("'","''") +"'");
}

// Borra de cabeceraestados un titulo
void basedatos::deleteCabeceraestadostitulo (QString estado) {
    ejecutar("delete from cabeceraestados where titulo='"+ estado.left(-1).replace("'","''") +"'");
}

// Borra de estados un titulo
void basedatos::deleteEstadostitulo (QString estado) {
    ejecutar("delete from estados where titulo='"+ estado.left(-1).replace("'","''") +"'");
}

// Borra todo de nuevoplan
void basedatos::deleteNuevoplan () {
    ejecutar("delete from nuevoplan");
}

// Borra de nuevoplan un codigo
void basedatos::deleteNuevoplancodigo (QString qcodigo) {
    ejecutar("delete from nuevoplan where codigo = '"+ qcodigo.left(-1).replace("'","''") +"'");
}

// Borra de dif_conciliación un codigo
void basedatos::deleteDif_conciliacioncodigo (QString qcodigo) {
    ejecutar("delete from dif_conciliacion where codigo = '"+ qcodigo.left(-1).replace("'","''") +"'");
}

// Borra de borrador un asiento
void basedatos::deleteBorrador (QString asiento) {
    ejecutar("delete from borrador where asiento="+ asiento.left(-1).replace("'","''"));
}

// Borra de ajustes_conci a partir de fechas y cuenta
void basedatos::deleteAjustes_concifechascuenta (QDate inicial, QDate final, QString cuenta) {
    QString cadena = "delete from ajustes_conci where fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);

}

// 
void basedatos::deleteEjercicioscodigo (QString elcodigo) {
    QString cadena = "delete from ejercicios where codigo='";
    cadena += elcodigo.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// 
void basedatos::deletePeriodosejercicio (QString elcodigo) {
    QString cadena = "delete from periodos where ejercicio='";
    cadena += elcodigo.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// 
void basedatos::deleteEquivalenciasplan () {
    ejecutar("delete from equivalenciasplan");
}

// Borra todo de plancontable
void basedatos::deletePlancontable () {
    ejecutar("delete from plancontable");
}

// 
void basedatos::deleteDiarioasiento (QString asiento, QString ejercicio) {
    // OJO solo borra diario y tabla analítica - sólo sirve para amortizaciones
    QSqlQuery query = selectPasedebehabercuentadiario(asiento,asiento,ejercicio);

    if ( query.isActive() )
    {
        while (query.next())
        {
         deletediarioci_clave (query.value(4).toString());
         registra_apunte_borrado(query.value(0).toString());
        }
    }
    QString cadena="delete from diario where asiento="+ asiento.left(-1).replace("'","''");
    cadena+=" and ejercicio='";
    cadena+= ejercicio;
    cadena += "'";
    ejecutar(cadena);
}

// 
void basedatos::deleteAmortcontableejercicio (QString ejercicio) {
    ejecutar("delete from amortcontable where ejercicio='"+ ejercicio.left(-1).replace("'","''") +"'");
}

// 
void basedatos::deleteAmortfiscalycontableejercicio (QString ejercicio) {
    ejecutar("delete from amortfiscalycontable where ejercicio='"+ ejercicio.left(-1).replace("'","''") +"'");
}

void basedatos::deleteAmortfiscalycontableejercicio_mes (QString ejercicio,int mes) {
    QString cadnum; cadnum.setNum(mes);
    ejecutar("delete from amortfiscalycontable where ejercicio='"
             + ejercicio.left(-1).replace("'","''") +"' and mes="+cadnum);
}


// 
void basedatos::deleteAmortperscuenta (QString ctaactivo) {
    ejecutar("delete from amortpers where cuenta='"+ ctaactivo.left(-1).replace("'","''") +"'");
}

// 
void basedatos::deleteCi_amortcuentaci (QString cuenta, QString ci) {
    QString cadquery = "delete from ci_amort where cuenta='";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "' and ci='";
    cadquery += ci.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}

// 
void basedatos::deletePlanamortizacionescuenta_activo (QString cuenta) {
    ejecutar("delete from planamortizaciones where cuenta_activo='"+ cuenta.left(-1).replace("'","''") +"'");
}

// 
void basedatos::deleteCi_amortcuenta (QString cuenta) {
    ejecutar("delete from ci_amort where cuenta='"+ cuenta.left(-1).replace("'","''") +"'");
}

// 
void basedatos::deletePresupuestoejerciciosubcuentas (QString ejercicio, bool subcuentas) {
    QString cadena= "delete from presupuesto where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' and ";
    QString cadnum;
    cadnum.setNum(anchocuentas());
    if (!cod_longitud_variable())
    {
        if (cualControlador() == SQLITE) {
            if (subcuentas)
                cadena += "length(cuenta)=";
            else cadena += "length(cuenta)<";
        }
        else {
            if (subcuentas)
                cadena += "char_length(cuenta)=";
            else cadena += "char_length(cuenta)<";
        }
        cadena += cadnum;
    }
    else
    {
        if (subcuentas)
            cadena += "cuenta in (select codigo from plancontable where auxiliar)";
        else cadena += "cuenta in (select codigo from plancontable where not auxiliar)";
    }
    ejecutar(cadena);
}

// 
void basedatos::deletePresupuestoejerciciocuenta (QString ejercicio, QString cuenta) {
    QString cadena = "delete from presupuesto where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' and cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// 
void basedatos::deleteCuenta_ext_concifechacuenta (QDate fecha, QString cuenta) {
    QString cadena = "delete from cuenta_ext_conci where fecha>='";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "' and cuenta='";
    cadena += cuenta;
    cadena += "'";
    ejecutar(cadena);
}

// 
void basedatos::deleteDiariodiariofechas (QString diario, QDate inicial, QDate final) {
    // averiguamos primero los apuntes que compone el diario a borrar
    // y los registramos en la tabla "apuntes_borrados"
    QString cadena="select pase from diario where diario='";
    cadena += diario.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    QSqlQuery query=ejecutar(cadena);

    if (query.isActive())
        while (query.next())
           {
            registra_apunte_borrado(query.value(0).toString());
           }

    cadena="delete from diario where diario='";
    cadena += diario.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    ejecutar(cadena);
}

// importaplan de creaempresa
// inicia todos los saldos de subcuenta
void basedatos::insertImportaplan () {
    QString cadena = "insert into saldossubcuenta (codigo,saldo,saldomedio1,saldomedio2) ";
    cadena += "select codigo,0,0,0 from plancontable where ";

    if (!cod_longitud_variable())
    {
        if ( cualControlador() == SQLITE ) {
            cadena += "length(codigo)=";
        }
        else {
            cadena += "char_length(codigo)=";
        }
        QString cadnum;
        cadnum.setNum(anchocuentas());
        cadena += cadnum;
    }
    else 
    {
        cadena += "auxiliar";
    }
    ejecutar(cadena);
}

// inserta datos en la tabla plancontable
void basedatos::insertPlancontable (QString codigo, QString descripcion, bool auxiliar) {
    QString cad = "insert into plancontable (codigo,descripcion,auxiliar) values ('";
    cad += codigo.left(-1).replace("'","''");
    cad += "','";
    cad += descripcion.left(-1).replace("'","''");
    cad += "',";
    if ( cualControlador() == SQLITE )
    {
        if (auxiliar) cad += "1"; else cad += "0";
    }
    else
    {
        if (auxiliar) cad += "true"; else cad += "false";
    }
    cad += ")";

    ejecutar(cad);
}

// Inserta datos en la tabla conceptos
void basedatos::insertConceptos (QString codigo,QString descripcion) {
    QString cadquery = "INSERT INTO conceptos (clave,descripcion) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}

// Inserta datos en tabla
void basedatos::inserttabla (QString tabla,QString codigo,QString descripcion) {
    QString cadquery = "INSERT INTO ";
    cadquery+=tabla;
    cadquery+=" (codigo,descripcion) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}

// Inserta datos en saldossubcuenta
void basedatos::insertSaldossubcuenta (QString codigo, QString saldo, QString saldomedio1, QString saldomedio2) {
    QString cadquery = "INSERT INTO saldossubcuenta (codigo,saldo,saldomedio1,saldomedio2) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "',"+ saldo.left(-1).replace("'","''");
    cadquery += ","+ saldomedio1.left(-1).replace("'","''");
    cadquery += ","+ saldomedio2.left(-1).replace("'","''")+ ")";
    ejecutar(cadquery);
}

// Inserta datos en ci
void basedatos::insertCi (QString codigo,QString descripcion,int nivel) {
    QString cadquery = "INSERT INTO ci (codigo,descripcion,nivel) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "',";
    cadquery += QString::number(nivel);
    cadquery += ")";
    ejecutar(cadquery);
}

// Inserta datos en tipoiva
void basedatos::insertTiposiva (QString clave,QString tipo, QString re, QString descripcion) {
    if (re.length()==0) re="0";
    QString cadquery = "INSERT INTO tiposiva (clave,tipo,re,descripcion) VALUES ('";
    cadquery += clave.left(-1).replace("'","''") +"',";
    cadquery += tipo.left(-1).replace("'","''") +",";
    cadquery += re.left(-1).replace("'","''") +",'";
    cadquery += descripcion.left(-1).replace("'","''") +"')";
    ejecutar(cadquery);
}

// Inserta datos en cabeceraestados
void basedatos::insert18Cabeceraestados (QString titulo, QString cabecera, QString parte1, QString parte2,
    QString observaciones, QString formulabasepor, QString fechacalculo, QString ejercicio1, 
    QString ejercicio2, QString diarios, QString estadosmedios, QString valorbasepor1, QString valorbasepor2, 
    QString analitica, QString haycolref, QString colref, QString ci, QString grafico, QString desglose,
    QString desglose_ctas, QString descabecera, QString despie) {
    QString cad1 = "insert into cabeceraestados (titulo,cabecera,parte1,parte2, observaciones,"
	    "formulabasepor,fechacalculo, ejercicio1,ejercicio2,diarios,"
	    "estadosmedios,valorbasepor1, valorbasepor2,"
        "analitica,haycolref,colref,ci,grafico,desglose, desglose_ctas, des_cabecera, des_pie"
	    ") values ('";
    cad1 += titulo.left(-1).replace("'","''") +"','";
    cad1 += cabecera.left(-1).replace("'","''") +"','";
    cad1 += parte1.left(-1).replace("'","''") +"','";
    cad1 += parte2.left(-1).replace("'","''") +"','";
    cad1 += observaciones.left(-1).replace("'","''") +"','";
    cad1 += formulabasepor.left(-1).replace("'","''") +"','";
    cad1 += fechacalculo.left(-1).replace("'","''") +"','";
    cad1 += ejercicio1.left(-1).replace("'","''") +"','";
    cad1 += ejercicio2.left(-1).replace("'","''") +"','";
    cad1 += diarios.left(-1).replace("'","''") +"',";
    if (cualControlador() == SQLITE) {
        if (estadosmedios.toLower() == "false") { cad1 += "0,"; }
        else { cad1 += "1,"; }
    }
    else {
        cad1 += estadosmedios.left(-1).replace("'","''") +",";
    }
    cad1 += valorbasepor1.left(-1).replace("'","''") +",";
    cad1 += valorbasepor2.left(-1).replace("'","''") +",";
    if (cualControlador() == SQLITE) {
        if (analitica.toLower() == "false") { cad1 += "0,"; }
        else { cad1 += "1,"; }
        if (haycolref.toLower() == "false") { cad1 += "0,'"; }
        else { cad1 += "1,'"; }
    }
    else {
        cad1 += analitica.left(-1).replace("'","''") +",";
        cad1 += haycolref.left(-1).replace("'","''") +",'";
    }
    cad1 += colref.left(-1).replace("'","''") +"','";
    cad1 += ci.left(-1).replace("'","''") +"',";
    if (cualControlador() == SQLITE) {
        if (grafico.toLower() == "false") { cad1 += "0,"; }
        else { cad1 += "1,"; }
    }
    else {
        cad1 += grafico.left(-1).replace("'","''") +",";
    }

    if (cualControlador() == SQLITE) {
        if (desglose.toLower() == "false") { cad1 += "0,"; }
        else { cad1 += "1,"; }
    }
    else {
        cad1 += desglose.left(-1).replace("'","''")+",";
    }

    if (cualControlador() == SQLITE) {
        if (desglose_ctas.toLower() == "false") { cad1 += "0,'"; }
        else { cad1 += "1,'"; }
    }
    else {
        cad1 += desglose_ctas.left(-1).replace("'","''")+",'";
    }

    cad1 += descabecera.left(-1).replace("'","''") +"','";
    cad1 += despie.left(-1).replace("'","''") +"')";

    ejecutar(cad1);
}

// Inserta datos en estados
void basedatos::insertEstados (QString titulo,QString apartado, QString parte1, QString clave, QString nodo,
    QString formula, QString importe1, QString importe2, QString calculado, QString referencia) {
    QString cad1 = "insert into estados (titulo,apartado,parte1,clave,nodo,formula,"
	    "importe1,importe2,calculado,referencia) values ('" ;
    cad1 += titulo.left(-1).replace("'","''") +"','";
    cad1 += apartado.left(-1).replace("'","''") +"',";
    if (cualControlador() == SQLITE) {
        if (parte1.toLower() == "false") { cad1 += "0,'"; }
        else { cad1 += "1,'"; }
    }
    else {
        cad1 += parte1.left(-1).replace("'","''") +",'";
    }
    cad1 += clave.left(-1).replace("'","''") +"','";
    cad1 += nodo.left(-1).replace("'","''") +"','";
    cad1 += formula.left(-1).replace("'","''") +"',";
    cad1 += importe1.left(-1).replace("'","''") +",";
    cad1 += importe2.left(-1).replace("'","''") +",";
    if (cualControlador() == SQLITE) {
        if (calculado.toLower() == "false") { cad1 += "0,'"; }
        else { cad1 += "1,'"; }
    }
    else {
        cad1 += calculado.left(-1).replace("'","''") +",'";
    }
    cad1 += referencia.left(-1).replace("'","''") +"')";
    ejecutar(cad1);	
}

// 
void basedatos::insertNuevoplan (QString codigo, QString descripcion, bool auxiliar) {
    QString cad1 = "insert into nuevoplan (codigo,descripcion,auxiliar) values ('";
    cad1 += codigo.left(-1).replace("'","''") +"','";
    cad1 += descripcion.left(-1).replace("'","''") +"',";
    if (cualControlador() == SQLITE) {
        if (auxiliar) { cad1 += "1"; }
        else { cad1 += "0"; }
    }   
    else {
        if (auxiliar) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1 += ")";
    ejecutar(cad1);	    
}

// Introduce los datos en cab_as_modelo
void basedatos::insertCab_as_modelo (QString asientomodelo, QString fecha, QString tipo,
                                     bool aib, bool autofactura_ue, bool autofactura_no_ue,
                                     bool eib, bool eib_servicios,
                                     bool isp_op_interiores,
                                     bool importacion,
                                     bool exportacion, QString diario)
{
    /* "aib                   bool,"
    "autofactura_ue        bool,"
    "autofactura_no_ue     bool,"
    "eib                   bool,"
    "eib_servicios         bool," */
    bool lite=cualControlador() == SQLITE;
    QString cad1 = "insert into cab_as_modelo (asientomodelo,fecha, tipo,"
            "aib, autofactura_ue, autofactura_no_ue, eib, eib_servicios, "
            "isp_op_interiores, importacion, exportacion, diario) values ('";
    cad1 += asientomodelo.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += fecha.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += tipo.left(-1).replace("'","''");
    cad1 += "',";
    if (lite)
      {
        cad1 += aib ? "1" : "0";
        cad1 += ",";
        cad1 += autofactura_ue ? "1" : "0";
        cad1 += ",";
        cad1 += autofactura_no_ue ? "1" : "0";
        cad1 += ",";
        cad1 += eib ? "1" : "0";
        cad1 += ",";
        cad1 += eib_servicios ? "1" : "0";
        cad1 += ",";
        cad1 += isp_op_interiores ? "1" : "0";
        cad1 += ",";
        cad1 += importacion ? "1" : "0";
        cad1 += ",";
        cad1 += exportacion ? "1" : "0";

      }
      else
        {
         cad1 += aib ? "true" : "false";
         cad1 += ",";
         cad1 += autofactura_ue ? "true" : "false";
         cad1 += ",";
         cad1 += autofactura_no_ue ? "true" : "false";
         cad1 += ",";
         cad1 += eib ? "true" : "false";
         cad1 += ",";
         cad1 += eib_servicios ? "true" : "false";
         cad1 += ",";
         cad1 += isp_op_interiores ? "true" : "false";
         cad1 += ",";
         cad1 += importacion ? "true" : "false";
         cad1 += ",";
         cad1 += exportacion ? "true" : "false";
         }
    cad1 +=", '";
    cad1 += diario;
    cad1 += "')";
    // QMessageBox::information( this, tr("Importar estado contable"),cad1);   
    ejecutar(cad1);	
}


// Introduce datos en det_as_modelo
void basedatos::insert_Det_as_modelo (QString asientomodelo,QString cuenta,QString concepto,
                                      QString expresion,QString d_h,QString ci,QString baseiva,
                                      QString cuentafra,QString cuentabaseiva,QString claveiva,
                                      QString documento,QString fecha_factura,
                                      QString prorrata_e, bool rectificativa,
                                      QString fecha_op, QString clave_op, QString bicoste,
                                      QString rectificada, QString numfacts, QString facini,
                                      QString facfinal, bool binversion) {
    QString cad1 = "insert into det_as_modelo (asientomodelo,"
        "cuenta,concepto, expresion,d_h, ci,baseiva,cuentafra,"
        "cuentabaseiva, claveiva,documento ,fecha_factura,"
        "prorrata_e, rectificativa, fecha_op,"
        "clave_op, bicoste, rectificada, numfacts, facini, facfinal, bien_inversion"
        ") values ('";
    cad1 += asientomodelo.left(-1).replace("'","''") +"','";
    cad1 += cuenta.left(-1).replace("'","''") +"','";
    cad1 += concepto.left(-1).replace("'","''") +"','";
    cad1 += expresion.left(-1).replace("'","''") +"','";
    cad1 += d_h.left(-1).replace("'","''") +"','";
    cad1 += ci.left(-1).replace("'","''") +"','";
    cad1 += baseiva.left(-1).replace("'","''") +"','";
    cad1 += cuentafra.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += cuentabaseiva.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += claveiva.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += documento.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += fecha_factura.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += prorrata_e.left(-1).replace("'","''");
    cad1 += "',";
    if (cualControlador() == SQLITE) cad1+= rectificativa ? "1" : "0" ;
       else cad1+= rectificativa ? "true" : "false" ;
    cad1 += ",'";
    cad1 += fecha_op.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += clave_op.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += bicoste.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += rectificada.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += numfacts.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += facini.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += facfinal.left(-1).replace("'","''");
    cad1 += "',";
    if (cualControlador() == SQLITE) cad1+= binversion ? "1" : "0" ;
       else cad1+= binversion ? "true" : "false" ;
    cad1 += ")";
    ejecutar(cad1);	  
}

// Introduce datos en diarios
void basedatos::insertDiarios (QString codigo, QString descripcion) {
    QString cadquery = "INSERT INTO diarios (codigo,descripcion) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}

// Introduce datos en dif_conciliacion
void basedatos::insertDif_conciliacion (QString codigo, QString descripcion) {
    QString cadena;
    if (!existecodigoplan(codigo,&cadena))
    {
        QString cadquery = "INSERT INTO dif_conciliacion (codigo,descripcion) VALUES ('";
        cadquery += codigo.left(-1).replace("'","''");
        cadquery += "','";
        cadquery += descripcion.left(-1).replace("'","''");
        cadquery += "')";
        ejecutar(cadquery);
    }
}

// Introduce 10 datos en diario
void basedatos::insertDiario10 (QString cadnumasiento, QString cadnumpase, QString cadinicioej1,
                                QString cuenta, QString debe, QString haber, QString concepto,
                                QString documento, QString usuario, QString ejercicio) {
    QString cad2 = "INSERT into diario (asiento,pase,fecha,cuenta,debe,haber,concepto,"
        "documento,diario,ejercicio,usuario) VALUES(";
    cad2 += cadnumasiento.left(-1).replace("'","''") +",";
    cad2 += cadnumpase.left(-1).replace("'","''") +",'";
    cad2 += cadinicioej1.left(-1).replace("'","''") +"','";
    cad2 += cuenta.left(-1).replace("'","''") +"',";
    cad2 += debe.left(-1).replace("'","''") +",";
    cad2 += haber.left(-1).replace("'","''");
    cad2 += ",'"+ concepto.replace("'","''") +"','";
    cad2 += documento.replace("'","''");
    cad2 += "','','";
    cad2 += ejercicio.replace("'","''");
    cad2 += "'";
    if (!usuario.isEmpty()) cad2 += ",'"+ usuario.replace("'","''") +"')";
      else
         {
           if (cualControlador() == SQLITE) {
             cad2 += ",'"+ usuario_sistema().replace("'","''") +"')";
             }
          else {
                cad2 += ",CURRENT_USER)";
               }
         }
    ejecutar(cad2);
}

// Introduce datos en diario para impor. cplus
void basedatos::insertDiario_ic (QString cadnumasiento, QString cadnumpase, QString cadinicioej1,
                                QString cuenta, QString debe, QString haber, QString concepto,
                                QString documento, QString usuario, QString ejercicio,
                                QString nrecepcion) {
    QString cad2 = "INSERT into diario (asiento,pase,fecha,cuenta,debe,haber,concepto,"
        "documento,diario,ejercicio,nrecepcion, usuario) VALUES(";
    cad2 += cadnumasiento.left(-1).replace("'","''") +",";
    cad2 += cadnumpase.left(-1).replace("'","''") +",'";
    cad2 += cadinicioej1.left(-1).replace("'","''") +"','";
    cad2 += cuenta.left(-1).replace("'","''") +"',";
    cad2 += debe.left(-1).replace("'","''") +",";
    cad2 += haber.left(-1).replace("'","''");
    cad2 += ",'"+ concepto.replace("'","''") +"','";
    cad2 += documento.replace("'","''");
    cad2 += "','','";
    cad2 += ejercicio.replace("'","''");
    cad2 += "', ";
    cad2 += nrecepcion.toInt()==0 ? "0" : nrecepcion ;
    if (!usuario.isEmpty()) cad2 += ",'"+ usuario.replace("'","''") +"')";
      else
         {
           if (cualControlador() == SQLITE) {
             cad2 += ",'"+ usuario_sistema().replace("'","''") +"')";
             }
          else {
                cad2 += ",CURRENT_USER)";
               }
         }
    ejecutar(cad2);
}


void basedatos::insertDiario_con_diario (QString cadnumasiento, QString cadnumpase,
                                         QString cadinicioej1, QString cuenta,
                                         QString debe, QString haber,
                                         QString concepto, QString documento,
                                         QString qdiario, QString usuario,
                                         QString ejercicio) {
    QString cad2 = "INSERT into diario (asiento,pase,fecha,cuenta,debe,haber,concepto,"
        "documento,diario,ejercicio,usuario) VALUES(";
    cad2 += cadnumasiento.left(-1).replace("'","''") +",";
    cad2 += cadnumpase.left(-1).replace("'","''") +",'";
    cad2 += cadinicioej1.left(-1).replace("'","''") +"','";
    cad2 += cuenta.left(-1).replace("'","''") +"',";
    cad2 += debe.left(-1).replace("'","''") +",";
    cad2 += haber.left(-1).replace("'","''");
    cad2 += ",'"+ concepto.left(-1).replace("'","''") +"','";
    cad2 += documento.left(-1).replace("'","''");
    cad2 += "','";
    cad2 += qdiario;
    cad2 += "','";
    cad2 += ejercicio;
    if (!usuario.isEmpty()) cad2 += "','"+usuario+"')";
       else
           {
            if (cualControlador() == SQLITE) {
             cad2 += "','"+ usuario_sistema() +"')";
             }
           else {
                 cad2 += "',CURRENT_USER)";
                }
           }
    ejecutar(cad2);
}


void basedatos::insertDiario_con_diario_ci (QString cadnumasiento, QString cadnumpase,
                                         QString cadinicioej1, QString cuenta,
                                         QString debe, QString haber,
                                         QString concepto, QString documento,
                                         QString qdiario, QString usuario, QString ci,
                                         QString ejercicio) {
    QString cad2 = "INSERT into diario (asiento,pase,fecha,cuenta,debe,haber,concepto,"
        "documento,diario,ejercicio,ci,usuario) VALUES(";
    cad2 += cadnumasiento.left(-1).replace("'","''") +",";
    cad2 += cadnumpase.left(-1).replace("'","''") +",'";
    cad2 += cadinicioej1.left(-1).replace("'","''") +"','";
    cad2 += cuenta.left(-1).replace("'","''") +"',";
    cad2 += debe.isEmpty() ? "0" : debe.left(-1).replace("'","''");
    cad2 += ",";
    cad2 += haber.isEmpty() ? "0" : haber.left(-1).replace("'","''");
    cad2 += ",'"+ concepto.left(-1).replace("'","''") +"','";
    cad2 += documento.left(-1).replace("'","''");
    cad2 += "','";
    cad2 += qdiario;
    cad2 += "','";
    cad2 += ejercicio;
    cad2 += "','";
    cad2 += ci;
    if (!usuario.isEmpty()) cad2 += "','"+usuario+"')";
       else
           {
            if (cualControlador() == SQLITE) {
             cad2 += "','"+ usuario_sistema() +"')";
             }
           else {
                 cad2 += "',CURRENT_USER)";
                }
           }
    ejecutar(cad2);
}



void basedatos::insertDiarioRegularizacion (QString cadnumasiento, QString cadnumpase,
                                            QString cadinicioej1, QString cuenta,
                                            QString debe, QString haber, QString usuario,
                                            QString ejercicio) {
    QString cad2 = "INSERT into diario (asiento,pase,fecha,cuenta,debe,haber,concepto,"
        "documento,diario,ejercicio,usuario) VALUES(";
    cad2 += cadnumasiento.left(-1).replace("'","''") +",";
    cad2 += cadnumpase.left(-1).replace("'","''") +",'";
    cad2 += cadinicioej1.left(-1).replace("'","''") +"','";
    cad2 += cuenta.left(-1).replace("'","''") +"',";
    cad2 += debe.left(-1).replace("'","''") +",";
    cad2 += haber.left(-1).replace("'","''");
    cad2 += ",'"+ msjdiario_regularizacion() +"','";
    // cad2 += documento;
    cad2 += "','";
    cad2 += diario_regularizacion();
    cad2 += "','";
    cad2 += ejercicio;
    if (!usuario.isEmpty()) cad2 += "','"+usuario+"')";
       else
           {
            if (cualControlador() == SQLITE) {
              cad2 += "','"+ usuario_sistema() +"')";
            }
           else {
                cad2 += "',CURRENT_USER)";
                }
           }
    ejecutar(cad2);
}


void basedatos::insertDiarioRegularizacion_ci (QString cadnumasiento, QString cadnumpase,
                                            QString cadinicioej1, QString cuenta,
                                            QString debe, QString haber, QString usuario,
                                            QString ci,
                                            QString ejercicio) {
    QString cad2 = "INSERT into diario (asiento,pase,fecha,cuenta,debe,haber,concepto,"
        "documento,diario, ci, ejercicio,usuario) VALUES(";
    cad2 += cadnumasiento.left(-1).replace("'","''") +",";
    cad2 += cadnumpase.left(-1).replace("'","''") +",'";
    cad2 += cadinicioej1.left(-1).replace("'","''") +"','";
    cad2 += cuenta.left(-1).replace("'","''") +"',";
    cad2 += (debe.isEmpty()? "0" : debe.left(-1).replace("'","''")) +",";
    cad2 += haber.isEmpty() ? "0" : haber.left(-1).replace("'","''");
    cad2 += ",'"+ msjdiario_regularizacion() +"','";
    // cad2 += documento;
    cad2 += "','";
    cad2 += diario_regularizacion();
    cad2 += "','";
    cad2 += ci;
    cad2 += "','";
    cad2 += ejercicio;
    if (!usuario.isEmpty()) cad2 += "','"+usuario+"')";
       else
           {
            if (cualControlador() == SQLITE) {
              cad2 += "','"+ usuario_sistema() +"')";
            }
           else {
                cad2 += "',CURRENT_USER)";
                }
           }
    ejecutar(cad2);
}


// Introduce datos de Vencimientos
void basedatos::insertVencimientosespecial (QString cadnumvto, QString qcuenta,
                                            QString qfecha, QString qdebehaber,
                                            double valor1, double valor2, QString pase) {
    QString vdomiciliable;
    bool domiciliable=cuenta_domiciliable(qcuenta);
    if (cualControlador()==SQLITE) vdomiciliable="0"; else vdomiciliable="false";
    if ( domiciliable && cualControlador()==SQLITE) vdomiciliable="1";
    if ( domiciliable && cualControlador()!=SQLITE) vdomiciliable="true";

    QString cad1 = "INSERT into vencimientos (num,cta_ordenante, fecha_operacion,importe,";
    cad1 += "cuenta_tesoreria, fecha_vencimiento,derecho, pendiente,anulado,";
    cad1 += "pase_diario_operacion,pase_diario_vencimiento, domiciliable,usuario) VALUES(";
    cad1 += cadnumvto.left(-1).replace("'","''");
    cad1 += ",'";
    cad1 += qcuenta.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += qfecha.left(-1).replace("'","''");
    cad1 += "',";
    cad1 += qdebehaber.left(-1).replace("'","''");
    cad1 += ",'',";
    if (cualControlador() == SQLITE) {
        cad1 += "'"+ QDate::currentDate().toString("yyyy-MM-dd") +"',";
        if (valor1-valor2<0) cad1+="0"; else cad1+="1";
        cad1 += ",1,1,";
       
    }
    else {
        cad1 += "CURRENT_DATE,";
        if (valor1-valor2<0) cad1+="FALSE"; else cad1+="TRUE";
        cad1 += ",TRUE,TRUE,";
    }
    cad1 += pase.left(-1).replace("'","''");
    cad1 += ",0,";
    cad1 += vdomiciliable;
    cad1 += ", ";
    if(cualControlador() == SQLITE) {
        cad1 += "'"+ usuario_sistema() +"')";
    }
    else {
        cad1 += "CURRENT_USER)";
    }
    ejecutar(cad1);
}

// Introduce datos de Vencimientos
void basedatos::insertVencimientosespecialtf (QString cadnumvto, QString qcuenta,
                                              QString qfecha, QString qdebehaber,
                                              double valor1, double valor2, QString pase) {
    QString vdomiciliable;
    bool domiciliable=cuenta_domiciliable(qcuenta);
    if (cualControlador()==SQLITE) vdomiciliable="0"; else vdomiciliable="false";
    if ( domiciliable && cualControlador()==SQLITE) vdomiciliable="1";
    if ( domiciliable && cualControlador()!=SQLITE) vdomiciliable="true";

    QString cad1 = "INSERT into vencimientos (num,cta_ordenante,fecha_operacion,importe,";
    cad1 += "cuenta_tesoreria, fecha_vencimiento, derecho,pendiente,anulado,";
    cad1 += "pase_diario_operacion, pase_diario_vencimiento,domiciliable, usuario) VALUES(";
    cad1 += cadnumvto.left(-1).replace("'","''");
    cad1 += ",'";
    cad1 += qcuenta.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += qfecha.left(-1).replace("'","''");
    cad1 += "',";
    cad1 += qdebehaber.left(-1).replace("'","''");
    cad1 += ",'',";
    if (cualControlador() == SQLITE) {
        cad1 += "'"+ QDate::currentDate().toString("yyyy-MM-dd") +"',";
        if (valor1-valor2<0) cad1+="0"; else cad1+="1";
        cad1 += ",1,0,";
       
    }
    else {
        cad1 += "CURRENT_DATE,";
        if (valor1-valor2<0) cad1+="FALSE"; else cad1+="TRUE";
        cad1 += ",TRUE,FALSE,";
    }
    cad1 += pase.left(-1).replace("'","''");
    cad1 += ",0,";
    cad1 += vdomiciliable;
    cad1 += ", ";
    if(cualControlador() == SQLITE) {
        cad1 += "'"+ usuario_sistema() +"')";
    }
    else {
        cad1 += "CURRENT_USER)";
    }
    ejecutar(cad1);
}

// Inserta datos en ajustes_conci
void basedatos::insertAjustes_conci (QString cuenta, QString fecha, QString concepto, QString debe, QString haber, QString dif_conciliacion, bool conciliado) {
    QString cadena ="insert into ajustes_conci (cuenta,fecha,concepto,debe,haber,dif_conciliacion,conciliado) "
        "values ('";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "','";
    cadena += fecha;
    cadena += "','";
    cadena += concepto.left(-1).replace("'","''");
    cadena += "',";
    cadena += debe.left(-1).replace("'","''");
    cadena += ",";
    cadena += haber.left(-1).replace("'","''");
    cadena += ",'";
    cadena += dif_conciliacion.left(-1).replace("'","''");
    cadena += "',";
    if (cualControlador() == SQLITE) {
        if (conciliado) { cadena += "1"; }
        else { cadena += "0"; }
    }
    else {
        if (conciliado) { cadena += "true"; }
        else { cadena += "false"; }
    }
    cadena+=")";
    ejecutar(cadena);
}

// 
void basedatos::insertVar_as_modelo (QString asientomodelo, QString variable, QString tipo,
                                     QString descripcion, QString valor, int veces) {
    QString cadquery="INSERT into var_as_modelo (asientomodelo,variable,tipo,descripcion,valor,orden) values('";
    cadquery += asientomodelo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += variable.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += tipo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += valor.left(-1).replace("'","''");
    cadquery += "',";
    QString cadnum;
    cadnum.setNum(veces + 1);
    cadquery += cadnum;
    cadquery += ")";

    ejecutar(cadquery); 
}


void basedatos::insertVar_as_modelo(QString asientomodelo, QString variable, QString tipo, QString descripcion,
                                 QString valor, bool inicializa,int veces) {
    QString cadquery="INSERT into var_as_modelo (asientomodelo,variable,tipo,"
                     "descripcion,valor,guardar,orden) values('";
    cadquery += asientomodelo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += variable.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += tipo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += valor.left(-1).replace("'","''");
    cadquery += "',";

    if (inicializa)
      {
        if ( basedatos::instancia()->cualControlador() == SQLITE )
          cadquery += "1,";
         else  cadquery += "true,";
      }
      else
          {
            if ( basedatos::instancia()->cualControlador() == SQLITE )
              cadquery += "0,";
             else  cadquery += "false,";
          }

    QString cadnum;
    cadnum.setNum(veces + 1);
    cadquery += cadnum;
    cadquery += ")";

    ejecutar(cadquery); 
}

// 
void basedatos::insertDet_as_modelo (QString asientomodelo, QString cuenta, QString concepto,
                                     QString expresion, QString d_h, QString ci, QString baseiva,
                                     QString cuentabaseiva, QString cuentafra, QString claveiva,
                                     QString documento, QString fecha_fact,
                                     QString prorrata_e, bool rectificativa,
                                     QString fechaop, QString clave_op, QString bicoste,
                                     QString rectificada, QString numfacts, QString facini, QString facfinal,
                                     bool binversion, QString afecto, bool agrario,
                                     QString nombre, QString cif,
                                     QString cta_retenido, bool arrendamiento,
                                     QString clave, QString base_ret,
                                     QString tipo_ret, QString retencion, QString ing_a_cta,
                                     QString ing_a_cta_rep, QString nombre_ret,
                                     QString cif_ret, QString provincia,
                                     int veces) {
    QString cadquery = "INSERT into det_as_modelo (asientomodelo, cuenta, concepto,expresion, ";
    cadquery += "d_h, ci, baseiva, cuentabaseiva, cuentafra, claveiva, documento, fecha_factura,"
                "prorrata_e, rectificativa, fecha_op, clave_op, bicoste, "
                "rectificada, numfacts, facini, facfinal, bien_inversion, afecto,"
                "agrario, nombre, cif, cta_retenido, arrendamiento, clave, base_ret, "
                "tipo_ret, retencion, ing_a_cta, ing_a_cta_rep, nombre_ret, "
                "cif_ret, provincia, orden) values('";


    cadquery += asientomodelo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += concepto.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += convapunto(expresion.left(-1).replace("'","''"));
    cadquery += "','";
    cadquery += d_h.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += convapunto(ci.left(-1).replace("'","''"));
    cadquery += "','";
    cadquery += convapunto(baseiva.left(-1).replace("'","''"));
    cadquery += "','";
    cadquery += cuentabaseiva.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += cuentafra.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += claveiva.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += documento.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += fecha_fact.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += prorrata_e.left(-1).replace("'","''");
    cadquery += "',";
    if ( cualControlador() == SQLITE )
        cadquery+= rectificativa ? "1" : "0";
      else cadquery+= rectificativa ? "true" : "false" ;
    cadquery += ",'";
    cadquery += fechaop.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += clave_op.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += bicoste.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += rectificada.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += numfacts.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += facini.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += facfinal.left(-1).replace("'","''");
    cadquery += "',";
    if ( cualControlador() == SQLITE )
        cadquery+= binversion ? "1" : "0";
      else cadquery+= binversion ? "true" : "false" ;
    cadquery += ",'";
    cadquery += afecto.left(-1).replace("'","''");
    cadquery += "',";
    if ( cualControlador() == SQLITE )
        cadquery+= agrario ? "1" : "0";
      else cadquery+= agrario ? "true" : "false" ;
    cadquery += ",'";
    cadquery += nombre.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += cif.left(-1).replace("'","''");
    cadquery += "', '";
    cadquery += cta_retenido;
    cadquery += "', ";
    if ( cualControlador() == SQLITE )
         cadquery+= arrendamiento ? "1" : "0";
      else cadquery+= arrendamiento ? "true" : "false" ;
    cadquery += ", '";
    cadquery += clave;
    cadquery += "', '";
    cadquery += base_ret;
    cadquery += "', '";
    cadquery += tipo_ret;
    cadquery += "', '";
    cadquery += retencion;
    cadquery += "', '";
    cadquery += ing_a_cta;
    cadquery += "', '";
    cadquery += ing_a_cta_rep;
    cadquery += "', '";
    cadquery += nombre_ret;
    cadquery += "', '";
    cadquery += cif_ret;
    cadquery += "', '";
    cadquery += provincia;
    cadquery += "', ";

    /*    cadena+="cta_retenido varchar(255) default '',";
        if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
            cadena += "arrendamiento bool default 0,";
           else cadena += "arrendamiento bool default false,";
        cadena+="clave       varchar(255) default '',";
        cadena+="base_ret    varchar(255) default '',";
        cadena+="tipo_ret    varchar(255) default '',";
        cadena+="retencion   varchar(255) default '',";
        cadena+="ing_a_cta   varchar(255) default '',";
        cadena+="ing_a_cta_rep varchar(255) default '',";
        cadena+="nombre_ret    varchar(255) default '',";
        cadena+="cif_ret      varchar(255) default '',";
        cadena+="provincia    varchar(255) default '',"; */

    QString cadnum;
    cadnum.setNum(veces+1);
    cadquery += cadnum;
    cadquery += ")";

    ejecutar(cadquery); 
}

// 
void basedatos::insertEquivalenciasplan (QString cod, QString equiv) {
    QString cadena = "insert into equivalenciasplan (cod,equiv) VALUES ('";
    cadena += cod.left(-1).replace("'","''");
    cadena += "','";
    cadena += equiv.left(-1).replace("'","''");
    cadena += "')";
    ejecutar(cadena);
}

// 
void basedatos::insertPlancontablenuevoplan () {
    ejecutar("insert into plancontable select codigo,descripcion,auxiliar from nuevoplan");
}

void basedatos::insertPlancontablenuevoplan9999 () {
    if (cualControlador()==SQLITE || cualControlador()==POSTGRES)
      {
       ejecutar("insert into plancontable select '9999'||codigo,descripcion,auxiliar from nuevoplan");
      }
       else
         ejecutar("insert into plancontable select concat('9999',codigo),descripcion,auxiliar from nuevoplan");
}


void basedatos::borradeplancontable_NO_9999 () {
    if (cualControlador()==SQLITE)
       ejecutar("delete from plancontable where substr(codigo,1,4)!='9999'");
    if (cualControlador()==POSTGRES)
       ejecutar("delete from plancontable where substring(codigo from 1 for 4)!='9999'");
    if (cualControlador()==MYSQL)
       ejecutar("delete from plancontable where left(codigo,4)!='9999'");

}

void basedatos::duplicaplancontable_NO_9999 () {
    ejecutar("insert into plancontable select codigo,descripcion,auxiliar from nuevoplan");
}

void basedatos::borradeplancontable9999 () {
    if (cualControlador()==SQLITE)
       ejecutar("delete from plancontable where substr(codigo,1,4)='9999'");
    if (cualControlador()==POSTGRES)
       ejecutar("delete from plancontable where substring(codigo from 1 for 4)='9999'");
    if (cualControlador()==MYSQL)
       ejecutar("delete from plancontable where left(codigo,4)='9999'");

}


//
void basedatos::insert11Diario (QString cadnumasiento, qlonglong pase, QString cadfecha,
                                QString cuentagasto, double debe, double haber,
                                QString concepto, QString documento, QString usuario,
                                QString ejercicio) {
    QString cad1="INSERT into diario (asiento,pase,fecha,cuenta,debe,haber,concepto,"
        "documento,diario,usuario,ci,ejercicio) VALUES(";
    cad1 += cadnumasiento.left(-1).replace("'","''") +",";
    QString cadnumpase;
	cadnumpase.setNum(pase);
	cad1 += cadnumpase +",'";
	cad1 += cadfecha.left(-1).replace("'","''") +"','";
	cad1 += cuentagasto.left(-1).replace("'","''") +"',";
    QString cadimporte;
	cadimporte.setNum(debe,'f',2);
	cad1 += cadimporte;
	cad1 += ",";
	cadimporte.setNum(haber,'f',2);
    cad1 += cadimporte;
    cad1 += ",'";
	cad1 += concepto.left(-1).replace("'","''");
	cad1 += "','";
    cad1 += documento.left(-1).replace("'","''");
    cad1 += "','',";
    if (!usuario.isEmpty()) cad1+="'"+usuario+"'";
       else
            {
             if (cualControlador() == SQLITE) { cad1 += "'"+ usuario_sistema() +"'"; }
               else { cad1 += "CURRENT_USER"; }
            }
    cad1 += ",'','";
    cad1 += ejercicio;
    cad1 +="')";
    ejecutar(cad1);
}



void basedatos::insertpegaDiario (QString cadnumasiento, qlonglong pase,
                                  QString cadfecha, QString cuentagasto,
                                  double debe, double haber, QString concepto,
                                  QString documento, QString ci,
                                  QString usuario, QString ejercicio) {
    QString cad1="INSERT into diario (asiento,pase,fecha,cuenta,debe,haber,concepto,"
        "documento,diario,usuario,ci, ejercicio) VALUES(";
    cad1 += cadnumasiento.left(-1).replace("'","''") +",";
    QString cadnumpase;
	cadnumpase.setNum(pase);
	cad1 += cadnumpase +",'";
	cad1 += cadfecha.left(-1).replace("'","''") +"','";
	cad1 += cuentagasto.left(-1).replace("'","''") +"',";
    QString cadimporte;
	cadimporte.setNum(debe,'f',2);
	cad1 += cadimporte;
	cad1 += ",";
	cadimporte.setNum(haber,'f',2);
    cad1 += cadimporte;
    cad1 += ",'";
	cad1 += concepto.left(-1).replace("'","''");
	cad1 += "','";
    cad1 += documento.left(-1).replace("'","''");
    cad1 += "','',";
    if (!usuario.isEmpty()) cad1+="'"+usuario+"'";
       else
           {
            if (cualControlador() == SQLITE) { cad1 += "'"+ usuario_sistema() +"'"; }
              else { cad1 += "CURRENT_USER"; }
          }
    cad1 += ",'";
    cad1+=ci.left(-1).replace("'","''");
    cad1+="','";
    cad1+=ejercicio;
    cad1+="')";
    ejecutar(cad1);
}



// 
void basedatos::insert12Diario (QString cadnumasiento, qlonglong pase, QString cadfecha,
                                QString cuentagasto, double debe, double haber,
                                QString concepto, QString ci, QString usuario,
                                QString ejercicio) {
    QString cad1="INSERT into diario (asiento,pase,fecha,cuenta,debe,haber,concepto,"
        "documento,diario,usuario,ci,ejercicio) VALUES(";
    cad1 += cadnumasiento.left(-1).replace("'","''") +",";
    QString cadnumpase;
	cadnumpase.setNum(pase);
	cad1 += cadnumpase +",'";
	cad1 += cadfecha.left(-1).replace("'","''") +"','";
	cad1 += cuentagasto.left(-1).replace("'","''") +"',";
    QString cadimporte;
	cadimporte.setNum(debe,'f',2);
	cad1 += cadimporte;
	cad1 += ",";
	cadimporte.setNum(haber,'f',2);
    cad1 += cadimporte;
    cad1 += ",'";
	cad1 += concepto.left(-1).replace("'","''");
	cad1 += "','','',";
    if (!usuario.isEmpty()) cad1+="'"+usuario+"'";
       else
           {
            if (cualControlador() == SQLITE) { cad1 += "'"+ usuario_sistema() +"'"; }
            else { cad1 += "CURRENT_USER"; }
           }
    cad1 += ",'";
    cad1+=ci.left(-1).replace("'","''");
    cad1+="','";
    cad1+=ejercicio;
    cad1+="')";
    ejecutar(cad1);
}

int basedatos::recepcion_mas_datos_diario(QString pase, QString *asiento, QString *ejercicio)
{
  QString cadena="select asiento, nrecepcion, ejercicio from diario where pase=";
  cadena+=pase;
  QSqlQuery query = ejecutar(cadena);
  if ( (query.isActive()) && (query.first()) )
  {
      *asiento=query.value(0).toString();
      *ejercicio=query.value(2).toString();
      return query.value(1).toInt();
  }
  else return 0;
}

void basedatos::actu_recep_asto_ejercicio(QString cadnum, QString asiento, QString ejercicio)
{
  QString cadena="update diario set nrecepcion=";
  cadena+=cadnum;
  cadena+=" where asiento=";
  cadena+=asiento;
  cadena+=" and ejercicio='";
  cadena+=ejercicio;
  cadena+="'";
  ejecutar(cadena);
}

//
void basedatos::asigna_clave_ci_apunte(QString apunte, QString clave_ci)
{
  QString cadena="update diario set clave_ci=";
  cadena+=clave_ci;
  cadena+=" where pase=";
  cadena+=apunte;
  ejecutar(cadena);
}

// 
void basedatos::insertDiario (QString cadnumasiento, qlonglong pase, QString cadfecha,
                              QString cuentagasto, double debe, double haber,
                              QString concepto, QString documento, QString diario,
                              QString ci, QString usuario, QString clave_ci,
                              QString ejercicio, QString nrecepcion, bool hay_fecha_factura,
                              QDate fecha_factura) {
    QString cad1="INSERT into diario (asiento,pase,fecha, cuenta,debe, haber,concepto,"
        "documento,diario,usuario,ci, clave_ci, ejercicio, nrecepcion, "
        "fecha_factura) VALUES(";
    cad1 += cadnumasiento.left(-1).replace("'","''") +",";
    QString cadnumpase;
	cadnumpase.setNum(pase);
	cad1 += cadnumpase +",'";
	cad1 += cadfecha.left(-1).replace("'","''") +"','";
	cad1 += cuentagasto.left(-1).replace("'","''") +"',";
    QString cadimporte;
	cadimporte.setNum(debe,'f',2);
	cad1 += cadimporte;
	cad1 += ",";
	cadimporte.setNum(haber,'f',2);
    cad1 += cadimporte;
    cad1 += ",'";
	cad1 += concepto.left(-1).replace("'","''");
	cad1 += "','";
    cad1 += documento.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += diario.left(-1).replace("'","''");
    cad1 += "',";
    if (!usuario.isEmpty())
      {
        cad1+="'";
        cad1+=usuario;
        cad1+="'";
      }
      else
         {
           if (cualControlador() == SQLITE) { cad1 += "'"+ usuario_sistema() +"'"; }
             else { cad1 += "CURRENT_USER"; }
         }
    cad1 += ",'";
    cad1 += ci.left(-1).replace("'","''");
    cad1 += "',";
    cad1 += clave_ci.isEmpty() ? "0" : clave_ci.left(-1).replace("'","''");
    cad1 += ",'";
    cad1 += ejercicio.left(-1).replace("'","''");
    cad1 += "',";
    QString cad;
    if (nrecepcion.isEmpty() || nrecepcion.toLongLong()==0) cad="0";
       else cad=nrecepcion;
    cad1+=cad;
    cad1+=", '";
    if (hay_fecha_factura) cad1+=fecha_factura.toString("yyyy-MM-dd");
      else cad1+=cadfecha;
    cad1+="')";
    ejecutar(cad1);
}



void basedatos::insertDiario_imp (QString cadnumasiento, qlonglong pase, QString cadfecha,
                              QString cuentagasto, double debe, double haber,
                              QString concepto, QString documento, QString diario,
                              QString ci, QString usuario, QString clave_ci,
                              QString ejercicio, QString codfactura) {
    QString cad1="INSERT into diario (asiento,pase,fecha, cuenta,debe, haber,concepto,"
        "documento,diario,usuario,ci, clave_ci, ejercicio, codfactura) VALUES(";
    cad1 += cadnumasiento.left(-1).replace("'","''") +",";
    QString cadnumpase;
        cadnumpase.setNum(pase);
        cad1 += cadnumpase +",'";
        cad1 += cadfecha.left(-1).replace("'","''") +"','";
        cad1 += cuentagasto.left(-1).replace("'","''") +"',";
    QString cadimporte;
        cadimporte.setNum(debe,'f',2);
        cad1 += cadimporte;
        cad1 += ",";
        cadimporte.setNum(haber,'f',2);
    cad1 += cadimporte;
    cad1 += ",'";
        cad1 += concepto.left(-1).replace("'","''");
        cad1 += "','";
    cad1 += documento.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += diario.left(-1).replace("'","''");
    cad1 += "',";
    if (!usuario.isEmpty())
      {
        cad1+="'";
        cad1+=usuario;
        cad1+="'";
      }
      else
         {
           if (cualControlador() == SQLITE) { cad1 += "'"+ usuario_sistema() +"'"; }
             else { cad1 += "CURRENT_USER"; }
         }
    cad1 += ",'";
    cad1 += ci.left(-1).replace("'","''");
    cad1 += "',";
    cad1 += clave_ci.isEmpty() ? "0" : clave_ci.left(-1).replace("'","''");
    cad1 += ",'";
    cad1 += ejercicio.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += codfactura;
    cad1 += "')";
    ejecutar(cad1);
}






// 
void basedatos::insert7Amortfiscalycontable(QString ejercicio, QString cuenta_activo,
                                            QString codigo_activo, QString cuenta_am_acum,
                                            QString cuenta_gasto, double dotacion_contable,
                                            double dotacion_fiscal) {
    QString cad1 = "INSERT into amortfiscalycontable (ejercicio,cuenta_activo,codigo_activo,";
    cad1 += "cuenta_am_acum,cuenta_gasto,dotacion_contable,dotacion_fiscal) values ('";
    cad1 += ejercicio.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += cuenta_activo.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += codigo_activo.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += cuenta_am_acum.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += cuenta_gasto.left(-1).replace("'","''");
    cad1 += "',";
    QString cadimporte;
    cadimporte.setNum(dotacion_contable,'f',2);
    cad1 += cadimporte;
    cad1 += ",";
    cadimporte.setNum(dotacion_fiscal,'f',2); 
    cad1 += cadimporte;
    cad1 += ")";
    ejecutar(cad1);
}


void basedatos::insert7Amortfiscalycontable_mes(QString ejercicio, int mes, QString cuenta_activo,
                                            QString codigo_activo, QString cuenta_am_acum,
                                            QString cuenta_gasto, double dotacion_contable,
                                            double dotacion_fiscal) {
    QString cad1 = "INSERT into amortfiscalycontable (ejercicio,cuenta_activo,codigo_activo,";
    cad1 += "cuenta_am_acum,cuenta_gasto,dotacion_contable,dotacion_fiscal, mes) values ('";
    cad1 += ejercicio.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += cuenta_activo.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += codigo_activo.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += cuenta_am_acum.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += cuenta_gasto.left(-1).replace("'","''");
    cad1 += "',";
    QString cadimporte;
    cadimporte.setNum(dotacion_contable,'f',2);
    cad1 += cadimporte;
    cad1 += ",";
    cadimporte.setNum(dotacion_fiscal,'f',2);
    cad1 += cadimporte;
    cad1 += ", ";
    QString cadmes; cadmes.setNum(mes);
    cad1 += cadmes;
    cad1 += ")";
    ejecutar(cad1);
}



// 
void basedatos::insertAmortcontable (QString ejercicio, QString asiento) {
    QString cad1 = "insert into amortcontable (ejercicio,asiento) values ('";
    cad1 += ejercicio.left(-1).replace("'","''");
    cad1 += "',";
    cad1 += asiento.left(-1).replace("'","''");
    cad1 += ")";
    ejecutar(cad1);
}

// 
void basedatos::insert20Cabeceraestados (QString titulo, QString cabecera, QString parte1,
                                         QString parte2, QString observaciones, QString formulabasepor,
                                         QString fechacalculo, QString ejercicio1, QString ejercicio2,
                                         bool analitica, bool grafico, bool haycolref, QString colref,
                                         QString diarios, bool estadosmedios, double valorbasepor1,
                                         double valorbasepor2, bool desglose, bool desglosectas,
                                         QString des_cabecera,
                                         QString des_pie) {
    QString cadquery = "INSERT into cabeceraestados "
        "(titulo, cabecera, parte1, parte2, observaciones, formulabasepor, fechacalculo, horacalculo,"
        "ejercicio1, ejercicio2, analitica, grafico, haycolref, colref,"
        "diarios, estadosmedios,"
        "valorbasepor1, valorbasepor2, desglose, desglose_ctas, des_cabecera, des_pie) values ('";
    cadquery += titulo.left(-1).replace("'","''").trimmed() +"','";
    cadquery += cabecera.left(-1).replace("'","''") +"','";
    cadquery += parte1.left(-1).replace("'","''") +"','";
    cadquery += parte2.left(-1).replace("'","''") +"','";
    cadquery += observaciones.left(-1).replace("'","''") +"','";
    cadquery += formulabasepor.left(-1).replace("'","''") +"','";
    cadquery += fechacalculo.left(-1).replace("'","''") +"','";
    cadquery += "','"; // horacalculo vacía
    cadquery += ejercicio1.left(-1).replace("'","''") +"','";
    cadquery += ejercicio2.left(-1).replace("'","''") +"',";
    if (cualControlador() == SQLITE) {
        if (analitica) cadquery += "1,"; else cadquery += "0,";
    }
    else {
        if (analitica) cadquery += "true,"; else cadquery += "false,";
    }
    if (cualControlador() == SQLITE) {
        if (grafico) cadquery += "1,"; else cadquery += "0,";
    }
    else {
        if (grafico) cadquery += "true,"; else cadquery += "false,";
    }
    if (cualControlador() == SQLITE) {
        if (haycolref) cadquery += "1,"; else cadquery += "0,";
    }
    else {
        if (haycolref) cadquery += "true,"; else cadquery += "false,";
    }
    if (haycolref) {
        cadquery += "'";
        cadquery += colref.left(-1).replace("'","''");
        cadquery += "','";
    }
    else cadquery+="'','";

    cadquery += diarios;
    cadquery += "',";
    if (cualControlador() == SQLITE) {
        if (estadosmedios) cadquery+="1,"; else cadquery+="0,";
    }
    else {
        if (estadosmedios) cadquery+="true,"; else cadquery+="false,";
    }
    QString cadval;
    cadval.setNum(valorbasepor1,'f',2);
    cadquery += cadval +",";
    cadval.setNum(valorbasepor2,'f',2);
    cadquery += cadval +",";
    if (cualControlador() == SQLITE) {
        if (desglose) cadquery += "1,"; else cadquery += "0,";
    }
    else {
        if (desglose) cadquery += "true,"; else cadquery += "false,";
    }
    if (cualControlador() == SQLITE) {
        if (desglosectas) cadquery += "1,"; else cadquery += "0,";
    }
    else {
        if (desglosectas) cadquery += "true,"; else cadquery += "false,";
    }
    cadquery += "'";
    cadquery += des_cabecera.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += des_pie.left(-1).replace("'","''");
    cadquery += "')";

    ejecutar(cadquery);
}

// 
void basedatos::insert8Estados (QString titulo, QString apartado, bool parte1, QString clave, QString nodo, QString formula, double importe1, double importe2) {
    QString cadquery = "INSERT into estados (titulo,apartado,parte1,clave,nodo,formula,importe1,importe2) values ('";
    cadquery += titulo.left(-1).replace("'","''").trimmed();
    cadquery += "','";
    cadquery += apartado.left(-1).replace("'","''");
    cadquery += "',";
    if (cualControlador() == SQLITE) {
        if (parte1) { cadquery += "1"; }
        else { cadquery += "0"; }
    }
    else {
        if (parte1) { cadquery += "TRUE"; }
        else { cadquery += "FALSE"; }
    }
    cadquery += ",'";
    cadquery += clave.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += nodo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += formula.left(-1).replace("'","''");
    cadquery += "',";
    QString valor;
    valor.setNum(importe1,'f',2);
    cadquery += valor +",";
    valor.setNum(importe2,'f',2);
    cadquery += valor +")";
    ejecutar(cadquery);
}

// 
void basedatos::insert6Estados (QString titulo, QString apartado, bool parte1, QString clave, QString nodo, QString formula) {
    QString cadquery = "INSERT into estados (titulo,apartado,parte1,clave,nodo,formula) values ('";
    cadquery += titulo.left(-1).replace("'","''").trimmed();
    cadquery += "','";
    cadquery += apartado.left(-1).replace("'","''");
    if (cualControlador() == SQLITE) {
        if (parte1) { cadquery += "',1,'"; }
        else { cadquery += "',0,'"; }
    }
    else {
        if (parte1) { cadquery += "',TRUE,'"; }
        else { cadquery += "',FALSE,'"; }
    }
    cadquery += clave.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += nodo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += formula.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}

// 
void basedatos::insertPlanamortizaciones (QString cuenta_activo, QDate fecha_func,
                                          QString codigo_activo, QString cuenta_am_acum,
                                          QString cuenta_gasto, QString valor_residual,
                                          QString coef_amort_contable,
                                          QString coef_amort_fiscal,
                                          bool amort_fiscal_1ejer,
                                          bool amort_personalizada,
                                          bool baja,
                                          QDate fecha_baja,
                                          QString motivo_baja,
                                          QString cuenta_proveedor) {
    QString cadquery = "insert into planamortizaciones (cuenta_activo, fecha_func, codigo_activo,";
    cadquery += "cuenta_am_acum,cuenta_gasto, valor_residual,coef_amort_contable,";
    cadquery += "coef_amort_fiscal,amort_fiscal_1ejer,amort_personalizada,"
                "baja, fecha_baja, motivo_baja, cuenta_proveedor) values ('";
    cadquery += cuenta_activo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += fecha_func.toString("yyyy-MM-dd");
    cadquery += "','";
    cadquery += codigo_activo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += cuenta_am_acum.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += cuenta_gasto.left(-1).replace("'","''");
    cadquery += "',";
    if (valor_residual.length()>0) cadquery += convapunto(valor_residual);
    else cadquery += "0";
    cadquery += ",";
    if (coef_amort_contable.length()>0)
	{
	    cadquery += convapunto(coef_amort_contable);
	    cadquery += "/100.0";
	}
    else cadquery += "0";
    cadquery += ",";
    if (coef_amort_fiscal.length()>0)
	{
	    cadquery += convapunto(coef_amort_fiscal);
	    cadquery += "/100.0";
	}
    else cadquery += "0";
    cadquery += ",";
    if (cualControlador() == SQLITE) {
        if (amort_fiscal_1ejer) cadquery += "1,"; else cadquery += "0,";
    }
    else {
        if (amort_fiscal_1ejer) cadquery += "TRUE,"; else cadquery += "FALSE,";
    }

    if (cualControlador() == SQLITE) {
        if (amort_personalizada) cadquery += "1"; else cadquery += "0";
    }
    else {
        if (amort_personalizada) cadquery += "TRUE"; else cadquery += "FALSE";
    }

    cadquery+=" ,";

    if (cualControlador() == SQLITE) {
        if (baja) cadquery += "1"; else cadquery += "0";
    }
    else {
        if (baja) cadquery += "TRUE"; else cadquery += "FALSE";
    }
    cadquery +=", '";
    cadquery += fecha_baja.toString("yyyy-MM-dd");
    cadquery +="', '";
    cadquery += motivo_baja;
    cadquery +="', '";
    cadquery += cuenta_proveedor;
    cadquery +="'";
    cadquery += ")";
    ejecutar(cadquery);
}

// 
void basedatos::insertAmortpers (QString cuenta, QString ejercicio, QString importe) {
    QString cadquery = "insert into amortpers (cuenta,ejercicio,importe) values ('";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += ejercicio.left(-1).replace("'","''");
    cadquery += "',";
    if (importe.length()>0)
        cadquery += convapunto(importe);
    else cadquery += "0";
    cadquery += ")";
    ejecutar(cadquery);
}

// 
void basedatos::insertCi_amort (QString cuenta, QString ci, QString asignacion) {
    QString cadquery = "insert into ci_amort (cuenta,ci,asignacion) values ('";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += ci.left(-1).replace("'","''");
    cadquery += "',";
    cadquery += asignacion.left(-1).replace("'","''");
    cadquery += ")";
    ejecutar(cadquery);
}

// 
void basedatos::insert6Ejercicios (QString codigo, QDate apertura, QDate cierre,
                                   bool cerrado, bool cerrando) {
    QString cadena = "insert into ejercicios (codigo,apertura,cierre,cerrado,"
                     "cerrando,prox_asiento) values ('";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "','";
    cadena += apertura.toString("yyyy-MM-dd");
    cadena += "','";
    cadena += cierre.toString("yyyy-MM-dd");
    cadena += "',";
    if (cualControlador() == SQLITE) {
        if (cerrado) { cadena += "1"; }
        else { cadena += "0"; }
        cadena += ",";
        if (cerrando) { cadena += "1"; }
        else { cadena += "0"; }
    }
    else
         {
          if (cerrado) cadena+= "TRUE";
             else cadena+="FALSE";
          cadena+=",";
          if (cerrando) cadena+= "TRUE";
             else cadena+="FALSE";
         }
    cadena +=",";
    cadena += diario_vacio() ? "1" : "2";
    cadena += ")";

    ejecutar(cadena);
}

void basedatos::borra_venci_pase_diario_operacion(QString pase)
{
    if (pase.isEmpty()) pase="0";
    QString cadena="delete from vencimientos where pase_diario_operacion=";
    cadena+=pase;
    ejecutar(cadena);
}

// 
void basedatos::insertVencimientos ( QString num, QString cta_ordenante, QDate fecha_operacion,
                                     QString importe, QString cuenta_tesoreria,
                                     QDate fecha_vencimiento, bool derecho, bool pendiente,
                                     bool anulado, QString pase_diario_operacion,
                                     QString pase_diario_vencimiento, QString concepto) {
    QString vdomiciliable;
    bool domiciliable=cuenta_domiciliable(cta_ordenante);
    if (cualControlador()==SQLITE) vdomiciliable="0"; else vdomiciliable="false";
    if ( domiciliable && cualControlador()==SQLITE) vdomiciliable="1";
    if ( domiciliable && cualControlador()!=SQLITE) vdomiciliable="true";

    QString cad1 = "INSERT into vencimientos (num,cta_ordenante,fecha_operacion,importe,";
    cad1 += "cuenta_tesoreria, fecha_vencimiento,derecho,pendiente,anulado,";
    cad1 += "pase_diario_operacion,pase_diario_vencimiento,concepto,domiciliable, usuario) VALUES(";
    cad1 += num.left(-1).replace("'","''");
    cad1 += ",'";
    cad1 += cta_ordenante.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += fecha_operacion.toString("yyyy-MM-dd");
    cad1 += "',";
    cad1 += importe.left(-1).replace("'","''");
    cad1 += ",'";
    cad1 += cuenta_tesoreria.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += fecha_vencimiento.toString("yyyy-MM-dd");
    cad1 += "',";
    if (cualControlador() == SQLITE) {
        if (derecho) cad1+="1"; else cad1+="0";
    }
    else {
        if (derecho) cad1+="TRUE"; else cad1+="FALSE";
    }
    cad1 += ",";
    if (cualControlador() == SQLITE) {
        if (pendiente) cad1+="1"; else cad1+="0";
    }
    else {
        if (pendiente) cad1+="TRUE"; else cad1+="FALSE";
    }
    cad1 += ",";
    if (cualControlador() == SQLITE) {
        if (anulado) cad1+="1"; else cad1+="0";
    }
    else {
        if (anulado) cad1+="TRUE"; else cad1+="FALSE";
    }
    cad1 += ",";
    cad1 += pase_diario_operacion.left(-1).replace("'","''");
    cad1 += ",";
    if (pase_diario_vencimiento.isEmpty()) pase_diario_vencimiento="0";
    cad1 += pase_diario_vencimiento.left(-1).replace("'","''");
    cad1 += ",'";
    cad1 += concepto.left(-1).replace("'","''");
    cad1 += "',";
    cad1 += vdomiciliable;
    cad1 += ", ";
    if (cualControlador() == SQLITE) { cad1 += "'"+ usuario_sistema() +"'"; }
    else { cad1 += "CURRENT_USER"; }
    cad1 += " )";
    ejecutar(cad1);
}


//
void basedatos::insertVencimientos_plantilla ( QString num, QString cta_ordenante, QDate fecha_operacion,
                                     QString importe, QString cuenta_tesoreria,
                                     QDate fecha_vencimiento, bool derecho, bool pendiente,
                                     bool anulado, QString pase_diario_operacion,
                                     QString pase_diario_vencimiento, QString concepto,
                                     bool domiciliable) {
    QString vdomiciliable;
    // bool domiciliable=cuenta_domiciliable(cta_ordenante);
    if (cualControlador()==SQLITE) vdomiciliable="0"; else vdomiciliable="false";
    if ( domiciliable && cualControlador()==SQLITE) vdomiciliable="1";
    if ( domiciliable && cualControlador()!=SQLITE) vdomiciliable="true";

    QString cad1 = "INSERT into vencimientos (num,cta_ordenante,fecha_operacion,importe,";
    cad1 += "cuenta_tesoreria, fecha_vencimiento,derecho,pendiente,anulado,";
    cad1 += "pase_diario_operacion,pase_diario_vencimiento,concepto,domiciliable,"
            " usuario) VALUES(";
    cad1 += num.left(-1).replace("'","''");
    cad1 += ",'";
    cad1 += cta_ordenante.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += fecha_operacion.toString("yyyy-MM-dd");
    cad1 += "',";
    cad1 += importe.left(-1).replace("'","''");
    cad1 += ",'";
    cad1 += cuenta_tesoreria.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += fecha_vencimiento.toString("yyyy-MM-dd");
    cad1 += "',";
    if (cualControlador() == SQLITE) {
        if (derecho) cad1+="1"; else cad1+="0";
    }
    else {
        if (derecho) cad1+="TRUE"; else cad1+="FALSE";
    }
    cad1 += ",";
    if (cualControlador() == SQLITE) {
        if (pendiente) cad1+="1"; else cad1+="0";
    }
    else {
        if (pendiente) cad1+="TRUE"; else cad1+="FALSE";
    }
    cad1 += ",";
    if (cualControlador() == SQLITE) {
        if (anulado) cad1+="1"; else cad1+="0";
    }
    else {
        if (anulado) cad1+="TRUE"; else cad1+="FALSE";
    }
    cad1 += ",";
    cad1 += pase_diario_operacion.left(-1).replace("'","''");
    cad1 += ",";
    if (pase_diario_vencimiento.isEmpty()) pase_diario_vencimiento="0";
    cad1 += pase_diario_vencimiento.left(-1).replace("'","''");
    cad1 += ",'";
    cad1 += concepto.left(-1).replace("'","''");
    cad1 += "',";
    cad1 += vdomiciliable;
    cad1 += ", ";
    if (cualControlador() == SQLITE) { cad1 += "'"+ usuario_sistema() +"'"; }
    else { cad1 += "CURRENT_USER"; }
    cad1 += " )";
    ejecutar(cad1);
}



// 
void basedatos::insertPeriodos (QString ejercicio, QString codigo, QDate fechaini, QDate fechafin) {
    QString cadena = "INSERT INTO periodos (ejercicio,codigo,inicio,fin) VALUES('";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "','";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "','";
    cadena += fechaini.toString("yyyy-MM-dd");
    cadena += "','";
    cadena += fechafin.toString("yyyy-MM-dd");
    cadena += "')";
    ejecutar(cadena);
}

void basedatos::actuperiodos(QString ejercicio, QString periodo, bool inhabilitado)
{
    QString cadena = "UPDATE periodos set ";
    if ( cualControlador() == SQLITE ) {
        if (inhabilitado) cadena += "deshabilitado = 1 ";
          else cadena += "deshabilitado = 0 ";
    }
    else {
           if (inhabilitado) cadena += "deshabilitado = true ";
             else cadena+= "deshabilitado = false ";
         }
    cadena += "where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' and codigo='";
    cadena += periodo.left(-1).replace("'","''");
    cadena += "';";
    ejecutar(cadena);
    // QMessageBox::warning( 0, QObject::tr("actuperiodos"),cadena);

}

// 
void basedatos::insertPresupuestocuentaejercicio (QString cuenta, QString ejercicio) {
    QString cadquery = "INSERT INTO presupuesto (cuenta,ejercicio) VALUES ('";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += ejercicio.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}

// 
void basedatos::insert6Cuenta_ext_conci(QString cuenta, QDate fecha, double debe, double haber, QString concepto, double saldo) {
    QString cadena="insert into cuenta_ext_conci (cuenta,fecha,debe,haber,concepto,saldo) values ('";
    cadena += cuenta;
    cadena += "','";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "',";
    QString cadnum;
    cadnum.setNum(debe,'f',2);
    cadena += cadnum;
    cadena += ",";
    cadnum.setNum(haber,'f',2);
    cadena += cadnum;
    cadena += ",'";
    cadena += concepto;
    cadena += "',";
    cadnum.setNum(saldo,'f',2);
    cadena += cadnum;
    cadena += ")";
    ejecutar(cadena);
}

void basedatos::actu_libro_iva(QString pase, QString pasefactura, QString cad_importe_factura)
{
  QString cadena="update libroiva set apunte_tot_factura=";
  cadena+=pasefactura;
  cadena+=", total_factura=";
  cadena+=cad_importe_factura;
  cadena+=" where pase=";
  cadena+=pase;
  ejecutar(cadena);
}

// 
void basedatos::insertLibroiva (QString pase, QString cta_base_iva, QString base_iva, QString clave_iva,
                                QString tipo_iva, QString tipo_re, QString cuota_iva, QString cuenta_fra,
                                QDate fecha_fra, bool soportado, bool aib, bool eib, QString prorrata,
                                bool rectificativa, bool autofactura,
                                QDate fechaop, QString claveop, QString bicoste,
                                QString rectificada, int nfacturas, QString finicial, QString ffinal,
                                bool autofactura_no_ue, bool pr_servicios_ue, bool binversion,
                                bool opnosujeta, QString afecto, bool agrario,
                                QString nombre, QString cif,
                                bool importacion, bool exportacion, bool exenta_no_deduc,
                                bool isp_op_interiores, bool caja_iva) {
    if (bicoste.length()==0) bicoste="0";
    QString cad1="INSERT into libroiva (pase, cta_base_iva, base_iva, clave_iva, tipo_iva, tipo_re,";
    cad1 += "cuota_iva, cuenta_fra, fecha_fra, soportado, aib, eib, prorrata, rectificativa,"
            " autofactura, fecha_operacion, clave_operacion, bi_costo,"
            " rectificada, nfacturas, finicial, ffinal,autofactura_no_ue, pr_servicios_ue,"
            " bien_inversion,op_no_sujeta,afecto, agrario, nombre, cif,"
            " importacion, exportacion, exento_dcho_ded, isp_op_interiores, caja_iva"
            ") VALUES(";
    cad1 += pase +",'";
    cad1 += cta_base_iva.left(-1).replace("'","''") +"',";
    cad1 += convapunto(base_iva) +",'";
    cad1 += clave_iva.left(-1).replace("'","''") +"',";
    cad1 += convapunto(tipo_iva) +",";
    if (tipo_re.length()==0) cad1 += "0";
    else cad1 += convapunto(tipo_re);
    cad1 += ",";
    cad1 += convapunto(cuota_iva) + ",'";
    cad1 += cuenta_fra.left(-1).replace("'","''"); // cueta_fra
    cad1 += "','";
    cad1 += fecha_fra.toString("yyyy-MM-dd") +"',";
    if (cualControlador() == SQLITE) {
        if (soportado) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (soportado) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (aib && (!autofactura) && (!autofactura_no_ue) && (!isp_op_interiores))
          { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (aib && (!autofactura)  && (!autofactura_no_ue) && (!isp_op_interiores))
          { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (eib && (!pr_servicios_ue)) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (eib && (!pr_servicios_ue)) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";
    if (prorrata.length() == 0) cad1+="1";
    else
    {
        double valprorrata = prorrata.toDouble()/100;
        QString cadnum;
        cadnum.setNum(valprorrata,'f',2);
        cad1 += cadnum;
    }
    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (rectificativa) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (rectificativa) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (autofactura) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (autofactura) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1 += ",'";
    cad1 += fechaop.toString("yyyy-MM-dd") +"','";
    cad1 += claveop.left(-1).replace("'","''");
    cad1 += "',";
    cad1 += bicoste.left(-1).replace("'","''");
    cad1 += ",'";
    cad1 += rectificada.left(-1).replace("'","''");
    cad1 += "',";
    QString cadn; cadn.setNum(nfacturas);
    cad1 += cadn;
    cad1 += ",'";
    cad1 += finicial.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += ffinal.left(-1).replace("'","''");
    cad1+="', ";

    if (cualControlador() == SQLITE) {
        if (autofactura_no_ue) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (autofactura_no_ue) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (pr_servicios_ue) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (pr_servicios_ue) { cad1 += "true"; }
        else { cad1 += "false"; }
    }

    cad1+=",";
    if (cualControlador() == SQLITE) {
        if (binversion) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (binversion) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (opnosujeta) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (opnosujeta) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";
    if (afecto.length() == 0) cad1+="1";
    else
    {
        double valafecto = convapunto(afecto).toDouble()/100;
        QString cadnum;
        cadnum.setNum(valafecto,'f',4);
        cad1 += cadnum;
    }

    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (agrario) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (agrario) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1 += ", '";
    cad1 += nombre.left(-1).replace("'","''") +"', '";
    cad1 += cif.left(-1).replace("'","''") +"'";

    //--------------------------------------------------------------
    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (importacion) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (importacion) { cad1 += "true"; }
        else { cad1 += "false"; }
    }

    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (exportacion) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (exportacion) { cad1 += "true"; }
        else { cad1 += "false"; }
    }

    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (!exenta_no_deduc) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (!exenta_no_deduc) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";

    if (cualControlador() == SQLITE) {
        if (isp_op_interiores) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (isp_op_interiores) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";

    if (cualControlador() == SQLITE) {
        if (caja_iva) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (caja_iva) { cad1 += "true"; }
        else { cad1 += "false"; }
    }

    //--------------------------------------------------------------
    cad1+=") ";
    ejecutar(cad1);
}


void basedatos::insertLibroivaAIB0 (QString pase, QString cta_base_iva, QString base_iva, QString clave_iva,
                                    QString tipo_iva, QString tipo_re, QString cuota_iva, QString cuenta_fra,
                                    QDate fecha_fra, QString prorrata,
                                    bool rectificativa, bool autofactura,
                                    QDate fechaop, QString claveop, QString bicoste,bool autofactura_no_ue,
                                    bool binversion) {
    // insertamos sólo libro facturas recibidas
    QString cad1="INSERT into libroiva (pase, cta_base_iva, base_iva, clave_iva, tipo_iva, tipo_re,";
    cad1 += "cuota_iva, cuenta_fra, fecha_fra, soportado, aib, eib, prorrata, rectificativa,"
            " autofactura, fecha_operacion, clave_operacion, bi_costo, autofactura_no_ue, "
            " bien_inversion) VALUES(";
    cad1 += pase +",'";
    cad1 += cta_base_iva.left(-1).replace("'","''") +"',";
    cad1 += convapunto(base_iva) +",'";
    cad1 += clave_iva.left(-1).replace("'","''") +"',";
    cad1 += convapunto(tipo_iva) +",";
    if (tipo_re.length()==0) cad1 += "0";
    else cad1 += convapunto(tipo_re);
    cad1 += ",";
    cad1 += convapunto(cuota_iva) + ",'";
    cad1 += cuenta_fra.left(-1).replace("'","''"); // cuenta_fra
    cad1 += "','";
    cad1 += fecha_fra.toString("yyyy-MM-dd") +"',";
    if (cualControlador() == SQLITE) cad1 += "1";
        else cad1 += "true";
    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (!autofactura && !autofactura_no_ue) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (!autofactura && !autofactura_no_ue) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";
    if (cualControlador() == SQLITE) cad1+="0";
      else cad1 += "false";
    cad1+=", ";
    if (prorrata.length() == 0) cad1+="1";
    else
    {
        double valprorrata = prorrata.toDouble()/100;
        QString cadnum;
        cadnum.setNum(valprorrata,'f',2);
        cad1 += cadnum;
    }
    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (rectificativa) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (rectificativa) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";
    if (cualControlador() == SQLITE) {
        if (autofactura) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (autofactura) { cad1 += "true"; }
        else { cad1 += "false"; }
    }
    cad1+=", ";
    cad1+="'";
    cad1+=fechaop.toString("yyyy-MM-dd");
    cad1+="','";
    cad1+=claveop;
    cad1+="',";
    cad1+=bicoste.isEmpty() ? "0" : convapunto(bicoste);
    cad1+=",";
    if (cualControlador() == SQLITE) {
        if (autofactura_no_ue) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (autofactura_no_ue) { cad1 += "true"; }
        else { cad1 += "false"; }
    }

    cad1+=",";
    if (cualControlador() == SQLITE) {
        if (binversion) { cad1 += "1"; }
        else { cad1 += "0"; }
    }
    else {
        if (binversion) { cad1 += "true"; }
        else { cad1 += "false"; }
    }

    cad1+=") ";
    ejecutar(cad1);

}




// 
void basedatos::insertBorrador (QString asiento, QString cuenta, QString concepto,
                                QString debe, QString haber, QString documento,
                                QString ctabaseiva, QString baseiva, QString claveiva,
                                QString tipoiva, QString tipore, QString cuotaiva,
                                QString cuentafra, QString diafra, QString mesfra,
                                QString anyofra, QString soportado, QString ci,
                                QString prorrata, QString rectificativa, QString autofactura,
                                QString fechaoperacion, QString claveop, QString biacoste,
                                QString rectificada, QString nfacturas, QString finicial, QString ffinal,
                                QString pr_serv_ue, QString autofactura_noue, QString bien_inversion,
                                QString sujeto, QString afecto,
                                QString col32, QString col33, QString col34, QString col35,
                                QString col36, QString col37, QString col38, QString col39,
                                QString col40, QString col41, QString col42, QString col43,
                                QString col44, QString col45, QString col46, QString col47,
                                QString col48, QString col49, QString col50,
                                QString usuario, QDate fecha) {
    QString cad1 = "insert into borrador ( asiento, usuario, cuenta, concepto, debe, haber,"
        "documento, ctabaseiva, baseiva, claveiva, tipoiva, tipore, cuotaiva, "
    	"cuentafra,diafra, mesfra, anyofra, soportado, ci,prorrata,"
        "rectificativa,autofactura, fecha_operacion, clave_operacion, biacoste, "
        "rectificada, nfacturas, finicial, ffinal, pr_serv_ue, autofactura_noue, "
        "bien_inversion, sujeto, afecto, fecha,"
        "col32, col33, col34, col35, col36, col37, col38, col39, col40, col41, "
        "col42, col43, col44, col45, col46, col47, col48, col49, col50 "
        " ) VALUES  (";
    cad1 += asiento + ","; // asiento
    if (!usuario.isEmpty())
        cad1 += "'"+ usuario +"','";
     else
      {
       if (cualControlador() == SQLITE) {
        cad1 += "'"+ usuario_sistema() +"','";
         }
       else {
        cad1 += "CURRENT_USER,'";
        }
      }
    cad1 += cuenta.left(-1).replace("'","''")+"','";
    cad1 += concepto.left(-1).replace("'","''")+"','";
    cad1 += convapunto(debe.left(-1).replace("'","''"))+"','"; //debe
    cad1 += convapunto(haber.left(-1).replace("'","''"))+"','"; //haber
    cad1 += documento.left(-1).replace("'","''")+"','"; //documento
    cad1 += ctabaseiva.left(-1).replace("'","''")+"','"; //ctabaseiva
    cad1 += convapunto(baseiva.left(-1).replace("'","''"))+"','"; //baseiva
    cad1 += claveiva.left(-1).replace("'","''")+"','"; //claveiva
    cad1 += convapunto(tipoiva.left(-1).replace("'","''"))+"','"; //tipoiva
    cad1 += convapunto(tipore.left(-1).replace("'","''"))+"','"; //tipore
    cad1 += convapunto(cuotaiva.left(-1).replace("'","''"))+"','"; //cuotaiva
    cad1 += cuentafra.left(-1).replace("'","''")+"','"; //cuentafra
    cad1 += diafra.left(-1).replace("'","''")+"','"; //diafra
    cad1 += mesfra.left(-1).replace("'","''")+"','"; //mesfra
    cad1 += anyofra.left(-1).replace("'","''")+"','"; //anyofra
    cad1 += soportado.left(-1).replace("'","''")+"','"; //soportado
    cad1 += ci.left(-1).replace("'","''")+"','"; //ci
    cad1 += prorrata.left(-1).replace("'","''")+"','"; //prorrata
    cad1 += rectificativa.left(-1).replace("'","''")+"','"; //rectificativa
    cad1 += autofactura.left(-1).replace("'","''")+"','"; //autofactura
    cad1 += fechaoperacion.left(-1).replace("'","''")+"','"; //fecha_operacion
    cad1 += claveop.left(-1).replace("'","''")+"','"; //clave_operacion
    cad1 += biacoste.left(-1).replace("'","''")+"','"; //biacoste
    cad1 += rectificada.left(-1).replace("'","''")+"','";
    cad1 += nfacturas.left(-1).replace("'","''")+"','";
    cad1 += finicial.left(-1).replace("'","''")+"','";
    cad1 += ffinal.left(-1).replace("'","''")+"','";
    cad1 += pr_serv_ue.left(-1).replace("'","''")+"','";
    cad1 += autofactura_noue.left(-1).replace("'","''")+"','";
    cad1 += bien_inversion.left(-1).replace("'","''")+"','";
    cad1 += sujeto.left(-1).replace("'","''")+"','";
    cad1 += afecto.left(-1).replace("'","''")+"','";
    cad1 += fecha.toString("yyyy-MM-dd")+"','";
    cad1 += col32.left(-1).replace("'","''")+"','";
    cad1 += col33.left(-1).replace("'","''")+"','";
    cad1 += col34.left(-1).replace("'","''")+"','";
    cad1 += col35.left(-1).replace("'","''")+"','";
    cad1 += col36.left(-1).replace("'","''")+"','";
    cad1 += col37.left(-1).replace("'","''")+"','";
    cad1 += col38.left(-1).replace("'","''")+"','";
    cad1 += col39.left(-1).replace("'","''")+"','";
    cad1 += col40.left(-1).replace("'","''")+"','";
    cad1 += col41.left(-1).replace("'","''")+"','";
    cad1 += col42.left(-1).replace("'","''")+"','";
    cad1 += col43.left(-1).replace("'","''")+"','";
    cad1 += col44.left(-1).replace("'","''")+"','";
    cad1 += col45.left(-1).replace("'","''")+"','";
    cad1 += col46.left(-1).replace("'","''")+"','";
    cad1 += col47.left(-1).replace("'","''")+"','";
    cad1 += col48.left(-1).replace("'","''")+"','";
    cad1 += col49.left(-1).replace("'","''")+"','";
    cad1 += col50.left(-1).replace("'","''");
    cad1 += "')";
    ejecutar(cad1);
}

// Devuelve el código del ejercicio de una fecha determinada
QString basedatos::selectEjerciciodelafecha (QDate fecha) {
    if (fecha.toString().isEmpty()) return "";
    QString cadena = "select codigo from ejercicios where apertura<='";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "' and cierre>='";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    else return "";
}


// Devuelve el primer asiento de un ejercicio
qlonglong basedatos::selectPrimerasiento (QString ejercicio) {
    QString cadena = "select primerasiento from ejercicios where codigo='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
	    return query.value(0).toLongLong();
    return 0;
}

// señala la fecha de inicio de ejercicio
QDate basedatos::selectAperturaejercicios (QString ejercicio) {
    QString cadena = "select apertura from ejercicios where codigo='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDate();
    }
    return QDate::currentDate();
}

// primer asiento reservado para apertura de ejercicio
bool basedatos::ejercicio_primero_apertura (QString ejercicio) {
    QString cadena = "select primero_apertura from ejercicios where codigo='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toBool();
    }
    return false;
}


// señala la fecha de cierre de ejercicio
QDate basedatos::selectCierreejercicios (QString ejercicio) {
    QString cadena = "select cierre from ejercicios where codigo='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDate();
    }
    return QDate::currentDate();
}

// Devuelve el último número de asiento del diario
QString basedatos::selectUltimonumasiento(QString filtro) {
    QString cadquery = "SELECT max(asiento) from diario";
    if (filtro.length()>0)
    {
        cadquery += " where " + filtro;
    }
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve la descripción de una cuenta
QString basedatos::selectDescripcionplancontable (QString qcodigo) {
    QString cadquery = "SELECT descripcion from plancontable where codigo = '";
    cadquery += qcodigo.left(-1).replace("'","''");
    cadquery += "'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}


// Devuelve el saldo de una cuenta en un ejercicio
double basedatos::selectSaldocuentaendiarioejercicio (QString cuenta,QString ejercicio) {
    QString cadquery = "select sum(debe-haber) from diario where cuenta='";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "' and fecha>='";
    cadquery += inicioejercicio(ejercicio).toString("yyyy-MM-dd");
    cadquery += "' and fecha<='";
    cadquery += finejercicio(ejercicio).toString("yyyy-MM-dd");
    cadquery += "'"; 
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDouble();
    }
    return 0;
}

// Devuelve el saldo de una cuenta en unas fechas
QSqlQuery basedatos::selectSaldocuentaendiariofechas (QString cuenta,QString inicial, QString final) {
    QString cadquery = "select sum(debe-haber) from diario where cuenta='";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "' and fecha>='";
    cadquery += inicial;
    cadquery += "' and fecha<='";
    cadquery += final;
    cadquery += "'"; 
    return ejecutar(cadquery);
}

// Devuelve el nombre de la empresa
QString basedatos::selectEmpresaconfiguracion () {
    QString cadquery = "SELECT empresa from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// devuelve fecha de constitución
QDate basedatos::fechaconst () {
    QString cadquery = "SELECT fecha_constitucion from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDate();
    }
    return QDate::currentDate();
}


// Devuelve ciudad de la empresa
QString basedatos::ciudad () {
    QString cadquery = "SELECT poblacion from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve provincia de la empresa
QString basedatos::provincia () {
    QString cadquery = "SELECT provincia from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve domicilio de la empresa
QString basedatos::domicilio () {
    QString cadquery = "SELECT domicilio from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}


// Devuelve cpostal de la empresa
QString basedatos::cpostal () {
    QString cadquery = "SELECT cpostal from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve objeto de la empresa
QString basedatos::objeto() {
    QString cadquery = "SELECT objeto from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve actividad de la empresa
QString basedatos::actividad() {
    QString cadquery = "SELECT actividad from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve id_registral de la empresa
QString basedatos::idregistral() {
    QString cadquery = "SELECT id_registral from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve cif/nif
QString basedatos::cif() {
    QString cadquery = "SELECT nif from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve el ancho de las cuentas
int basedatos::selectAnchocuentasconfiguracion () {
    QString cadquery = "SELECT anchocuentas from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

// Indica si un ejercicio está cerrado o no
int basedatos::selectCerradoejercicios (QString ejercicio) {
    QString cadena = "select cerrado from ejercicios where codigo='";
    cadena += ejercicio.left(-1).replace("'","''") + "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        if (query.value(0).toBool()) return 1;
    }
    return 0;
}

// Indica si un ejercicio se está cerrando
int basedatos::selectCerrandoejercicios (QString ejercicio) {
    QString cadena = "select cerrando from ejercicios where codigo='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
       if (query.value(0).toBool()) return 1;
    }
    return 0;
}

// Devuelve una cola de pase debe haber y cuenta de la tabla diario desde el asiento inicial hasta el final
QSqlQuery basedatos::selectPasedebehabercuentadiario (QString inicial, QString final,
                                                      QString ejercicio) {
    QString cadena = "select pase,debe,haber,cuenta,clave_ci from diario where asiento>=" +
                     inicial.left(-1).replace("'","''") + " and asiento<=" +
                     final.left(-1).replace("'","''");
    cadena+=" and ejercicio='";
    cadena+=ejercicio.left(-1).replace("'","''");
    cadena+="'";
    return ejecutar(cadena);
}

// Devuelve los datos de una subcuenta
QSqlQuery basedatos::selectTodoDatossubcuentacuenta (QString cuenta) {
// pais_dat, qtipo_ret, ret_arrendamiento, tipo_operacion_ret
    QString cadquery = "SELECT cuenta,razon,nombrecomercial,cif,nifrprlegal,domicilio,poblacion,";
    cadquery += "codigopostal,provincia,pais,tfno,fax,email,observaciones,ccc,cuota, ";
    cadquery += "venciasoc,codvenci,tesoreria,ivaasoc,cuentaiva,tipoiva,conceptodiario, ";
    cadquery += "web, claveidfiscal, procesavenci, pais_dat, tipo_ret, tipo_operacion_ret,"
                " ret_arrendamiento, vto_dia_fijo, cuenta_ret_asig, "
                "es_ret_asig_arrend, tipo_ret_asig, tipo_oper_ret_asig,"
                "dom_bancaria, iban, nriesgo, cedente1, cedente2, bic, sufijo, caja_iva,"
                "ref_mandato, firma_mandato";
    cadquery += " from datossubcuenta where cuenta = '";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "'";
    return ejecutar(cadquery);

}

bool basedatos::hay_datos_accesorios(QString cuenta)
{
    QString cadquery = "SELECT cuenta";
    cadquery += " from datossubcuenta where cuenta = '";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "'";
    QSqlQuery q = ejecutar(cadquery);

    if (q.isActive())
        if (q.next()) return true;

    return false;
}

void basedatos::razon_nif_datos(QString cuenta, QString *razon, QString *nif)
{
    QString cadquery = "SELECT razon, cif";
    cadquery += " from datossubcuenta where cuenta = '";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "'";
    QSqlQuery q = ejecutar(cadquery);

    if (q.isActive())
        if (q.next())
         {
           *razon=q.value(0).toString();
           *nif=q.value(1).toString();
         }

}


// Devuelve la foto codificada en caracteres de una subcuenta
QString basedatos::fotocuenta (QString cuenta) {
    QString cadquery = "SELECT imagen from datossubcuenta where cuenta = '";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "'";
    QSqlQuery query=ejecutar(cadquery);
    if (query.next()) return query.value(0).toString();
    return "";
}


// Indica si es contabilidad analítica
bool basedatos::selectAnaliticaconfiguracion () {
    QSqlQuery query = ejecutar("select analitica from configuracion");
    if ( (query.isActive()) && (query.next()) )
    	return query.value(0).toBool();
    return false;
}

// Indica si es contabilidad analítica parcial
bool basedatos::selectAnalitica_parc_configuracion () {
    QSqlQuery query = ejecutar("select analitica_parc from configuracion");
    if ( (query.isActive()) && (query.next()) )
    	return query.value(0).toBool();
    return false;
}


// Devuelve cuentas en analítica (para analítica parcial)
QString basedatos::select_codigos_en_ctas_analitica() {
    QSqlQuery query = ejecutar("select ctas_analitica from configuracion");
    if ( (query.isActive()) && (query.next()) )
    	return query.value(0).toString();
    return "";
}


// Indica si es igic
bool basedatos::selectIgicconfiguracion () {
    QSqlQuery query = ejecutar("select igic from configuracion");
    if ( (query.isActive()) && (query.next()) )
    	return query.value(0).toBool();
    return false;
}

// Devuelve los datos de tiposiva a partir de una clave
QSqlQuery basedatos::selectTodotiposivaclave (QString cadena) {
    QString cadquery = "SELECT clave,tipo,re,descripcion from tiposiva where clave = '"+ cadena.left(-1).replace("'","''") +"'";
    return ejecutar(cadquery);
}

// Devuelve la clave por defecto del IVA
QString basedatos::selectClave_iva_defectoconfiguracion () {
    QSqlQuery query = ejecutar("SELECT clave_iva_defecto from configuracion");
 
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve los datos del ci
bool basedatos::selectTodoCicodigo (QString codigo, QString *descripcion, QString *qnivel) {
    QSqlQuery query = ejecutar("SELECT codigo,nivel,descripcion from ci where codigo = '"+ codigo.left(-1).replace("'","''") +"'");
    if ( (query.isActive()) && (query.first()>0) )
    {
        *qnivel = query.value(1).toString();
        *descripcion = query.value(2).toString();
        return 1;
    }
    return 0;
}

// Devuelve la fecha de un asiento del diario
QDate basedatos::selectFechadiarioasiento (QString qasiento, QString ejercicio) {
    QString cadena="select fecha from diario where asiento="+
                   qasiento.left(-1).replace("'","''");
    cadena+=" and ejercicio='"+ejercicio.left(-1).replace("'","''");
    cadena+="'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDate();
    }
    QDate fecha(2000,1,1);
    return fecha;
}

// Devuelve el asiento de un pase
QString basedatos::selectAsientodiariopase (QString pase) {
    if (pase.isEmpty()) pase="0";
    QSqlQuery query = ejecutar("select asiento from diario where pase="+ pase.left(-1).replace("'","''"));
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}


QString basedatos::select_codigo_var_evpn_pymes_diariopase (QString pase) {
    if (pase.isEmpty()) pase="0";
    QSqlQuery query = ejecutar("select codigo_var_evpn_pymes from diario where pase="+ pase.left(-1).replace("'","''"));
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}


// Devuelve la fecha de un pase
QDate basedatos::select_fecha_diariopase (QString pase) {
    if (pase.isEmpty()) pase="0";
    QSqlQuery query = ejecutar("select fecha from diario where pase="+ pase.left(-1).replace("'","''"));
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDate();
    }
    return QDate();
}


// Devuelve el diario donde está ubicado un asiento
QString basedatos::selectDiariodiarioasiento (QString qasiento, QString ejercicio) {
    QString cadena="select diario from diario where asiento="+ qasiento.left(-1).replace("'","''");
    cadena+=" and ejercicio='";
    cadena+=ejercicio;
    cadena+="'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve el tipo y el re de un IVA
bool basedatos::selectTiporetiposivaclave (QString cadena,QString *tipo,QString *re) {
    QString cadquery = "SELECT tipo,re from tiposiva where clave = '";
    cadquery += cadena.left(-1).replace("'","''");
    cadquery += "'";
    QSqlQuery query = ejecutar(cadquery);

    *tipo="";
    *re="";
 
    if ( (query.isActive()) && (query.first()) )
    {
        *tipo=query.value(0).toString();
        *re=query.value(1).toString();
        return true;
    }
    return false;
}

//  Devuelve el debe-haber de una cuenta
double basedatos::selectDebehaberdiariocuenta (QString cuenta) {
    QString cadquery = "select sum(debe-haber) from diario where cuenta='";
    cadquery += cuenta.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDouble();
    }
    return 0;
}

// Devuelve la suma de la dotación fiscal
double basedatos::selectSumDotacion_fiscalamortfiscalycontablecuenta_activo (QString cuenta) {
    QString cadquery="select sum(dotacion_fiscal) from amortfiscalycontable where cuenta_activo='";
    cadquery += cuenta.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()>0) )
    {
        return query.value(0).toDouble();
    }
    return 0;
}

// Devuelve si el vencimiento está pendiente o no
bool basedatos::selectPendientevencimientosnum (QString vto) {
    QSqlQuery query = ejecutar("SELECT pendiente from vencimientos where num="+ vto.left(-1).replace("'","''"));
    if ( (query.isActive()) && (query.first()) )
    {
        return !query.value(0).toBool();
    }
    return false;
}

// Devuelve los datos de tiposiva a partir de una clave y si existe
int basedatos::selectTodoTiposivaclave (QString cadena,QString *qtipo,QString *qre,QString *qdescrip) {
    QString cadquery = "SELECT clave,tipo,re,descripcion from tiposiva where clave = '";
    cadquery += cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qtipo = query.value(1).toString();
        *qre = query.value(2).toString();
        *qdescrip = query.value(3).toString();
        return 1;
    }
    return 0;
}

// Devuelve el nifcomunitario de la subcuenta
/* QString basedatos::selectNifcomunitariodatossubcuentacuenta (QString codigo) {
    QString cadquery = "SELECT nifcomunitario from datossubcuenta where cuenta='";
    cadquery += codigo.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}
*/

// Devuelve el cif de la subcuenta
QString basedatos::selectCifdatossubcuentacuenta (QString codigo) {
    QString cadquery = "SELECT cif from datossubcuenta where cuenta='";
    cadquery += codigo.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve la cuenta del cif de la subcuenta especificada
QString basedatos::selectCuentaCifDatossubcuenta (QString codigo) {
    QString cadquery = "SELECT cuenta from datossubcuenta where cif='";
    cadquery += codigo.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return QString();
}


// Seleccina los datos de cabecera estados con una fecha especial
QSqlQuery basedatos::selectTodoespecialcabeceraestadostitulo (QString estado) {
    QString cadena = "select titulo,cabecera,parte1,parte2, observaciones,formulabasepor,'2000-01-01', "
        "ejercicio1, ejercicio2, diarios, estadosmedios,"
        "valorbasepor1, valorbasepor2, analitica,haycolref,"
        "colref,ci,grafico,desglose, desglose_ctas, des_cabecera,des_pie "
        "from cabeceraestados where titulo='";
    cadena+=estado.left(-1).replace("'","''") +"'";
    return ejecutar(cadena);
}

// Devuelve una serie de campos de estados a partir de un estado
QSqlQuery basedatos::selectTituloapartadoparte1clavenodoformulaestadostitulo (QString estado) {
    QString cadena = "select titulo,apartado,parte1,clave,nodo,formula "
        "from estados where titulo='";
    cadena += estado.left(-1).replace("'","''") +"'";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectCodigosaldossubcuenta () {
    return ejecutar("select codigo from saldossubcuenta");
}

// Devuelve la versión de la base de datos
QString basedatos::selectVersionconfiguracion () {
    QSqlQuery query = ejecutar("SELECT version from configuracion");
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve la descripción del nuevoplan
QString basedatos::selectDescripcionnuevoplancodigo (QString qcodigo) {
    QSqlQuery query = ejecutar("SELECT descripcion from nuevoplan where codigo = '"+ qcodigo.left(-1).replace("'","''") +"'");
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve asientomodelo, fecha de cab_as_modelo desde un asientomodelo
QSqlQuery basedatos::selectAsientomodelofechacab_as_modeloasientomodelo (QString nombre) {
    QString cadena="select asientomodelo,fecha, "
                   "aib, autofactura_ue, autofactura_no_ue, "
                   "eib, eib_servicios, diario, "
                   "importacion, exportacion, isp_op_interiores"
                   " from cab_as_modelo where asientomodelo='";
    cadena += nombre.left(-1).replace("'","''") +"'";
    return ejecutar(cadena);
}

// Devuelve variable, tipo, descripcion de var_as_modelo desde un asientomodelo
QSqlQuery basedatos::selectVariabletipodescripcionvar_as_modeloasientomodelo (QString nombre) {
    QString cadena="select variable,tipo,descripcion,valor,orden,guardar from var_as_modelo"
                   " where asientomodelo='";
    cadena+=nombre.left(-1).replace("'","''") +"'";
    return ejecutar(cadena);
}

// Devuelve varios datos de det_as_modelo
QSqlQuery basedatos::select_det_as_modeloasientomodelo (QString nombre) {
    QString cadena = "select cuenta,concepto,expresion,d_h, ci,baseiva,cuentafra, cuentabaseiva,"
                     "claveiva,documento, fecha_factura, prorrata_e, rectificativa,"
                     "fecha_op,clave_op,bicoste, rectificada, numfacts, facini, facfinal,"
                     "bien_inversion, afecto, agrario, nombre,  cif, "
                     "cta_retenido, arrendamiento, clave, base_ret, tipo_ret, "
                     "retencion, ing_a_cta, ing_a_cta_rep, nombre_ret, cif_ret, provincia ";
    cadena += "from det_as_modelo where asientomodelo='";
    cadena += nombre.left(-1).replace("'","''") +"'";
    return ejecutar(cadena);
}

// Devuelve presupuesto de presupuesto a partir de ejercicio y cuenta
QSqlQuery basedatos::selectPresupuestopresupuestoejerciciocuenta (QString codigo, QString ejercicio) {
    QString cadena = "select presupuesto from presupuesto where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' and cuenta='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// Devuelve la suma de presupuesto de un ejercicio a partir de cuenta
QSqlQuery basedatos::selectSumpresupuestopresupuestoejerciciocuenta (QString codigo, QString ejercicio) {
    QString cadena = "select sum(presupuesto) from presupuesto where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' and ";
    if (cualControlador() == SQLITE) {
        cadena += "cuenta like '"+ codigo.left(-1).replace("'","''") +"%' ";
        cadena += " and length(cuenta)=";
        QString cadnum;
        cadnum.setNum(anchocuentas());
        cadena += cadnum;
    }
    else {
        cadena += "position('";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "' in cuenta)=1 and char_length(cuenta)=";
        QString cadnum;
        cadnum.setNum(anchocuentas());
        cadena += cadnum;
    }
    return ejecutar(cadena);
}

// Devuelve un presupuesto de un ejercicio y cuenta
QSqlQuery basedatos::selectPresupuestopresupuestoEjercicioPosCuenta (QString codigo, QString ejercicio) {
    QString cadena = "select presupuesto from presupuesto where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' and ";
    if (cualControlador() == SQLITE) {
        cadena += "cuenta like '"+ codigo.left(-1).replace("'","''") +"%' ";
        cadena += " and length(cuenta)=";
        QString cadnum;
        cadnum.setNum(anchocuentas());
        cadena+=cadnum;
    }
    else {
        cadena += "position('";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "' in cuenta)=1 and char_length(cuenta)=";
        QString cadnum;
        cadnum.setNum(anchocuentas());
        cadena+=cadnum;
    }
    return ejecutar(cadena);
}

// Devuelve un presupuesto de un presupuesto con un ejercicio una cuenta
QSqlQuery basedatos::selectPresupuestopresupuestoEjercicioCuenta (QString codigo, QString ejercicio) {
    QString cadena = "select presupuesto from presupuesto where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' and cuenta='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// Devuelve el código y auxiliar de plancontable a aprtir de un código
QSqlQuery basedatos::selectCodigoauxiliarplancontablecodigo (QString codigo) {
    QString cadena = "select codigo,auxiliar from plancontable where codigo='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// Devuelve la dif_conciliacoin de un diario
QSqlQuery basedatos::selectDif_conciliaciondiariodif_conciliacion (QString cadena) {
    QString cadquery = "SELECT dif_conciliacion from diario where dif_conciliacion = '";
    cadquery += cadena.left(-1).replace("'","''");
    cadquery += "'";
    return ejecutar(cadquery);
}

// Devuelve la dif_conciliacoin de un cuenta_ext_conci
QSqlQuery basedatos::selectDif_conciliacioncuenta_ext_concidif_conciliacion (QString cadena) {
    QString cadquery = "SELECT dif_conciliacion from cuenta_ext_conci where dif_conciliacion = '";
    cadquery += cadena.left(-1).replace("'","''");
    cadquery += "'";
    return ejecutar(cadquery);
}

// Devuelve la dif_conciliacoin de un ajustes_conci
QSqlQuery basedatos::selectDif_conciliacionajustes_concidif_conciliacion (QString cadena) {
    QString cadquery = "SELECT dif_conciliacion from ajustes_conci where dif_conciliacion = '";
    cadquery += cadena.left(-1).replace("'","''");
    cadquery += "'";
    return ejecutar(cadquery);
}

// Devuelve la descripción de una cuenta en dif_conciliacion
QString basedatos::selectDescripciondif_conciliacioncodigo (QString qcodigo) {
    QString cadquery = "SELECT descripcion from dif_conciliacion where codigo = '";
    cadquery += qcodigo.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve los códigos de los ejercicios
QSqlQuery basedatos::selectCodigoejerciciosordercodigo () {
    return ejecutar("select codigo from ejercicios order by codigo DESC");
}

// Devuelve datos de un diario a partir de una fecha (apertura)
QSqlQuery basedatos::selectSubdebesumhabersumdebehaberdiariodiarioapertura (
        QDate fechainicial,QDate fechafinal,QString codigo) {
    QString cadena="select sum(debe),sum(haber), sum(debe-haber) from diario where diario='";
    cadena+=diario_apertura();
    cadena+="' and fecha>='";
    cadena+=fechainicial.toString("yyyy-MM-dd");
    cadena+="' and fecha<='";
    cadena+=fechafinal.toString("yyyy-MM-dd");
    cadena+="' and ";

    if (cualControlador() == SQLITE) {
        cadena += " cuenta like '"+ codigo.left(-1).replace("'","''") +"%'";
    }
    else {
        cadena += " position('";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "' in cuenta)=1";
    }

    // "cuenta='";
    // cadena+=sub.left(-1).replace("'","''");
    // cadena+="'";
    return ejecutar(cadena);
}

// Devuelve datos del diario
QSqlQuery basedatos::selectMonthyeardebehaberdiario (QDate fechainicial,
                                                     QDate fechafinal,
                                                     QString codigo) {
    QString cadena;
    if (cualControlador() == SQLITE) {
        cadena = "select substr(fecha,6,2),substr(fecha,1,4),sum(debe),sum(haber),";
    }
    else {
    cadena =  "select extract(month from fecha),extract(year from fecha),sum(debe),sum(haber),";
    }
    cadena += "sum(debe-haber) from diario where fecha>='";
    cadena += fechainicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += fechafinal.toString("yyyy-MM-dd");
    cadena += "' and "; // and cuenta='";
    if (cualControlador() == SQLITE) {
        cadena += " cuenta like '"+ codigo.left(-1).replace("'","''") +"%'";
    }
    else {
        cadena += " position('";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "' in cuenta)=1";
    }

    // cadena += sub.left(-1).replace("'","''");

    cadena += " and diario!='";
    cadena += diario_apertura();
    cadena += "' and diario!='";
    cadena += diario_regularizacion();
    cadena += "' and diario!='";
    cadena += diario_cierre();
    if (cualControlador() == SQLITE) {
        cadena += "' group by substr(fecha,6,2),substr(fecha,1,4) ";
        cadena += "order by substr(fecha,6,2),substr(fecha,1,4);";
    }
    else {
        cadena += "' group by extract(month from fecha),extract(year from fecha) ";
        cadena += "order by extract(month from fecha),extract(year from fecha);";
    }
    return ejecutar(cadena);
}

// Devuelve datos de un diario a partir de una fecha (regularización
QSqlQuery basedatos::selectSubdebesumhabersumdebehaberdiariodiarioregularizacion (QDate fechainicial,
                                                                                  QDate fechafinal,
                                                                                  QString codigo)
{
    QString cadena="select sum(debe),sum(haber), sum(debe-haber) from diario where diario='";
    cadena+=diario_regularizacion();
    cadena+="' and fecha>='";
    cadena+=fechainicial.toString("yyyy-MM-dd");
    cadena+="' and fecha<='";
    cadena+=fechafinal.toString("yyyy-MM-dd");
    cadena+="' and";

    if (cualControlador() == SQLITE) {
        cadena += " cuenta like '"+ codigo.left(-1).replace("'","''") +"%'";
    }
    else {
        cadena += " position('";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "' in cuenta)=1";
         }
    // cadena+=sub.left(-1).replace("'","''");
    // cadena+="'";
    return ejecutar(cadena);
}

// Devuelve datos de un diario a partir de una fecha (cierre)
QSqlQuery basedatos::selectSubdebesumhabersumdebehaberdiariodiariocierre (QDate fechainicial,
                                                                          QDate fechafinal,
                                                                          QString codigo) {
    QString cadena="select sum(debe),sum(haber), sum(debe-haber) from diario where diario='";
    cadena+=diario_cierre();
    cadena+="' and fecha>='";
    cadena+=fechainicial.toString("yyyy-MM-dd");
    cadena+="' and fecha<='";
    cadena+=fechafinal.toString("yyyy-MM-dd");
    cadena+="' and";

    if (cualControlador() == SQLITE) {
        cadena += " cuenta like '"+ codigo.left(-1).replace("'","''") +"%'";
    }
    else {
        cadena += " position('";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "' in cuenta)=1";
         }

    // cadena+=sub.left(-1).replace("'","''");
    // cadena+="'";
    return ejecutar(cadena);
}

// Devuelve datos de tiposiva distintos de clave
QSqlQuery basedatos::selectTodoTiposivanoclave (QString Clavedefecto) {
    QString cadquery = "SELECT clave,tipo,re,descripcion from tiposiva where clave != '";
    cadquery += Clavedefecto.left(-1).replace("'","''") +"'";
    return ejecutar(cadquery);
} 


// Devuelve datos de tiposiva ordenados por tipo
QStringList basedatos::tiposiva () {
    QStringList lista;
    QString cadquery = "SELECT clave,tipo from tiposiva order by tipo";
    QSqlQuery query = ejecutar(cadquery);
    if (query.isActive())
        while (query.next())
           {
             lista << query.value(0).toString();
           }
    return lista;
}


// Devuelve lista de ejercicios de amortcontable ordenados
QSqlQuery basedatos::selectEjercicioamortcontableorderejercicio () {
    return ejecutar("select ejercicio from amortcontable group by ejercicio order by ejercicio DESC");
}

// Cantidad de cuenta_activo en amortfiscalycontable para un ejercicio
QSqlQuery basedatos::selectCountcuenta_activoamortfiscalycontableejercicio (QString ejercicio) {
    QString cadena = "select count(cuenta_activo) from amortfiscalycontable where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' group by ejercicio";
    return ejecutar(cadena);
}

// Distintos datos de amortfiscalycontable
QSqlQuery basedatos::select5amortfiscalycontableejercicio (QString ejercicio) {
    QString cadena = "select cuenta_activo,max(codigo_activo),sum(dotacion_contable),";
    cadena += "sum(dotacion_fiscal), sum(dotacion_contable-dotacion_fiscal) ";
    cadena += "from amortfiscalycontable where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' group by cuenta_activo order by cuenta_activo";
    return ejecutar(cadena);
}

// Distintas sumas de amortfiscalycontable
QSqlQuery basedatos::selectSumasamortfiscalycontableejercicio (QString ejercicio) {
    QString cadena = "select sum(dotacion_contable),sum(dotacion_fiscal),";
    cadena += "sum(dotacion_contable-dotacion_fiscal) ";
    cadena += "from amortfiscalycontable where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// Datos del diario en una fecha que sean de cierre
QSqlQuery basedatos::selectCuentadebehaberdiariofechascierre (QString cadinicioej, QString cadfinej) {
    QString cadena = "select cuenta,debe, haber,ci, clave_ci from diario where fecha>='";
    cadena += cadinicioej;
    cadena += "' and fecha<='";
    cadena += cadfinej;
    cadena += "' and diario='";
    cadena += diario_cierre();
    cadena += "'";
    return ejecutar(cadena);
}

// Datos del próximo pase
qlonglong basedatos::selectProx_paseconfiguracion () {
    QSqlQuery query = ejecutar("select prox_pase from configuracion");
    qlonglong vnum = 0;
    if ( (query.isActive()) &&(query.next()) )
    {
        vnum = query.value(0).toLongLong();
        if (vnum == 0)
            vnum = 1;
    }
    else vnum = 1;
    return vnum;
}

// Códigos de los diarios ordenados
QSqlQuery basedatos::selectCodigoDiariosordercodigo () {
    return ejecutar("select codigo from diarios order by codigo");
}

// Devuelve cuáles son las cuentas a pagar
QString basedatos::selectCuentasapagarconfiguracion () {
    QSqlQuery query = ejecutar("select cuentasapagar from configuracion");
    if ( (query.isActive()) && (query.next()) ) 
        return query.value(0).toString();
    return "";
}

// Devuelve cuáles son las cuentas a cobrar
QString basedatos::selectCuentasacobrarconfiguracion () {
    QSqlQuery query = ejecutar("select cuentasacobrar from configuracion");
    if ( (query.isActive()) && (query.next()) ) 
        return query.value(0).toString();
    return "";
}

// Devuelve datos del diario  y plancontable
QSqlQuery basedatos::selectDatosDiarioPlancontable (QString cadenacuentas, QDate fechaini, QDate fechafin) {
    QString cadquery = "select diario.cuenta,diario.asiento,diario.fecha,plancontable.descripcion,";
    cadquery += "diario.concepto,diario.debe,diario.haber,diario.documento,diario.pase ";
    cadquery += "from diario,plancontable where plancontable.codigo=diario.cuenta and (";
    int partes = cadenacuentas.count(',');
    for (int veces=0;veces<=partes;veces++)
    {
        if (cualControlador() == SQLITE) {
            cadquery += "cuenta like '"+ cadenacuentas.section(',',veces,veces).left(-1).replace("'","''") +"%'";
        }
        else {
            cadquery += "position('";
            cadquery += cadenacuentas.section(',',veces,veces).left(-1).replace("'","''");
            cadquery += "' in cuenta)=1";
        }
        if (veces == partes) cadquery += ")";
        else cadquery += " or ";
    }
    cadquery += " and diario.fecha>='";
    cadquery += fechaini.toString("yyyy-MM-dd");
    cadquery += "' and diario.fecha<='";
    cadquery += fechafin.toString("yyyy-MM-dd");
    cadquery += "' and diario !='";
    cadquery += diario_apertura();
    cadquery += "' and diario !='";
    cadquery += diario_cierre();
    cadquery += "' order by diario.fecha";
    return ejecutar(cadquery);
}

// Cantidad de pase_diario_vencimiento de vencimiento 
int basedatos::selectCountvencimientospase_diario_vencimiento (QString pase) {
    QString cadquery = "select count(pase_diario_vencimiento) from vencimientos where pase_diario_vencimiento=";
    cadquery += pase.left(-1).replace("'","''");
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) ){
        return query.value(0).toInt();
    }
    return 0;
}

// 
void basedatos::selectSumimportevencimientospase_diario_operacion (QString pase, double v5, double v6, double *v1, double *v2) {
    double valor1=0;
    double valor2=0;

    QString cadquery = "select sum(importe) from vencimientos where pase_diario_operacion=";
	cadquery += pase.left(-1).replace("'","''");
	QSqlQuery query = ejecutar(cadquery);
	valor1 = 0;

	if ( (query.isActive()) && (query.first()) ) {
        valor1 = v5 + v6;
        if ( valor1 < 0 ) valor1 = -1 * valor1;
        valor2 = query.value(0).toDouble();
    }

    *v1 = valor1;
    *v2 = valor2;
}

// Devuelve cuenta, fecha y suma de debe y haber para un pase
bool basedatos::selectCuentafechadebehaberdiariopase (QString pase, QString *qcuenta, QString *qfecha, QString *qdebehaber) {
    QString cad2 = "select cuenta,fecha,abs(debe)+abs(haber) from diario where diario.pase=";
    cad2 += pase.left(-1).replace("'","''");
    QSqlQuery query = ejecutar(cad2);

    if ( (query.isActive()) && (query.first()) )
    {
        *qcuenta = query.value(0).toString();
        *qfecha = query.value(1).toString();
        *qdebehaber = query.value(2).toString();
        return true;
    }
    return false;
}

// Cantidad de plancontable con un filtro
int basedatos::selectCountcodigoplancontablefiltro (QString filtro) {
    QString cadconsulta = "select count(codigo) from plancontable where ";
    cadconsulta += filtro;
    QSqlQuery query = ejecutar(cadconsulta);

    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

// 
QSqlQuery basedatos::selectCodigodescripcionplancontablefiltro(QString filtro) {
    QString cadena = "select codigo,descripcion from plancontable";
    if (filtro.length() > 0) {
        cadena += " where ";
        cadena += filtro;
    }
    cadena += " ORDER by codigo";

    return ejecutar(cadena);
}

// 12 elementos de cabeceraestados desde un título
QSqlQuery basedatos::selectCabeceraestadostitulo (QString titulo) {
    QString cadena = "select titulo,ejercicio1,ejercicio2,fechacalculo,"
        "diarios,estadosmedios,"
        "analitica,ci,haycolref,colref,desglose,grafico,horacalculo, desglose_ctas "
        "from cabeceraestados ";
    cadena += "where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// Devuelve el máximo de apertura de ejercicios
QString basedatos::selectMaxaperturaejercicios () {
    QSqlQuery query = ejecutar("select max(apertura) from ejercicios;");
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// 
QString basedatos::selectCodigoejercicioapertura (QString apertura) {
    QString cadena = "select codigo from ejercicios where apertura='";
    cadena += apertura.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Indica si los saldos medios están bloqueados
bool basedatos::selectBloqsaldosmedios () {
    QSqlQuery query = ejecutar("select bloqsaldosmedios from configuracion");
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toBool();
    }
    return false;
}

// Cantidad de saldossubcuenta
int basedatos::selectCountcodigosaldossubcuenta () {
    QString cadquery = "select count(codigo) from saldossubcuenta";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.next()) )
    {
	      return query.value(0).toInt();
    }
    return 0;
}

// fecha, debe y haber de una cuenta en unas fechas
QSqlQuery basedatos::selectFechadebehaberdiariocuentafechasnocierreorderfecha (QString cuenta, QDate fechaini, QDate fechafin) {
    QString cadsubquery = "select fecha,debe,haber from diario where cuenta='";
	cadsubquery += cuenta.left(-1).replace("'","''");
	cadsubquery += "' and fecha>='";
	cadsubquery += fechaini.toString("yyyy-MM-dd");
	cadsubquery += "' and fecha<='";
	cadsubquery += fechafin.toString("yyyy-MM-dd");
	cadsubquery += "' and diario !='";  //falta diario distinto de CIERRE
	cadsubquery += diario_cierre() ; // DIARIO_CIERRE;
	cadsubquery += "' order by fecha";
	return ejecutar(cadsubquery);
}

// Devuelve datos de estados a partir de un título ordenado por nodo
QSqlQuery basedatos::select5Estadostituloordernodo (QString titulo) {
    QString cadena = "select parte1,clave,nodo,formula,calculado from estados where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "' order by nodo";
    return ejecutar(cadena);
}

// Devuelve la formulabasepor de cabeceraestados para un titulo
QString basedatos::selectFormulabaseporcabeceraestadostitulo (QString titulo) {
    QString cadena = "select formulabasepor from cabeceraestados where titulo='"; 
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return NULL;
}

// Devuelve datos de cuenta debe y haber para una cuenta en unas fechas
QSqlQuery basedatos::selectCuentadebehaberdiariocuentaigualfechasgroupcuenta (QString codigo, QDate fechaini, QDate fechafin, QString subcadena, QString ci) {
    QString cadena = "select cuenta,sum(debe)-sum(haber) from diario where cuenta='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += fechaini.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += fechafin.toString("yyyy-MM-dd");
    cadena += "'";

    cadena += subcadena;

    if (ci.length() > 0)
    {
        cadena += " and ";
        cadena += filtroci(ci,false);
    }

    cadena+=" group by cuenta";

    return ejecutar(cadena);
}



//
QSqlQuery basedatos::cuentas_saldo_estados_ci (QDate fechaini,
                                   QDate fechafin, QString subcadena,
                                               QString ci) {
    QString cadena = "select diario.cuenta, "
                     "sum(diario.debe/abs(diario.debe+diario.haber)*diario_ci.importe)- "
                     "sum(diario.haber/abs(diario.debe+diario.haber)*diario_ci.importe)"
                     " from diario, diario_ci where "
                     "diario_ci.clave_ci = diario.clave_ci and ";
    cadena += "fecha>='";
    cadena += fechaini.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += fechafin.toString("yyyy-MM-dd");
    cadena += "'";

    cadena += subcadena;

    if (ci.length() > 0)
    {
        cadena += " and ";
        cadena += filtroci(ci,true);
    }

    cadena+=" group by cuenta";

    return ejecutar(cadena);
}


// Devuelve datos cuenta y daldo en unas fechas
QSqlQuery basedatos::cuentas_saldo_estados (QDate fechaini, QDate fechafin,
                                            QString subcadena, QString ci) {
    QString cadena = "select cuenta,sum(debe)-sum(haber) from diario where ";
    cadena += " fecha>='";
    cadena += fechaini.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += fechafin.toString("yyyy-MM-dd");
    cadena += "'";

    cadena += subcadena;

    if (ci.length() > 0)
    {
        cadena += " and ";
        cadena += filtroci(ci,false);
    }

    cadena+=" group by cuenta";

    return ejecutar(cadena);
}



// Devuelve datos de cuenta debe y haber para una cuenta en unas fechas
QSqlQuery basedatos::selectCuentadebehaberdiariocuentafechasgroupcuenta (QString codigo, QDate fechaini, QDate fechafin,
                                                                         QString subcadena, QString ci) {
    QString cadena = "select cuenta,sum(debe)-sum(haber) from diario where ";
    if (cualControlador() == SQLITE) {
        cadena += "cuenta like '"+ codigo.left(-1).replace("'","''") +"%'";
    }
    else {
        cadena += "position('";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "' in cuenta)=1";
    }
    cadena += " and fecha>='";
    cadena += fechaini.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += fechafin.toString("yyyy-MM-dd");
    cadena += "'";

    cadena += subcadena;

    if (ci.length() > 0)
    {
        cadena += " and ";
        cadena += filtroci(ci,false);
    }

    cadena+=" group by cuenta";

    return ejecutar(cadena);
}

// Devuelve importe1 y calculado de estados para un nodo
QSqlQuery basedatos::selectImporte1calculadonodoparte1titulo (QString nodo,
                                                              bool izquierdo,
                                                              QString titulo,
                                                              int mes) {
    // consultamos estados en busca del nodo extract 
    QString cadena;
    cadena = "select ";
    if (mes==0) cadena+="importe1";
      else
          {
           cadena+="m";
           QString cadnum; cadnum.setNum(mes);
           cadena+=cadnum;
          }

    cadena+=", calculado from estados where nodo='";
    cadena += nodo.left(-1).replace("'","''");
    if (izquierdo)
        { cadena += "' and parte1 "; }
    else
        { cadena+="' and not parte1 "; }
    // -------------------------------------------------
    cadena += "and titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";
    // --------------------------------------------------
    return ejecutar(cadena);
}

// Devuelve importe1 y calculado de estados para un nodo
QSqlQuery basedatos::selectImporte2calculadonodoparte1titulo (QString nodo, bool izquierdo, QString titulo) {
    // consultamos estados en busca del nodo extract 
    QString cadena;
    cadena = "select importe2,calculado from estados where nodo='";
    cadena += nodo.left(-1).replace("'","''");
    if (izquierdo) {
        cadena += "' and parte1 ";
    }
    else {
        cadena+="' and not parte1 ";
    }
    // -------------------------------------------------
    cadena += "and titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";
    // --------------------------------------------------
    return ejecutar(cadena);
}

// Devuelve suma de saldomedio1 o saldomedio2 de saldossubcuenta a partir de código
double basedatos::selectSumsaldomediosaldossubcuentacodigo (bool ejercicio1, QString codigo, bool positivo, bool negativo) {
    double resultado=0;
    QString cadena;
    if (ejercicio1) cadena = "select sum(saldomedio1) from saldossubcuenta where ";
    else cadena = "select sum(saldomedio2) from saldossubcuenta where ";
    if (cualControlador() == SQLITE) {
        cadena += "codigo like '"+ codigo.left(-1).replace("'","''") +"%'";
    }
    else {
        cadena += "position('";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "' in codigo)=1";
    }

    if (positivo && ejercicio1) cadena += " and saldomedio1>0;";
    if (positivo && !ejercicio1) cadena += " and saldomedio2>0;";
    if (negativo && ejercicio1) cadena += " and saldomedio1<0;";
    if (negativo && !ejercicio1) cadena += " and saldomedio2<0;";
    // QMessageBox::warning( this, tr("Estados Contables"),cadena);

    QSqlQuery query = ejecutar(cadena);
    if ( query.isActive() ) {
        while ( query.next() ) 
        {
	        resultado = query.value(0).toDouble();
        }
    }
    return resultado;
}

// Devuelve suma de saldomedio1 o saldomedio2 de saldossubcuenta a partir de código
double basedatos::selectSumsaldomediosaldossubcuentaigualcodigo (bool ejercicio1, QString codigo, bool positivo, bool negativo) {
    double resultado=0;
    QString cadena;
    if (ejercicio1) cadena = "select sum(saldomedio1) from saldossubcuenta where codigo='";
    else cadena = "select sum(saldomedio2) from saldossubcuenta where codigo='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "'";
    if (positivo && ejercicio1) cadena += " and saldomedio1>0;";
    if (positivo && !ejercicio1) cadena += " and saldomedio2>0;";
    if (negativo && ejercicio1) cadena += " and saldomedio1<0;";
    if (negativo && !ejercicio1) cadena += " and saldomedio2<0;";
    // QMessageBox::warning( this, tr("Estados Contables"),cadena);

    QSqlQuery query = ejecutar(cadena);
    if ( query.isActive() ) {
        while ( query.next() ) 
        {
	        resultado = query.value(0).toDouble();
        }
    }
    return resultado;
}

// Devuelve datos de cabeceraestados a partir de un título
QSqlQuery basedatos::select14Cabeceraestadostitulo (QString titulo) {
    QString cadena = "select parte1,parte2,ejercicio1,ejercicio2,estadosmedios,formulabasepor,"
        "valorbasepor1,valorbasepor2,observaciones,"
        "analitica,ci,haycolref,colref,cabecera from cabeceraestados ";
    cadena += "where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select5Estadostituloparte1ordernodo (QString titulo,bool parte1) {
    QString cad2 = "select nodo,apartado,importe1,importe2,referencia, "
                   "m1, m2, m3, m4, m5, m6, m7, m8, m9, m10, m11, m12, formula "
                   "from estados where titulo='";
    cad2 += titulo.left(-1).replace("'","''");
    if (parte1) { cad2 += "' and parte1"; }
    else { cad2 += "' and not parte1"; }
    cad2 += " order by nodo";
    return ejecutar(cad2);
}

// 
QSqlQuery basedatos::select4Estadostituloordernodo (QString titulo, bool parte1) {
    QString cad2 = "select nodo,apartado,importe1,importe2 from estados where titulo='";
    cad2 += titulo.left(-1).replace("'","''");
    cad2 += "' and ";
    if (!parte1) { cad2 += "not "; }
    cad2 +="parte1 order by nodo";
    return ejecutar(cad2);
}

//
QSqlQuery basedatos::select16Cabeceraestadostitulo(QString titulo) {
    QString cadena = "select parte1,parte2,ejercicio1,ejercicio2,estadosmedios,formulabasepor,"
        "valorbasepor1,valorbasepor2,observaciones,"
        "analitica,ci,haycolref,colref,cabecera,des_cabecera,des_pie from cabeceraestados ";
    cadena += "where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select6Estadostituloparte1ordernodo (QString titulo,bool parte1) {
    QString cad2 = "select nodo,apartado,importe1,importe2,referencia,formula,"
                   "m1,m2,m3,m4,m5,m6,m7,m8,m9,m10,m11,m12 from estados where titulo='";
    cad2 += titulo.left(-1).replace("'","''");
    if (parte1) { cad2 += "' and parte1"; }
    else { cad2 += "' and not parte1"; }
    cad2 += " order by nodo";
    return ejecutar(cad2);
}

// Devuelve el código del plancontable
QSqlQuery basedatos::selectCodigoplancontablecodigolength (QString codigo) {
    QString cad = "select codigo from plancontable where ";
    if (cualControlador() == SQLITE) {
        cad += "codigo like '"+ codigo.left(-1).replace("'","''") +"%' and length(codigo)=";
    }
    else {
        cad += "position('";
        cad += codigo.left(-1).replace("'","''");
        cad += "' in codigo)=1 and char_length(codigo)=";
    }
    QString cadnum;
    cadnum.setNum(anchocuentas());
    cad += cadnum;
    cad += " order by codigo";
    return ejecutar(cad);
}

// Devuelve el código del plancontable para cuenta auxiliar
QSqlQuery basedatos::selectCodigoplancontablecodigoauxiliar (QString codigo) {
    QString cad = "select codigo from plancontable where ";
    if (cualControlador() == SQLITE) {
        cad += "codigo like '"+ codigo.left(-1).replace("'","''") + "%' and auxiliar";
    }
    else {
        cad += "position('";
        cad += codigo.left(-1).replace("'","''");
        cad += "' in codigo)=1 and auxiliar";
    }
    return ejecutar(cad);
}

// 
QSqlQuery basedatos::select4Estadostitulo (QString titulo) {
    QString cadena = "select parte1,parte2,ejercicio1,ejercicio2 from cabeceraestados ";
    cadena += "where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// Devuelve la fecha mínima de un asiento para una cuenta
QSqlQuery basedatos::selectMinfechadiariocuenta (QString cuenta) {
    QString cad = "select min(fecha) from diario where cuenta='";
    cad += cuenta.left(-1).replace("'","''");
    cad += "';";
    return ejecutar(cad);
}

// Devuelve la fecha mínima de un asiento para un ci
QSqlQuery basedatos::selectMinfechadiarioci (QString ci) {
    QString cad = "select min(fecha) from diario where ci='";
    cad += ci.left(-1).replace("'","''");
    cad += "';";
    return ejecutar(cad);
}

// Devuelve la fecha mínima del diario
QSqlQuery basedatos::selectMinfechadiario () {
    QString cad = "select min(fecha) from diario";
    return ejecutar(cad);
}

//
QSqlQuery basedatos::selectSumdebesumhaberdiariocuenta (QString cuenta) {
    QString cadena = "select sum(debe)-sum(haber) from diario where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// Devuelve código y descripción de un ci para un nivel
QSqlQuery basedatos::selectCodigodescripcioncinivelordercodigo (QString cadnum) {
    QString cadq = "select codigo,descripcion from ci where nivel=";
    cadq += cadnum;
    cadq += " order by codigo";
    return ejecutar(cadq);
}

// Devuelve lista de códigos de plancontable
QSqlQuery basedatos::selectCodigoplancontablelengthauxiliar (QString cadanchoctas) {
    QString cadena = "select codigo from plancontable where " ;
    if (!cod_longitud_variable())
    {
        if (cualControlador() == SQLITE) {
            cadena += "length(codigo)=";
        }
        else {
            cadena += "char_length(codigo)=";
        }
        cadena += cadanchoctas;
    }
    else 
        cadena += "auxiliar";
    return ejecutar(cadena);
}

// Devuelve lista de códigos de plancontable filtro
QSqlQuery basedatos::selectCodigoplancontablelengthauxiliarfiltro (QString cadanchoctas,QString filtro) {
    QString cadena = "select codigo from plancontable where " ;
    if (!cod_longitud_variable())
    {
        if (cualControlador() == SQLITE) {
            cadena += "length(codigo)=";
        }
        else {
            cadena += "char_length(codigo)=";
        }
        cadena += cadanchoctas;
    }
    else 
        cadena += "auxiliar";
    cadena+=" ";
    cadena+=filtro;
    return ejecutar(cadena);
}


QSqlQuery basedatos::selec_auxiliares_codigo(QString codigo)
{
  int ancho=selectAnchocuentasconfiguracion();
  QString cadancho; cadancho.setNum(ancho);
  QString cadena = "select p.codigo, p.descripcion, d.cuota from "
                   "plancontable p, datossubcuenta d where " ;
  if (!cod_longitud_variable())
  {
      if (cualControlador() == SQLITE) {
          cadena += "length(p.codigo)=";
      }
      else {
          cadena += "char_length(p.codigo)=";
      }
      cadena += cadancho;
  }
  else
      cadena += "p.auxiliar";

  cadena+=" and ";
  if (cualControlador() == SQLITE) {
      cadena += "p.codigo like '"+ codigo.left(-1).replace("'","''") +"%'";
  }
  else {
        cadena += "position('";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "' in p.codigo)=1";
       }
  cadena+=" and p.codigo=d.cuenta order by p.codigo";
  return ejecutar(cadena);
}


QSqlQuery basedatos::selectDiario_campoci_filtro (QString filtro) {
    QString cadena = "select cuenta,ci,sum(debe),sum(haber) from diario where " ;
    cadena+=filtro;
    cadena+=" group by cuenta,ci";
    return ejecutar(cadena);
}


QSqlQuery basedatos::select_cuenta_sumadebe_sumahaber_diario (QString filtro) {
    QString cadena = "select cuenta, sum(debe), sum(haber) from diario where " ;
    cadena+=filtro;
    cadena+=" group by cuenta";
    return ejecutar(cadena);
}


QSqlQuery basedatos::select_desglose_ci_tabla_positivos (QString inicioej, QString finej,
                                               QString cuenta) {
    QString cadena = "select diario.cuenta, diario_ci.ci, "
            "sum(diario.debe/(diario.debe+diario.haber)*diario_ci.importe)+ "
            "sum(diario.haber/(diario.debe+diario.haber)*diario_ci.importe) "            
            "from diario, diario_ci where diario.clave_ci=diario_ci.clave_ci and "
            "diario.debe>=0 and diario.haber>=0 and ";


    cadena += "fecha>='";
    cadena += inicioej;
    cadena += "' and fecha<='";
    cadena += finej;
    cadena += "' and cuenta='";
    cadena +=cuenta.left(-1).replace("'","''");
    cadena +="' group by diario.cuenta, diario_ci.ci";
    return ejecutar(cadena);
}

QSqlQuery basedatos::select_desglose_ci_tabla_negativos (QString inicioej, QString finej,
                                               QString cuenta) {
    QString cadena = "select diario.cuenta, diario_ci.ci, "
            "sum(diario.debe/(diario.debe+diario.haber)*diario_ci.importe)+ "
            "sum(diario.haber/(diario.debe+diario.haber)*diario_ci.importe) "
            "from diario, diario_ci where diario.clave_ci=diario_ci.clave_ci and "
            "diario.debe<=0 and diario.haber<=0 and ";


    cadena += "fecha>='";
    cadena += inicioej;
    cadena += "' and fecha<='";
    cadena += finej;
    cadena += "' and cuenta='";
    cadena +=cuenta.left(-1).replace("'","''");
    cadena +="' group by diario.cuenta, diario_ci.ci";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectSumdebesumhaberdiariocuentafecha (QString codigo, QString cadinicioej, QString cadfinej) {
    QString cad2 = "select sum(debe-haber) from diario where cuenta='";
    cad2 += codigo.left(-1).replace("'","''");
    cad2 += "' and fecha>='";
    cad2 += cadinicioej;
    cad2 += "' and fecha<='";
    cad2 += cadfinej;
    cad2 += "'";
    return ejecutar(cad2);
}

//
QSqlQuery basedatos::selectCodigodif_conciliacionordercodigo () {
  return ejecutar("select codigo from dif_conciliacion order by codigo");
}

//
QSqlQuery basedatos::select8Diariocuentafechasorder (QString cuenta, QDate inicial, QDate final) {
    QString cadena = "select asiento,pase,fecha,concepto,debe,haber,conciliado,dif_conciliacion from diario where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' order by fecha,asiento,pase";

    return ejecutar(cadena);
}

//
QSqlQuery basedatos::selectSumdebesumhaberdiariofechascuenta (QDate fechainiejercicio, QDate inicial, QString cuenta) {
    QString cadena = "select sum(debe),sum(haber) from diario where fecha>='";
    cadena += fechainiejercicio.toString("yyyy-MM-dd");
    cadena += "' and fecha<'";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

//
QSqlQuery basedatos::selectSumdebesumhaberdiariofechacuenta (QDate inicial, QString cuenta) {
    QString cadena="select sum(debe),sum(haber) from diario where fecha<'";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select6Ajustes_concicuentafechasorderfecha (QString cuenta, QDate inicial, QDate final) {
    QString cadena = "select fecha,concepto,debe,haber,conciliado,dif_conciliacion from ajustes_conci where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' order by fecha";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select8Cuenta_ext_concicuentafechasordernumero (QString cuenta, QDate inicial, QDate final) {
    QString cadena = "select fecha,concepto,debe,haber,conciliado,dif_conciliacion,saldo,numero "
        "from cuenta_ext_conci where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' order by numero";
    return ejecutar(cadena);
}

//
QSqlQuery basedatos::selectDif_conciliaciondiariocuentafechasgrouporderdif_conciliacion (QString cuenta, QDate inicial, QDate final) {
    QString cadena = "select dif_conciliacion from diario where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena +="' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and haber>0 and ";
    cadena += "not conciliado";
    cadena += " group by dif_conciliacion order by dif_conciliacion";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select3Diariocuentafechasconciliadodif_conciliacionorderfecha (QString cuenta, QDate inicial, QDate final, QString dif_conciliacion) {
    QString cad2 = "select fecha,concepto,haber from diario where cuenta='";
    cad2 += cuenta.left(-1).replace("'","''");
    cad2 += "' and fecha>='";
    cad2 += inicial.toString("yyyy-MM-dd");
    cad2 += "' and fecha<='";
    cad2 += final.toString("yyyy-MM-dd");
    cad2 += "' and haber>0 and ";
    cad2 += "not conciliado";
    cad2 += " and dif_conciliacion='";
    cad2 += dif_conciliacion.left(-1).replace("'","''");
    cad2 += "' order by fecha";
    return ejecutar(cad2);
}

// 
QSqlQuery basedatos::selectDifconciliacioncuenta_ext_concicuentafechasgrouporderdif_conciliacion (QString cuenta, QDate inicial, QDate final) {
    QString cadena = "select dif_conciliacion from cuenta_ext_conci where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and haber>0 and ";
    cadena += "not conciliado";
    cadena += " group by dif_conciliacion order by dif_conciliacion";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectDifconciliacionajustes_concicuentafechasgrouporderdif_conciliacion (QString cuenta, QDate inicial, QDate final) {
    QString cadena = "select dif_conciliacion from ajustes_conci where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and haber>0 and ";
    cadena += "not conciliado";
    cadena += " group by dif_conciliacion order by dif_conciliacion";

    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select3Cuenta_ext_concicuentafechasdif_conciliacionorderfecha (QString cuenta, QDate inicial, QDate final, QString dif) {
    QString cad2 = "select fecha,concepto,haber from cuenta_ext_conci where cuenta='";
    cad2 += cuenta.left(-1).replace("'","''");
    cad2 += "' and fecha>='";
    cad2 += inicial.toString("yyyy-MM-dd");
    cad2 += "' and fecha<='";
    cad2 += final.toString("yyyy-MM-dd");
    cad2 += "' and haber>0 and ";
    cad2 += "not conciliado";
    cad2 += " and dif_conciliacion='";
    cad2 += dif.left(-1).replace("'","''");
    cad2 += "' order by fecha";
    return ejecutar(cad2);
}

// 
QSqlQuery basedatos::select3Ajustes_concicuentafechasdif_conciliacionorderfecha (QString cuenta, QDate inicial, QDate final, QString dif) {
    QString cad2= "select fecha,concepto,haber from ajustes_conci where cuenta='";
    cad2 += cuenta.left(-1).replace("'","''");
    cad2 += "' and fecha>='";
    cad2 += inicial.toString("yyyy-MM-dd");
    cad2 += "' and fecha<='";
    cad2 += final.toString("yyyy-MM-dd");
    cad2 += "' and haber>0 and ";
    cad2 += "not conciliado";
    cad2 += " and dif_conciliacion='";
    cad2 += dif.left(-1).replace("'","''");
    cad2 += "' order by fecha";
    return ejecutar(cad2);
}

// 
QSqlQuery basedatos::selectDif_conciliaciondiariocuentafechasdebegrouporderdif_conciliacion (QString cuenta, QDate inicial, QDate final) {
    QString cadena = "select dif_conciliacion from diario where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and debe>0 and ";
    cadena += "not conciliado";
    cadena += " group by dif_conciliacion order by dif_conciliacion";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select3Diariocuentafechasdebedif_conciliacionorderfecha (QString cuenta, QDate inicial, QDate final, QString dif) {
    QString cad2 = "select fecha,concepto,debe from diario where cuenta='";
    cad2 += cuenta.left(-1).replace("'","''");
    cad2 += "' and fecha>='";
    cad2 += inicial.toString("yyyy-MM-dd");
    cad2 += "' and fecha<='";
    cad2 += final.toString("yyyy-MM-dd");
    cad2 += "' and debe>0 and not conciliado and dif_conciliacion='";
    cad2 += dif.left(-1).replace("'","''");
    cad2 += "' order by fecha";
    return ejecutar(cad2);
}

//
QSqlQuery basedatos::selectDif_conciliacioncuenta_ext_concicuentafechasdebegrouporderdif_conciliacion (QString cuenta, QDate inicial, QDate final) {
    QString cadena = "select dif_conciliacion from cuenta_ext_conci where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and debe>0 and ";
    cadena += "not conciliado";
    cadena += " group by dif_conciliacion order by dif_conciliacion";
    return ejecutar(cadena);
}

//
QSqlQuery basedatos::selectDif_conciliacionajustes_concicuentafechasdebegrouporderdif_conciliacion (QString cuenta, QDate inicial, QDate final) {
    QString cadena = "select dif_conciliacion from ajustes_conci where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and debe>0 and ";
    cadena += "not conciliado";
    cadena += " group by dif_conciliacion order by dif_conciliacion";

    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select3Cuenta_ext_concicuentafechasdebedif_conciliacionorderfecha (QString cuenta, QDate inicial, QDate final, QString dif) {
    QString cad2 = "select fecha,concepto,debe from cuenta_ext_conci where cuenta='";
    cad2 += cuenta.left(-1).replace("'","''");
    cad2 += "' and fecha>='";
    cad2 += inicial.toString("yyyy-MM-dd");
    cad2 += "' and fecha<='";
    cad2 += final.toString("yyyy-MM-dd");
    cad2 += "' and debe>0 and ";
    cad2 += "not conciliado";
    cad2 += " and dif_conciliacion='";
    cad2 += dif.left(-1).replace("'","''");
    cad2 += "' order by fecha";
    return ejecutar(cad2);
}

// 
QSqlQuery basedatos::select3Ajustes_concicuentafechasdebedif_conciliacionorderfecha (QString cuenta, QDate inicial, QDate final, QString dif) {
    QString cad2 = "select fecha,concepto,debe from ajustes_conci where cuenta='";
    cad2 += cuenta.left(-1).replace("'","''");
    cad2 += "' and fecha>='";
    cad2 += inicial.toString("yyyy-MM-dd");
    cad2 += "' and fecha<='";
    cad2 += final.toString("yyyy-MM-dd");
    cad2 += "' and debe>0 and ";
    cad2 += "not conciliado";
    cad2 += " and dif_conciliacion='";
    cad2 += dif.left(-1).replace("'","''");
    cad2 += "' order by fecha";
    return ejecutar(cad2);
}

// 
QDate basedatos::selectAperturaejerciciosaperturacierre(QString ini, QString fin) {
    QString cadena = "select apertura from ejercicios where apertura<='";
    cadena += ini;
    cadena += "' and cierre>='";
    cadena += fin;
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) &&(query.first()) )
    {
        return query.value(0).toDate();
    }
    return QDate::currentDate();
}

// 
QDate basedatos::selectCierreejerciciosaperturacierre(QString ini, QString fin) {
    QString cadena = "select cierre from ejercicios where apertura<='";
    cadena += ini.left(-1).replace("'","''");
    cadena += "' and cierre>='";
    cadena += fin.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) &&(query.first()) )
    {
        return query.value(0).toDate();
    }
    return QDate::currentDate();
}

// 
qlonglong basedatos::selectCountasientodiariofechasfiltro (QDate inicial, QDate final, QString filtro) {
    QString cadena = "select count(asiento) from diario where fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    if (filtro.length() > 0)
    {
        cadena += " and ";
        cadena += filtro;
    }
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toLongLong();
    }
    return 0;
}


//
QSqlQuery basedatos::select8Diariofechasfiltroorderfechapase (QDate inicial, QDate final, QString filtro) {
    QString cadena = "select fecha,asiento,concepto,debe,haber,documento,cuenta,ci from diario where fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena +="' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena +="'";
    if (filtro.length()>0)
    {
        cadena+=" and ";
        cadena+=filtro;
    }
    
    // cadena+="' and cuenta='";
    // cadena+=ui.subcuentalineEdit->text();
    cadena+=" order by fecha,pase";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select9Diarioasientoorderpase (QString asiento) {
    QString consulta = "SELECT cuenta,concepto,debe,haber,documento,asiento,fecha,pase,ci ";
    consulta += "FROM diario where asiento=";
    consulta += asiento.left(-1).replace("'","''");
    consulta+=" order by pase";
    return ejecutar(consulta);
}

// 
QSqlQuery basedatos::select14Libroivapase (QString cadpase) {
    QString consulta = "SELECT cta_base_iva, base_iva, clave_iva, tipo_iva, ";
    consulta += "tipo_re, cuota_iva, cuenta_fra, ";
    if (cualControlador() == SQLITE) {
        consulta += "substr(fecha_fra,9,2), ";
        consulta += "substr(fecha_fra,6,2), substr(fecha_fra,1,4)";
    }
    else if (cualControlador() == MYSQL) {
        consulta += "DAY(fecha_fra), ";
        consulta += "MONTH(fecha_fra), YEAR(fecha_fra)";
    }
    else {
        consulta += "date_part('day',fecha_fra), ";
        consulta += "date_part('month',fecha_fra), date_part('year',fecha_fra)";
    }
    consulta += ",soportado,prorrata,rectificativa,autofactura,fecha_fra, fecha_operacion, ";
    consulta += "clave_operacion, bi_costo, rectificada, nfacturas, finicial, ffinal, "
                "bien_inversion, afecto, agrario, nombre, cif, exento_dcho_ded, "
                "isp_op_interiores, importacion, exportacion, caja_iva ";
    consulta += "from libroiva where pase=";
    consulta += cadpase.left(-1).replace("'","''");
	return ejecutar(consulta);
}

// 
QSqlQuery basedatos::select7Diariofechasfiltroorderfechapase (QDate inicial, QDate final, QString filtro) {
    QString cadena = "select fecha,asiento,concepto,cuenta,ci,debe,haber from diario where fecha>='";  
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    if (filtro.length()>0)
    {
        cadena += " and ";
        cadena += filtro;
    }
    cadena += " order by fecha,pase";
    return ejecutar(cadena);
}

//
qlonglong basedatos::selectCountasientodiariofechascondicion (QDate inicial, QDate final, QString condicion) {
    QString cadena = "select count(asiento) from diario where fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and ";
    cadena += condicion;

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toLongLong();
    }
    return 0;
}

// 
QSqlQuery basedatos::select9Diariofechascondicionorderfechapase (QDate inicial, QDate final, QString condicion) {
    QString cadena = "select cuenta,fecha, asiento,concepto, debe,haber, documento, "
                     "diario,ci, pase from diario where fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and ";
    cadena += condicion;
    cadena += " order by fecha,pase";
    return ejecutar(cadena);
}

//
QSqlQuery basedatos::selectSumdebesumhaberdiariofechascondicion (QDate fechainiejercicio, QDate inicial, QString condicion) {
    QString cadena = "select sum(debe),sum(haber) from diario where fecha>='";
    cadena += fechainiejercicio.toString("yyyy-MM-dd");
    cadena += "' and fecha<'";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and ";
    cadena += condicion;
    return ejecutar(cadena);
}

//
QSqlQuery basedatos::selectSumdebesumhaberdiariofechacondicion (QDate inicial, QString condicion) {
    QString cadena="select sum(debe),sum(haber) from diario where fecha<'";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and ";
    cadena += condicion;
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectCodigodescripcionplancontableauxiliarordercodigo (bool auxiliar) {
    QString cadena = "select codigo,descripcion from plancontable where ";
    if (auxiliar) { cadena += "auxiliar"; }
    else { cadena += "not auxiliar"; }
    cadena += " order by codigo";
    return ejecutar(cadena);
}

// 
double basedatos::selectSumimportevencimientospase_diario_operacion (QString elpase) {
    QString consultaimpvto = "select sum(importe) from vencimientos where ";
    consultaimpvto += "pase_diario_operacion=";
    consultaimpvto += elpase.left(-1).replace("'","''");
    QSqlQuery query = ejecutar( consultaimpvto );
    if ( (query.isActive()) && (query.next()) )
    {
	    return query.value(0).toDouble();
    }
    return 0.0;
}

// 
bool basedatos::selectsiPendientevencimientosnum (QString venci) {
    QString consulta = "select pendiente from vencimientos where num=";
    consulta += venci.left(-1).replace("'","''");
    QSqlQuery query = ejecutar(consulta);

    if ( (query.isActive()) && (query.first()) )
        if (query.value(0).toBool()) return true;
    return false;
}

// 
qlonglong basedatos::selectCountasientodiariofechascuenta (QDate inicial, QDate final, QString cuenta) {
    QString cadena = "select count(asiento) from diario where fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
	QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toLongLong();
    }
    return -1;
}

// 
QSqlQuery basedatos::select5Diariofechascuentaorderfechapase (QDate inicial, QDate final, QString cuenta) {
    QString cadena = "select fecha,asiento,concepto,debe,haber,documento from diario where fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' order by fecha,pase";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectCodigoperiodoejercicioorderinicioasc (QString ejercicio) {
    QString cadena = "select codigo from periodos where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' order by inicio asc";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectPresupuestospresupuestoejerciciocuenta (QString ejercicio, QString cuenta) {
    QString cadena = "select presupuesto1,presupuesto2,presupuesto3,presupuesto4,"
        "presupuesto5,presupuesto6,presupuesto7,presupuesto8,presupuesto9,"
        "presupuesto10,presupuesto11,presupuesto12,presupuesto13,presupuesto14,"
        "presupuesto15 from presupuesto where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' and cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectIniciofinperiodosejercicioorderinicio (QString ejercicio) {
    QString cad = "select inicio,fin from periodos where ejercicio='";
    cad += ejercicio.left(-1).replace("'","''");
    cad += "' order by inicio";
    return ejecutar(cad);
}

// 
QSqlQuery basedatos::selectSumdebesumhaberdiariocuentafechasnocierrenoregularizacion (QString cuenta, QDate inicial, QDate final) {
    QString cadbase = "select sum(debe)-sum(haber) from diario where ";
    if (!esauxiliar(cuenta))
    {
        if (cualControlador() == SQLITE) {
            cadbase += "cuenta like '"+ cuenta.left(-1).replace("'","''") +"%'";
        }
        else {
            cadbase += "position('";
            cadbase += cuenta.left(-1).replace("'","''");
            cadbase += "' in cuenta)=1";
        }
     }
    else
    {
        cadbase += "cuenta='";
        cadbase += cuenta.left(-1).replace("'","''");
        cadbase += "'";
    }
    cadbase += " and fecha>='";
    cadbase += inicial.toString("yyyy-MM-dd");
    cadbase += "' and fecha<='";
    cadbase += final.toString("yyyy-MM-dd");
    cadbase += "' and diario!='";
    cadbase += diario_cierre();
    cadbase += "' and diario!='";
    cadbase += diario_regularizacion();
    cadbase += "'";
    return ejecutar(cadbase);
}

// 
int basedatos::selectCountcuentapresupuestoauxiliar (bool subcuentas, QString selec, QString ejercicio) {
    QString cadena;
    if (!cod_longitud_variable())
    {
        if (cualControlador() == SQLITE) {
            cadena =  " length(cuenta)";
        }
        else {
            cadena =  " char_length(cuenta)";
        }
        if (subcuentas) cadena += "=";
        else cadena += "<";
        QString cadnum;
        cadnum.setNum(anchocuentas());
        cadena += cadnum;
    }
    else
      {
       if (subcuentas)
         cadena=" cuenta in (select codigo from plancontable where auxiliar)";
        else cadena=" cuenta in (select codigo from plancontable where not auxiliar)";
      }

    cadena += " and ejercicio='";
    cadena += ejercicio;
    cadena += "'";

    QString cadenacuentas = selec.left(-1).replace("'","''");
    int partes = cadenacuentas.count(',');
    if (cadenacuentas.length() > 0)
    {
        for (int veces=0;veces<=partes;veces++)
        {
            if (veces==0) cadena += " and (";
            if (cualControlador() == SQLITE) {
                cadena += "cuenta like '"+ cadenacuentas.section(',',veces,veces).left(-1).replace("'","''") +"%'";
            }
            else {
                cadena += "position('";
                cadena += cadenacuentas.section(',',veces,veces).left(-1).replace("'","''");
                cadena += "' in cuenta)=1";
            }
            if (veces==partes) cadena+=")";
            else cadena+=" or ";
        }
    }

    QString cadcuenta = "select count(cuenta) from presupuesto where";
    cadcuenta += cadena;

    QSqlQuery query = ejecutar(cadcuenta);

    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toInt();
    }
    return -1;
}

// 
QSqlQuery basedatos::selectCuentapresupuestopresupuestocuentaauxiliar (bool subcuentas, QString selec, QString ejercicio) {
    QString cad="select cuenta,presupuesto from presupuesto where";

    QString cadena;
    if (!cod_longitud_variable())
    {
        if (cualControlador() == SQLITE) {
            cadena =  " length(cuenta)";
        }
        else {
            cadena =  " char_length(cuenta)";
        }
        if (subcuentas) cadena += "=";
        else cadena += "<";
        QString cadnum;
        cadnum.setNum(anchocuentas());
        cadena += cadnum;
    }
    else
    {
     if (subcuentas)
       cadena=" cuenta in (select codigo from plancontable where auxiliar)";
     else cadena=" cuenta in (select codigo from plancontable where not auxiliar)";
    }

    cadena += " and ejercicio='";
    cadena += ejercicio;
    cadena += "'";

    QString cadenacuentas = selec.left(-1).replace("'","''");
    int partes = cadenacuentas.count(',');
    if (cadenacuentas.length() > 0)
    {
        for (int veces=0;veces<=partes;veces++)
        {
            if (veces==0) cadena += " and (";
            if (cualControlador() == SQLITE) {
                cadena += "cuenta like '"+ cadenacuentas.section(',',veces,veces).left(-1).replace("'","''") +"%'";
            }
            else {
                cadena += "position('";
                cadena += cadenacuentas.section(',',veces,veces).left(-1).replace("'","''");
                cadena += "' in cuenta)=1";
            }
            if (veces==partes) cadena+=")";
            else cadena+=" or ";
        }
    }

    cad += cadena;
    return ejecutar(cad);
}

// 
QSqlQuery basedatos::selectCuentasumdebesumhaberdiariofechas (int *totallineas, QString *periodo, QString ejercicio, int veces, int mescorriente) {
    QString cadena = "where fecha>='";
    cadena += inicioejercicio(ejercicio).toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += finejercicio(ejercicio).toString("yyyy-MM-dd");
    cadena += "' and ";
    if (veces==0 || veces>12)
    {
        if (veces==0) 
        {
            cadena += "diario='";
            cadena += diario_apertura();
            cadena += "'";
            *periodo = diario_apertura();
        }
        if (veces==13) 
        {
            cadena += "diario='";
            cadena += diario_regularizacion();
            cadena += "'";
            *periodo = diario_regularizacion();
        }
        if (veces==14) 
        {
            cadena += "diario='";
            cadena += diario_cierre();
            cadena += "'";
            *periodo = diario_cierre();
        }
    }
    else
    {
        QString cadnum;
        if (cualControlador() == SQLITE) {
            cadena += "substr(fecha,6,2)='";
            if (mescorriente<10)
               cadena += "0";
            cadena +=cadnum.setNum(mescorriente);
            cadena+="'";
        }
        else {
            cadena += "extract(month from fecha)=";
            cadena += cadnum.setNum(mescorriente);
        }

        if (veces==1)
        {
            if (cualControlador() == SQLITE) {
                cadena += " and substr(fecha,1,4)='";
                cadena += cadnum.setNum(inicioejercicio(ejercicio).year());
                cadena += "'";
            }
            else {
                cadena += " and extract(year from fecha)=";
                cadena += cadnum.setNum(inicioejercicio(ejercicio).year());
            }
            cadena += " and not diario='"+diario_apertura()+"'";

        }
        if (veces==12)
        {
            if (cualControlador() == SQLITE) {
                cadena += " and substr(fecha,1,4)='";
                cadena+=cadnum.setNum(finejercicio(ejercicio).year());
                cadena += "'";
            }
            else {
                cadena += " and extract(year from fecha)=";
                cadena+=cadnum.setNum(finejercicio(ejercicio).year());
            }
            cadena += " and not diario='"+diario_regularizacion()+"'";
            cadena += " and not diario='"+diario_cierre()+"'";

        }
        *periodo = cadmes(mescorriente);
    }
    cadena+=" group by cuenta order by cuenta";

    QString cadenatotal = "select cuenta,sum(debe),sum(haber) from diario ";
    cadenatotal += cadena;

    // totallineas
    QSqlQuery query = ejecutar("select count(*) from ("+ cadenatotal +") as tabla");
    if ( (query.isActive()) && (query.first()) ) {
        *totallineas = query.value(0).toInt();
    }
    else *totallineas = 0;

    // ahora vendría la consulta y el bucle
    return ejecutar(cadenatotal);
}

// 
QSqlQuery basedatos::selectFechacab_as_modeloasientomodelo (QString asientomodelo) {
    /* "aib                   bool,"
    "autofactura_ue        bool,"
    "autofactura_no_ue     bool,"
    "eib                   bool,"
    "eib_servicios         bool," */

    QString cadquery = "select fecha,tipo, aib, autofactura_ue, autofactura_no_ue,"
                       " eib, eib_servicios, diario, "
                       "isp_op_interiores, importacion, exportacion "
                       "from cab_as_modelo where asientomodelo='";
    cadquery += asientomodelo.left(-1).replace("'","''");
    cadquery += "'";
    return ejecutar(cadquery);
}

// 
QSqlQuery basedatos::selectClavetiposiva () {
    return ejecutar("SELECT clave from tiposiva order by clave");
}

// Devuelve variable, tipo, descripcion de var_as_modelo desde un asientomodelo ordenado
QSqlQuery basedatos::selectTablaVariable (QString nombre) {
    QString cadena = "select variable,tipo,descripcion,valor,guardar from var_as_modelo where asientomodelo='";
    cadena += nombre.left(-1).replace("'","''") +"'";
    cadena += " order by orden";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectDet_as_modeloasientomodeloorderorden (QString asientomodelo) {
    QString cadquery = "select cuenta,concepto,expresion,d_h,ci,baseiva,cuentabaseiva,"
                       "cuentafra,claveiva,documento,fecha_factura, "
                       "prorrata_e, rectificativa, fecha_op, clave_op, bicoste, "
                       "rectificada, numfacts, facini, facfinal, bien_inversion, afecto, "
                       "agrario, nombre, cif, cta_retenido, arrendamiento, clave, base_ret, "
                       "tipo_ret, retencion, ing_a_cta, ing_a_cta_rep, nombre_ret, "
                       "cif_ret, provincia ";
    cadquery += "from det_as_modelo where asientomodelo='";
    cadquery += asientomodelo.left(-1).replace("'","''");
    cadquery += "' order by orden";
    return ejecutar(cadquery);
}

// 
QSqlQuery basedatos::selectCodigodescripcionnuevoplanordercodigo () {
    return ejecutar("select codigo,descripcion from nuevoplan order by codigo");
}

// 
QSqlQuery basedatos::selectCodigodescripcionplancontableordercodigo () {
    return ejecutar("select codigo,descripcion from plancontable order by codigo");
}


// 
QSqlQuery basedatos::select10Diariodiarioasientoorderpase (QString elasiento, QString ejercicio) {
    QString consulta = "SELECT cuenta,concepto,debe,haber, documento,"
                       "asiento,fecha,pase,ci,diario, clave_ci, nrecepcion ";
    consulta += "FROM diario where asiento=";
    consulta += elasiento.left(-1).replace("'","''");
    consulta += " and ejercicio='";
    consulta += ejercicio.left(-1).replace("'","''");
    consulta += "'";
    consulta += " order by pase";
    return ejecutar(consulta);
}

// 
QSqlQuery basedatos::selectProrratalibroivapase (QString pase) {
    QString cad = "select prorrata from libroiva where pase=";
    cad += pase.left(-1).replace("'","''");
    return ejecutar(cad);
}

// 
QSqlQuery basedatos::selectConfiguracion () {
    QString cadena = "select empresa, nif, domicilio, poblacion, cpostal, provincia, email, web, "
        "anchocuentas, cuenta_iva_soportado, cuenta_iva_repercutido, "
        "cuenta_ret_ing, cuenta_ret_irpf,"
        "cuentasapagar, cuentasacobrar, clave_iva_defecto, clave_gastos, "
                "clave_ingresos, cuenta_pyg, prox_pase,prox_vencimiento, "
                "igic, analitica, id_registral, prorrata_iva, prorrata_especial, "
                "fecha_constitucion,objeto,actividad, analitica_parc, ctas_analitica, "
                "prox_documento, gestion_usuarios, analitica_tabla, provincia_def,"
                "cuentas_aa, cuentas_ag, amoinv, sec_recibidas, prox_domiciliacion, "
                "cod_ine_plaza, caja_iva, imagen "
        "from configuracion";
    return ejecutar(cadena);
}

bool basedatos::hay_secuencia_recibidas()
{
    QString cadena = "select sec_recibidas from configuracion";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toBool();
    }
    return false;
}



QString basedatos::cuentas_aa () {
    QString cadena = "select cuentas_aa from configuracion";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return QString();
}

QString basedatos::cuentas_ag () {
    QString cadena = "select cuentas_ag from configuracion";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return QString();
}



bool basedatos::con_amoinv()
{
    QString cadena = "select amoinv from configuracion";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toBool();
    }
    return false;
}

bool basedatos::con_facturacion()
{
    QString cadena = "select facturacion from configuracion";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toBool();
    }
    return false;
}

//
QString basedatos::provincia_def () {
    QString cadena = "select provincia_def from configuracion";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return QString();
}



// 
bool basedatos::selectasientodiariofechas (QDate inicial, QDate final) {
    QString cadena = "select asiento from diario where fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}

// 
bool basedatos::selectEjerciciopresupuestoejercicio (QString elcodigo) {
    QString cadena = "select ejercicio from presupuesto where ejercicio='";
    cadena += elcodigo.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}

// 
QSqlQuery basedatos::selectCodigodescripcionplancontablelengthauxiliarordercodigo (bool auxiliar) {
    QString cadena="select codigo,descripcion from plancontable where ";
    if (! cod_longitud_variable())
    {
        if (auxiliar) {
            if (cualControlador() == SQLITE) { cadena += "length(codigo)="; }
            else { cadena += "char_length(codigo)="; }
        }
        else {
            if (cualControlador() == SQLITE) { cadena += "length(codigo)<"; }
            else { cadena += "char_length(codigo)<"; }
        }
        QString cadnum;
        cadnum.setNum(anchocuentas());
        cadena += cadnum;
    }
    else 
    {
        if (!auxiliar) { cadena += "not "; }
        cadena += "auxiliar";
    }
    cadena += " order by codigo";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectCodequivequivalenciasplan () {
    return ejecutar("select cod,equiv from equivalenciasplan");
}

// 
QSqlQuery basedatos::selectCodigoplancontablecodigoordercodigo () {
    QString cadena = "select codigo from plancontable where ";
    if (!cod_longitud_variable())
    {
        if (cualControlador() == SQLITE) { cadena += "length(codigo)="; }
        else { cadena += "char_length(codigo)="; }
        QString cadnum;
        cadnum.setNum(anchocuentas());
        cadena += cadnum;
       }
       else cadena += "auxiliar";
    cadena += " order by codigo";
    return ejecutar(cadena);
}

// 
int basedatos::selectCountvariablevar_as_modeloasientomodelo (QString asientomodelo) {
    QString cadquery="select count(variable) from var_as_modelo where asientomodelo='";
    cadquery+=asientomodelo.left(-1).replace("'","''");
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

// 
QSqlQuery basedatos::selectCodigoejercicioscerradocerrandoordercodigo (bool cerrado, bool cerrando) {
    QString cadena = "select codigo from ejercicios where ";
    if (!cerrado) { cadena += "not "; }
    cadena += "cerrado";
    cadena += " and ";
    if (!cerrando) { cadena += "not "; }
    cadena += "cerrando";
    cadena += " order by codigo DESC";

    return ejecutar(cadena);
}

// 
int basedatos::selectCountcuenta_activoplanamortizaciones () {
    QSqlQuery query = ejecutar("select count(cuenta_activo) from planamortizaciones");
    if ( (query.isActive()) && (query.next()) ) return query.value(0).toInt();
    return 0;

}

// 
QSqlQuery basedatos::select10Planamortizacionesordercuenta_gasto () {
    QString cadena = "select cuenta_activo,fecha_func,codigo_activo,cuenta_am_acum,cuenta_gasto,";
    cadena += "valor_residual,coef_amort_contable,coef_amort_fiscal,amort_fiscal_1ejer,";
    cadena += "amort_personalizada ";
    cadena += "from planamortizaciones order by cuenta_gasto";
    return ejecutar(cadena);
}


QSqlQuery basedatos::select_amoinv_cuenta_gasto () {
    QString cadena = "select valor_adq, valor_amortizado, fecha_inicio, coef,";
    cadena += "cta_aa, cta_ag, numero ";
    cadena += "from amoinv order by cta_ag";
    return ejecutar(cadena);
}


int basedatos::registros_amoinv () {
    QString cadena = "select count(cta_aa) from amoinv";
    QSqlQuery query =ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) ) return query.value(0).toInt();
    return 0;
}


void basedatos::actu_amoinv(int numero, QString valor, QDate fecha_ult_amort)
{
   QString cadena="update amoinv set valor_amortizado=valor_amortizado +";
   cadena+=convapunto(valor);
   cadena+=", fecha_ult_amort='";
   cadena+=fecha_ult_amort.toString("yyyy-MM-dd");
   cadena+="' where numero=";
   QString cadnum; cadnum.setNum(numero);
   cadena+=cadnum;
   ejecutar(cadena);
}


QSqlQuery basedatos::select_cuenta_gasto_amoinv () {
    QString cadena = "select cta_ag ";
    cadena += "from amoinv";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectCiasignacionci_amortcuenta (QString cuenta) {
    QString cadq = "select ci,asignacion from ci_amort where cuenta='";
    cadq += cuenta.left(-1).replace("'","''");
    cadq += "'";
    return ejecutar(cadq);
}

// 
QString basedatos::selectAsientoamortcontableejercicio (QString ejercicio) {
    QString cadena = "select asiento from amortcontable where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toString();
    }
    return "";
}



QString basedatos::selectAsientoamortcontableejercicio_mes (QString ejercicio, int mes) {
QString cadquery = "select ";
switch (mes)
 {
 case 1: cadquery+="as_enero"; break;
 case 2: cadquery+="as_febrero"; break;
 case 3: cadquery+="as_marzo"; break;
 case 4: cadquery+="as_abril"; break;
 case 5: cadquery+="as_mayo"; break;
 case 6: cadquery+="as_junio"; break;
 case 7: cadquery+="as_julio"; break;
 case 8: cadquery+="as_agosto"; break;
 case 9: cadquery+="as_septiembre"; break;
 case 10: cadquery+="as_octubre"; break;
 case 11: cadquery+="as_noviembre"; break;
 case 12: cadquery+="as_diciembre"; break;
 }
cadquery+=" from amortcontable where ejercicio='";
cadquery += ejercicio.left(-1).replace("'","''");
cadquery+="'";
QSqlQuery query = ejecutar(cadquery);
// QMessageBox::warning( 0, QObject::tr("consulta"),cadquery);
if ( (query.isActive()) && (query.next()) )
   {
    return query.value(0).toString();
   }
return "";

}

// 
QSqlQuery basedatos::selectCuentadebehaberdiarioasiento (QString cadasiento) {
    QString cadena = "select cuenta,debe,haber,concepto,fecha from diario where asiento=";
    cadena += cadasiento.left(-1).replace("'","''");
    return ejecutar(cadena);
}

// 
QString basedatos::selectImporteamortpersejerciciocuenta (QString ejercicio, QString cuenta) {
    QString cadena = "select importe from amortpers where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' and cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// 
QSqlQuery basedatos::select15Cabeceraestadostitulo (QString titulo) {
    QString cadena = "select titulo,cabecera,parte1,parte2,observaciones,formulabasepor,"
        "analitica,grafico,haycolref,colref,diarios,estadosmedios,"
        "desglose, desglose_ctas, des_cabecera,des_pie "
        "from cabeceraestados ";
    cadena += "where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

bool basedatos::existe_estado(QString titulo)
{
    QString cadena="select titulo from cabeceraestados where titulo='";
    cadena+=titulo;
    cadena+="'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
       {
        return true;
       }
    return false;
}

// 
QSqlQuery basedatos::select5apartadoEstadostituloordernodo (QString titulo) {
    QString cadena = "select apartado,parte1,clave,nodo,formula from estados where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "' order by nodo";
    return ejecutar(cadena);
}

//
QSqlQuery basedatos::select4AmortfiscalycontableEjercicioscuentaejercicioorderapertura (QString cuenta) {
    QString cadena = "select a.ejercicio,sum(a.dotacion_contable),sum(a.dotacion_fiscal),"
        "sum(a.dotacion_contable-a.dotacion_fiscal) "
        "from amortfiscalycontable a,ejercicios e where a.cuenta_activo='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' and e.codigo=a.ejercicio group by a.ejercicio,e.apertura order by e.apertura;";

    return ejecutar(cadena);
}

// 
int basedatos::selectCountasientodiariofiltro (QString filtro) {
    QString cadena = "select count(asiento) from diario ";
    if (filtro.length()>0) cadena += " where "+ filtro;
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) ) return query.value(0).toInt();
    return 0;
}

// 
QSqlQuery basedatos::select7DiarioPlancontablecuentafiltroorderasientopase (QString filtro) {
    QString cadena = "select diario.asiento,diario.fecha,diario.cuenta,plancontable.descripcion,";
    cadena += "diario.concepto,diario.debe,diario.haber, diario.documento from diario,plancontable where ";
    cadena += "plancontable.codigo=diario.cuenta";
    if (filtro.length()>0) cadena += " and "+ filtro;
    cadena += " order by diario.asiento, diario.pase";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectCuenta_activocuenta_am_acumplanamortizacionesordercuenta_activo () {
    return ejecutar("select cuenta_activo, cuenta_am_acum, "
                    "cuenta_proveedor, coef_amort_contable, baja, fecha_baja, "
                    "motivo_baja"
                    " from planamortizaciones order by cuenta_activo");
}

// 
QSqlQuery basedatos::selectCodigocinivelordercodigo (int nivel) {
    QString cadquery = "SELECT codigo from ci where nivel=";
    cadquery += QString::number(nivel);
    cadquery += " order by codigo";
    return ejecutar(cadquery);
}

// 
QDate basedatos::selectMinaerturaejerciciosnocerrado () {
    QSqlQuery query = ejecutar("select min(apertura) from ejercicios where not cerrado");
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDate();
    }
    return QDate();
}

// 
int basedatos::selectCountcodigoejerciciosapertura (QString cadfecha) {
    QString cadena = "select count(codigo) from ejercicios where apertura>='";
    cadena += cadfecha.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return (query.value(0).toInt());
    }
    return 0;
}

// 
QSqlQuery basedatos::selectAperturacodigoejerciciosaperturaorderapertura (QString cadfecha) {
    QString cadena = "select apertura,codigo from ejercicios where apertura>='";
    cadena += cadfecha.left(-1).replace("'","''");
    cadena += "' order by apertura";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectEjercicioimporteaperturaamortpersejerciciosorderapertura (QString ctaactivo) {
    QString cadena = "select a.ejercicio, a.importe, e.apertura from "
	    "amortpers a, ejercicios e where "
		"a.ejercicio=e.codigo and "
		"a.cuenta='";
    cadena += ctaactivo.left(-1).replace("'","''");
    cadena += "' order by e.apertura;";
    return ejecutar(cadena); 
}

// 
bool basedatos::selectCici_amortcicuenta (QString ci, QString ctaactivo) {
    QString cadena = "select ci from ci_amort where ci='";
    cadena += ci.left(-1).replace("'","''");
    cadena += "' and cuenta='";
    cadena += ctaactivo.left(-1).replace("'","''");
    cadena += "'";

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive())&& (query.first()) ) return true;
    return false;
}

// 
QSqlQuery basedatos::selectSumasignacion100ci_amortcuenta (QString cuenta) {
    QString cadena = "select sum(asignacion)*100 from ci_amort where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectClavetiposivanoclave (QString clave) {
    QString cadquery = "SELECT clave from tiposiva where clave != '";
    cadquery += clave.left(-1).replace("'","''");
    cadquery += "'";
    return ejecutar(cadquery);
}


int basedatos::num_registros_recibidas( QString ejercicio,bool fechacontable,
                                        QDate fechaini, QDate fechafin, bool interiores,
                                        bool aib, bool autofactura, bool autofacturanoue,
                                        bool rectificativa, bool porseries,
                                        QString serie_inicial, QString serie_final,
                                        bool filtrarbinversion, bool solobi, bool sinbi,
                                        bool filtrarnosujetas, bool solonosujetas,
                                        bool sinnosujetas, bool agrario,
                                        bool isp_interiores, bool importaciones, bool solo_caja)
{
    QString cadena = "select count(libroiva.cuenta_fra) from libroiva,diario "
        "where diario.pase=libroiva.pase and ";
    if (solo_caja) cadena+="libroiva.caja_iva and ";
    if (ejercicio.length()>0)
    {
     cadena += "diario.fecha>='";
     cadena += inicioejercicio(ejercicio).toString("yyyy-MM-dd");
     cadena += "' and diario.fecha<='";
     cadena += finejercicio(ejercicio).toString("yyyy-MM-dd");
     cadena += "' ";
    }
     else
          {
           if (fechacontable)
              {
               cadena += "diario.fecha>='";
               cadena += fechaini.toString("yyyy-MM-dd");
               cadena += "' and diario.fecha<='";
               cadena += fechafin.toString("yyyy-MM-dd");
               cadena += "' ";
              }
              else
                  {
                   cadena += "libroiva.fecha_fra>='";
                   cadena += fechaini.toString("yyyy-MM-dd");
                   cadena += "' and libroiva.fecha_fra<='";
                   cadena += fechafin.toString("yyyy-MM-dd");
                   cadena += "' ";
                  }
          }

    if (porseries)
       {
        cadena += " and diario.documento>='";
        cadena += serie_inicial;
        cadena += "' and diario.documento<='";
        cadena += serie_final;
        cadena += "' ";
       }

    if (filtrarbinversion)
      {
        //  bool solobi, bool sinbi
        if (solobi)
            cadena+=" and libroiva.bien_inversion ";
        if (sinbi)
            cadena+=" and not libroiva.bien_inversion ";
      }

    if (filtrarnosujetas)
      {
        // bool solonosujetas, bool sinnosujetas
        if (solonosujetas)
            cadena+=" and libroiva.op_no_sujeta ";
        if (sinnosujetas)
            cadena+=" and not libroiva.op_no_sujeta ";
      }

    cadena+=" and libroiva.soportado";
    if (interiores)
       {
        if (!aib) cadena += " and not libroiva.aib";
        if (!autofactura) cadena += " and not libroiva.autofactura";
        if (!autofacturanoue) cadena += " and not libroiva.autofactura_no_ue";
        if (!rectificativa) cadena+=" and not libroiva.rectificativa";
        if (!agrario) cadena+=" and not libroiva.agrario";
        if (!isp_interiores) cadena+=" and not libroiva.isp_op_interiores";
        if (!importaciones) cadena+=" and not libroiva.importacion";
       }
       else
           {
            QString filtro;
            if (aib) filtro = "libroiva.aib";
            if (autofactura)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.autofactura";
               }
            if (autofacturanoue)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.autofactura_no_ue";
               }
            if (rectificativa)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.rectificativa";
               }
            if (agrario)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.agrario";
               }
            if (isp_interiores)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.isp_op_interiores";
               }
            if (importaciones)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.importacion";
               }
            cadena+=" and (";
            cadena+=filtro;
            cadena+=")";
           }
    // QMessageBox::warning( 0, QObject::tr("libroiva"),cadena);
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
       {
        return query.value(0).toInt();
       }
    return 0;
}

QSqlQuery basedatos::registros_recibidas(QString ejercicio, bool fechacontable,
                                        QDate fechaini, QDate fechafin,
                                        bool interiores, bool aib, bool autofactura, bool autofacturanoue,
                                        bool rectificativa, bool porseries,
                                        QString serie_inicial, QString serie_final,
                                        bool filtrarbinversion, bool solobi, bool sinbi,
                                        bool filtrarnosujetas, bool solonosujetas,
                                        bool sinnosujetas, bool agrario,
                                        bool isp_interiores, bool importaciones,
                                        bool orden_fecha_fac)
{
    QString cadena ="select d.documento,d.fecha,l.cuenta_fra,p.descripcion,d.asiento,"
            "l.base_iva,l.tipo_iva,l.cuota_iva,l.base_iva+l.cuota_iva,l.fecha_fra,l.autofactura, "
            "l.autofactura_no_ue, l.cta_base_iva, l.nombre, l.cif, d.nrecepcion, "
            "l.isp_op_interiores, l.importacion, l.caja_iva, l.pase "
	    "from diario d, libroiva l, plancontable p "
            "where d.pase=l.pase and ";

    if (ejercicio.length()>0)
    {
     cadena += "d.fecha>='";
     cadena += inicioejercicio(ejercicio).toString("yyyy-MM-dd");
     cadena += "' and d.fecha<='";
     cadena += finejercicio(ejercicio).toString("yyyy-MM-dd");
     cadena += "' ";
    }
     else
          {
           if (fechacontable)
              {
               cadena += "d.fecha>='";
               cadena += fechaini.toString("yyyy-MM-dd");
               cadena += "' and d.fecha<='";
               cadena += fechafin.toString("yyyy-MM-dd");
               cadena += "' ";
              }
              else
                  {
                   cadena += "l.fecha_fra>='";
                   cadena += fechaini.toString("yyyy-MM-dd");
                   cadena += "' and l.fecha_fra<='";
                   cadena += fechafin.toString("yyyy-MM-dd");
                   cadena += "' ";
                  }
          }

    if (porseries)
       {
        cadena += " and d.documento>='";
        cadena += serie_inicial;
        cadena += "' and d.documento<='";
        cadena += serie_final;
        cadena += "' ";
       }

    if (filtrarbinversion)
      {
        //  bool solobi, bool sinbi
        if (solobi)
            cadena+=" and l.bien_inversion ";
        if (sinbi)
            cadena+=" and not l.bien_inversion ";
      }

    if (filtrarnosujetas)
      {
        // bool solonosujetas, bool sinnosujetas
        if (solonosujetas)
            cadena+=" and l.op_no_sujeta ";
        if (sinnosujetas)
            cadena+=" and not l.op_no_sujeta ";
      }

    cadena+=" and l.soportado";
    if (interiores)
       {
        if (!aib) cadena += " and not l.aib";
        if (!autofactura) cadena += " and not l.autofactura";
        if (!autofacturanoue) cadena += " and not l.autofactura_no_ue";
        if (!rectificativa) cadena+=" and not l.rectificativa";
        if (!agrario) cadena+=" and not l.agrario";
        if (!isp_interiores) cadena+=" and not l.isp_op_interiores";
        if (!importaciones) cadena+=" and not l.importacion";
       }
       else
           {
            QString filtro;
            if (aib) filtro = "l.aib";
            if (autofactura)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.autofactura";
               }
            if (autofacturanoue)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.autofactura_no_ue";
               }
            if (rectificativa)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.rectificativa";
               }
            if (agrario)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.agrario";
               }
            if (isp_interiores)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.isp_op_interiores";
               }
            if (importaciones)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.importacion";
               }
            cadena+=" and (";
            cadena+=filtro;
            cadena+=")";
           }

    cadena += " and p.codigo=l.cuenta_fra";
    if (!orden_fecha_fac)
      {
       if (hay_secuencia_recibidas())
          cadena+=" order by d.nrecepcion";
         else
           cadena+=" order by d.fecha,d.asiento";
      }
      else cadena+=" order by l.fecha_fra";

    // QMessageBox::warning( 0, QObject::tr("libroiva"),cadena);

    return ejecutar(cadena);
}

double basedatos::total_factura_pase_iva(QString apunte, double *cuotaiva)
{
    QString cadena="select total_factura, cuota_iva, soportado, prorrata, afecto from libroiva where pase=";
    cadena+=apunte;
    QSqlQuery q=ejecutar(cadena);
    double factura=0;
    if (q.isActive())
        if (q.next())
           {
            factura=q.value(0).toDouble();
            *cuotaiva=q.value(1).toDouble();
            if (q.value(2).toBool()) *cuotaiva=*cuotaiva*q.value(3).toDouble()*q.value(4).toDouble();
           }
    return factura;
}

void basedatos::actu_venci_forzar_liq_iva(QString numvenci, QDate fecha)
{
  QString cadena="update vencimientos set forzar_liq_iva=";
  cadena +=cualControlador() == SQLITE ? "1" : "true";
  cadena+=", liq_iva_fecha='";
  cadena+=fecha.toString("yyyy-MM-dd");
  cadena+="' where num=";
  cadena+=numvenci;
  ejecutar(cadena);
}


void basedatos::actu_venci_quitar_forzar_liq_iva(QString numvenci)
{
  QString cadena="update vencimientos set forzar_liq_iva=";
  cadena +=cualControlador() == SQLITE ? "0" : "false";
  cadena+=" where num=";
  cadena+=numvenci;
  ejecutar(cadena);
}


QSqlQuery basedatos::datos_venci_apunte_iva_caja_liquidados(QString apunte)
{
    QString cadena="select v.fecha_vencimiento, v.liq_iva_fecha, v.forzar_liq_iva, v.importe, ";
    cadena.append("v.num, v.medio_realizacion, v.cuenta_bancaria from libroiva l, vencimientos v where l.pase=");
    cadena.append(apunte);
    cadena.append(" and v.pase_diario_operacion=l.apunte_tot_factura "
                  "and (not v.pendiente or v.forzar_liq_iva) order by v.num");
    return ejecutar(cadena);
}

QSqlQuery basedatos::datos_venci_apunte_iva_caja_no_liquidados(QString apunte)
{
    QString cadena="select v.fecha_vencimiento,  v.importe, v.num ";
    cadena.append("from libroiva l, vencimientos v where l.pase=");
    cadena.append(apunte);
    cadena.append(" and v.pase_diario_operacion=l.apunte_tot_factura "
                  "and ( v.pendiente and not v.forzar_liq_iva) order by v.num");
    return ejecutar(cadena);
}


double basedatos::ratio_iva_caja_liquidado(QString apuntediario, QDate fechaini, QDate fechafin)
{
  // primero vemos el total de la factura
  QString apunte_tot_factura;
  QString cadena="select total_factura, apunte_tot_factura from libroiva where pase=";
  cadena+=apuntediario;
  QSqlQuery q=ejecutar(cadena);
  double factura=0;
  if (q.isActive())
      if (q.next())
         {
          factura=q.value(0).toDouble();
          apunte_tot_factura=q.value(1).toString();
         }
  // QMessageBox::warning( 0, QObject::tr("libroiva"),cadena);
  // vemos importe cobrado/pagado de la factura en vencimientos

  cadena="select sum(importe) from vencimientos where (not pendiente or forzar_liq_iva) ";
  cadena+="and liq_iva_fecha>='";
  cadena+=fechaini.toString("yyyy-MM-dd");
  cadena+="' and liq_iva_fecha<='";
  cadena+=fechafin.toString("yyyy-MM-dd");
  cadena+="' and pase_diario_operacion=";
  cadena+=apunte_tot_factura;
  // QMessageBox::warning( 0, QObject::tr("libroiva"),cadena);
  double vencido=0;
  q=ejecutar(cadena);
  if (q.isActive())
      if (q.next())
          vencido=q.value(0).toDouble();

  if (factura>0.001 || factura< -0.001) return vencido/factura;

  return 0;
}

QSqlQuery basedatos::registros_recibidas_prorrata(QString ejercicio, bool fechacontable,
                                        QDate fechaini, QDate fechafin,
                                        bool interiores, bool aib, bool autofactura,
                                        bool autofacturanoue,
                                        bool rectificativa, bool porseries,
                                        QString serie_inicial, QString serie_final,
                                        bool filtrarbinversion, bool solobi, bool sinbi,
                                        bool filtrarnosujetas, bool solonosujetas,
                                        bool sinnosujetas, bool agrario,
                                        bool isp_interiores, bool importaciones, bool solo_caja)
{
    QString cadena ="select d.documento,d.fecha,l.cuenta_fra,p.descripcion,d.asiento,"
	    "l.base_iva,l.tipo_iva,l.cuota_iva,l.base_iva+l.cuota_iva,"
            "l.prorrata,l.cuota_iva*l.prorrata*l.afecto, l.fecha_fra,l.autofactura, "
            "l.autofactura_no_ue, l.afecto, l.nombre, l.cif, d.pase, d.nrecepcion, "
            "l.isp_op_interiores, l.importacion, l.caja_iva, d.cuenta "
            "from diario d, libroiva l, plancontable p "
            "where d.pase=l.pase and ";

    if (ejercicio.length()>0)
    {
     cadena += "d.fecha>='";
     cadena += inicioejercicio(ejercicio).toString("yyyy-MM-dd");
     cadena += "' and d.fecha<='";
     cadena += finejercicio(ejercicio).toString("yyyy-MM-dd");
     cadena += "' ";
    }
     else
          {
           if (fechacontable)
              {
               cadena += "d.fecha>='";
               cadena += fechaini.toString("yyyy-MM-dd");
               cadena += "' and d.fecha<='";
               cadena += fechafin.toString("yyyy-MM-dd");
               cadena += "' ";
              }
              else
                  {
                   cadena += "l.fecha_fra>='";
                   cadena += fechaini.toString("yyyy-MM-dd");
                   cadena += "' and l.fecha_fra<='";
                   cadena += fechafin.toString("yyyy-MM-dd");
                   cadena += "' ";
                  }
          }

    if (porseries)
       {
        cadena += " and d.documento>='";
        cadena += serie_inicial;
        cadena += "' and d.documento<='";
        cadena += serie_final;
        cadena += "' ";
       }

    if (filtrarbinversion)
      {
        //  bool solobi, bool sinbi
        if (solobi)
            cadena+=" and l.bien_inversion ";
        if (sinbi)
            cadena+=" and not l.bien_inversion ";
      }

    if (filtrarnosujetas)
      {
        // bool solonosujetas, bool sinnosujetas
        if (solonosujetas)
            cadena+=" and l.op_no_sujeta ";
        if (sinnosujetas)
            cadena+=" and not l.op_no_sujeta ";
      }

    cadena+=" and l.soportado";
    if (interiores)
       {
        if (!aib) cadena += " and not l.aib";
        if (!autofactura) cadena += " and not l.autofactura";
        if (!rectificativa) cadena+=" and not l.rectificativa";
        if (!autofacturanoue) cadena+=" and not l.autofactura_no_ue";
        if (!agrario) cadena+=" and not l.agrario";
        if (!isp_interiores) cadena+=" and not l.isp_op_interiores";
        if (!importaciones) cadena+=" and not l.importacion";
       }
       else
           {
            QString filtro;
            if (aib) filtro = "l.aib";
            if (autofactura)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.autofactura";
               }
            if (autofacturanoue)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.autofactura_no_ue";
               }
            if (rectificativa)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.rectificativa";
               }
            if (agrario)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.agrario";
               }
            if (isp_interiores)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.isp_op_interiores";
               }
            if (importaciones)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.importacion";
               }
            cadena+=" and (";
            cadena+=filtro;
            cadena+=")";
           }

    cadena += " and p.codigo=l.cuenta_fra";
    if (solo_caja) cadena+= " and l.caja_iva";
    if (hay_secuencia_recibidas()) cadena+= " order by d.nrecepcion";
       else cadena+=" order by d.fecha,d.asiento";

    return ejecutar(cadena);
}



QSqlQuery basedatos::iva_caja_recibidas_prorrata(QDate fechaini, QDate fechafin, bool binversion)
{
    QString cadena ="select d.documento,d.fecha,l.cuenta_fra,p.descripcion,d.asiento,"
            "l.base_iva,l.tipo_iva,l.cuota_iva,l.base_iva+l.cuota_iva,"
            "l.prorrata,l.cuota_iva*l.prorrata*l.afecto, l.fecha_fra,l.autofactura, "
            "l.autofactura_no_ue, l.afecto, l.nombre, l.cif, d.pase, d.nrecepcion, "
            "l.isp_op_interiores, l.importacion, l.caja_iva, d.cuenta "
            "from diario d, libroiva l, plancontable p "
            "where d.pase=l.pase and ";

     if (binversion)
            cadena+=" l.bien_inversion ";
        else
            cadena+=" not l.bien_inversion ";


    cadena+=" and l.soportado";

    cadena += " and p.codigo=l.cuenta_fra";
    cadena+= " and l.caja_iva ";

    cadena+= "and (l.apunte_tot_factura in ";
    cadena+= "( select pase_diario_operacion from vencimientos where liq_iva_fecha>='";
    cadena+=fechaini.toString("yyyy-MM-dd");
    cadena+="' and liq_iva_fecha<='";
    cadena+=fechafin.toString("yyyy-MM-dd");
    cadena+="' and not pendiente) or (d.fecha>='";
    cadena+=fechaini.toString("yyyy-MM-dd");
    cadena+="' and d.fecha<='";
    cadena+=fechafin.toString("yyyy-MM-dd");
    cadena+="') )";
    cadena+=" order by d.fecha,d.asiento";

    // QMessageBox::warning( 0, "Consulta",cadena);
    return ejecutar(cadena);
}


int basedatos::num_registros_emitidas( QString ejercicio,bool fechacontable, QDate fechaini,
                                       QDate fechafin, bool interiores, bool aib, bool eib,
                                       bool autofactura, bool rectificativa,  bool autofacturanoue,
                                       bool prestserviciosue,
                                       bool porseries,
                                       QString serie_inicial, QString serie_final,
                                       bool filtrarnosujetas, bool solonosujetas,
                                       bool sinnosujetas, bool exportaciones,
                                       bool isp_interiores, bool exentas_deduccion)
{
    QString cadena = "select count(libroiva.cuenta_fra) from libroiva,diario where "
                     "diario.pase=libroiva.pase and ";

    if (ejercicio.length()>0)
    {
     cadena += "diario.fecha>='";
     cadena += inicioejercicio(ejercicio).toString("yyyy-MM-dd");
     cadena += "' and diario.fecha<='";
     cadena += finejercicio(ejercicio).toString("yyyy-MM-dd");
     cadena += "' ";
    }
     else
          {
           if (fechacontable)
              {
               cadena += "diario.fecha>='";
               cadena += fechaini.toString("yyyy-MM-dd");
               cadena += "' and diario.fecha<='";
               cadena += fechafin.toString("yyyy-MM-dd");
               cadena += "' ";
              }
              else
                  {
                   cadena += "libroiva.fecha_fra>='";
                   cadena += fechaini.toString("yyyy-MM-dd");
                   cadena += "' and libroiva.fecha_fra<='";
                   cadena += fechafin.toString("yyyy-MM-dd");
                   cadena += "' ";
                  }
          }

    if (porseries)
       {
        cadena += " and diario.documento>='";
        cadena += serie_inicial;
        cadena += "' and diario.documento<='";
        cadena += serie_final;
        cadena += "' ";
       }

    if (filtrarnosujetas)
      {
        // bool solonosujetas, bool sinnosujetas
        if (solonosujetas)
            cadena+=" and libroiva.op_no_sujeta ";
        if (sinnosujetas)
            cadena+=" and not libroiva.op_no_sujeta ";
      }

    if (!exentas_deduccion) cadena+=" and not libroiva.exento_dcho_ded";

    cadena+=" and (not libroiva.soportado or (libroiva.soportado and libroiva.tipo_iva<0.00001 and"
            "(libroiva.aib or libroiva.autofactura or libroiva.autofactura_no_ue or libroiva.isp_op_interiores)))";
    if (interiores)
       {
        if (!aib) cadena += " and not libroiva.aib";
        if (!eib) cadena += " and not libroiva.eib";
        if (!autofactura) cadena += " and not libroiva.autofactura";
        if (!autofacturanoue) cadena += " and not libroiva.autofactura_no_ue";
        if (!prestserviciosue) cadena += " and not libroiva.pr_servicios_ue";
        if (!rectificativa) cadena += " and not libroiva.rectificativa";

        if (!exportaciones) cadena += " and not libroiva.exportacion";
        if (!isp_interiores) cadena += " and not libroiva.isp_op_interiores";
       }
       else
           {
            QString filtro;
            if (aib) filtro = "libroiva.aib";
            if (eib)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.eib";
               }
            if (autofactura)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.autofactura";
               }
            if (autofacturanoue)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.autofactura_no_ue";
               }
            if (prestserviciosue)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.pr_servicios_ue";
               }
            if (rectificativa)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.rectificativa";
               }

            if (exportaciones)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="libroiva.exportacion";
               }
            if (isp_interiores)
                {
                 if (filtro.length()>0) filtro+=" or ";
                 filtro+="libroiva.isp_op_interiores";
                }

            cadena+=" and (";
            cadena+=filtro;
            cadena+=")";
           }

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}


QSqlQuery basedatos::registros_emitidas(QString ejercicio, bool fechacontable, QDate fechaini,
                                        QDate fechafin, bool interiores, bool aib, bool eib,
                                        bool autofactura, bool rectificativa,
                                        bool autofacturanoue,
                                        bool prestserviciosue,
                                        bool porseries,
                                        QString serie_inicial, QString serie_final,
                                        bool filtrarnosujetas, bool solonosujetas,
                                        bool sinnosujetas, bool exportaciones,
                                        bool isp_interiores, bool exentas_deduccion,
                                        bool ordenfechafac, bool ordendocumento)
{
    QString cadena = "select d.documento, d.fecha,l.cuenta_fra, p.descripcion,d.asiento,"
            "l.base_iva,l.tipo_iva, l.base_iva*l.tipo_iva/100, l.tipo_re,"
            "l.tipo_re*l.base_iva/100, l.base_iva+l.cuota_iva, l.fecha_fra, l.cta_base_iva, "
            "l.nombre, l.cif, l.pase, l.caja_iva "
	    "from diario d, libroiva l, plancontable p "
            "where d.pase=l.pase and ";

    if (ejercicio.length()>0)
    {
     cadena += "d.fecha>='";
     cadena += inicioejercicio(ejercicio).toString("yyyy-MM-dd");
     cadena += "' and d.fecha<='";
     cadena += finejercicio(ejercicio).toString("yyyy-MM-dd");
     cadena += "' ";
    }
     else
          {
           if (fechacontable)
              {
               cadena += "d.fecha>='";
               cadena += fechaini.toString("yyyy-MM-dd");
               cadena += "' and d.fecha<='";
               cadena += fechafin.toString("yyyy-MM-dd");
               cadena += "' ";
              }
              else
                  {
                   cadena += "l.fecha_fra>='";
                   cadena += fechaini.toString("yyyy-MM-dd");
                   cadena += "' and l.fecha_fra<='";
                   cadena += fechafin.toString("yyyy-MM-dd");
                   cadena += "' ";
                  }
          }

    if (porseries)
       {
        cadena += " and d.documento>='";
        cadena += serie_inicial;
        cadena += "' and d.documento<='";
        cadena += serie_final;
        cadena += "' ";
       }

    if (filtrarnosujetas)
      {
        // bool solonosujetas, bool sinnosujetas
        if (solonosujetas)
            cadena+=" and l.op_no_sujeta ";
        if (sinnosujetas)
            cadena+=" and not l.op_no_sujeta ";
      }

    if (!exentas_deduccion) cadena+=" and not l.exento_dcho_ded";

    // cadena+=" and not l.soportado";
    // para AIB's y autofacturas exentas sólo hay un registro de libro facturas en soportado
    cadena+=" and (not l.soportado or (l.soportado and l.tipo_iva<0.00001 and"
            "(l.aib or l.autofactura or l.autofactura_no_ue or l.isp_op_interiores)))";


    if (interiores)
       {
        if (!aib) cadena += " and not l.aib";
        if (!eib) cadena += " and not l.eib";
        if (!autofactura) cadena += " and not l.autofactura";
        if (!autofacturanoue) cadena += " and not l.autofactura_no_ue";
        if (!prestserviciosue) cadena += " and not l.pr_servicios_ue";
        if (!rectificativa) cadena += " and not l.rectificativa";
        if (!exportaciones) cadena += " and not l.exportacion";
        if (!isp_interiores) cadena += " and not l.isp_op_interiores";
       }
       else
           {
            QString filtro;
            if (aib) filtro = "l.aib";
            if (eib)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.eib";
               }
            if (autofactura)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.autofactura";
               }
            if (autofacturanoue)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.autofactura_no_ue";
               }
            if (prestserviciosue)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.pr_servicios_ue";
               }
            if (rectificativa)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.rectificativa";
               }
            if (exportaciones)
               {
                if (filtro.length()>0) filtro+=" or ";
                filtro+="l.exportacion";
               }
            if (isp_interiores)
                {
                 if (filtro.length()>0) filtro+=" or ";
                 filtro+="l.isp_op_interiores";
                }
            cadena+=" and (";
            cadena+=filtro;
            cadena+=")";
           }
    cadena += " and p.codigo=l.cuenta_fra";
    if (ordendocumento)
       {
        cadena += " order by d.documento";
       }
    else
        {
         if (!ordenfechafac)
           cadena += " order by d.fecha,d.asiento";
         else
           cadena += " order by l.fecha_fra";
        }

    // QMessageBox::warning( 0, QObject::tr("consulta"),cadena);

    return ejecutar(cadena);

}




QSqlQuery basedatos::registros_emitidas_liq_caja(QDate fechaini,
                                        QDate fechafin)
{
    QString cadena = "select d.documento, d.fecha,l.cuenta_fra, p.descripcion,d.asiento,"
            "l.base_iva,l.tipo_iva, l.base_iva*l.tipo_iva/100, l.tipo_re,"
            "l.tipo_re*l.base_iva/100, l.base_iva+l.cuota_iva, l.fecha_fra, l.cta_base_iva, "
            "l.nombre, l.cif, l.pase, l.caja_iva, d.cuenta "
            "from diario d, libroiva l, plancontable p "
            "where d.pase=l.pase and ";

/*    cadena += "d.fecha>='";
    cadena += fechaini.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += fechafin.toString("yyyy-MM-dd");
    cadena += "' "; */
    cadena += "not l.soportado";
    cadena += " and p.codigo=l.cuenta_fra ";
    cadena+= "and l.caja_iva ";

    cadena+= "and (l.apunte_tot_factura in ";
    cadena+= "( select pase_diario_operacion from vencimientos where liq_iva_fecha>='";
    cadena+=fechaini.toString("yyyy-MM-dd");
    cadena+="' and liq_iva_fecha<='";
    cadena+=fechafin.toString("yyyy-MM-dd");
    cadena+="' and not pendiente) or (d.fecha>='";
    cadena+=fechaini.toString("yyyy-MM-dd");
    cadena+="' and d.fecha<='";
    cadena+=fechafin.toString("yyyy-MM-dd");
    cadena+="') )";


    cadena += " order by d.fecha,d.asiento";
    // QMessageBox::warning( 0, QObject::tr("consulta"),cadena);

    return ejecutar(cadena);

}


// a eliminar
int basedatos::selectCountcuenta_fralibroivadiariofechaaibautoractura (QString fecha, bool aib, bool autofactura) {
    QString cadena = "select count(libroiva.cuenta_fra) from libroiva,diario "
        "where diario.pase=libroiva.pase and "
        "diario.fecha>='";
    cadena += inicioejercicio(fecha).toString("yyyy-MM-dd");
    cadena += "' and diario.fecha<='";
    cadena += finejercicio(fecha).toString("yyyy-MM-dd");
    cadena += "' and soportado";
    if (aib) cadena += " and aib"; else cadena += " and not aib";
    if (autofactura) cadena += " and autofactura"; else cadena += " and not autofactura";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

// a eliminar
QSqlQuery basedatos::select10DiarioLibroivaPlancontablefechaaibautofacturaorderfechaasiento (QString fecha, bool aib, bool autofactura) {
    QString cadena ="select d.documento,d.fecha,l.cuenta_fra,p.descripcion,d.asiento,"
	    "l.base_iva,l.tipo_iva,l.cuota_iva,l.base_iva+l.cuota_iva,l.fecha_fra "
	    "from diario d, libroiva l, plancontable p "
	    "where d.pase=l.pase and d.fecha>='";
    cadena += inicioejercicio(fecha).toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += finejercicio(fecha).toString("yyyy-MM-dd");
    cadena += "'";
    if (aib) cadena+=" and aib"; else cadena+=" and not aib";
    if (autofactura) cadena += " and autofactura"; else cadena += " and not autofactura";
    cadena += " and p.codigo=l.cuenta_fra and soportado"
	       " order by d.fecha,d.asiento";
    return ejecutar(cadena);
}

// a borrar
int basedatos::selectCountcuenta_fralibroivadiariofechaeibaibautofacturarectificativa (QString fecha, bool eib, bool aib, bool autofactura, bool rectificativa) {
    QString cadena = "select count(libroiva.cuenta_fra) from libroiva,diario where diario.pase=libroiva.pase and "
	    "diario.fecha>='";
    cadena += inicioejercicio(fecha).toString("yyyy-MM-dd");
    cadena += "' and diario.fecha<='";
    cadena += finejercicio(fecha).toString("yyyy-MM-dd");
    cadena += "' and not soportado";
    if (eib) cadena += " and eib"; else cadena += " and not eib";
    if (aib) cadena += " and aib"; else cadena += " and not aib";
    if (autofactura) cadena += " and autofactura"; else cadena += " and not autofactura";
    if (rectificativa) cadena += " and rectificativa"; else cadena += " and not rectificativa";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

// a borrar
QSqlQuery basedatos::select12DiarioLibroivaPlancontablefechaaibeibautofacturarectivicativa (QString fecha, bool aib, bool eib, bool autofactura, bool rectificativa) {
    QString cadena = "select d.documento,d.fecha,l.cuenta_fra,p.descripcion,d.asiento,"
	    "l.base_iva,l.tipo_iva,round(l.base_iva*l.tipo_iva/100,2),l.tipo_re,"
	    "round(l.tipo_re*l.base_iva/100,2),l.base_iva+l.cuota_iva, l.fecha_fra "
	    "from diario d, libroiva l, plancontable p "
	    "where d.pase=l.pase and d.fecha>='";
    cadena += inicioejercicio(fecha).toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += finejercicio(fecha).toString("yyyy-MM-dd");
    cadena += "'";
    if (aib) cadena += " and aib"; else cadena += " and not aib";
    if (eib) cadena += " and eib"; else cadena += " and not eib";
    if (autofactura) cadena += " and autofactura"; else cadena += " and not autofactura";
    if (rectificativa) cadena += " and rectificativa"; else cadena += " and not rectificativa";
    cadena += " and p.codigo=l.cuenta_fra and not soportado"
	    " order by d.fecha,d.asiento";
    return ejecutar(cadena);
}

// a borrar
int basedatos::selectCountcuenta_fralibroivadiariofechaaib (QString fecha, bool aib) {
    QString cadena="select count(libroiva.cuenta_fra) from libroiva,diario "
        "where diario.pase=libroiva.pase and "
        "diario.fecha>='";
    cadena+=inicioejercicio(fecha).toString("yyyy-MM-dd");
    cadena+="' and diario.fecha<='";
    cadena+=finejercicio(fecha).toString("yyyy-MM-dd");
    cadena+="' and soportado";
    if (aib) cadena+=" and aib"; else cadena+=" and not aib";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

// a borrar
QSqlQuery basedatos::select11DiarioLibroivaPlancontablefechaaiborderfechaasientodocumento (QString fecha, bool aib) {
    QString cadena = "select d.documento,d.fecha,l.cuenta_fra,p.descripcion,d.asiento,"
	    "l.base_iva,l.tipo_iva,l.cuota_iva,l.base_iva+l.cuota_iva, "
        "l.prorrata,l.cuota_iva*l.prorrata "
	    "from diario d, libroiva l, plancontable p "
	    "where d.pase=l.pase and d.fecha>='";
    cadena += inicioejercicio(fecha).toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += finejercicio(fecha).toString("yyyy-MM-dd");
    cadena += "'";
    if (aib) cadena += " and aib"; else cadena += " and not aib";
    cadena += " and p.codigo=l.cuenta_fra and soportado"
	    " order by d.fecha,d.asiento,d.documento";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::libroivafechas_sop_corrientes (QDate inicial, QDate final, bool caja_iva) {
    QString cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(l.cuota_iva),"
        "sum(l.base_iva+l.cuota_iva),sum(l.cuota_iva*l.prorrata*l.afecto), sum(debe-haber) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and l.soportado and not l.aib and not l.eib and l.cuota_iva != 0";
    cadena += " and not l.agrario";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and not l.bien_inversion and not l.autofactura and not l.autofactura_no_ue "
              " and not l.isp_op_interiores and not l.importacion ";
    cadena += caja_iva ? "and caja_iva " : "and not caja_iva "
              "group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}

QSqlQuery basedatos::libroivafechas_sop_inversion (QDate inicial, QDate final, bool caja_iva) {
    QString cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(l.cuota_iva),"
        "sum(l.base_iva+l.cuota_iva),sum(l.cuota_iva*l.prorrata*l.afecto), sum(debe-haber) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and l.soportado and not l.aib and not l.eib and l.cuota_iva != 0";
    cadena += " and not l.agrario";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and l.bien_inversion and not l.autofactura and not l.autofactura_no_ue "
              " and not l.isp_op_interiores and not l.importacion ";
    cadena += caja_iva ? "and caja_iva " : "and not caja_iva "
              "group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}



QSqlQuery basedatos::libroivafechas_sop_corrientes_importacion (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(l.cuota_iva),"
        "sum(l.base_iva+l.cuota_iva),sum(l.cuota_iva*l.prorrata*l.afecto), sum(debe-haber) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and l.soportado and not l.aib and not l.eib and l.cuota_iva != 0";
    cadena += " and not l.agrario";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and not l.bien_inversion and not l.autofactura and not l.autofactura_no_ue "
              " and not l.isp_op_interiores and l.importacion "
              "group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}

QSqlQuery basedatos::libroivafechas_sop_inversion_importacion (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(l.cuota_iva),"
        "sum(l.base_iva+l.cuota_iva),sum(l.cuota_iva*l.prorrata*l.afecto), sum(debe-haber) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and l.soportado and not l.aib and not l.eib and l.cuota_iva != 0";
    cadena += " and not l.agrario";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and l.bien_inversion and not l.autofactura and not l.autofactura_no_ue "
              " and not l.isp_op_interiores and l.importacion "
              "group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}









//
QSqlQuery basedatos::libroivafechas_sop_agrario (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(l.cuota_iva) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and l.soportado and l.cuota_iva != 0";
    cadena += " and l.agrario";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' "
              "group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}



QSqlQuery basedatos::baserecibidasexentas_corrientes (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and l.soportado and l.cuota_iva = 0 and not l.aib"
        " and not l.autofactura and not l.autofactura_no_ue and not l.isp_op_interiores";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and not l.bien_inversion and not l.op_no_sujeta and not l.importacion";
    return ejecutar(cadena);
}

QSqlQuery basedatos::baserecibidasexentas_corrientes_importacion (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and l.soportado and l.cuota_iva = 0 and not l.aib"
        " and not l.autofactura and not l.autofactura_no_ue and not l.isp_op_interiores";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and not l.bien_inversion and not l.op_no_sujeta and l.importacion";
    return ejecutar(cadena);
}


QSqlQuery basedatos::baserecibidasexentas_inversion (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and l.soportado and l.cuota_iva = 0 and not l.aib"
        " and not l.autofactura and not l.autofactura_no_ue and not l.isp_op_interiores";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and l.bien_inversion and not l.op_no_sujeta and not l.importacion";
    return ejecutar(cadena);
}

QSqlQuery basedatos::baserecibidasexentas_inversion_importacion (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and l.soportado and l.cuota_iva = 0 and not l.aib"
        " and not l.autofactura and not l.autofactura_no_ue and not l.isp_op_interiores";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and l.bien_inversion and not l.op_no_sujeta and l.importacion";
    return ejecutar(cadena);
}


QSqlQuery basedatos::baseautofacturasrecibidasexentas (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and l.soportado and l.cuota_iva = 0 and l.autofactura";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    return ejecutar(cadena);
}

QSqlQuery basedatos::baseautofacturasnouerecibidasexentas (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and l.soportado and l.cuota_iva = 0 and l.autofactura_no_ue";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    return ejecutar(cadena);
}

QSqlQuery basedatos::base_isp_interiores_recibidasexentas (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and l.soportado and l.cuota_iva = 0 and l.isp_op_interiores";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    return ejecutar(cadena);
}

QSqlQuery basedatos::baseemitidasexentasnoeib (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and not l.soportado and not l.eib "
        "and not l.autofactura and not l.autofactura_no_ue and not l.aib "
        "and not l.pr_servicios_ue "
        "and l.cuota_iva = 0";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    return ejecutar(cadena);
}



QSqlQuery basedatos::baseemitidasexentas_noeib_noexport (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and not l.soportado and not l.eib "
        "and not l.autofactura and not l.autofactura_no_ue and not l.aib "
        "and not l.pr_servicios_ue and not l.exportacion "
        "and l.cuota_iva = 0";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    return ejecutar(cadena);
}


QSqlQuery basedatos::baseemitidasexentas_noeib_noexport_nodeduc (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and not l.soportado and not l.eib "
        "and not l.autofactura and not l.autofactura_no_ue and not l.aib "
        "and not l.pr_servicios_ue and not l.exportacion and not l.exento_dcho_ded "
        "and l.cuota_iva = 0";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    return ejecutar(cadena);
}




// 
QSqlQuery basedatos::select7DiarioLibroivafechasgroupcuentatipo_ivatipo_re (QDate inicial,
                                                                            QDate final,
                                                                            bool caja_iva) {
  QString cadena;
  if (cualControlador() == SQLITE)
     {
      cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),"
               "sum(round(cast(l.base_iva*l.tipo_iva as real)/100,2)),"
               "l.tipo_re,sum(round(cast(l.base_iva*l.tipo_re as real)/100,2)),";
     }
    else
      {
       cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(round(l.base_iva*l.tipo_iva/100,2)),"
        "l.tipo_re,sum(round(l.base_iva*l.tipo_re/100,2)),";
      }

    cadena+="sum(l.base_iva+l.cuota_iva) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and not l.soportado and not l.aib and not l.eib "
        "and not l.autofactura and not l.autofactura_no_ue "
        "and not l.isp_op_interiores and not l.importacion "
        "and l.cuota_iva !=0";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' ";
    cadena += caja_iva ? "and caja_iva" : "and not caja_iva";
    cadena += " group by d.cuenta,l.tipo_iva,l.tipo_re";
    return ejecutar(cadena);
}

// ESTO ES PARA LIQUIDACIÓN AIB
QSqlQuery basedatos::libroiva_aib_corrientes (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(l.cuota_iva*l.prorrata) "
        "from diario d, libroiva l "
    	"where d.pase=l.pase and l.soportado and  l.aib and not l.eib";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and (l.cuota_iva>0.0001 or l.cuota_iva<-0.0001) and not bien_inversion group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}


// ESTO ES PARA LIQUIDACIÓN AIB
QSqlQuery basedatos::libroiva_aib_inversion (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(l.cuota_iva*l.prorrata) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and l.soportado and l.aib and not l.eib";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and (l.cuota_iva>0.0001 or l.cuota_iva<-0.0001) and bien_inversion group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}

QSqlQuery basedatos::baseemitidasexentasaib_corrientes (QDate inicial, QDate final)
{

    QString cadena = "select sum(l.base_iva) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and l.soportado and  l.aib and not l.eib and l.cuota_iva = 0";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and not bien_inversion";
    return ejecutar(cadena);
}

QSqlQuery basedatos::baseemitidasexentasaib_inversion (QDate inicial, QDate final)
{

    QString cadena = "select sum(l.base_iva) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and l.soportado and  l.aib and not l.eib and l.cuota_iva = 0";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and bien_inversion";
    return ejecutar(cadena);
}


// esto es para aib la parte repercutido
QSqlQuery basedatos::select4DiarioLibroivafechasgroupcuentatipo_ivasinprorrata (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(l.cuota_iva) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and not l.soportado and  l.aib and not l.eib";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and (l.cuota_iva>0.0001 or l.cuota_iva<-0.0001) group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}

// suma base eib
QSqlQuery basedatos::selectSumbase_ivalibroivadiariofechas (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and l.eib";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    return ejecutar(cadena);
}

// suma base prestación servicios ue
QSqlQuery basedatos::selectSumbase_serv_ue_libroivadiariofechas (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and l.pr_servicios_ue";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    return ejecutar(cadena);
}


// suma base exportaciones
QSqlQuery basedatos::selectSumbase_expor_fechas (QDate inicial, QDate final) {
    QString cadena = "select sum(l.base_iva) "
        "from libroiva l, diario d "
        "where d.pase=l.pase and l.exportacion";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    return ejecutar(cadena);
}




// ESTO ES PARA LIQUIDACIÓN Autofacturas servicios ue
QSqlQuery basedatos::libroiva_autofacturas_ue (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(l.cuota_iva*l.prorrata) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and l.soportado and  l.autofactura";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and (l.cuota_iva>0.0001 or l.cuota_iva<-0.0001) group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}


// esto es para autofacturas servicios ue, la parte repercutido
QSqlQuery basedatos::libroiva_autofacturas_ue_emitidas (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta, l.tipo_iva,sum(l.base_iva), sum(l.cuota_iva) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and not l.soportado and  l.autofactura";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and (l.cuota_iva>0.0001 or l.cuota_iva<-0.0001) group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}


// --------------------------------
// ESTO ES PARA LIQUIDACIÓN Autofacturas servicios fuera ue
QSqlQuery basedatos::libroiva_autofacturas_no_ue (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(l.cuota_iva*l.prorrata) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and l.soportado and  l.autofactura_no_ue";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and (l.cuota_iva>0 or l.cuota_iva<-0.0001) group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}


// esto es para autofacturas servicios ue, la parte repercutido
QSqlQuery basedatos::libroiva_autofacturas_no_ue_emitidas (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta, l.tipo_iva,sum(l.base_iva), sum(l.cuota_iva) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and not l.soportado and  l.autofactura_no_ue";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'  and (l.cuota_iva>0 or l.cuota_iva<-0.0001) group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}



QSqlQuery basedatos::libroiva_isp_interiores_recibidas (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta,l.tipo_iva,sum(l.base_iva),sum(l.cuota_iva*l.prorrata) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and l.soportado and  l.isp_op_interiores";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and (l.cuota_iva>0 or l.cuota_iva<-0.0001) group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}


// esto es para autofacturas servicios ue, la parte repercutido
QSqlQuery basedatos::libroiva_isp_interiores_emitidas (QDate inicial, QDate final) {
    QString cadena = "select d.cuenta, l.tipo_iva,sum(l.base_iva), sum(l.cuota_iva) "
        "from diario d, libroiva l "
        "where d.pase=l.pase and not l.soportado and  l.isp_op_interiores";
    cadena += " and d.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'  and (l.cuota_iva>0 or l.cuota_iva<-0.0001) group by d.cuenta,l.tipo_iva";
    return ejecutar(cadena);
}





// 
double basedatos::selectSumimportevendimientosfiltroderecho (QString filtro, bool derecho) {
    QString consulta = "select sum(importe) from vencimientos where ";
    if (filtro.trimmed().length() > 0) {
        consulta += filtro;
        consulta += " and ";
    }
    if (derecho) { consulta += "derecho"; }
    else { consulta += "not derecho"; }
    QSqlQuery query = ejecutar(consulta);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDouble();
    }
    return 0;
}


double basedatos::selectSumimpagados (QString filtro, bool derecho) {
    QString consulta = "select sum(importe) from vencimientos where ";
    if (filtro.trimmed().length() > 0) {
        consulta += filtro;
        consulta += " and ";
    }
    if (derecho) { consulta += "derecho"; }
    else { consulta += "not derecho"; }
    consulta+=" and impagado";
    QSqlQuery query = ejecutar(consulta);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDouble();
    }
    return 0;
}

// 
QDate basedatos::selectMaxcierrecountcierreejercicios () {
    QDate vfecha;
    vfecha.setYMD(QDate::currentDate().year(),1,1);
    QSqlQuery query = ejecutar("select max(cierre),count(cierre) from ejercicios");
    if ( (query.isActive()) && (query.next()) && (query.value(1).toInt()>0) )
    {
        vfecha = query.value(0).toDate().addDays(1);
    }
    return vfecha;
}

// 
bool basedatos::selectCodigoejercicioscodigo (QString codigo) {
    QString cadena = "select codigo from ejercicios where codigo='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return true;
    }
    return false;
}

// 
QSqlQuery basedatos::selectAperturacierreejercicios () {
    return ejecutar("select apertura,cierre from ejercicios");
}

// 
QSqlQuery basedatos::selectCodigoiniciofinperiodosejercicioorrderinicio (QString ejercicio) {
    QString cadena = "select codigo,inicio,fin,deshabilitado from periodos where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' order by inicio desc";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select9Planamortizacionesordercuenta_activo () {
    QString cadena = "select cuenta_activo,fecha_func,codigo_activo,cuenta_am_acum,cuenta_gasto,"
	    "valor_residual,coef_amort_contable,coef_amort_fiscal,amort_fiscal_1ejer "
	    "from planamortizaciones order by cuenta_activo";
    return ejecutar(cadena);
}


//
QSqlQuery basedatos::select_inv_inmovilizado () {

           /*   "cta_aa       varchar(40),"
              "cta_ag       varchar(40),"
              "concepto     varchar(255),"
              "fecha_inicio date,"
              "fecha_compra date,"
              "fecha_ult_amort date,"
              "coef         float8,"
              "valor_adq    numeric(14,2),"
              "valor_amortizado numeric(14,2),"
              "factura      varchar(255),"
              "proveedor    varchar(255))"; */
    QString cadena = "select numero,cta_aa, cta_ag ,concepto,fecha_inicio, fecha_compra,"
            "fecha_ult_amort, coef, valor_adq, valor_amortizado, factura, proveedor "
            "from amoinv order by numero";
    return ejecutar(cadena);
}

QSqlQuery basedatos::select_reg_inv_inmovilizado (QString numero) {
    QString cadena = "select numero,cta_aa, cta_ag ,concepto,fecha_inicio, fecha_compra,"
            "fecha_ult_amort, coef, valor_adq, valor_amortizado, factura, proveedor "
            "from amoinv where numero=";
    cadena+=numero;
    return ejecutar(cadena);

}


void basedatos::borra_reg_inv_inmovilizado (QString numero) {
    QString cadena = "delete from amoinv where numero=";
    cadena+=numero;
    ejecutar(cadena);
}


void basedatos::inserta_item_inmov(QString cta_aa,
                                   QString cta_ag,
                                   QString concepto,
                                   QDate fecha_inicio,
                                   QDate fecha_compra,
                                   QDate fecha_ult_amort,
                                   QString coef,
                                   QString valor_adq,
                                   QString valor_amortizado,
                                   QString factura,
                                   QString proveedor)
{
   QString cadena="insert into amoinv (cta_aa, cta_ag ,concepto,fecha_inicio, fecha_compra,"
                  "fecha_ult_amort, coef, valor_adq, valor_amortizado, factura, proveedor)"
                  " values('";
   cadena+=cta_aa + "', '";
   cadena+=cta_ag + "', '";
   cadena+=concepto.left(-1).replace("'","''") + "', '";
   cadena+=fecha_inicio.toString("yyyy-MM-dd") + "', '";
   cadena+=fecha_compra.toString("yyyy-MM-dd") + "', '";
   cadena+=fecha_ult_amort.toString("yyyy-MM-dd") + "', ";
   double valcoef; valcoef=coef.toDouble()/100;
   QString cadcoef; cadcoef.setNum(valcoef,'f',5);
   cadena+=cadcoef + ", ";
   cadena+=(valor_adq.isEmpty() ? "0" : convapunto(valor_adq)) + ", ";
   cadena+=(valor_amortizado.isEmpty() ? "0" : convapunto(valor_amortizado)) + ", '";
   cadena+=factura.left(-1).replace("'","''") + "', '";
   cadena+=proveedor.left(-1).replace("'","''") + "');";

   // QMessageBox::warning( 0, "CONSULTA",cadena);
   ejecutar(cadena);

}

void basedatos::modifica_item_imov(QString num,
                                   QString cta_aa,
                                   QString cta_ag,
                                   QString concepto,
                                   QDate fecha_inicio,
                                   QDate fecha_compra,
                                   QDate fecha_ult_amort,
                                   QString coef,
                                   QString valor_adq,
                                   QString valor_amortizado,
                                   QString factura,
                                   QString proveedor)
{
   QString cadena="update amoinv set ";
   cadena+="cta_aa='";
   cadena+=cta_aa;
   cadena+="', cta_ag='" + cta_ag;
   cadena+="', concepto='" + concepto.left(-1).replace("'","''");
   cadena+="', fecha_inicio='" + fecha_inicio.toString("yyyy-MM-dd");
   cadena+="', fecha_compra='" + fecha_compra.toString("yyyy-MM-dd");
   cadena+="', fecha_ult_amort='" + fecha_ult_amort.toString("yyyy-MM-dd");
   double valcoef; valcoef=coef.toDouble()/100;
   QString cadcoef; cadcoef.setNum(valcoef,'f',5);

   cadena+="', coef=" +cadcoef;
   cadena+=", valor_adq=" +(valor_adq.isEmpty() ? "0" : convapunto(valor_adq));
   cadena+=", valor_amortizado=" +(valor_amortizado.isEmpty() ? "0" : convapunto(valor_amortizado));
   cadena+= ", factura='" + factura.left(-1).replace("'","''");
   cadena+= "', proveedor='" + proveedor.left(-1).replace("'","''");
   cadena+="' where numero=";
   cadena+=num;
   ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectBasesPresupuestospresupuestoejerciciocuenta (QString ejercicio, QString cuenta) {
    QString cadena = "select base,base1,base2,base3,base4,base5,base6,base7,base8,base9,"
        "base10,base11,base12,base13,base14,base15,"
        "presupuesto,presupuesto1,presupuesto2,presupuesto3,presupuesto4,"
        "presupuesto5,presupuesto6,presupuesto7,presupuesto8,presupuesto9,"
        "presupuesto10,presupuesto11,presupuesto12,presupuesto13,presupuesto14,"
        "presupuesto15 from presupuesto where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' and cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select4Ejercicioscodigo (QString qejercicio) {
    QString cad = "select base_ejercicio,base_presupuesto,presupuesto_base_cero,incremento from ejercicios where codigo='";
    cad += qejercicio.left(-1).replace("'","''");
    cad += "'";
    return ejecutar(cad);
}

// 
QSqlQuery basedatos::select6casepresupuestoplancontableejercicio (QString ejercicio, bool subcuentas) {
    QString cadena = "SELECT p.cuenta,c.descripcion,p.ejerciciobase,p.presupuestobase,p.base,p.presupuesto,"
        "case when p.base<>0 then (p.presupuesto-p.base)/p.base*100 else 0 end "
        "from presupuesto p, plancontable c where p.cuenta=c.codigo and p.ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' ";
    QString cadnum;
    if (!cod_longitud_variable())
    {
        if (cualControlador() == SQLITE) {
            if (subcuentas) cadena += "and length(cuenta)=";
            else cadena += "and length(cuenta)<";
        }
        else {
            if (subcuentas) cadena += "and char_length(cuenta)=";
            else cadena += "and char_length(cuenta)<";
        }
        cadnum.setNum(anchocuentas());
        cadena += cadnum;
    }
    else
    {
        if (subcuentas) cadena += "and auxiliar";
        else cadena += "and not auxiliar";
    }
    cadena += " order by p.cuenta";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectCuentapresupuestoejercicio (QString ejercicio) {
    QString cadena = "select cuenta from presupuesto where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectPresupuestospresupuestocuentaejercicio (QString cuenta, QString ejercicio) {
    QString cad = "select presupuesto,presupuesto1,presupuesto2,presupuesto3,presupuesto4,"
        "presupuesto5,presupuesto6,presupuesto7,presupuesto8,presupuesto9,"
        "presupuesto10,presupuesto11,presupuesto12,presupuesto13,presupuesto14,presupuesto15 "
        "from presupuesto where ";
    if (cod_longitud_variable())
    {
        if (cualControlador() == SQLITE) {
            cad += "cuenta like '"+ cuenta.left(-1).replace("'","''") +"%'";
        }
        else {
            cad += "position('";
            cad += cuenta.left(-1).replace("'","''");
            cad += "' in cuenta)=1";
        }
    }
    else
    {
        cad += "cuenta='";
        cad += cuenta.left(-1).replace("'","''");
        cad += "'";
    }
    cad += " and ejercicio='";
    cad += ejercicio.left(-1).replace("'","''");
    cad += "'";
    return ejecutar(cad);
}

// 
QSqlQuery basedatos::selectCodigodescripcionauxiliarplancontablesubcuentasordercodigo (bool subcuentas) {
    QString cadena = "select codigo,descripcion,auxiliar from plancontable ";
    if (!subcuentas) 
    {
        if (!cod_longitud_variable())
        {
            if (cualControlador() == SQLITE) cadena += "where length(codigo)<";
            else cadena += "where char_length(codigo)<";
            QString cadnum;
            cadena += cadnum.setNum(anchocuentas());
        }
        else cadena += "where not auxiliar";
        cadena += " ";
    }
    cadena += "order by codigo";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select20Borradorasiento (qlonglong AstoEspera) {
    QString consulta = "SELECT cuenta,concepto,debe,haber, documento,ctabaseiva,baseiva, ";
    consulta += "claveiva,tipoiva,tipore, cuotaiva,cuentafra,diafra, mesfra,anyofra,soportado, ci,prorrata,"
        "rectificativa, autofactura, fecha_operacion, clave_operacion, biacoste, "
        "rectificada, nfacturas, finicial, ffinal, pr_serv_ue, autofactura_noue, "
        "bien_inversion, sujeto, afecto, fecha, "
        "col32, col33, col34, col35, col36, col37, col38, col39, col40, "
        "col41, col42, col43, col44, col45, col46, col47, col48, col49, col50 ";
    consulta += "FROM borrador where asiento=";
    QString cadasiento; cadasiento.setNum(AstoEspera);
    consulta += cadasiento;
    return ejecutar(consulta);
}

// 
QDate basedatos::selectMaxfechacuenta_ext_concicuenta (QString cuenta) {
    QString cadena="select max(fecha) from cuenta_ext_conci where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDate();
    }
    else return QDate::fromString("1970-01-01","yyyy-MM-dd");
}

// 
QSqlQuery basedatos::select9Vencimientosnum (QString numvenci) {
    QString cadquery = "select cta_ordenante, fecha_operacion, importe, cuenta_tesoreria, ";
    cadquery += "fecha_vencimiento, derecho, pase_diario_operacion, usuario, concepto from vencimientos ";
    cadquery += "where num=";
    cadquery += numvenci.left(-1).replace("'","''");
    return ejecutar(cadquery);
}

// 
QSqlQuery basedatos::selectAsientodocumentodiariopase (QString pase) {
    QString cadquery = "select asiento,documento from diario where pase=";
    cadquery += pase.left(-1).replace("'","''");
    return ejecutar(cadquery);
}

// 
QSqlQuery basedatos::select4Cabeceraestadostitulo (QString titulo) {
    QString cadena = "select titulo,parte1,parte2,colref from cabeceraestados ";
    cadena += "where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}

// 
int basedatos::selectCountnodoestadostituloparte1 (QString titulo, bool parte1) {
    QString cadena = "select count(nodo) from estados where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "' and ";
    if (!parte1) { cadena += "not "; }
    cadena += "parte1";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

// 
QSqlQuery basedatos::select3Estadostituloparte1ordernodo (QString titulo, bool parte1) {
    QString cadena = "select nodo,apartado,referencia"
        " from estados where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "' and ";
    if (!parte1) { cadena += "not "; }
    cadena += "parte1 order by nodo";

    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectclavescuenta_pygconfiguracion () {
    return ejecutar("select clave_gastos,clave_ingresos,cuenta_pyg from configuracion");
}





// 
QSqlQuery basedatos::selectCuenta_frabase_ivafechasgroupordercuenta_fra (
                                         QDate inicial, QDate final,
                                         bool aib, bool eib, bool radio1, bool radio2,
                                         bool autofacturaue, bool autofacturanoue,
                                         bool prestserviciosue, bool isp_op_interiores) {
    // radio1 adquisiciones en general
    // radio2 ventas en general
    QString cadena;
    if (aib || autofacturaue || autofacturanoue || isp_op_interiores)
        cadena = "select l.cuenta_fra,sum(l.base_iva) from libroiva l,diario d where d.fecha>='";
    else 
        cadena = "select l.cuenta_fra,sum(l.base_iva+l.cuota_iva) from libroiva l, diario d where d.fecha>='";

    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena+="' and l.pase=d.pase and ";
    if (radio1) cadena += "l.soportado and not l.aib and not l.eib and not l.autofactura"
                          " and not l.autofactura_no_ue and not l.pr_servicios_ue ";
    if (radio2) cadena += "not l.soportado and not l.aib and not l.eib and not l.autofactura"
                          " and not l.autofactura_no_ue and not l.pr_servicios_ue ";
    if (aib) cadena += "l.soportado and l.aib ";
    if (eib) cadena += "l.eib ";
    if (autofacturaue) cadena += "l.soportado and l.autofactura ";
    if (autofacturanoue) cadena += "l.soportado and l.autofactura_no_ue ";
    if (prestserviciosue) cadena += "l.pr_servicios_ue ";
    if (isp_op_interiores) cadena += "l.soportado and l.isp_op_interiores ";
    cadena += "group by l.cuenta_fra order by l.cuenta_fra";
    return ejecutar(cadena);
}


// Datos 347
QSqlQuery basedatos::datos347 (QDate inicial, QDate final, QString cadenacuentas) {
    QString cadena;
    cadena = "select l.cuenta_fra, sum(l.base_iva+l.cuota_iva), soportado from libroiva l,"
             " diario d where d.fecha>='";

    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena+="' and l.pase=d.pase and ";
    cadena += "not l.aib and not l.eib and not l.autofactura"
              " and not l.autofactura_no_ue and not l.pr_servicios_ue ";

    if (!cadenacuentas.isEmpty())
      {
       cadena += "and (";

       int partes = cadenacuentas.count(',');

       for (int veces=0; veces<=partes; veces++)
        {
          if (cualControlador() == SQLITE) {
            cadena += "l.cuenta_fra like '"+ cadenacuentas.section(',',veces,veces).left(-1).replace("'","''") +"%'";
            }
           else {
            cadena += "position('";
            cadena += cadenacuentas.section(',',veces,veces).left(-1).replace("'","''");
            cadena += "' in l.cuenta_fra)=1";
            }
          if (veces==partes) cadena+=") ";
            else cadena+=" or ";
        }
      }

    cadena += "group by l.cuenta_fra, soportado order by l.cuenta_fra";
    return ejecutar(cadena);
}



// volumen operaciones en libroiva por cuenta
double basedatos::vol_op_iva_cuenta (QString cuenta, QDate inicial, QDate final,
                                 bool aib, bool eib, bool radio1, bool radio2,
                                 bool autofacturaue, bool autofacturanoue,
                                 bool prestserviciosue, bool isp_op_interiores) {
    QString cadena;
    if (aib || autofacturaue || autofacturanoue || isp_op_interiores)
        cadena = "select l.cuenta_fra,sum(l.base_iva) from libroiva l,diario d where d.fecha>='";
    else
        cadena = "select l.cuenta_fra,sum(l.base_iva+l.cuota_iva) from libroiva l, diario d where d.fecha>='";

    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena+="' and l.pase=d.pase and ";
    if (radio1) cadena += "l.soportado and not l.aib and not l.eib and not l.autofactura"
                          " and not l.autofactura_no_ue and not l.pr_servicios_ue ";
    if (radio2) cadena += "not l.soportado and not l.aib and not l.eib and not l.autofactura"
                          " and not l.autofactura_no_ue and not l.pr_servicios_ue ";
    if (aib) cadena += "l.soportado and l.aib ";
    if (eib) cadena += "l.eib ";
    if (autofacturaue) cadena += "l.soportado and l.autofactura ";
    if (autofacturanoue) cadena += "l.soportado and l.autofactura_no_ue ";
    if (isp_op_interiores) cadena += "l.soportado and l.isp_op_interiores ";
    if (prestserviciosue) cadena += "l.pr_servicios_ue ";
    cadena+= "and l.cuenta_fra='";
    cadena+=cuenta;
    cadena+= "' ";
    cadena += "group by l.cuenta_fra order by l.cuenta_fra";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(1).toDouble();
    }
    return 0;
}


// volumen operaciones 347
double basedatos::vol_op_347_cuenta (QString cuenta, QDate inicial, QDate final) {
    QString cadena;
    cadena = "select l.cuenta_fra,sum(l.base_iva+l.cuota_iva) from libroiva l,"
             "diario d where d.fecha>='";

    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and d.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena+="' and l.pase=d.pase and ";
    cadena += "not l.aib and not l.eib and not l.autofactura"
              " and not l.autofactura_no_ue and not l.pr_servicios_ue ";
    cadena+= "and l.cuenta_fra='";
    cadena+=cuenta;
    cadena+= "' ";
    cadena += "group by l.cuenta_fra order by l.cuenta_fra";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(1).toDouble();
    }
    return 0;
}



// 
QSqlQuery basedatos::select7Datossubcuentacuenta (QString cuenta) {
    QString cad2 = "select razon,cif,poblacion,codigopostal,provincia,claveidfiscal,pais,domicilio "
	    "from datossubcuenta where cuenta='";
    cad2 += cuenta.left(-1).replace("'","''");
    cad2 += "'";
    return ejecutar(cad2);
}

// devuelve volumen de operaciones a partir de los datos del diario
QSqlQuery basedatos::selectsumdiarionoaperturanocierrefechas (bool radio1, QString ejercicio,
                                                              QString cadenacuentas) {
    QString cadena = "select cuenta,";
    if (radio1) cadena += "sum(haber) ";
    else cadena += "sum(debe) ";
    cadena += "from diario where diario!='";
    cadena += diario_apertura();
    cadena += "' and diario!='";
    cadena += diario_cierre();
    cadena += "' and fecha>='";
    cadena += inicioejercicio(ejercicio).toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += finejercicio(ejercicio).toString("yyyy-MM-dd");
    cadena += "' and (";

    int partes = cadenacuentas.count(',');

    for (int veces=0; veces<=partes; veces++)
    {
        if (cualControlador() == SQLITE) {
            cadena += "cuenta like '"+ cadenacuentas.section(',',veces,veces).left(-1).replace("'","''") +"%'";
        }
        else {
            cadena += "position('";
            cadena += cadenacuentas.section(',',veces,veces).left(-1).replace("'","''");
            cadena += "' in cuenta)=1";
        }
        if (veces==partes) cadena+=")";
        else cadena+=" or ";
    }

    cadena+=" group by cuenta order by cuenta";
    return ejecutar(cadena);
}


// devuelve volumen de operaciones a partir de los datos del diario primer trimestre
// para una cuenta determinada
double basedatos::operaciones_diario_1T (bool radio1, QString ejercicio,
                                             QString cuenta) {
    int anyo=inicioejercicio(ejercicio).year();
    QDate fechainicial; fechainicial.setDate(anyo,1,1);
    QDate fechafinal; fechafinal.setDate(anyo,3,31);
    QString cadena = "select cuenta,";
    if (radio1) cadena += "sum(haber) ";
    else cadena += "sum(debe) ";
    cadena += "from diario where diario!='";
    cadena += diario_apertura();
    cadena += "' and diario!='";
    cadena += diario_cierre();
    cadena += "' and fecha>='";
    cadena += fechainicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += fechafinal.toString("yyyy-MM-dd");
    cadena += "' and cuenta='";
    cadena += cuenta;
    cadena += "'";
    cadena+=" group by cuenta";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(1).toDouble();
    }
    return 0;
}


// devuelve volumen de operaciones a partir de los datos del diario segundo trimestre
double basedatos::operaciones_diario_2T (bool radio1, QString ejercicio,
                                             QString cuenta) {
    int anyo=inicioejercicio(ejercicio).year();
    QDate fechainicial; fechainicial.setDate(anyo,4,1);
    QDate fechafinal; fechafinal.setDate(anyo,6,30);
    QString cadena = "select cuenta,";
    if (radio1) cadena += "sum(haber) ";
    else cadena += "sum(debe) ";
    cadena += "from diario where diario!='";
    cadena += diario_apertura();
    cadena += "' and diario!='";
    cadena += diario_cierre();
    cadena += "' and fecha>='";
    cadena += fechainicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += fechafinal.toString("yyyy-MM-dd");
    cadena += "' and cuenta='";
    cadena += cuenta;
    cadena += "'";
    cadena+=" group by cuenta";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(1).toDouble();
    }
    return 0;
}



// devuelve volumen de operaciones a partir de los datos del diario tercer trimestre
double basedatos::operaciones_diario_3T (bool radio1, QString ejercicio,
                                             QString cuenta) {
    int anyo=inicioejercicio(ejercicio).year();
    QDate fechainicial; fechainicial.setDate(anyo,7,1);
    QDate fechafinal; fechafinal.setDate(anyo,9,30);
    QString cadena = "select cuenta,";
    if (radio1) cadena += "sum(haber) ";
    else cadena += "sum(debe) ";
    cadena += "from diario where diario!='";
    cadena += diario_apertura();
    cadena += "' and diario!='";
    cadena += diario_cierre();
    cadena += "' and fecha>='";
    cadena += fechainicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += fechafinal.toString("yyyy-MM-dd");
    cadena += "' and cuenta='";
    cadena += cuenta;
    cadena += "'";
    cadena+=" group by cuenta";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(1).toDouble();
    }
    return 0;
}



// devuelve volumen de operaciones a partir de los datos del diario tercer trimestre
double basedatos::operaciones_diario_4T (bool radio1, QString ejercicio,
                                             QString cuenta) {
    int anyo=inicioejercicio(ejercicio).year();
    QDate fechainicial; fechainicial.setDate(anyo,10,1);
    QDate fechafinal; fechafinal.setDate(anyo,12,31);
    QString cadena = "select cuenta,";
    if (radio1) cadena += "sum(haber) ";
    else cadena += "sum(debe) ";
    cadena += "from diario where diario!='";
    cadena += diario_apertura();
    cadena += "' and diario!='";
    cadena += diario_cierre();
    cadena += "' and fecha>='";
    cadena += fechainicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += fechafinal.toString("yyyy-MM-dd");
    cadena += "' and cuenta='";
    cadena += cuenta;
    cadena += "'";
    cadena+=" group by cuenta";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(1).toDouble();
    }
    return 0;
}


// 
QSqlQuery basedatos::select6Datossubcuentacuenta (QString cuenta) {
    QString cad2 = "select nombrecomercial,razon,cif,poblacion,codigopostal,provincia "
        "from datossubcuenta "
        "where cuenta='";
    cad2 += cuenta.left(-1).replace("'","''");
    cad2 += "'";
    return ejecutar(cad2);
}

// 
qlonglong basedatos::selectMaxasientofecha (QDate fecha) {
    QString cadena = "select max(asiento) from diario where fecha<'";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toLongLong();
    }
    return 0;
}


//
qlonglong basedatos::min_asiento (QString ejercicio) {
    QString cadena = "select min(asiento) from diario where ejercicio='";
    cadena += ejercicio;
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toLongLong();
    }
    return 0;
}



// 
qlonglong basedatos::selectMaxpasediariofecha (QDate fecha) {
    QString cadena = "select max(pase) from diario where fecha<'";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) ) {
        return query.value(0).toLongLong();
    }
    return 0;
}



// sólo del ejercicio de la fecha
int basedatos::selectCountasientodiariofecha (QDate fecha) {
    QString ejercicio=ejerciciodelafecha(fecha);
    QString cadena = "select count(asiento) from diario where fecha>='";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "' and ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

// 
QSqlQuery basedatos::selectAsientopasediariofecha (QDate fecha) {
    QString cadena = "select asiento,pase from diario where fecha>='";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "'";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectAsientopasediariofechaorderfechaasientopase (QDate fecha) {
    QString ejercicio=ejerciciodelafecha(fecha);
    QString cadena = "select asiento,pase from diario where fecha>='";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "' and ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' order by fecha,asiento,pase;";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::selectAsientomodelocab_as_modeloorderasientomodelo () {
    return ejecutar("select asientomodelo,tipo from cab_as_modelo order by asientomodelo");
}

// 
QSqlQuery basedatos::selectTitulocabeceraestadosordertitulo () {
    return ejecutar("select titulo from cabeceraestados order by titulo");
}

// 
QSqlQuery basedatos::select7Vencimientosfechasanuladoordernum (QDate inicial, QDate final) {
    QString cadena = "SELECT num, cta_ordenante, fecha_operacion, importe, cuenta_tesoreria, "
        "fecha_vencimiento, derecho "
        "from vencimientos "
        "where fecha_operacion>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha_operacion<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and anulado ";
    cadena += "order by num";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select8Borradororderasiento () {
    return ejecutar ("SELECT asiento,usuario,cuenta,concepto,debe,haber,documento,ci "
        "from borrador order by asiento");
}

// 
QSqlQuery basedatos::select9Vencimientospase_diario_operacion (QString qpase) {
    QString cadena = "SELECT num, cta_ordenante, fecha_operacion, importe, cuenta_tesoreria,fecha_vencimiento, "
        "concepto,derecho, pendiente, domiciliable from vencimientos where pase_diario_operacion=";
    cadena += qpase.left(-1).replace("'","''");
    cadena += " and ";
    cadena += "not anulado";
    cadena += " order by num";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select11Diariofiltro (QString filtro) {
    QString cadena="SELECT cuenta, fecha, asiento, concepto, debe, "
        "haber, documento, diario, usuario, pase,ci, clave_ci, copia_doc,"
        "ejercicio, nrecepcion, revisado from diario ";
    if (filtro.length()>0) cadena += filtro;
    return ejecutar(cadena);
}


QSqlQuery basedatos::selectDiariofiltro_nomsj_error (QString filtro, bool *correcto) {
    QString cadena="SELECT cuenta, fecha, asiento, concepto, debe, "
        "haber, documento, diario, usuario, pase,ci, clave_ci, copia_doc,"
        "ejercicio, nrecepcion, revisado from diario ";
    if (filtro.length()>0) cadena += filtro;
    QSqlQuery query;
    if (!query.exec(cadena)) *correcto=false;
      else *correcto=true;
    return query;
}

// 
QSqlQuery basedatos::select6ejerciciosorderapertura () {
    QString cadena = "SELECT codigo,apertura,cierre,cerrado,prox_asiento, prox_nrecepcion "
        "from ejercicios order by apertura";
    return ejecutar(cadena);
}

// 
QSqlQuery basedatos::select10Planamortizacionesordercuenta_activo () {
    QString cadena = "SELECT cuenta_activo, fecha_func, codigo_activo, cuenta_am_acum, cuenta_gasto, "
        "valor_residual, coef_amort_contable, amort_personalizada, coef_amort_fiscal,"
        "amort_fiscal_1ejer  from planamortizaciones order by cuenta_activo ";
    return ejecutar(cadena);
}

// 
double basedatos::selectSaldosaldossubcuentacodigo (QString codigo, bool *ok) {
    QString cadquery = "SELECT saldo from saldossubcuenta where codigo = '";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    QSqlQuery query = ejecutar(cadquery);
    double valor = 0;
    if ( (query.isActive()) && (query.first()) )
    {
        valor = query.value(0).toDouble(ok);
    }
    return valor;
}

// 
int basedatos::selectMaxasientoborrador () {
    QSqlQuery query = ejecutar("select max(asiento) from borrador");

    int vnum = 0;
    if ( (query.isActive()) && (query.next()) )
    {
        vnum = query.value(0).toInt()+1;
    } else vnum = 1;
    return vnum;
}

// Actualiza vencimientos a partir de pase_diario_vencimiento
void basedatos::updateVencimientospase_diario_vencimientopendientepase_diario_vencimiento (QString pase) {
    QString cadena = "update vencimientos set pase_diario_vencimiento=0, pendiente=";

    if ( cualControlador() == SQLITE ) {
        cadena += "1";
    }
    else {
        cadena += "TRUE";
    }

    cadena += " where pase_diario_vencimiento="+ pase.left(-1).replace("'","''");
    ejecutar(cadena);
}

// Actualiza saldos subcuenta a partir del saldo y el código
void basedatos::updateSaldossubcuentasaldomenosmascodigo(QString menos, QString mas, QString codigo) {
    QString cad = "update saldossubcuenta set saldo=saldo-("+ menos+ ")+("+ mas;
    cad += ") where codigo='"+ codigo.left(-1).replace("'","''") +"'";
    ejecutar(cad);
}

// Actualiza la descripción de una cuenta del plancontable
void basedatos::updatePlancontabledescripcioncodigo (QString codigo,QString descripcion) {
    QString cadquery = "UPDATE plancontable SET descripcion='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE codigo='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}

// Actualiza la descripción de la clave de conceptos
void basedatos::updateConceptosdescripcionclave (QString codigo,QString descripcion) {
    QString cadquery = "UPDATE conceptos SET descripcion='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE clave='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}

// Actualiza la descripción del código de la tabla
void basedatos::updatetabladescripcionclave (QString tabla, QString codigo,QString descripcion) {
    QString cadquery = "UPDATE ";
    cadquery+=tabla;
    cadquery+=" SET descripcion='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE codigo='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";

    // QMessageBox::warning( 0, QObject::tr("update"),cadquery);

    ejecutar(cadquery);
}

// Actualiza la descripción y el nivel de un ci
void basedatos::updateCidescripcioncodigo (QString codigo,QString descripcion,int nivel) {
    QString cadquery = "UPDATE ci SET ";
    cadquery += "descripcion='"+ descripcion.left(-1).replace("'","''") +"',";
    cadquery += " nivel="+ QString::number(nivel);
    cadquery += " WHERE codigo='"+ codigo.left(-1).replace("'","''") +"'";
    ejecutar(cadquery);
}

// Actualiza saldos subcuenta a partir del importe y el código
void basedatos::updateSaldossubcuentasaldocargocodigo (QString cuenta,QString importe,bool cargo) {
    QString cadq2 = "update saldossubcuenta set saldo=saldo";
    if (cargo) cadq2 += "+"; else cadq2 += "-";
    cadq2 += "("+ importe.left(-1).replace("'","''") +")";
    cadq2 += " where codigo='";
    cadq2 +=  cuenta.left(-1).replace("'","''");
    cadq2 += "'";
    ejecutar(cadq2);
}

// Actualiza los vencimientos con un numero
void basedatos::updateVencimientosanuladonum (QString vto,bool b) {
    QString cadena = "update vencimientos set anulado=";
    if (cualControlador() == SQLITE) {
        if (b) cadena += "1";
        else cadena += "0";
    }
    else {
        if (b) cadena += "TRUE";
        else cadena += "FALSE";
    }
    cadena += " where num="+ vto.left(-1).replace("'","''");
    ejecutar(cadena);
}

// Actualiza los tipos de iva con una clave
void basedatos::updateTiposivatiporedescripcionclave (QString clave,QString tipo,QString re,QString descripcion) {
    if (re.length()==0) re="0";
    QString cadquery = "UPDATE tiposiva SET"; 
    cadquery += " tipo="+ tipo.left(-1).replace("'","''");
    cadquery += ", re="+ re.left(-1).replace("'","''");
    cadquery += ", descripcion='"+ descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE clave='"+ clave.left(-1).replace("'","''") +"'";
    ejecutar(cadquery);
}

// Desbloquea saldos medios
void basedatos::updateConfiguracionbloqsaldosmedios () {
    if (cualControlador() == SQLITE) {
        ejecutar("update configuracion set bloqsaldosmedios=0;");
    }
    else {
        ejecutar("update configuracion set bloqsaldosmedios=FALSE;");
    }
}

// Actualiza los saldossubcuenta con un codigo
void basedatos::updateSaldossubcuentasaldocodigo (QString cuenta,QString importe,bool cargo) {
    QString cadq2 = "update saldossubcuenta set saldo=";
    if (!cargo) cadq2 += "-";
    cadq2 += importe.left(-1).replace("'","''");
    cadq2 += " where codigo='";
    cadq2 += cuenta.left(-1).replace("'","''");
    cadq2 += "'";
    ejecutar(cadq2);
}

// Establece todos los saldos a 0
void basedatos::updateSaldossubcuentasaldo0 () {
    ejecutar("update saldossubcuenta set saldo=0");
}

bool basedatos::esta_en_saldossubcuenta(QString codigo)
{
    QString cadena="select codigo from saldossubcuenta where codigo='";
    cadena+=codigo;
    cadena+="'";
    QSqlQuery query = ejecutar(cadena);

    if  (query.isActive())
        if (query.next()) return true;
    return false;
}

void basedatos::actucodigos_saldossubcuenta()
{
   QString cadena = "select codigo from plancontable";
   QSqlQuery query = ejecutar(cadena);

   if  (query.isActive())
       while (query.next())
       {
        if (esauxiliar(query.value(0).toString()))
         {
            if (!esta_en_saldossubcuenta(query.value(0).toString()))
               insertSaldossubcuenta (query.value(0).toString(),
                                      "0", "0", "0");
         }
       }
}

// Actualiza nuevoplan con un código
void basedatos::updateNuevoplandescripcioncodigo (QString codigo,QString descripcion) {
    QString cadquery = "UPDATE nuevoplan SET descripcion='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE codigo='";
    cadquery += codigo.left(-1).replace("'","''") +"'";
    ejecutar(cadquery);

}

// Actualiza amortfiscalycontable con una cuenta_activo
void basedatos::updateAmortfiscalycontablecuenta_activocuenta_activo (QString origen, QString destino) {
    QString cadena = "update amortfiscalycontable set cuenta_activo='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta_activo='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza amortfiscalycontable con una cuenta_am_acum
void basedatos::updateAmortfiscalycontablecuenta_am_acumcuenta_am_acum (QString origen, QString destino) {
    QString cadena = "update amortfiscalycontable set cuenta_am_acum='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta_am_acum='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza amortfiscalycontable con una cuenta_gasto
void basedatos::updataAmortfiscalycontablecuenta_gastocuenta_gasto (QString origen, QString destino) {
    QString cadena = "update amortfiscalycontable set cuenta_gasto='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta_gasto='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza borrador con una cuenta
void basedatos::updateBorradorcuentacuenta (QString origen, QString destino) {
    QString cadena = "update borrador set cuenta='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza datossubcuenta con una cuenta
void basedatos::updateDatossubcuentacuentacuenta (QString origen, QString destino) {
    QString cadena = "update datossubcuenta set cuenta='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza el diario con una cuenta
void basedatos::updateDiariocuentacuenta (QString origen, QString destino) {
    QString cadena = "update diario set cuenta='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza libroiva con una cuenta base iva
void basedatos::updateLibroivacta_base_ivacta_base_iva (QString origen, QString destino) {
    QString cadena = "update libroiva set cta_base_iva='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cta_base_iva='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza libroiva con una cuenta_fra
void basedatos::updatelibroivacuenta_fracuenta_fra (QString origen, QString destino) {
    QString cadena = "update libroiva set cuenta_fra='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta_fra='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza planamortizaciones con una cuenta_activo
void basedatos::updateplanamortizacionescuenta_activocuenta_activo (QString origen, QString destino) {
    QString cadena = "update planamortizaciones set cuenta_activo='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta_activo='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza planamortizacoines con una cuenta_am_acum
void basedatos::updatePlanamortizacionescuenta_am_acumcuenta_am_acum (QString origen, QString destino) {
    QString cadena = "update planamortizaciones set cuenta_am_acum='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta_am_acum='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza planamortizacioens con una cuenta_gasto
void basedatos::updatePlanamortizacionescuenta_gastocuenta_gasto (QString origen, QString destino) {
    QString cadena = "update planamortizaciones set cuenta_gasto='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta_gasto='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza amortpers con una cuenta
void basedatos::updateAmortperscuentacuenta (QString origen, QString destino) {
    QString cadena = "update amortpers set cuenta='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza saldossubcuenta con un codigo
void basedatos::updateSaldossubcuentacodigocodigo (QString origen, QString destino) {
    QString cadena = "update saldossubcuenta set codigo='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where codigo='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza vencimientos desde cta-ordenante
void basedatos::updateVencimientoscta_ordenantecta_ordenante (QString origen, QString destino) {
    QString cadena = "update vencimientos set cta_ordenante='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cta_ordenante='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza vencimientos desde cta_tesoreria
void basedatos::updateVencimientoscta_tesoreriacta_tesoreria (QString origen, QString destino) {
    QString cadena = "update vencimientos set cuenta_tesoreria='";
    cadena += destino.left(-1).replace("'","''");
    cadena += "' where cuenta_tesoreria='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza Diarios desde un código
void basedatos::updateDiariosdescripcioncodigo(QString codigo, QString descripcion) {
    QString cadquery = "UPDATE diarios SET descripcion='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE codigo='";
    cadquery += codigo.left(-1).replace("'","''") +"'";
    ejecutar(cadquery);
}

// Actualiza dif_conciliacion desde un código
void basedatos::updateDif_conciliaciondescripcioncodigo(QString codigo, QString descripcion) {
    QString cadquery ="UPDATE dif_conciliacion SET descripcion='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE codigo='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}

// Actualiza el prox_asiento de configuracion
void basedatos::update_ejercicio_prox_asiento(QString ejercicio, QString cadnum) {
    QString cadena="update ejercicios set prox_asiento="+cadnum.left(-1).replace("'","''");
    cadena+=" where codigo='";
    cadena+=ejercicio.left(-1).replace("'","''");
    cadena+="'";
    ejecutar(cadena);
}

// Actualiza el prox_pase de configuracion
void basedatos::updateConfiguracionprox_pase (QString cadnumpase) {
    ejecutar("update configuracion set prox_pase="+ cadnumpase.left(-1).replace("'","''"));
}

// Actualiza el prox_pase de configuracion
void basedatos::updateConfiguracionprox_vencimiento (QString cadnumpase) {
    ejecutar("update configuracion set prox_vencimiento="+ cadnumpase.left(-1).replace("'","''"));
}

// Actualiza el diario de diario en un asiento
void basedatos::updateDiariodiarioasiento (QString diario, QString asiento, QString ejercicio) {
    QString cadquery = "update diario set diario='";
    cadquery += diario.left(-1).replace("'","''");
    cadquery += "' where asiento=";
    cadquery += asiento.left(-1).replace("'","''");
    cadquery += " and ejercicio='";
    cadquery += ejercicio;
    cadquery += "'";
    ejecutar(cadquery);
}

// Bloquea o desbloquea saldos medios
void basedatos::updateConfiguracionbloqsaldosmedios (bool b) {
    QSqlDatabase::database().transaction();

    if (cualControlador() == SQLITE) {
        if (b) { ejecutar("update configuracion set bloqsaldosmedios=1"); }
        else  { ejecutar("update configuracion set bloqsaldosmedios=0"); }
    }
    else {
        if (b) { ejecutar("update configuracion set bloqsaldosmedios=TRUE"); }
        else  { ejecutar("update configuracion set bloqsaldosmedios=FALSE"); }
    }    

    QSqlDatabase::database().commit();
}

// Actualiza saldomedio1 de saldossubcuenta
void basedatos::updateSaldossubcuentasaldomedio1codigo (QString cadnum, QString codigo) {
    QString cadquery = "update saldossubcuenta set saldomedio1=";
    cadquery += cadnum;
    cadquery += " where codigo='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}

// Actualiza saldomedio2 de saldossubcuenta
void basedatos::updateSaldossubcuentasaldomedio2codigo (QString cadnum, QString codigo) {
    QString cadquery = "update saldossubcuenta set saldomedio2=";
    cadquery += cadnum;
    cadquery += " where codigo='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}

// Actualiza estados con calcularo falso para un título
void basedatos::updateEstadoscalculadotitulo(bool b, QString titulo) {
    QString cadena = "update estados set calculado=";
    if (cualControlador() == SQLITE) {
        if (b) { cadena += "1"; }
        else { cadena += "0"; }
    }
    else {
        if (b) { cadena += "TRUE"; }
        else { cadena += "FALSE"; }
    }
    cadena += " where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}


// Actualiza Estados con m1,m2... calculado desde parte1, nodo y título
void basedatos::actu_rdos_mes (double m1, double m2, double m3, double m4, double m5,
                               double m6,
                               double m7, double m8, double m9, double m10, double m11,
                               double m12,
                               bool calculado, bool parte1,
                               QString qnodo, QString titulo)
{
  QString cadena="update estados set m1=";
  QString cadval;
  cadval.setNum(m1,'f',2);
  cadena+=cadval;

  cadena+=", m2=";
  cadval.setNum(m2,'f',2);
  cadena+=cadval;

  cadena+=", m3=";
  cadval.setNum(m3,'f',2);
  cadena+=cadval;

  cadena+=", m4=";
  cadval.setNum(m4,'f',2);
  cadena+=cadval;

  cadena+=", m5=";
  cadval.setNum(m5,'f',2);
  cadena+=cadval;

  cadena+=", m6=";
  cadval.setNum(m6,'f',2);
  cadena+=cadval;

  cadena+=", m7=";
  cadval.setNum(m7,'f',2);
  cadena+=cadval;

  cadena+=", m8=";
  cadval.setNum(m8,'f',2);
  cadena+=cadval;

  cadena+=", m9=";
  cadval.setNum(m9,'f',2);
  cadena+=cadval;

  cadena+=", m10=";
  cadval.setNum(m10,'f',2);
  cadena+=cadval;

  cadena+=", m11=";
  cadval.setNum(m11,'f',2);
  cadena+=cadval;

  cadena+=", m12=";
  cadval.setNum(m12,'f',2);
  cadena+=cadval;

  cadena += ",calculado=";
  if (cualControlador() == SQLITE) {
      if (calculado) { cadena += "1"; }
      else { cadena += "0"; }
  }
  else {
      if (calculado) { cadena += "TRUE"; }
      else { cadena += "FALSE"; }
  }
  if (parte1) cadena += " where parte1";
  else cadena+=" where not parte1";

  cadena += " and nodo='";
  cadena += qnodo.left(-1).replace("'","''");
  cadena += "' and titulo='";
  cadena += titulo.left(-1).replace("'","''");
  cadena += "'";

  ejecutar(cadena);

  // QMessageBox::warning( 0, QObject::tr("Crear base de datos"),cadena);

}


// Actualiza Estados con importe1 importe2 calculado desde parte1, nodo y título
void basedatos::updateEstadoscalcularestado (double resultado1, double resultado2,
                                             bool calculado, bool parte1,
                                             QString qnodo, QString titulo) {
    QString cadenaactu = "update estados set importe1=";
    QString cadval;
    cadval.setNum(resultado1,'f',2);
    cadenaactu += cadval;
    cadenaactu += ", importe2=";
    cadval.setNum(resultado2,'f',2);
    cadenaactu += cadval;
    cadenaactu += ",calculado=";
    if (cualControlador() == SQLITE) {
        if (calculado) { cadenaactu += "1"; }
        else { cadenaactu += "0"; }
    }
    else {
        if (calculado) { cadenaactu += "TRUE"; }
        else { cadenaactu += "FALSE"; }
    }
    if (parte1) cadenaactu += " where parte1";
    else cadenaactu+=" where not parte1";

    cadenaactu += " and nodo='";
    cadenaactu += qnodo.left(-1).replace("'","''");
    cadenaactu += "' and titulo='";
    cadenaactu += titulo.left(-1).replace("'","''");
    cadenaactu += "'";

    ejecutar(cadenaactu);
}

// Actualiza cabeceraestados con valores base para un título
void basedatos::updateCabeceraestadosvalorbaseportitulo (double resultado1, double resultado2, QString titulo) {
    QString cadena = "update cabeceraestados set valorbasepor1=";
    QString cadval;
    cadval.setNum(resultado1,'f',2);
    cadena += cadval;
    cadena += ", valorbasepor2=";
    cadval.setNum(resultado2,'f',2);
    cadena += cadval;
    cadena += " where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";

    ejecutar(cadena);
}

// Actualiza datos de cabeceraestados
void basedatos::updateCabeceraestadosfechacalculoejercicio1ciejercicio2titulo (QDate fechaactual,
                                                                         QString ejercicio1, QString ci,
                                                                         QString ejercicio2, QString titulo) {
    QTime hora=QTime::currentTime();
    QString cadhora=hora.toString("hh:mm:ss");
    QString cadena = "update cabeceraestados set fechacalculo='";
    cadena += fechaactual.toString("yyyy-MM-dd");
    cadena += "', ejercicio1='";
    cadena += ejercicio1.left(-1).replace("'","''");
    cadena += "', ci='";
    cadena += ci.left(-1).replace("'","''");
    cadena += "'";
    if (ejercicio2 != noejercicio())
      {
        cadena += ", ejercicio2='";
        cadena += ejercicio2.left(-1).replace("'","''");
        cadena += "'";
      }
     else cadena += ", ejercicio2=''";
    cadena += ", horacalculo='";
    cadena += cadhora;
    cadena += "'";
    cadena += " where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";

    ejecutar(cadena);
}

//
void basedatos::updateDiariocuentafechasnodiario_apertura (QString por, QString origen, bool fechas, QDate inicial, QDate final) {
    QString cadena = "update diario set cuenta='";
    cadena += por.left(-1).replace("'","''");
    cadena += "' where cuenta='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";
  
    if (fechas)
    {
        cadena += " and fecha>='";
        cadena += inicial.toString("yyyy-MM-dd");
        cadena += "' and fecha<='";
        cadena += final.toString("yyyy-MM-dd");
        cadena += "'";
    }
    cadena+=" and diario!='";
    cadena+=diario_apertura().left(-1).replace("'","''");
    cadena+="'";
    ejecutar(cadena);
}

// Actualiza el diario a partir de cuenta
void basedatos::updateDiariocuentapase (QString codigo, QString apunte) {
    QString cadena = "update diario set cuenta='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "' where pase=";
    cadena += apunte.left(-1).replace("'","''");
    ejecutar(cadena);
}


// Actualiza apunte
void basedatos::updateconcepto_evpn_pymes (QString apunte, QString concepto) {
    QString cadena = "update diario set codigo_var_evpn_pymes='";
    cadena += concepto.left(-1).replace("'","''");
    cadena += "' where pase=";
    cadena += apunte.left(-1).replace("'","''");
    ejecutar(cadena);
}



// Actualiza el ejercicio a partir de código
void basedatos::updateEjercicioscerrandocodigo (bool cerrando, QString codigo) {
    QString cadena = "update ejercicios set cerrando=";
    if (cualControlador() == SQLITE) {
        if (cerrando) { cadena += "1"; }
        else { cadena += "0"; }
    }
    else {
        if (cerrando) { cadena += "true"; }
        else { cadena += "false"; }
    }
    cadena += " where codigo='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// Actualiza el ejercicio a partir de código
void basedatos::updateEjercicioscerradocodigo (bool cerrado, QString codigo) {
    QString cadena = "update ejercicios set cerrado=";
    if (cualControlador() == SQLITE) {
        if (cerrado) { cadena += "1"; }
        else { cadena += "0"; }
    }
    else {
        if (cerrado) { cadena += "true"; }
        else { cadena += "false"; }
    }
    cadena += " where codigo='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

//
void basedatos::updateDiarioconciliadodif_conciliacionpase (bool conciliado, QString cad, QString pase) {
    QString cadena = "update diario set conciliado=";
    if (cualControlador() == SQLITE) {
        if (conciliado) { cadena += "1"; }
        else { cadena += "0"; }
    }
    else {
        if (conciliado) { cadena += "true"; }
        else { cadena += "false"; }
    }
    cadena += ", dif_conciliacion='";
    cadena += cad.left(-1).replace("'","''");
    cadena += "'";
    cadena += " where pase=";
    cadena += pase.left(-1).replace("'","''");
    ejecutar(cadena);
}

// 
void basedatos::updateCuenta_ext_conciconciliadodif_conciliacionnumero (bool conciliado, QString dif, QString numero) {
    QString cadena="update cuenta_ext_conci set conciliado=";
    if (cualControlador() == SQLITE) {
        if (conciliado) { cadena += "1"; }
        else { cadena += "0"; }
    }
    else {
        if (conciliado) { cadena += "true"; }
        else { cadena += "false"; }
    }
    cadena += ",dif_conciliacion='";
    cadena += dif.left(-1).replace("'","''");
    cadena += "' where numero=";
    cadena += numero.left(-1).replace("'","''");
    ejecutar(cadena);
}

// 
void basedatos::updateDiariofechaasiento (QDate fecha, QString gasiento) {
QString ejercicio =basedatos::instancia()->selectEjerciciodelafecha(fecha);
QString cadena = "update diario set fecha='";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "' where asiento=";
    cadena += gasiento.left(-1).replace("'","''");
    cadena += " and ejercicio='";
    cadena += ejercicio;
    cadena += "'";
    ejecutar(cadena);
}

// 
void basedatos::updateEjercicioscierrecodigo (QDate fecha, QString ejercicio) {
    QString cadena="update ejercicios set cierre='";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "' where codigo='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// 
void basedatos::updateDiarioconceptodocumentopase (QString concepto, QString documento, QString numpase) {
    QString cadena = "update diario set concepto='";
    cadena += concepto.left(-1).replace("'","''");
    cadena += "', documento='";
    cadena += documento.left(-1).replace("'","''");
    cadena += "' where pase=";
    cadena += numpase.left(-1).replace("'","''");
    ejecutar(cadena);
}

bool basedatos::hay_criterio_caja_config()
{
  QString cadena="select caja_iva from configuracion";
  QSqlQuery q=ejecutar(cadena);
  if(q.isActive())
      if (q.next())
          return q.value(0).toBool();
  return false;
}

// 
void basedatos::updateConfiguracion (QString empresa ,QString nif, QString domicilio,
                                     QString poblacion, QString cpostal, QString provincia,
                                     QString email, QString web, QString cuenta_iva_soportado,
                                     QString cuenta_iva_repercutido, QString retirpf,
                                     QString cuenta_ret_irpf,
                                     QString cuentasapagar, QString cuentasacobrar,
                                     QString clave_iva_defecto, QString clave_gastos,
                                     QString clave_ingresos, QString cuenta_pyg,
                                     QString prox_pase, QString prox_vencimiento,
                                     bool igic, bool analitica, QString id_registral, QString prorrata_iva,
                                     bool prorrata_especial, QDate fechaconst, QString objeto,
                                     QString actividad, bool analitica_parc,QString ctas_analitica,
                                     bool usuarios, QString proxdoc, bool analitica_tabla,
                                     QString provincia_def,
                                     QString cuentas_aa, QString cuentas_ag, bool amoinv,
                                     bool sec_recibidas, QString prox_domicilia,
                                     QString cod_ine_plaza, bool caja_iva) {
    QString cadena = "update configuracion set ";
    cadena += "empresa='"+ empresa.left(-1).replace("'","''");
    cadena += "',nif='"+ nif.left(-1).replace("'","''");
    cadena += "', domicilio='"+ domicilio.left(-1).replace("'","''");
    cadena += "',poblacion='"+ poblacion.left(-1).replace("'","''");
    cadena += "',cpostal='"+ cpostal.left(-1).replace("'","''");
    cadena += "', provincia='"+ provincia.left(-1).replace("'","''");
    cadena += "',email='"+ email.left(-1).replace("'","''");
    cadena += "',web='"+ web.left(-1).replace("'","''");
    cadena += "', cuenta_iva_soportado='"+ cuenta_iva_soportado.left(-1).replace("'","''").trimmed();
    cadena += "',cuenta_iva_repercutido='"+ cuenta_iva_repercutido.left(-1).replace("'","''").trimmed();
    cadena += "',cuenta_ret_ing='"+ retirpf.left(-1).replace("'","''").trimmed();
    cadena += "',cuenta_ret_irpf='"+ cuenta_ret_irpf.left(-1).replace("'","''").trimmed();
    cadena += "', cuentasapagar='"+ cuentasapagar.left(-1).replace("'","''");
    cadena += "',cuentasacobrar='"+ cuentasacobrar.left(-1).replace("'","''");
    cadena += "',clave_iva_defecto='"+ clave_iva_defecto.left(-1).replace("'","''");
    cadena += "', clave_gastos='"+ clave_gastos.left(-1).replace("'","''");
    cadena += "',clave_ingresos='"+ clave_ingresos.left(-1).replace("'","''");
    cadena += "',cuenta_pyg='"+ cuenta_pyg.left(-1).replace("'","''").trimmed();
    cadena += "'";
    cadena += ",prox_pase="+ prox_pase.left(-1).replace("'","''");
    cadena += ",prox_vencimiento="+ prox_vencimiento.left(-1).replace("'","''");
    cadena += ",igic=";
    if (cualControlador() == SQLITE) { if (igic) cadena += "1"; else cadena += "0"; }
    else { if (igic) cadena += "TRUE"; else cadena += "FALSE"; }
    cadena += ",analitica=";
    if (cualControlador() == SQLITE) { if (analitica) cadena += "1"; else cadena += "0"; }
    else { if (analitica) cadena += "TRUE"; else cadena += "FALSE"; }
    cadena += ", id_registral='"+ id_registral.left(-1).replace("'","''");
    cadena += "',fecha_constitucion='"+ fechaconst.toString("yyyy-MM-dd");
    cadena += "',objeto='"+ objeto.left(-1).replace("'","''");
    cadena += "', actividad='"+ actividad.left(-1).replace("'","''");
    cadena += "',prorrata_iva=";
    if (prorrata_iva.length()>0) cadena += prorrata_iva.left(-1).replace("'","''");
    else cadena += "0";
    cadena += ",prorrata_especial=";
    if (cualControlador() == SQLITE) { if (prorrata_especial) cadena += "1"; else cadena += "0"; }
    else { if (prorrata_especial) cadena += "TRUE"; else cadena += "FALSE"; }
    cadena += ", analitica_parc=";
    if (cualControlador() == SQLITE) { if (analitica_parc) cadena += "1"; else cadena += "0"; }
    else { if (analitica_parc) cadena += "TRUE"; else cadena += "FALSE"; }
    cadena += ",ctas_analitica='"+ ctas_analitica.left(-1).replace("'","''");
    cadena+="', prox_documento="+proxdoc.left(-1).replace("'","''");
    cadena+=", gestion_usuarios=";
    if (cualControlador() == SQLITE) { if (usuarios) cadena += "1"; else cadena += "0"; }
    else { if (usuarios) cadena += "TRUE"; else cadena += "FALSE"; }

    cadena+=", analitica_tabla=";
    if (cualControlador() == SQLITE) { if (analitica_tabla) cadena += "1"; else cadena += "0"; }
    else { if (analitica_tabla) cadena += "TRUE"; else cadena += "FALSE"; }

    cadena += ", provincia_def='"+ provincia_def.left(-1).replace("'","''");
    cadena += "', cuentas_aa='"+ cuentas_aa.left(-1).replace("'","''");
    cadena += "', cuentas_ag='"+ cuentas_ag.left(-1).replace("'","''");
    cadena += "', amoinv=";
    if (cualControlador() == SQLITE) { if (amoinv) cadena += "1"; else cadena += "0"; }
    else { if (amoinv) cadena += "TRUE"; else cadena += "FALSE"; }
    cadena += ",sec_recibidas=";
    if (cualControlador() == SQLITE) { if (sec_recibidas) cadena += "1"; else cadena += "0"; }
    else { if (sec_recibidas) cadena += "TRUE"; else cadena += "FALSE"; }
    cadena += ", prox_domiciliacion=";
    cadena += prox_domicilia;
    cadena += ", cod_ine_plaza='";
    cadena += cod_ine_plaza;
    cadena += "', caja_iva=";
    if (cualControlador() == SQLITE) { if (caja_iva) cadena += "1"; else cadena += "0"; }
    else { if (caja_iva) cadena += "TRUE"; else cadena += "FALSE"; }

    ejecutar(cadena);
}

// 
void basedatos::updateDiarioasientoasiento (QString nuevo, QString antiguo, QString ejercicio) {
    QString cadena = "update diario set asiento=";
    cadena += nuevo.left(-1).replace("'","''");
    cadena += " where asiento=";
    cadena += antiguo.left(-1).replace("'","''");
    cadena += " and ejercicio='";
    cadena += ejercicio;
    cadena += "'";
    ejecutar(cadena);
}

// 
void basedatos::updatePlanamortizaciones9cuenta_activo (QDate fecha_func,
                             QString codigo_activo, QString cuenta_am_acum,
                             QString cuenta_gasto, bool amort_personalizada,
                             QString valor_residual, QString coef_amort_contable,
                             QString coef_amort_fiscal, bool amort_fiscal_1ejer,
                             bool baja, QDate fecha_baja, QString motivo_baja,
                             QString cuenta_proveedor,
                             QString cuenta_activo) {
    QString cadquery = "update planamortizaciones ";
    cadquery += "set fecha_func='"+ fecha_func.toString("yyyy-MM-dd");
    cadquery += "',codigo_activo='"+ codigo_activo.left(-1).replace("'","''");
    cadquery += "',cuenta_am_acum='"+ cuenta_am_acum.left(-1).replace("'","''");
    cadquery += "',cuenta_gasto='"+ cuenta_gasto.left(-1).replace("'","''");
    cadquery += "',amort_personalizada=";
    if (cualControlador() == SQLITE) {
        if (amort_personalizada) cadquery += "1"; else cadquery += "0";
    }
    else {
        if (amort_personalizada) cadquery += "TRUE"; else cadquery += "FALSE";
    }
    cadquery+=",valor_residual=";
    if (valor_residual.length()>0) cadquery += convapunto(valor_residual);
    else cadquery += "0";
    cadquery += ",coef_amort_contable=";
    if (coef_amort_contable.length()>0) 
    { 
        cadquery += convapunto(coef_amort_contable);
        cadquery += "/100.0";
    }
    else cadquery += "0";

    cadquery += ",coef_amort_fiscal=";
    if (coef_amort_fiscal.length()>0) 
    { 
        cadquery += convapunto(coef_amort_fiscal);
        cadquery += "/100.0";
    }
    else cadquery += "0";
    cadquery+=",amort_fiscal_1ejer=";
    if (cualControlador() == SQLITE) {
        if (amort_fiscal_1ejer) cadquery += "1"; else cadquery += "0";
    }
    else {
        if (amort_fiscal_1ejer) cadquery += "TRUE"; else cadquery += "FALSE";
    }

    cadquery+=", baja=";
    if (cualControlador() == SQLITE) {
        if (baja) cadquery += "1"; else cadquery += "0";
    }
    else {
        if (baja) cadquery += "TRUE"; else cadquery += "FALSE";
    }
    cadquery += ", fecha_baja='"+ fecha_baja.toString("yyyy-MM-dd");
    cadquery += "', motivo_baja='"+ motivo_baja;
    cadquery += "', cuenta_proveedor='"+ cuenta_proveedor;
    cadquery += "'";
    cadquery += " where cuenta_activo='";
    cadquery += cuenta_activo.left(-1).replace("'","''");
    cadquery += "'";
    // QMessageBox::warning( this, tr("Plan de amortizaciones"),cadquery);
    ejecutar(cadquery);
}

// 
void basedatos::updateCi_amortasignacioncuentaci (QString asignacion, QString cuenta, QString ci) {
    QString cadquery = "UPDATE ci_amort SET asignacion=";
    cadquery += asignacion.left(-1).replace("'","''");
    cadquery += " WHERE cuenta='";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "' and ci='";
    cadquery += ci.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}

// 
void basedatos::updateVencimientos7num (QString cta_ordenante, QDate fecha_operacion,
                                        QString importe, QString cuenta_tesoreria,
                                        QDate fecha_vencimiento, QString concepto,
                                        QString num, bool domiciliable) {
    QString vdomiciliable;
    // bool domiciliable=cuenta_domiciliable(cta_ordenante);
    if (cualControlador()==SQLITE) vdomiciliable="0"; else vdomiciliable="false";
    if ( domiciliable && cualControlador()==SQLITE) vdomiciliable="1";
    if ( domiciliable && cualControlador()!=SQLITE) vdomiciliable="true";

    QString cad1 = "UPDATE vencimientos set ";
    cad1 += "cta_ordenante='"+ cta_ordenante.left(-1).replace("'","''");
    cad1 += "',fecha_operacion='"+ fecha_operacion.toString("yyyy-MM-dd");
    cad1 += "',importe="+ importe.left(-1).replace("'","''");
    cad1 += ",cuenta_tesoreria='"+ cuenta_tesoreria.left(-1).replace("'","''");
    cad1 += "',fecha_vencimiento='"+ fecha_vencimiento.toString("yyyy-MM-dd");
    cad1 += "',concepto='"+ concepto.left(-1).replace("'","''");
    cad1 += "',domiciliable=" + vdomiciliable;

    cad1 += ",usuario=";
    if (cualControlador() == SQLITE) { cad1 += "'"+ usuario_sistema() +"'"; }
    else { cad1 += "CURRENT_USER"; }
    cad1 += " where num="+ num.left(-1).replace("'","''");
    ejecutar(cad1);
}

// 
void basedatos::updatePresupuestopresupuestosejerciciocuenta (QString presupuesto, QStringList lista, QString ejercicio, QString cuenta) {
    QString cadena = "update presupuesto set presupuesto=";
    cadena += convapunto(presupuesto);
    for (int veces=0;veces<lista.size();veces++)
    {
        QString cadnum;
        cadnum.setNum(veces+1);
        cadena += ",presupuesto";
        cadena += cadnum;
        cadena += "=";
        if (lista.at(veces).length()==0) cadena += "0";
        else cadena += convapunto(lista.at(veces));
    }
    cadena += " where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' and cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

// 
void basedatos::updateEjercicios4codigo (bool sibase_ejercicio, QString base_ejercicio, bool sibase_presupuesto, QString base_presupuesto, bool presupuesto_base_cero, QString incremento, QString ejercicio) {
    QString cad = "update ejercicios set base_ejercicio='";
    cad += (sibase_ejercicio) ? base_ejercicio.left(-1).replace("'","''") : "";
    cad += "', base_presupuesto='";
    cad += (sibase_presupuesto) ? base_presupuesto.left(-1).replace("'","''") : "";
    cad += "',presupuesto_base_cero=";
    if (cualControlador() == SQLITE) {
        cad += presupuesto_base_cero ? "1" : "0";
    }
    else {
        cad += presupuesto_base_cero ? "true" : "false";
    }
    cad += ", incremento=";
    cad += convapunto(incremento).left(-1).replace("'","''");
    cad += " where codigo='";
    cad += ejercicio.left(-1).replace("'","''");
    cad += "'";

    ejecutar(cad); 
}

// 
void basedatos::updatePresupuestobaseejerciciobaseejerciciocuenta (QString base, QString ejerciciobase, QString ejercicio, QString cuenta) {
    QString cad = "update presupuesto set base=";
    cad += base.left(-1).replace("'","''");
    cad += ",ejerciciobase='";
    cad += ejerciciobase.left(-1).replace("'","''");
    cad += "' where ejercicio='";
    cad += ejercicio.left(-1).replace("'","''");
    cad += "' and cuenta='";
    cad += cuenta.left(-1).replace("'","''");
    cad += "'";
    ejecutar(cad);
}

// 
void basedatos::updatePresupuestobaseejerciciocuenta (QString basen, QString base, QString ejercicio, QString cuenta) {
    QString cadbase="update presupuesto set ";
    cadbase += "base"+ basen;
    cadbase += "=";
    cadbase += base.left(-1).replace("'","''");
    cadbase += " where ejercicio='";
    cadbase += ejercicio.left(-1).replace("'","''");
    cadbase += "' and cuenta='";
    cadbase += cuenta.left(-1).replace("'","''");
    cadbase += "'";
    ejecutar(cadbase);
}

// 
void basedatos::updatePresupuestobasespresupuestobaseejerciciocuenta (QStringList lista, QString presupuestobase, QString ejercicio, QString cuenta) {
    QString cad = "update presupuesto set base="+ lista.at(0).left(-1).replace("'","''");
    cad += ", base1="+ lista.at(1).left(-1).replace("'","''");
    cad += ", base2="+ lista.at(2).left(-1).replace("'","''");
    cad += ", base3="+ lista.at(3).left(-1).replace("'","''");
    cad += ", base4="+ lista.at(4).left(-1).replace("'","''");
    cad += ", base5="+ lista.at(5).left(-1).replace("'","''");
    cad += ", base6="+ lista.at(6).left(-1).replace("'","''");
    cad += ", base7="+ lista.at(7).left(-1).replace("'","''");
    cad += ", base8="+ lista.at(8).left(-1).replace("'","''");
    cad += ", base9="+ lista.at(9).left(-1).replace("'","''");
    cad += ", base10="+ lista.at(10).left(-1).replace("'","''");
    cad += ", base11="+ lista.at(11).left(-1).replace("'","''");
    cad += ", base12="+ lista.at(12).left(-1).replace("'","''");
    cad += ", base13="+ lista.at(13).left(-1).replace("'","''");
    cad += ", base14="+ lista.at(14).left(-1).replace("'","''");
    cad += ", base15="+ lista.at(15).left(-1).replace("'","''");
    cad += ", presupuestobase='"+ presupuestobase.left(-1).replace("'","''");
    cad += "' where ejercicio='"+ ejercicio.left(-1).replace("'","''");
    cad += "' and cuenta='"+ cuenta.left(-1).replace("'","''");
    cad += "'";
    ejecutar(cad);
}

// 
void basedatos::updatePresupuestopresupuestoincrementoejerciciocuenta (QString incremento, QString ejercicio, QString cuenta) {
    QString cad = "update presupuesto set presupuesto=base*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto1=base1*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto2=base2*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto3=base3*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto4=base4*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto5=base5*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto6=base6*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto7=base7*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto8=base8*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto9=base9*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto10=base10*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto11=base11*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto12=base12*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto13=base13*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto14=base14*(1+";
    cad += convapunto(incremento);
    cad += "/100), presupuesto15=base15*(1+";
    cad += convapunto(incremento);
    cad += "/100) where ejercicio='";
    cad += ejercicio.left(-1).replace("'","''");
    cad += "' and cuenta='";
    cad += cuenta.left(-1).replace("'","''");
    cad += "'";
    ejecutar(cad);
}

// 
void basedatos::updateVencimientos4num (QString cuenta_tesoreria, QDate fecha_realizacion,
                                        bool pendiente, QString pase_diario_vencimiento,
                                        QString medio_realizacion, QString cuenta_bancaria,
                                        QString num) {
    // hacer dos update distinto según sea el valor de forzar_liq_iva
    // si es verdadero, liq_iva_fecha no debe de cambiar
    QString cadquery = "update vencimientos set ";
    cadquery += "cuenta_tesoreria='"+ cuenta_tesoreria.left(-1).replace("'","''");
    cadquery += "', medio_realizacion='"+ medio_realizacion.left(-1).replace("'","''");
    cadquery += "', cuenta_bancaria='"+ cuenta_bancaria.left(-1).replace("'","''");
    cadquery += "',fecha_realizacion='"+ fecha_realizacion.toString("yyyy-MM-dd");
    cadquery += "',liq_iva_fecha='"+ fecha_realizacion.toString("yyyy-MM-dd");
    cadquery += "',pendiente=";
    if (cualControlador() == SQLITE) {
        if (pendiente) { cadquery += "1"; }
        else { cadquery += "0"; }
    }
    else {
        if (pendiente) { cadquery += "true"; }
        else { cadquery += "false"; }
    }
    cadquery += ", pase_diario_vencimiento="+ pase_diario_vencimiento.left(-1).replace("'","''");
    cadquery += " where num="+ num.left(-1).replace("'","''");
    cadquery += " and not forzar_liq_iva";
    ejecutar(cadquery);

    // no actualizamos liq_iva_fecha en caso de liquidación de IVA forzada
    cadquery = "update vencimientos set ";
    cadquery += "cuenta_tesoreria='"+ cuenta_tesoreria.left(-1).replace("'","''");
    cadquery += "', medio_realizacion='"+ medio_realizacion.left(-1).replace("'","''");
    cadquery += "', cuenta_bancaria='"+ cuenta_bancaria.left(-1).replace("'","''");
    cadquery += "',fecha_realizacion='"+ fecha_realizacion.toString("yyyy-MM-dd");
    cadquery += "',pendiente=";
    if (cualControlador() == SQLITE) {
        if (pendiente) { cadquery += "1"; }
        else { cadquery += "0"; }
    }
    else {
        if (pendiente) { cadquery += "true"; }
        else { cadquery += "false"; }
    }
    cadquery += ", pase_diario_vencimiento="+ pase_diario_vencimiento.left(-1).replace("'","''");
    cadquery += " where num="+ num.left(-1).replace("'","''");
    cadquery += " and forzar_liq_iva";
    ejecutar(cadquery);


}

// 
void basedatos::updateEstadosreferencianodotituloparte1 (QString referencia, QString nodo, QString titulo, bool parte1) {
    QString cadena="update estados set referencia='";
    cadena += referencia.left(-1).replace("'","''");
    cadena += "' where nodo='";
    cadena += nodo.left(-1).replace("'","''");
    cadena += "' and titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "' and ";
    if (!parte1) { cadena += "not "; }
    cadena += "parte1";
    ejecutar(cadena);
}

// 
void basedatos::updateLibroivapasepase (QString antiguo, QString nuevo) {
    QString cad2 = "update libroiva set pase=";
    cad2 += nuevo.left(-1).replace("'","''");
    cad2 += " where pase=";
    cad2 += antiguo.left(-1).replace("'","''");
    ejecutar(cad2);
}

// 
void basedatos::updateVencimientospase_diario_operacionpase_diario_operacion (QString antiguo, QString nuevo) {
    QString cad2 = "update vencimientos set pase_diario_operacion=";
    cad2 += nuevo.left(-1).replace("'","''");
    cad2 += " where pase_diario_operacion=";
    cad2 += antiguo.left(-1).replace("'","''");
    ejecutar(cad2);
}

// 
void basedatos::updateVencimientospase_diario_vencimientopase_diario_vencimiento (QString antiguo, QString nuevo) {
    QString cad2 = "update vencimientos set pase_diario_vencimiento=";
    cad2 += nuevo.left(-1).replace("'","''");
    cad2 += " where pase_diario_vencimiento=";
    cad2 += antiguo.left(-1).replace("'","''");
    ejecutar(cad2);
}

// 
void basedatos::updateDiariopase5000000fecha (QDate inicioej) {
    QString cadena = "update diario set pase=pase+5000000 where fecha>='";
    cadena += inicioej.toString("yyyy-MM-dd");
    cadena += "'";
    ejecutar(cadena);
}

// 
void basedatos::updateDiariosetasientopaseasientopase (QString asientoA, QString paseA, QString asientoN, QString paseN) {
    QString cad2 = "update diario set asiento=";
    cad2 += asientoN.left(-1).replace("'","''");
    cad2 += ",pase=";
    cad2 += paseN.left(-1).replace("'","''");
    cad2 += " where asiento=";
    cad2 += asientoA.left(-1).replace("'","''");
    cad2 += " and pase=";
    cad2 += paseA.left(-1).replace("'","''");
    ejecutar(cad2);
}

void basedatos::cambia_asiento_a_pase (QString pase, QString asientoN) {
    QString cad2 = "update diario set asiento=";
    cad2 += asientoN.left(-1).replace("'","''");
    cad2 += " where pase=";
    cad2 += pase.left(-1).replace("'","''");
    ejecutar(cad2);
}


// Devuelve el primer asiento a partir de una fecha
qlonglong basedatos::primasejerciciodelafecha (QDate fecha) {
    QString cadena = "select primerasiento from ejercicios where apertura<='";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "' and cierre>='";
    cadena += fecha.toString("yyyy-MM-dd");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toLongLong();
    }
    return 0;
}

// 
void basedatos::calculaprimerasientoejercicio (QString ejercicio) {
    QString cadena = "select apertura,cierre,primerasiento from ejercicios where codigo='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
    {
        if (query.value(2).toInt()>0) return;
        QString cad2 = "select min(asiento) from diario where fecha>='";
        cad2 += query.value(0).toDate().toString("yyyy-MM-dd");
        cad2 += "' and fecha<='";
        cad2 += query.value(1).toDate().toString("yyyy-MM-dd");
        cad2 += "'";
        QSqlQuery query2 = ejecutar(cad2);
        QString cadnum;
        if ( (query.isActive()) && (query2.first()) )
	    {
            int num=query2.value(0).toInt();
            cadnum.setNum(num);
            QString cad3 = "update ejercicios set primerasiento=";
            cad3 += cadnum;
            cad3 += " where codigo='";
            cad3 += ejercicio.left(-1).replace("'","''");
            cad3 += "'";
            ejecutar(cad3);
	     }
    }
}

//


// 
QString basedatos::numrelativa (QString numabsoluta) {
    QString cadena = "select fecha from diario where asiento=";
    cadena += numabsoluta;
    QSqlQuery query = ejecutar(cadena);

    QString ejercicio;
    if ( (query.isActive()) && (query.first()) ) {
        ejercicio = selectEjerciciodelafecha(query.value(0).toDate());
    }

    QString cadnum;
    qlonglong absoluta = numabsoluta.toLongLong();
    cadnum.setNum( absoluta - selectPrimerasiento(ejercicio) + 1 );
    return cadnum;
}

// Indica si se sobreescribe el próximo asiento
bool basedatos::sobreescribeproxasiento (QString ejercicio) {
    QString cadena="select prox_asiento, primero_apertura from ejercicios where codigo='";
    cadena+=ejercicio.left(-1).replace("'","''");
    cadena+="'";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) ) {
        QString cadquery = "SELECT count(asiento) from diario where asiento="
                           + query.value(0).toString();
        cadquery+=" and ejercicio='";
        cadquery+=ejercicio.left(-1).replace("'","''");
        cadquery+="'";
        QSqlQuery query2 = ejecutar(cadquery);
        if ( (query2.isActive()) && (query2.first()) )
        {
            if (query2.value(0).toInt()>0) return true;
        }
    }
    return false;
}

// Indica si existe un código en el plan contable
int basedatos::existecodigoplan (QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descripcion from plancontable where codigo = '";
    cadquery += cadena.left(-1).replace("'","''") + "'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return 1;
    }
    return 0;
}


// Indica si existe un código de factura para la importación
bool basedatos::existecodigofactura_diario (QString cadena) {
    QString cadquery = "SELECT codfactura from diario where codfactura = '";
    cadquery += cadena.left(-1).replace("'","''") + "'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) ) return true;
    return false;
}

// Indica si existe un código de factura para la importación igual ejercicio y subcuenta
bool basedatos::existecodigofactura_ej_y_cta_diario (QString cadena, QString cuenta,
                                                     QString ejercicio) {
    QString cadquery = "SELECT codfactura from diario where codfactura = '";
    cadquery += cadena.left(-1).replace("'","''") + "'";
    cadquery += " and cuenta='";
    cadquery +=  cuenta.left(-1).replace("'","''") + "'";
    cadquery += " and ejercicio='";
    cadquery += ejercicio.left(-1).replace("'","''") +"'";
    // QMessageBox::information( 0, "import",cadquery);

    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) ) return true;
    return false;
}


// Indica si una subcuenta es cuenta de iva soportado
int basedatos::escuentadeivasoportado (const QString subcuenta) {
    QString cadquery, qcuenta, qcompara;
    cadquery = "SELECT cuenta_iva_soportado from configuracion";
    QSqlQuery query = ejecutar(cadquery);
 
    if ( (query.isActive()) && (query.first()) )
    {
        qcuenta = query.value(0).toString();
	    if (qcuenta.length()==0) return 0;
        qcompara = subcuenta.left(qcuenta.length());
	    if (qcompara.compare(qcuenta)==0) return 1; else return 0;
    }
    return 0;
}

// Indica si una subcuenta es cuenta de iva repercutido
int basedatos::escuentadeivarepercutido (const QString subcuenta) {
    QString cadquery, qcuenta, qcompara;
    cadquery="SELECT cuenta_iva_repercutido from configuracion";
    QSqlQuery query = ejecutar(cadquery);

    if ( (query.isActive()) && (query.first()) )
    {
        qcuenta = query.value(0).toString();
	    if (qcuenta.length()==0) return 0;
        qcompara = subcuenta.left(qcuenta.length());
	    if (qcompara.compare(qcuenta)==0) return 1; else return 0;
    }
    return 0;
}


bool basedatos::escuenta_de_ret_irpf (const QString subcuenta) {
            QString cadquery, qcuenta, qcompara;
            cadquery="SELECT cuenta_ret_irpf from configuracion";
            QSqlQuery query = ejecutar(cadquery);

            if ( (query.isActive()) && (query.first()) )
            {
                qcuenta = query.value(0).toString();
                    if (qcuenta.length()==0) return false;
                qcompara = subcuenta.left(qcuenta.length());
                    if (qcompara.compare(qcuenta)==0) return true; else return false;
            }
            return false;
        }


bool basedatos::escuenta_de_ret_ing (const QString subcuenta) {
            QString cadquery, qcuenta, qcompara;
            cadquery="SELECT cuenta_ret_ing from configuracion";
            QSqlQuery query = ejecutar(cadquery);

            if ( (query.isActive()) && (query.first()) )
            {
                qcuenta = query.value(0).toString();
                    if (qcuenta.length()==0) return false;
                qcompara = subcuenta.left(qcuenta.length());
                    if (qcompara.compare(qcuenta)==0) return true; else return false;
            }
            return false;
        }


// Borra los asientos desde el inicial hasta el final en una transacción
void basedatos::borraasientos (QString asientoinicial,QString asientofinal,QString ejercicio) {
    // primero borramos y actualizamos registros de iva y vencimientos
    QSqlDatabase::database().transaction();

    borraasientosnotransac (asientoinicial,asientofinal,ejercicio);

    QSqlDatabase::database().commit();

}


// Borra los asientos desde el inicial hasta el final
void basedatos::borraasientosnotransac (QString asientoinicial,QString asientofinal,
                                        QString ejercicio) {
    // primero borramos y actualizamos registros de iva y vencimientos

    QSqlQuery query = selectPasedebehabercuentadiario(asientoinicial,asientofinal,ejercicio);

    if ( query.isActive() )
    {
        while (query.next())
        {
            deletediarioci_clave (query.value(4).toString());
            deletelibroivapase(query.value(0).toString());
            deleteretencionespase (query.value(0).toString());
            deletevencimientospase_diario_operacion(query.value(0).toString());
            quitacontab_fra_de_pase (query.value(0).toString());
            updateVencimientospase_diario_vencimientopendientepase_diario_vencimiento(query.value(0).toString());
            updateSaldossubcuentasaldomenosmascodigo( query.value(1).toString(),query.value(2).toString(),
                                                      query.value(3).toString() );
            // procesamos aquí borrado de imagen de documento
            QString fichdoc=basedatos::instancia()->copia_docdiario (
                    basedatos::instancia()->selectAsientodiariopase(query.value(0).toString()),
                    ejercicio);
            if (!fichdoc.isEmpty())
              {
               fichdoc=adapta(expanderutadocfich(fichdoc));
               // QMessageBox::warning( 0, QObject::tr("sentencia"),fichdoc);
               QFile file(fichdoc);
               file.remove();
              }
            // registramos apunte borrado en tabla "apuntes_borrados"
            registra_apunte_borrado(query.value(0).toString());

        }
    }
    deleteDiarioasientomayorigualmenorigual(asientoinicial,asientofinal,ejercicio);

}

// registra apunte a borrar en tabla
void basedatos::registra_apunte_borrado(QString pase)
{
    QString cadena="insert into apuntes_borrados (pase, fecha_reg, hora_reg) values (";
    cadena+=pase;
    cadena+=", '";
    QDate fecha;
    cadena+=fecha.currentDate().toString("yyyy-MM-dd");
    cadena+="', '";
    QTime hora;
    hora=hora.currentTime();
    cadena+=hora.toString("hhmmss");
    cadena+="')";
    ejecutar(cadena);
}


// Borra los asientos desde el inicial hasta el final
void basedatos::borraasientosnotransac_ni (QString asientoinicial,
                                           QString asientofinal,
                                           QString ejercicio) {
    // primero borramos y actualizamos registros de iva y vencimientos

    QSqlQuery query = selectPasedebehabercuentadiario(asientoinicial,asientofinal,ejercicio);

    if ( query.isActive() )
    {
        while (query.next())
        {
            deletediarioci_clave (query.value(4).toString());
            deletelibroivapase(query.value(0).toString());
            deleteretencionespase (query.value(0).toString());
            deletevencimientospase_diario_operacion(query.value(0).toString());
            quitacontab_fra_de_pase (query.value(0).toString());
            updateVencimientospase_diario_vencimientopendientepase_diario_vencimiento(query.value(0).toString());
            updateSaldossubcuentasaldomenosmascodigo( query.value(1).toString(),query.value(2).toString(),
                                                      query.value(3).toString() );
            // procesamos aquí borrado de imagen de documento
            QString fichdoc=basedatos::instancia()->copia_docdiario (
                    basedatos::instancia()->selectAsientodiariopase(query.value(0).toString()),
                           ejercicio);
            if (!fichdoc.isEmpty())
              {
               fichdoc=adapta(expanderutadocfich(fichdoc));
               // QMessageBox::warning( 0, QObject::tr("sentencia"),fichdoc);
               QFile file(fichdoc);
               file.remove();
              }
            registra_apunte_borrado(query.value(0).toString());
        }
    }
    deleteDiarioasientomayorigualmenorigual(asientoinicial,asientofinal,ejercicio);

}

// Borra los asientos desde el inicial hasta el final
void basedatos::borraasientosnotransac_no_copiadoc (QString asientoinicial,
                                                    QString asientofinal,
                                                    QString ejercicio) {
    // primero borramos y actualizamos registros de iva y vencimientos

    QSqlQuery query = selectPasedebehabercuentadiario(asientoinicial,asientofinal,ejercicio);

    if ( query.isActive() )
    {
        while (query.next())
        {
            deletediarioci_clave (query.value(4).toString());
            deletelibroivapase(query.value(0).toString());
            deletevencimientospase_diario_operacion(query.value(0).toString());
            deleteretencionespase (query.value(0).toString());
            quitacontab_fra_de_pase (query.value(0).toString());
            updateVencimientospase_diario_vencimientopendientepase_diario_vencimiento(query.value(0).toString());
            updateSaldossubcuentasaldomenosmascodigo( query.value(1).toString(),query.value(2).toString(),
                                                      query.value(3).toString() );
            registra_apunte_borrado(query.value(0).toString());
        }
    }
    deleteDiarioasientomayorigualmenorigual(asientoinicial,asientofinal,ejercicio);

}


// Indica si hay un asiento en amortcontable entre el inicial y el final
/*bool basedatos::amortenintasientos (QString inicial,QString final,QString ejercicio) {
    QString cadena = "select asiento from amortcontable ";
    cadena += "where asiento>="+ inicial +" and asiento<="+ final;
    cadena += " and ejercicio='";
    cadena += ejercicio;
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) ) // antes query.size()>0
    {
        return true;
    }
    return false;
}
*/

bool basedatos::amortenintasientos (QString inicial,QString final,QString ejercicio) {
    QString cadena = "select asiento from amortcontable ";
    cadena += "where ((asiento>="+ inicial +" and asiento<="+ final +")";
    cadena += " or (as_enero>="+ inicial +" and as_enero<="+ final +")";
    cadena += " or (as_febrero>="+ inicial +" and as_febrero<="+ final +")";
    cadena += " or (as_marzo>="+ inicial +" and as_marzo<="+ final +")";
    cadena += " or (as_abril>="+ inicial +" and as_abril<="+ final +")";
    cadena += " or (as_mayo>="+ inicial +" and as_mayo<="+ final +")";
    cadena += " or (as_junio>="+ inicial +" and as_junio<="+ final +")";
    cadena += " or (as_julio>="+ inicial +" and as_julio<="+ final +")";
    cadena += " or (as_agosto>="+ inicial +" and as_agosto<="+ final +")";
    cadena += " or (as_septiembre>="+ inicial +" and as_septiembre<="+ final +")";
    cadena += " or (as_octubre>="+ inicial +" and as_octubre<="+ final +")";
    cadena += " or (as_noviembre>="+ inicial +" and as_noviembre<="+ final +")";
    cadena += " or (as_diciembre>="+ inicial +" and as_diciembre<="+ final +")";
    cadena += ") and ejercicio='";
    cadena += ejercicio;
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) ) // antes query.size()>0
    {
        return true;
    }
    return false;
}


//Indica si un pase corresponde a un asiento
bool basedatos::paseenasiento (QString pase, QString asiento) {
    QString cadena = "select pase from diario ";
    cadena += "where pase="+ pase + " and asiento="+ asiento;
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        if (query.value(0).toString() == pase) return true;
    }
    return false;
}

// Indica si la cuenta pertenece a un grupo de cuentas
int basedatos::subgrupocongrupo (QString cadena) {
    return codigoconcuentandigitos(cadena,"1");
}

// Indica si una cuenta pertenece a un subgrupo
int basedatos::cuentaconsubgrupo (QString cadena) {
    return codigoconcuentandigitos(cadena,"2");
}

// Indica si una cuenta coincide con un subgrupo de 3 dígitos
int basedatos::codigoconcuenta3digitos (QString cadena) {
    return codigoconcuentandigitos(cadena,"3");
}

// Indica si una cuenta coincide con un subgrupo de N dígitos
int basedatos::codigoconcuentandigitos (QString cadena,QString digitos) {
    QString cadquery = "SELECT codigo from plancontable where codigo = ";
    if ( cualControlador() == SQLITE ) {
        cadquery += "substr('"+ cadena.left(-1).replace("'","''") +"',1,"+ digitos +")";
    }
    else {
        cadquery += "substring('"+ cadena.left(-1).replace("'","''") +"' from 1 for "+ digitos +")";
    }
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return 1;
    }
    return 0;
}

// Elimina un elemento del plancontable si no tiene "hijos"
int basedatos::itemplaneliminado (QString qcodigo) {
    QString cadquery = "SELECT codigo from plancontable where ";
    if ( cualControlador() == SQLITE ) {
        cadquery += "length(codigo) > ";
        cadquery += "length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "') and substr(codigo,1,length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'))='";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'";
    }
    else {
        cadquery += "char_length(codigo) > ";
        cadquery += "char_length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "') and substring(codigo from 1 for char_length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'))='";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'";
    }
    
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return 0;
    }

    deletePlancontablecodigo(qcodigo);
    return 1;
}

// Indica si una cuenta tiene apuntes en el diario
int basedatos::cuentaendiario (QString cadena) {
    QString cadquery;
    cadquery="SELECT cuenta from diario where cuenta = '"+ cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return 1;
    }
    return 0;
}

// Guarda los datos accesorios de una subcuenta
void basedatos::guardadatosaccesorios (QString cuenta, QString razon, QString ncomercial,QString cif,
    QString nifrprlegal, QString domicilio, QString poblacion,
    QString codigopostal, QString provincia, QString pais,
    QString tfno, QString fax, QString email, QString observaciones,
    QString ccc, QString cuota, bool ivaasoc, QString tipoiva, QString cuentaiva,
    bool venciasoc, QString tipovenci, QString cuentatesor, QString conceptodiario,
    QString web, QString codclaveidfiscal, bool procvto,
    QString pais_dat, QString qtipo_ret, bool ret_arrendamiento, QString tipo_operacion_ret,
    int vdiafijo,
    QString cuenta_ret_asig, bool es_ret_asig_arrend,
    QString tipo_ret_asig, QString tipo_oper_ret_asig,
    bool dom_bancaria, QString iban, QString nriesgo, QString cedente1, QString cedente2,
    QString bic, QString sufijo, bool caja_iva, QString ref_mandato, QDate firma_mandato,
    QString fototexto) {
    QString qdescrip;
    if (!existecodigoplan(cuenta,&qdescrip)) return;
    QString tipo_ret;
    tipo_ret.setNum(convapunto(qtipo_ret).toDouble(),'f',2);
    // QMessageBox::warning( 0, QObject::tr("datos"),qtipo_ret);
    // QMessageBox::warning( 0, QObject::tr("datos"),tipo_ret);
    QString cadquery = "SELECT cuenta from datossubcuenta where cuenta = '"+ cuenta.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) &&  (query.first()) )
    {
        cadquery = "UPDATE datossubcuenta ";
        cadquery += "set razon = '"+ razon.left(-1).replace("'","''") +"', ";
        cadquery += "nombrecomercial = '"+ ncomercial.left(-1).replace("'","''") +"', ";
        cadquery += "cif = '"+ cif.left(-1).replace("'","''") +"', ";
        cadquery += "nifrprlegal = '"+ nifrprlegal.left(-1).replace("'","''") +"', ";
        cadquery += "domicilio = '"+ domicilio.left(-1).replace("'","''") +"', ";
        cadquery += "poblacion = '"+ poblacion.left(-1).replace("'","''") +"', ";
        cadquery += "codigopostal = '"+ codigopostal.left(-1).replace("'","''") +"', ";
        cadquery += "provincia = '"+ provincia.left(-1).replace("'","''") +"', ";
        cadquery += "pais = '"+ pais.left(-1).replace("'","''") +"', ";
        cadquery += "tfno = '"+ tfno.left(-1).replace("'","''") +"', ";
        cadquery += "fax = '"+ fax.left(-1).replace("'","''") +"', ";
        cadquery += "email = '"+ email.left(-1).replace("'","''") +"', ";
        cadquery += "observaciones = '"+ observaciones.left(-1).replace("'","''") +"', ";
        cadquery += "web = '"+ web.left(-1).replace("'","''") +"',";
        cadquery += "claveidfiscal = '"+ codclaveidfiscal.left(-1).replace("'","''") +"',";
        cadquery += "ccc = '"+ ccc.left(-1).replace("'","''") +"',";
        cadquery += "cuota = '"+ cuota.left(-1).replace("'","''") +"', ";
        if ( cualControlador() == SQLITE ) cadquery += venciasoc ? "venciasoc = 1," : "venciasoc = 0,";
           else cadquery += venciasoc ? "venciasoc = true," : "venciasoc = false,";
        cadquery+= "codvenci='";
        cadquery+= tipovenci.left(-1).replace("'","''") +"', ";
        cadquery+= "tesoreria='";
        cadquery+= cuentatesor.left(-1).replace("'","''") +"', ";
        if ( cualControlador() == SQLITE ) cadquery += ivaasoc ? "ivaasoc = 1," : "ivaasoc = 0,";
           else cadquery += ivaasoc ? "ivaasoc = true," : "ivaasoc = false,";
        if ( cualControlador() == SQLITE ) cadquery += procvto ? "procesavenci = 1," : "procesavenci = 0,";
           else cadquery += procvto ? "procesavenci = true," : "procesavenci = false,";
        cadquery+= "cuentaiva='";
        cadquery+=cuentaiva.left(-1).replace("'","''") +"', ";
        cadquery+="tipoiva='";
        cadquery+=tipoiva.left(-1).replace("'","''") +"', ";
        cadquery+="conceptodiario='";
        cadquery+=conceptodiario.left(-1).replace("'","''") +"', ";
        cadquery+="pais_dat='";
        cadquery+=pais_dat.left(-1).replace("'","''") +"', ";
        cadquery+="tipo_ret=";
        cadquery+=tipo_ret.isEmpty() ? "0" : convapunto(tipo_ret) +", ";;
        if ( cualControlador() == SQLITE )
            cadquery += ret_arrendamiento ? "ret_arrendamiento = 1," : "ret_arrendamiento = 0,";
           else cadquery += ret_arrendamiento ? "ret_arrendamiento = true," : "ret_arrendamiento = false,";
        cadquery+=" tipo_operacion_ret='";
        cadquery+= tipo_operacion_ret.replace("'","''") +"', vto_dia_fijo = ";
        QString cadnumdia; cadnumdia.setNum(vdiafijo);
        cadquery+= cadnumdia;
        // QString cuenta_ret_asig, bool es_ret_asig_arrend,
        // QString tipo_ret_asig, QString tipo_oper_ret_asig,
        cadquery+=", cuenta_ret_asig='";
        cadquery+=cuenta_ret_asig;
        cadquery+="', ";
        if ( cualControlador() == SQLITE )
            cadquery += es_ret_asig_arrend ? "es_ret_asig_arrend = 1," : "es_ret_asig_arrend = 0,";
           else cadquery += es_ret_asig_arrend ? "es_ret_asig_arrend = true," : "es_ret_asig_arrend = false,";
        cadquery += "tipo_ret_asig= ";
        cadquery += tipo_ret_asig.isEmpty() ? "0" : convapunto(tipo_ret_asig);
        cadquery += ", tipo_oper_ret_asig='";
        cadquery += tipo_oper_ret_asig;
        cadquery +="', ";
        if ( cualControlador() == SQLITE )
            cadquery += dom_bancaria ? "dom_bancaria = 1," : "dom_bancaria = 0,";
           else cadquery += dom_bancaria ? "dom_bancaria = true," : "dom_bancaria = false,";
        cadquery+=" iban='";
        cadquery+=iban;
        cadquery+="', ";
        cadquery+="nriesgo='";
        cadquery+=nriesgo;
        cadquery+="', cedente1='";
        cadquery+=cedente1;
        cadquery+="', cedente2='";
        cadquery+=cedente2;
        cadquery+="', BIC='";
        cadquery+=bic;
        cadquery+="', SUFIJO='";
        cadquery+=sufijo;
        cadquery+="', ref_mandato='";
        cadquery+=ref_mandato;
        cadquery+="', firma_mandato='";
        cadquery+=firma_mandato.toString("yyyy-MM-dd");
        cadquery+="',";
        if ( cualControlador() == SQLITE ) cadquery += caja_iva ? "caja_iva = 1" : "caja_iva = 0";
           else cadquery += caja_iva ? "caja_iva = true" : "caja_iva = false";
        // bool dom_bancaria, QString iban, QString nriesgo, QString cedente1, QString cedente2,
        cadquery += " where cuenta = '"+ cuenta.left(-1).replace("'","''") +"'";
            // QMessageBox::warning( 0, QObject::tr("sentencia"),cadquery);
        ejecutar(cadquery);
    }
    else
    {
        cadquery = "insert into datossubcuenta (cuenta,razon,nombrecomercial,cif,";
        cadquery += "nifrprlegal,domicilio,poblacion,codigopostal,";
        cadquery += "provincia,pais,tfno,fax,email,observaciones,ccc,cuota,venciasoc,"
                    "codvenci,tesoreria, ivaasoc,cuentaiva, tipoiva,conceptodiario,web,claveidfiscal,"
                    "procesavenci, "
                    "pais_dat, tipo_ret, ret_arrendamiento, tipo_operacion_ret, vto_dia_fijo, "
                    "cuenta_ret_asig, es_ret_asig_arrend, tipo_ret_asig, tipo_oper_ret_asig, "
                    "dom_bancaria, iban, nriesgo, cedente1, cedente2, bic, "
                    "sufijo, caja_iva, ref_mandato, firma_mandato) ";
        cadquery += "values('";
        cadquery += cuenta.left(-1).replace("'","''")+ "', '";
        cadquery += razon.left(-1).replace("'","''")+ "', '";
        cadquery += ncomercial.left(-1).replace("'","''")+ "', '";
        cadquery += cif.left(-1).replace("'","''")+ "', '";
        cadquery += nifrprlegal.left(-1).replace("'","''")+ "', '";
        cadquery += domicilio.left(-1).replace("'","''")+ "', '";
        cadquery += poblacion.left(-1).replace("'","''")+ "', '";
        cadquery += codigopostal.left(-1).replace("'","''")+ "', '";
        cadquery += provincia.left(-1).replace("'","''")+ "', '";
        cadquery += pais.left(-1).replace("'","''")+ "', '";
        cadquery += tfno.left(-1).replace("'","''")+ "', '";
        cadquery += fax.left(-1).replace("'","''")+ "', '";
        cadquery += email.left(-1).replace("'","''")+ "', '";
        cadquery += observaciones.left(-1).replace("'","''")+ "','";
        cadquery += ccc.left(-1).replace("'","''")+ "','";
        cadquery += cuota.left(-1).replace("'","''")+ "',";

        if ( cualControlador() == SQLITE ) cadquery += venciasoc ? "1, '" : "0, '";
           else cadquery += venciasoc ? "true,'" : "false, '";
        cadquery+= tipovenci.left(-1).replace("'","''") +"', '";
        cadquery+= cuentatesor.left(-1).replace("'","''") +"', ";
        if ( cualControlador() == SQLITE ) cadquery += ivaasoc ? "1, '" : "0, '";
           else cadquery += ivaasoc ? "true, '" : "false, '";
        cadquery+=cuentaiva.left(-1).replace("'","''") +"', '";
        cadquery+=tipoiva.left(-1).replace("'","''") +"','";
        cadquery+=conceptodiario.left(-1).replace("'","''") +"','";
        cadquery+=web.left(-1).replace("'","''") +"','";
        cadquery+=codclaveidfiscal.left(-1).replace("'","''") + "',";
        if ( cualControlador() == SQLITE ) cadquery += procvto ? "1" : "0";
           else cadquery += procvto ? "true" : "false";
        cadquery+=", '";
        cadquery+=pais_dat.left(-1).replace("'","''") +"', ";
        cadquery+=(tipo_ret.isEmpty() ? "0" : tipo_ret )+", ";;
        if ( cualControlador() == SQLITE )
               cadquery += ret_arrendamiento ? "1," : "0,";
              else cadquery += ret_arrendamiento ? "true," : "false,";
        cadquery+=" '";
        cadquery+= tipo_operacion_ret.replace("'","''") +"', ";
        QString cadnumdia; cadnumdia.setNum(vdiafijo);
        cadquery+=cadnumdia;
        // QString cuenta_ret_asig, bool es_ret_asig_arrend,
        // QString tipo_ret_asig, QString tipo_oper_ret_asig,
        cadquery+=", '";
        cadquery+=cuenta_ret_asig;
        cadquery+="', ";
        if ( cualControlador() == SQLITE )
               cadquery += es_ret_asig_arrend ? "1," : "0,";
              else cadquery += es_ret_asig_arrend ? "true," : "false,";
        cadquery+=tipo_ret_asig.isEmpty() ? "0" : convapunto(tipo_ret_asig);
        cadquery+=", '";
        cadquery+=tipo_oper_ret_asig;
        cadquery+="',";
        if ( cualControlador() == SQLITE )
               cadquery += dom_bancaria ? "1," : "0,";
              else cadquery += dom_bancaria ? "true," : "false,";
        cadquery+=" '";
        cadquery+=iban;
        cadquery+="', '";
        cadquery+=nriesgo;
        cadquery+="', '";
        cadquery+=cedente1;
        cadquery+="', '";
        cadquery+=cedente2;
        // bool dom_bancaria, QString iban, QString nriesgo, QString cedente1, QString cedente2,
        cadquery+="', '";
        cadquery+=bic;
        cadquery+="', '";
        cadquery+=sufijo;
        cadquery+="', ";
        if ( cualControlador() == SQLITE ) cadquery += caja_iva ? "1" : "0";
           else cadquery += caja_iva ? "true" : "false";
        cadquery+=", '";
        cadquery+=ref_mandato;
        cadquery+="', '";
        cadquery+=firma_mandato.toString("yyyy-MM-dd");
        cadquery+="')";
        ejecutar(cadquery);
    }
   // guardamos ahora la foto
        cadquery = "UPDATE datossubcuenta ";
        cadquery +="set imagen = '";
        cadquery += fototexto;
        cadquery += "' where cuenta = '"+ cuenta.left(-1).replace("'","''") +"'";
        ejecutar(cadquery);
}

// Indica si existe un concepto y devuelve su descripción en puntero
int basedatos::existecodigoconcepto (QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT clave,descripcion from conceptos where clave = '"+ cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return 1;
    }
    return 0;
}

bool basedatos::existecodigotabla (QString tabla, QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descripcion from ";
    cadquery+=tabla;
    cadquery+=" where codigo = '"+ cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return true;
    }
    return false;
}


// Indica si la subcuenta es de gasto
int basedatos::escuentadegasto (const QString subcuenta) {
    QString qcuentas, qcompara;
    QSqlQuery query = ejecutar("SELECT clave_gastos from configuracion");
    
    if ( (query.isActive()) && (query.first()) )
    {
        qcuentas = query.value(0).toString();
    	if (qcuentas.length() == 0) return 0;
    
        int partes = qcuentas.count(',');
        for (int veces=0;veces<=partes;veces++)
        {
            QString selec = qcuentas.section(',',veces,veces).trimmed();
            qcompara = subcuenta.left(selec.length());
       	    if (qcompara.compare(selec) == 0) return 1;
        }
    }
    return 0;
}

// Indica si la subcuenta es de ingresos
int basedatos::escuentadeingreso (const QString subcuenta) {
    QString qcuentas, qcompara;
    QSqlQuery query = ejecutar("SELECT clave_ingresos from configuracion");

    if ( (query.isActive()) && (query.first()) )
    {
        qcuentas = query.value(0).toString();
	    if (qcuentas.length() == 0) return 0;

        int partes = qcuentas.count(',');
        for (int veces=0;veces<=partes;veces++)
        {
            QString selec = qcuentas.section(',',veces,veces).trimmed();
            qcompara = subcuenta.left(selec.length());
    	    if (qcompara.compare(selec) == 0) return 1;
        }
    }
    return 0;
}

// Devuelve si existe una fila en saldossubcuenta para una cuenta
int basedatos::existecodigosaldosubcuenta (QString cadena) {
    QString cadquery = "SELECT codigo from saldossubcuenta where codigo = '";
    cadquery += cadena.left(-1).replace("'","''");
    cadquery += "'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return 1;
    }
    return 0;
}

// Devuelve la cadena de inicio del iva soportado
QString basedatos::cuentadeivasoportado () {
    QSqlQuery query = ejecutar("SELECT cuenta_iva_soportado from configuracion");
    
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve la cadena de inicio del iva repercutido
QString basedatos::cuentadeivarepercutido () {
    QSqlQuery query = ejecutar("SELECT cuenta_iva_repercutido from configuracion");
    
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Indica si un ci existe en el diario
bool basedatos::existeciendiario(QString codigo, int nivel) {
    QString cadena="select ci from diario where ";

    switch (nivel) {
        case 1: if (cualControlador() == SQLITE) {
                    cadena += "ci like '"+ codigo.left(-1).replace("'","''") +"%'";
                }
                else {
                    cadena += "position('"+ codigo.left(-1).replace("'","''") +"' in ci ) = 1";
                }
                break;
        case 2: if (cualControlador() == SQLITE) {
                    cadena += "ci like '___"+ codigo.left(-1).replace("'","''") +"%'";
                }
                else {
                    cadena += "position('"+ codigo.left(-1).replace("'","''") +"' in ci ) = 4";
                }
                break;
        case 3: if (cualControlador() == SQLITE) {
                    cadena += "ci like '______"+ codigo.left(-1).replace("'","''") +"%'";
                }
                else {
                    cadena += "position('"+ codigo.left(-1).replace("'","''") +"' in ci ) = 7";
                }
                break;
    }

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}

// Indica si un ci existe en ci_amort
bool basedatos::existecienplanamort (QString codigo, int nivel) {
    QString cadena="select ci from ci_amort where ";

    switch (nivel) {
        case 1: if (cualControlador() == SQLITE) {
                    cadena += "ci like '"+ codigo.left(-1).replace("'","''") +"%'";
                }
                else {
                    cadena += "position('"+ codigo.left(-1).replace("'","''") +"' in ci ) = 1";
                }
                break;
        case 2: if (cualControlador() == SQLITE) {
                    cadena += "ci like '___"+ codigo.left(-1).replace("'","''") +"%'";
                }
                else {
                    cadena += "position('"+ codigo.left(-1).replace("'","''") +"' in ci ) = 4";
                }
                break;
        case 3: if (cualControlador() == SQLITE) {
                    cadena += "ci like '______"+ codigo.left(-1).replace("'","''") +"%'";
                }
                else {
                    cadena += "position('"+ codigo.left(-1).replace("'","''") +"' in ci ) = 7";
                }
                break;
    }

    QSqlQuery query = ejecutar (cadena);
    if ( (query.isActive()) && (query.first()>0) )
    {
        return true;
    }
    return false;
}

// Devuelve la subcuenta anterior a una dada
QString basedatos::subcuentaanterior (QString qcodigo) {
    QString cadquery = "SELECT codigo from saldossubcuenta where codigo < '"+
                       qcodigo.left(-1).replace("'","''") +"' order by codigo;";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.last()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Devuelve la subcuenta posterior a una dada
QString basedatos::subcuentaposterior (QString qcodigo) {
    QString cadquery = "SELECT codigo from saldossubcuenta where codigo > '"+
                       qcodigo.left(-1).replace("'","''") +"' order by codigo;";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// Indica si un asiento tiene aib
bool basedatos::asientoenaib (QString asiento, QString ejercicio) {
    QString cadena="select libroiva.aib from diario,libroiva where diario.asiento=";
    cadena += asiento;
    cadena += " and diario.ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    cadena += " and diario.pase=libroiva.pase";
    QSqlQuery query = ejecutar(cadena);
    if ( query.isActive() )
    {
        while (query.next())
        {
    	    if (query.value(0).toBool()) return true;
        }
    }
    return false;
}

// Indica si un asiento tiene autofactura
bool basedatos::asientoenautofactura (QString asiento, QString ejercicio) {
    QString cadena="select libroiva.autofactura from diario,libroiva where diario.asiento=";
    cadena += asiento;
    cadena += " and diario.ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    cadena += " and diario.pase=libroiva.pase";
    QSqlQuery query = ejecutar(cadena);
    if ( query.isActive() )
    {
        while (query.next())
        {
    	    if (query.value(0).toBool()) return true;
        }
    }
    return false;
}

// indica si un asiento está en isp_op_interiores
bool basedatos::asiento_isp_op_interiores(QString asiento, QString ejercicio) {
    QString cadena="select libroiva.isp_op_interiores from diario,libroiva where diario.asiento=";
    cadena += asiento;
    cadena += " and diario.ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    cadena += " and diario.pase=libroiva.pase";
    QSqlQuery query = ejecutar(cadena);
    if ( query.isActive() )
    {
        while (query.next())
        {
            if (query.value(0).toBool()) return true;
        }
    }
    return false;
}

// Indica si un asiento tiene autofactura no ue
bool basedatos::asientoenautofactura_no_ue (QString asiento,QString ejercicio) {
    QString cadena="select libroiva.autofactura_no_ue from diario,libroiva where diario.asiento=";
    cadena += asiento;
    cadena += " and diario.ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    cadena += " and diario.pase=libroiva.pase";
    QSqlQuery query = ejecutar(cadena);
    if ( query.isActive() )
    {
        while (query.next())
        {
            if (query.value(0).toBool()) return true;
        }
    }
    return false;
}

// Indica si un asiento tiene pr_servicios_ue
bool basedatos::asientoenpr_servicios_ue (QString asiento,QString ejercicio) {
    QString cadena="select libroiva.pr_servicios_ue from diario,libroiva where diario.asiento=";
    cadena += asiento;
    cadena += " and diario.ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    cadena += " and diario.pase=libroiva.pase";
    QSqlQuery query = ejecutar(cadena);
    if ( query.isActive() )
    {
        while (query.next())
        {
            if (query.value(0).toBool()) return true;
        }
    }
    return false;
}


// Indica si un asiento tiene eib
bool basedatos::asientoeneib (QString asiento, QString ejercicio) {
    QString cadena="select libroiva.eib from diario,libroiva where diario.asiento=";
    cadena += asiento;
    cadena += " and diario.ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    cadena += " and diario.pase=libroiva.pase";
    QSqlQuery query = ejecutar(cadena);
    if ( query.isActive() )
    {
        while (query.next())
        {
	        if (query.value(0).toBool()) return true;
        }
    }
    return false;
}

// Indica si un asiento tiene eib
bool basedatos::asiento_exento_noaib_noeib (QString asiento, QString ejercicio) {
    QString cadena="select libroiva.eib,libroiva.aib from diario,libroiva where diario.asiento=";
    cadena += asiento;
    cadena += " and diario.ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    cadena += " and diario.pase=libroiva.pase and libroiva.cuota_iva=0";
    QSqlQuery query = ejecutar(cadena);
    if ( query.isActive() )
    {
        while (query.next())
        {
	        if (!query.value(0).toBool() && !query.value(1).toBool()) return true;
        }
    }
    return false;
}


bool basedatos::asientoenopnosujeta (QString asiento, QString ejercicio) {
    QString cadena="select libroiva.op_no_sujeta from diario,libroiva where diario.asiento=";
    cadena += asiento;
    cadena += " and diario.ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    cadena += " and diario.pase=libroiva.pase";
    QSqlQuery query = ejecutar(cadena);
    if ( query.isActive() )
    {
        while (query.next())
        {
                if (query.value(0).toBool()) return true;
        }
    }
    return false;
}


// Devuelve si existe un asiento en el diario
int basedatos::existeasiento (QString asiento, QString ejercicio) {
    QString cadquery;
    cadquery="SELECT asiento from diario where asiento=";
    cadquery+=asiento;
    cadquery+=" and ejercicio='";
    cadquery+=ejercicio;
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return 1;
    }
    return 0;
}


// Devuelve si existe un apunte en el diario
bool basedatos::existe_apunte (QString apunte) {
    QString cadquery;
    cadquery="SELECT asiento from diario where pase=";
    cadquery+=apunte;
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}





// Indica si hay una cuenta_activo en plan amortizaciones con un codigo_activo
bool basedatos::cuentaycodigoenplanamort (QString cuenta,QString codigo) {
    QString cadquery = "SELECT count(cuenta_activo) from planamortizaciones where cuenta_activo='";
    cadquery += cuenta.left(-1).replace("'","''") +"'";
    cadquery += " and codigo_activo='";
    cadquery += codigo.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        if (query.value(0).toInt()>0) return true;
    }
    return false;
}

// Indica si un ejercicio está en amortcontable
bool basedatos::practamort (QString ejercicio) {
    QString cadquery = "select count(ejercicio) from amortcontable where ejercicio='";
    cadquery += ejercicio.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        if (query.value(0).toInt()>0) return true;
    }
    return false;
}

bool basedatos::practamort_mes(QString ejercicio, int mes)
{
   QString cadquery = "select ";
   switch (mes)
   {
    case 1: cadquery+="as_enero"; break;
    case 2: cadquery+="as_febrero"; break;
    case 3: cadquery+="as_marzo"; break;
    case 4: cadquery+="as_abril"; break;
    case 5: cadquery+="as_mayo"; break;
    case 6: cadquery+="as_junio"; break;
    case 7: cadquery+="as_julio"; break;
    case 8: cadquery+="as_agosto"; break;
    case 9: cadquery+="as_septiembre"; break;
    case 10: cadquery+="as_octubre"; break;
    case 11: cadquery+="as_noviembre"; break;
    case 12: cadquery+="as_diciembre"; break;
   }

   cadquery+=" from amortcontable where ejercicio='";
   cadquery+=ejercicio;
   cadquery+="'";

   QSqlQuery query = ejecutar(cadquery);
   if ( (query.isActive()) && (query.first()) )
    {
       if (query.value(0).toLongLong()>0) return true;
    }
   return false;

}


void basedatos::asig_amort_mes_asiento(QString ejercicio, int mes, QString cadasiento)
{
   QString cadquery = "update amortcontable set ";
   switch (mes)
   {
    case 1: cadquery+="as_enero"; break;
    case 2: cadquery+="as_febrero"; break;
    case 3: cadquery+="as_marzo"; break;
    case 4: cadquery+="as_abril"; break;
    case 5: cadquery+="as_mayo"; break;
    case 6: cadquery+="as_junio"; break;
    case 7: cadquery+="as_julio"; break;
    case 8: cadquery+="as_agosto"; break;
    case 9: cadquery+="as_septiembre"; break;
    case 10: cadquery+="as_octubre"; break;
    case 11: cadquery+="as_noviembre"; break;
    case 12: cadquery+="as_diciembre"; break;
   }

   cadquery+="=";
   cadquery+=cadasiento.isEmpty() ? "0" : cadasiento;
   cadquery+=" where ejercicio='";
   cadquery+=ejercicio;
   cadquery+="'";

   ejecutar(cadquery);


}

void basedatos::borra_amort_mes_asiento(QString ejercicio, int mes)
{
    asig_amort_mes_asiento(ejercicio, mes, "0");
    QString cadquery = "delete from amortcontable where asiento=0 and as_enero=0 "
                       "and as_febrero=0 and as_marzo=0 and "
                       "as_abril=0 and as_mayo=0 and as_junio=0 and as_julio=0 "
                       "and as_agosto=0 and as_septiembre=0 "
                       "and as_octubre=0 and as_noviembre=0 and as_diciembre =0";
    ejecutar(cadquery);

}

bool basedatos::amortmensual_practicada(QString ejercicio)
{
    QString cadquery = "select asiento from amortcontable where asiento=0 and ejercicio='";
    cadquery+=ejercicio;
    cadquery+="'";
    QSqlQuery query=ejecutar(cadquery);
    if (query.isActive() && query.first())
        return true;
    return false;
}

// Indica si es pase de vencimiento procesado
bool basedatos::espasevtoprocesado (QString qpase) {
    QSqlQuery query = ejecutar("select count(pase_diario_vencimiento) from vencimientos where pase_diario_vencimiento="+ qpase);
    if ( (query.isActive()) &&(query.first()) )
        if (query.value(0).toInt()>0) return true;
    return false;
}

// 
QString basedatos::ejercicio_igual_o_maxigual (QDate fecha) {
    QString cadena="select max(cierre) from ejercicios where cierre<='";
    cadena+=fecha.toString("yyyy-MM-dd") +"'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) &&(query.first()) )
    {
        return ejerciciodelafecha(query.value(0).toDate());
    }
    return "";
}

// Devuelve el número de asiento de vencimiento
QString basedatos::numasientovto (QString vto) {
    QString cadquery = "SELECT asiento from diario,vencimientos where vencimientos.num=";
    cadquery += vto.left(-1).replace("'","''");
    cadquery += " and diario.pase=vencimientos.pase_diario_operacion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

// 
QString basedatos::numasientovtovto (QString vto) {
    QString cadquery = "SELECT asiento from diario,vencimientos where vencimientos.num=";
    cadquery += vto.left(-1).replace("'","''");
    cadquery += " and diario.pase=vencimientos.pase_diario_vencimiento";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}


QDate basedatos::fecha_pase_diario_vto (QString vto) {
    QString cadquery = "SELECT diario.fecha from diario,vencimientos where vencimientos.num=";
    cadquery += vto.left(-1).replace("'","''");
    cadquery += " and diario.pase=vencimientos.pase_diario_vencimiento";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDate();
    }
    return QDate();
}


// Indica si existe vencimiento
bool basedatos::existevencimiento (QString vto) {
    QSqlQuery query = ejecutar("SELECT pendiente from vencimientos where num="+vto.left(-1).replace("'","''"));
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}

// Devuelve si hay algún título en cabeceraestados
int basedatos::sihaytitulocabeceraestados (QString titulo) {
    QString cadena="select titulo from cabeceraestados where titulo='";
    cadena+=titulo.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return 2;
    }
    return 0;
}

// 
bool basedatos::int_asientos_ej_cerrado (QString asientoinicial,QString asientofinal) {
    QString cadena = "select fecha from diario where asiento>="+ asientoinicial.left(-1).replace("'","''");
    cadena += " and asiento<="+ asientofinal.left(-1).replace("'","''");
    QSqlQuery query = ejecutar(cadena);
    
    if (query.isActive()) {
        while (query.next())
        {
            if (fechadejerciciocerrado(query.value(0).toDate())) return true;
        }
    }
    return false;
}

// 
bool basedatos::cuentaenlibroiva (QString cuenta) {
    QString cadena = "select cuenta_fra from libroiva where cuenta_fra='";
    cadena += cuenta.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;  
}

// Indica si es un pase de apertura
bool basedatos::paseapertura (QString pase) {
    QSqlQuery query = ejecutar("select diario from diario where pase="+ pase.replace("'","''"));
    if ( (query.isActive()) && (query.first()) )
    {
        if (query.value(0).toString()==diario_apertura()) return true;
    }
    return false;
}

//
bool basedatos::paseconvto (QString pase) {
    QSqlQuery query = ejecutar("select pase_diario_operacion from vencimientos where pase_diario_operacion="+ pase.replace("'","''"));
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}

// 
bool basedatos::pasepagovto (QString pase) {
    QSqlQuery query = ejecutar("select pase_diario_vencimiento from vencimientos where pase_diario_vencimiento="+ pase.replace("'","''"));
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}

// 
bool basedatos::paseenlibroivaeib (QString pase) {
    QString cadena = "select pase from libroiva where pase="+ pase.left(-1).replace("'","''");
    cadena += " and eib";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return true;
    }
    return false;
}

// Indica si una cuenta pertenece a un grupo
int basedatos::subgrupocongruponuevoplan (QString cadena) {
    return codigoconcuentandigitosnuevoplan(cadena,"1");
}

// Indica si una cuenta pertenece a un grupo
int basedatos::cuentaconsubgruponuevoplan (QString cadena) {
    return codigoconcuentandigitosnuevoplan(cadena,"2");
}

// Indica si una cuenta pertenece a un grupo
int basedatos::codigoconcuenta3digitosnuevoplan(QString cadena)
{
    return codigoconcuentandigitosnuevoplan(cadena,"3");
}

// Indica si una cuenta pertenece a un grupo
int basedatos::codigoconcuentandigitosnuevoplan (QString cadena, QString n) {
    QString cadquery = "SELECT codigo from nuevoplan where codigo = ";
    if ( cualControlador() == SQLITE ) {
        cadquery += "substr('"+ cadena.left(-1).replace("'","''") +"',1,"+ n +")";
    }
    else {
        cadquery += "substring('"+ cadena.left(-1).replace("'","''") +"' from 1 for "+ n +")";
    }

    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return 1;
    }
    return 0;
}

// Si existe un nuevo plan y su descripción
int basedatos::existecodigoplannuevo (QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descripcion from nuevoplan where codigo = '";
    cadquery+=cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return 1;
    }
    return 0;
}

// 
int basedatos::siCodigoennuevoplanconlength (QString qcodigo) {
    QString cadquery = "SELECT codigo from nuevoplan where char_length(codigo) > ";

    if ( cualControlador() == SQLITE ) {
        cadquery += "length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "') and substr(codigo,1,length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'))='";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'";
    }
    else {
        cadquery += "char_length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "') and substring(codigo from 1 for char_length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'))='";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'";
    }
    
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return 0;
    }
    return 1;
}

// Si el código posee subcódigos que no sean subcuentas en la tabla
bool basedatos::codsubdivis (QString qcodigo,QString tabla) {
    // evaluamos si el código suministrado posee subcódigos 
    // que no sean subcuentas
    QString cadquery = "SELECT codigo from "+ tabla +" where ";

    if (cualControlador() == SQLITE) {
        cadquery += "length(codigo) > ";
        cadquery += "length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "') and substr(codigo,1,length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'))='";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'";
    }
    else {
        cadquery += "char_length(codigo) > ";
        cadquery += "char_length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "') and substring(codigo from 1 for char_length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'))='";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'";
    }

    if (!cod_longitud_variable())
    {
        if ( cualControlador() == SQLITE ) {
            cadquery += " and length(codigo)<";
            cadquery += QString::number( anchocuentas() );
        }
        else {
            cadquery += " and char_length(codigo)<";
            QString cadnum;
            cadnum.setNum(anchocuentas());
            cadquery += cadnum;
        }
    }
    else {
        cadquery += " and not auxiliar";
    }

    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}

//
int basedatos::asientomodeloencab_as_modelo (QString titulo) {
    // buscamos el nombre
    QString cadena = "select asientomodelo from cab_as_modelo where asientomodelo='";
    cadena += titulo.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return 2;
    }
    return 0;
}

// Indica si existe un código en el diario y devuelve su descripción
int basedatos::existecodigodiario(QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descripcion from diarios where codigo = '";
    cadquery += cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip=query.value(1).toString();
        return 1;
    }
    return 0;
}

// Borra un diario si no tiene asientos
int basedatos::eliminadiario(QString qcodigo) {
    QString cadquery = "SELECT diario from diario where diario = '";
    cadquery += qcodigo.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return 0;
    }

    cadquery = "delete from diarios where codigo = '";
    cadquery += qcodigo.left(-1).replace("'","''") +"'";
    ejecutar(cadquery);
    return 1;
}

// Indica si existe cuenta con presupuesto en el ejercicio
bool basedatos::existectapres(QString cuenta,QString ejercicio) {
    QString cadquery;
    cadquery="SELECT cuenta from presupuesto where cuenta = '";
    cadquery+=cuenta.left(-1).replace("'","''");
    cadquery+="' and ejercicio='";
    cadquery+=ejercicio.left(-1).replace("'","''");
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return 1;
    }
    return 0;
}

// Indica si la empresa tiene prorrata especial
bool basedatos::prorrata_especial() {
    QSqlQuery query = ejecutar("select prorrata_especial from configuracion");
    if ( (query.isActive()) && (query.first()) )
	    return query.value(0).toBool();
    return false;
}

// Indica la prorrata iva de la empresa
double basedatos::prorrata_iva() {
    QSqlQuery query = ejecutar("select prorrata_iva from configuracion");
    if ( (query.isActive()) && (query.first()) )
    	return query.value(0).toDouble();
    return false;
}

// Indica si hay cuenta_activo en planamortizaciones
bool basedatos::cuentaenplanamort(QString codigo) {
    QString cadena="select cuenta_activo from planamortizaciones where cuenta_activo='";
    cadena+=codigo.left(-1).replace("'","''");
    cadena+="'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) &&  (query.first()) )
    {
        return query.value(0).toBool();
    }
    return false;
}

// Indica si existe codigo en dif_conciliacion para una cuenta
bool basedatos::existecodigodif_conc(QString cadena) {
    QString cadquery = "SELECT codigo from dif_conciliacion where codigo = '";
    cadquery += cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}

// Indica si una cuenta está en el presupuesto
bool basedatos::cuentaenpresupuesto(QString cadena) {
    QString cadquery = "SELECT cuenta from presupuesto where cuenta = '";
    cadquery += cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}

// Indica si una cuenta está en ajustes_conci
bool basedatos::cuentaen_ajustes_conci(QString cadena) {
    QString cadquery = "SELECT cuenta from ajustes_conci where cuenta = '";
    cadquery += cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}

// Indica si una cuenta está en cuenta_ext_conci
bool basedatos::cuentaencuenta_ext_conci(QString cadena) {
    QString cadquery = "SELECT cuenta from cuenta_ext_conci where cuenta = '";
    cadquery += cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}

// Devuelve la cantidad de pases de un diario en una fecha que sea de cierre
int basedatos::registroscierrediarioenfechas (QString cadinicioej, QString cadfinej) {
    QString cadena = "select count(pase) from diario where fecha>='";
    cadena += cadinicioej;
    cadena += "' and fecha<='";
    cadena += cadfinej;
    cadena += "' and diario='";
    cadena += diario_cierre();
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

// para bloquear las tablas para asiento apertura
void basedatos::bloquea_para_asientoapertura()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE configuracion write, diario write, saldossubcuenta write,"
              " ejercicios write");
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
      ejecutar("LOCK TABLE ejercicios IN ACCESS EXCLUSIVE MODE");
    }
}

// para bloquear las tablas para asiento de cierre
void basedatos::bloquea_para_asientocierre()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE configuracion write, diario write, saldossubcuenta write,"
              "plancontable write, ejercicios write");
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
      ejecutar("LOCK TABLE ejercicios IN ACCESS EXCLUSIVE MODE");
    }
}

void basedatos::bloquea_para_genamort()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE configuracion write, diario write, saldossubcuenta write,"
              "planamortizaciones write, amortfiscalycontable write, ci_amort write,"
              "ejercicios write, amortcontable write, input_cta write, diario_ci write, "
              "amoinv write");
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
      ejecutar("LOCK TABLE ejercicios IN ACCESS EXCLUSIVE MODE");
    }
}

// para bloquear las tablas para asignavencimientos
void basedatos::bloquea_para_asignavencimientos()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE configuracion write, diario write, vencimientos write");
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
    }
}

// bloquea las tablas para listavenci
void basedatos::bloquea_para_listavenci()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE configuracion write, diario write, vencimientos write, "
              "plancontable write, saldossubcuenta write, ejercicios write");
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
      ejecutar("LOCK TABLE ejercicios IN ACCESS EXCLUSIVE MODE");
    }
}

// bloquea las tablas para nuevo_vencimiento
void basedatos::bloquea_para_nuevo_vencimiento()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE configuracion write, vencimientos write,"
              "plancontable write, datossubcuenta write");
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
    }
}

// bloquea las tablas para importar asientos
void basedatos::bloquea_para_importar_asientos()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE configuracion write, diario write, "
              "saldossubcuenta write, ejercicios write");
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
      ejecutar("LOCK TABLE ejercicios IN ACCESS EXCLUSIVE MODE");
    }
}

// bloquea las tablas para procesavencimientos
void basedatos::bloquea_para_procesavencimiento()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE configuracion write, diario write, saldossubcuenta write, "
              "vencimientos write, ejercicios write");
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
      ejecutar("LOCK TABLE ejercicios IN ACCESS EXCLUSIVE MODE");
    }
}

// bloquea las tablas para regularización
void basedatos::bloquea_para_regularizacion()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE configuracion write, diario write, saldossubcuenta write, plancontable write,"
               "ejercicios write;");
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
      ejecutar("LOCK TABLE ejercicios IN ACCESS EXCLUSIVE MODE");
    }
}

// bloquea las tablas para regularización
void basedatos::bloquea_para_regularizacion2()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE configuracion write, diario write, saldossubcuenta write, plancontable write,"
                "regul_sec write, ejercicios write, datossubcuenta write, bloqueos_cta write, periodos write;" );
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
      ejecutar("LOCK TABLE ejercicios IN ACCESS EXCLUSIVE MODE");
    }
}


// bloquea las tablas para tabla asientos
void basedatos::bloquea_para_tabla_asientos()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE configuracion write, diario write, saldossubcuenta write, "
              "libroiva write, retenciones write, venci_cta write, "
               "ejercicios write, datossubcuenta write, diario_ci write");
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
      ejecutar("LOCK TABLE ejercicios IN ACCESS EXCLUSIVE MODE");
    }
}

// bloquea las tablas para borrador
void basedatos::bloquea_para_borrador()
{
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE borrador write");
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE borrador IN ACCESS EXCLUSIVE MODE");
    }
}

// efectúa commit y desbloquea tablas en mysql
void basedatos::desbloquea_y_commit()
{
  QSqlDatabase::database().commit();
 if (cualControlador() == MYSQL)
    {
     ejecutar("unlock tables");
     ejecutar("SET AUTOCOMMIT=1");
    }
}

// Indica cuál es el próximo asiento
qlonglong basedatos::proximoasiento (QString ejercicio) {
    QString cadena="select prox_asiento, primero_apertura from ejercicios where codigo='";
    cadena+=ejercicio.left(-1).replace("'","''");
    cadena+="'";
    QSqlQuery query = ejecutar(cadena);
    qlonglong vnum = 0;
    if ( (query.isActive()) && (query.first()) )
    {
        vnum = query.value(0).toLongLong();
        if (vnum == 0)
            vnum = 1;
    }
    else vnum = 1;

    if (query.value(1).toBool() && vnum==1) vnum=2;

    return vnum;
}


qlonglong basedatos::proximo_nrecepcion (QString ejercicio) {
    QString cadena="select prox_nrecepcion from ejercicios where codigo='";
    cadena+=ejercicio.left(-1).replace("'","''");
    cadena+="'";
    QSqlQuery query = ejecutar(cadena);
    qlonglong vnum = 0;
    if ( (query.isActive()) && (query.first()) )
    {
        vnum = query.value(0).toLongLong();
        if (vnum == 0)
            vnum = 1;
    }
    else vnum = 1;

    return vnum;
}


// Indica cuál es el próximo vencimiento
qlonglong basedatos::proximovencimiento () {
    QSqlQuery query = ejecutar("select prox_vencimiento from configuracion;");
    qlonglong vnum = 0;
    if ( (query.isActive()) && (query.first()) )
    {
        vnum = query.value(0).toLongLong();
        if (vnum == 0)
            vnum = 1;
    }
    else vnum = 1;

    return vnum;
}

// Filtro para bss
QString basedatos::filtrobss (bool grupos, bool subgrupos, bool cuentas3dig, bool cuentas, bool subcuentas,
                              bool cuentasgroup, QString desde, QString hasta) {
    bool vacio=true;
    QString cadnum, filtro = "(";
    if (grupos)
    {
        if (cualControlador() == SQLITE) { filtro += "length(codigo)=1"; }
        else { filtro += "char_length(codigo)=1"; }
        vacio = false;
    }
    if (subgrupos) 
    {
        if (filtro.length()>1) filtro+=" OR ";
        if (cualControlador() == SQLITE) { filtro += "length(codigo)=2"; }
        else { filtro += "char_length(codigo)=2"; }
        vacio = false;
    }
    if (cuentas3dig)
    {
        if (filtro.length()>1) filtro+=" OR ";
        if (cualControlador() == SQLITE) { filtro += "length(codigo)=3"; }
        else { filtro += "char_length(codigo)=3"; }
        vacio = false;
    }
    if (cuentas)
    {
        if (filtro.length()>1) filtro+=" OR ";
        if (!cod_longitud_variable())
        {
            if (cualControlador() == SQLITE) { filtro += "(length(codigo)>2 and length(codigo)<"; }
            else { filtro += "(char_length(codigo)>2 and char_length(codigo)<"; }
        filtro += cadnum.setNum(uint(anchocuentas()));
        filtro += ")";
        }
        else
        {
            if (cualControlador() == SQLITE) { filtro += "(length(codigo)>2 and auxiliar=0) "; }
            else { filtro += "(char_length(codigo)>2 and not auxiliar) "; }
        }
        vacio = false;
    }
    if (subcuentas) 
    {
        if (filtro.length()>1) filtro += " OR ";
        if (!cod_longitud_variable())
        {
            if (cualControlador() == SQLITE) { filtro += "length(codigo)="; }
            else { filtro += "char_length(codigo)="; }
            filtro += cadnum.setNum(uint(anchocuentas()));
        }
        else filtro += "auxiliar";
        vacio = false;
    }
   
    filtro += ")";

    if (vacio)
    {
        filtro.clear();
    }

    // añadimos en filtro intervalo de cuentas
    if (cuentasgroup)
    {
        filtro+=" AND (codigo>='";
        filtro+=desde;
        filtro+="' and codigo<='";
        filtro+=hasta;
        filtro+="')";
    }
    return filtro;
}

// 
QString basedatos::sumadh (bool subcuenta, bool debe, QString codigo, QDate desde,
                           QDate hasta, QString filtro, bool analitica,
                           QString ci, bool comadecimal, bool decimales) {
    QString cadena;
    if (debe) cadena = "select sum(debe) from diario" ;
    else cadena = "select sum(haber) from diario" ;

    if (subcuenta)
    {
        cadena += " where cuenta='";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "'";
    }
    else
    {
        if (cualControlador() == SQLITE) {
            cadena += " where cuenta like '"+ codigo.left(-1).replace("'","''") +"%'";
        }
        else {
    	    cadena += " where position('";
    	    cadena += codigo.left(-1).replace("'","''");
    	    cadena += "' in cuenta)=1";
        }
    }

    cadena+=" and (fecha>='";
    cadena+=desde.toString("yyyy-MM-dd");
    cadena+="' and fecha<='";
    cadena+=hasta.toString("yyyy-MM-dd");
    cadena+="')";

    cadena += " and (";
    cadena += filtro;
    cadena += ")";

    if (analitica && ci.length()>0)
    {
        cadena += " and ";
        cadena += filtroci(ci,false);
    }

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        double valor = query.value(0).toDouble();
        return formateanumero(valor,comadecimal,decimales);
    }

    return "0";
}


// Devuelve datos de cuenta debe y haber para una cuenta en unas fechas con analitica-tabla
QString basedatos::sumadh_analitica_tabla (bool debe, QString codigo,
                                             QDate fechaini,
                                             QDate fechafin, QString ci, QString filtro,
                                             bool comadecimal, bool decimales) {

    QString cadena;
    if (debe) cadena = "select sum(diario.debe/abs(diario.debe+diario.haber)*diario_ci.importe) ";
      else cadena = "select sum(diario.haber/abs(diario.debe+diario.haber)*diario_ci.importe) " ;
    cadena+= " from diario, diario_ci where ";
    cadena+= "diario_ci.clave_ci = diario.clave_ci and ";

    if (cualControlador() == SQLITE) {
        cadena += "diario.cuenta like '"+ codigo.left(-1).replace("'","''") +"%'";
    }
    else {
        cadena += "position('";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "' in diario.cuenta)=1";
    }
    cadena += " and fecha>='";
    cadena += fechaini.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += fechafin.toString("yyyy-MM-dd");
    cadena += "'";

    cadena +=" and (";
    cadena += filtro;
    cadena += ")";
;

    if (ci.length() > 0)
    {
        cadena += " and ";
        cadena += filtroci(ci,true);
    }

    cadena+=" group by cuenta";

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        double valor = query.value(0).toDouble();
        return formateanumero(valor,comadecimal,decimales);
    }

    return "0";
}





// 
bool basedatos::calcestadohaymedias (QString titulo) {
    QString cadmedia = ":";
    cadmedia += cadenamedia();
    QString cadena = "select formula from estados where titulo='";
    cadena += titulo.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( query.isActive() ) {
        while ( query.next() ) 
        {
	        if (query.value(0).toString().contains(cadmedia) > 0) return true;
        }
    }
    return false;
}

// Devuelve la cantidad de códigos en plancontable
int basedatos::cantidadplancontable(QString cadanchoctas) {
    QString cadena="select count(codigo) from plancontable where " ;
    if (!cod_longitud_variable())
    {
        if (cualControlador() == SQLITE) {
            cadena += "length(codigo)=";
        }
        else {
            cadena += "char_length(codigo)=";
        }
        cadena += cadanchoctas;
    }
    else {
        cadena += "auxiliar";
    }

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

// Devuelve la cantidad de códigos en plancontable con filtro
int basedatos::cantidadplancontablefiltro(QString cadanchoctas, QString filtro) {
    QString cadena="select count(codigo) from plancontable where " ;
    if (!cod_longitud_variable())
    {
        if (cualControlador() == SQLITE) {
            cadena += "length(codigo)=";
        }
        else {
            cadena += "char_length(codigo)=";
        }
        cadena += cadanchoctas;
    }
    else {
        cadena += "auxiliar";
    }
    cadena += " "+ filtro;

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

// 
bool basedatos::siCuentaenpresupuestoejercicio (QString ejercicio, bool subcuentas) {
    QString cadena = "select cuenta from presupuesto where ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "' ";
    QString cadnum;
    if (!cod_longitud_variable())
    {
        if (cualControlador() == SQLITE) {
            if (subcuentas) cadena += "and length(cuenta)=";
            else cadena += "and length(cuenta)<";
        }
        else {
            if (subcuentas) cadena += "and char_length(cuenta)=";
            else cadena += "and char_length(cuenta)<";
        }
        cadnum.setNum(anchocuentas());
        cadena += cadnum;
    }
    else
        if (subcuentas) cadena += "and cuenta in (select codigo from plancontable where auxiliar)";
        else cadena += "and cuenta in (select codigo from plancontable where not auxiliar)";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
        return true;
    return false;
}





// actualizade20 no va a ser usado nunca con qsqlite
void basedatos::actualizade20 ()
{
    QMessageBox::information( 0 , QObject::tr("Apertura de base de datos"),
	QObject::tr("Se van a actualizar las tablas para la versión 2.1"));

    QSqlQuery query;

    ejecutar("CREATE TABLE diarios ("
             "codigo       varchar(40),"
             "descripcion  varchar(255) )");

    ejecutar("update configuracion set version='2.1'");

    ejecutar("alter table cabeceraestados drop column apertura, "
             "drop column general, drop column regularizacion");

    ejecutar("alter table cabeceraestados add column diarios varchar(255)");

    ejecutar("alter table var_as_modelo add column orden int");
    ejecutar("update var_as_modelo set orden=0");

    ejecutar("alter table det_as_modelo add column orden int");
    ejecutar("update det_as_modelo set orden=0");

    ejecutar("alter table configuracion add column id_registral varchar(255)");
    ejecutar("update configuracion set id_registral=''");

    ejecutar("alter table datossubcuenta add column ccc varchar(255), "
             "add column cuota varchar(40)");

    ejecutar("alter table ejercicios add column base_ejercicio varchar(80) default '',"
             "add column base_presupuesto varchar(80) default '',"
             "add column presupuesto_base_cero bool default true,"
             "add column incremento numeric(14,4) default 0");

    ejecutar("alter table vencimientos add column concepto varchar(255) default ''");

    ejecutar("CREATE TABLE presupuesto ("
             "ejercicio         varchar(40),"
             "cuenta            varchar(40),"
             "ejerciciobase     varchar(40) default '',"
             "presupuestobase   varchar(40) default '',"
             "base              numeric(14,2) default 0,"
             "base1             numeric(14,2) default 0,"
             "base2             numeric(14,2) default 0,"
             "base3             numeric(14,2) default 0,"
             "base4             numeric(14,2) default 0,"
             "base5             numeric(14,2) default 0,"
             "base6             numeric(14,2) default 0,"
             "base7             numeric(14,2) default 0,"
             "base8             numeric(14,2) default 0,"
             "base9             numeric(14,2) default 0,"
             "base10            numeric(14,2) default 0,"
             "base11            numeric(14,2) default 0,"
             "base12            numeric(14,2) default 0,"
             "base13            numeric(14,2) default 0,"
             "base14            numeric(14,2) default 0,"
             "base15            numeric(14,2) default 0,"
             "presupuesto       numeric(14,2) default 0,"
             "presupuesto1      numeric(14,2) default 0,"
             "presupuesto2      numeric(14,2) default 0,"
             "presupuesto3      numeric(14,2) default 0,"
             "presupuesto4      numeric(14,2) default 0,"
             "presupuesto5      numeric(14,2) default 0,"
             "presupuesto6      numeric(14,2) default 0,"
             "presupuesto7      numeric(14,2) default 0,"
             "presupuesto8      numeric(14,2) default 0,"
             "presupuesto9      numeric(14,2) default 0,"
             "presupuesto10      numeric(14,2) default 0,"
             "presupuesto11      numeric(14,2) default 0,"
             "presupuesto12      numeric(14,2) default 0,"
             "presupuesto13      numeric(14,2) default 0,"
             "presupuesto14      numeric(14,2) default 0,"
             "presupuesto15      numeric(14,2) default 0)");

    ejecutar("CREATE TABLE periodos ("
             "ejercicio  varchar(40),"
             "codigo     varchar(40),"
             "inicio     date,"
             "fin        date )");

}


// actualizade21 no va a ser usado nunca con qsqlite
void basedatos::actualizade21 ()
{
  // QSqlDatabase bd=QSqlDatabase::database ();
  // QString acontrolador= bd.driverName();
  // bool cmysql=(acontrolador.contains("MYSQL")) ;

    QMessageBox::information( 0 , QObject::tr("Apertura de base de datos"),
    QObject::tr("Se van a actualizar las tablas para la versión 2.2"));


    QSqlQuery query;
    ejecutar("update configuracion set version='2.2'");

    ejecutar("alter table plancontable add column auxiliar bool default false");

    ejecutar("alter table cabeceraestados add column grafico bool");

    ejecutar("alter table configuracion add column prorrata_iva numeric(5,2) default 0");

    ejecutar("alter table configuracion add column prorrata_especial bool default false");

    ejecutar("alter table nuevoplan add column auxiliar bool default false");

    ejecutar("alter table libroiva add column prorrata numeric(5,2) default 1");

    ejecutar("alter table libroiva add column rectificativa bool default false,"
             " add column autofactura bool default false;");

    ejecutar("alter table borrador add column prorrata varchar(40) default ''");
    ejecutar("alter table borrador add column rectificativa varchar(40) default '', "
             "add column autofactura varchar(40) default ''");

    ejecutar("alter table diario add column conciliado bool default false, "
             "add column dif_conciliacion varchar(40) default ''");

    ejecutar("CREATE TABLE dif_conciliacion ("
             "codigo        varchar(40),"
             "descripcion   varchar(80) default '',"
             "PRIMARY KEY (codigo) )");

    ejecutar("CREATE TABLE ci_amort ("
             "cuenta        varchar(40),"
             "ci            varchar(40),"
             "asignacion    float8,"
             "PRIMARY KEY (cuenta, ci) )");

    ejecutar("create table ajustes_conci ("
             "cuenta   varchar(40),"
             "fecha    date,"
             "debe     numeric(14,2),"
             "haber    numeric(14,2),"
             "concepto varchar(255),"
             "dif_conciliacion varchar(40) default '',"
             "conciliado bool default false)");

    ejecutar("create table cuenta_ext_conci ("
             "numero   serial,"
             "cuenta   varchar(40),"
             "fecha    date,"
             "debe     numeric(14,2),"
             "haber    numeric(14,2),"
             "saldo    numeric(14,2),"
             "concepto varchar(255),"
             "dif_conciliacion varchar(40) default '',"
             "conciliado bool default false,"
             "PRIMARY KEY (numero) )");

    if ( cualControlador() == MYSQL)
    {
        ejecutar("alter table estados change titulo titulo varchar(166)");
        ejecutar("alter table estados change nodo nodo varchar(166)");
        ejecutar("alter table var_as_modelo change asientomodelo asientomodelo varchar(166)");
        ejecutar("alter table var_as_modelo change variable variable varchar(166)");
    }
    else
    {
        ejecutar("alter table estados alter column titulo type varchar(166)");
        ejecutar("alter table estados alter column nodo type varchar(166)");
        ejecutar("alter table var_as_modelo alter column asientomodelo type varchar(166)");
        ejecutar("alter table var_as_modelo alter column variable type varchar(166)");
    }

    ejecutar("alter table amortcontable add primary key (ejercicio)");
    ejecutar("alter table amortfiscalycontable add primary key (ejercicio, cuenta_activo)");
    ejecutar("alter table amortpers add primary key (cuenta, ejercicio)");
    ejecutar("alter table cab_as_modelo add primary key (asientomodelo)");
    ejecutar("alter table cabeceraestados add primary key (titulo)");
    ejecutar("alter table ci add primary key (codigo, nivel)");
    ejecutar("alter table conceptos add primary key (clave)");
    ejecutar("alter table datossubcuenta add primary key (cuenta)");
    ejecutar("alter table diario add primary key (pase)");
    ejecutar("alter table diarios add primary key (codigo)");
    ejecutar("alter table ejercicios add primary key (codigo)");
    ejecutar("alter table equivalenciasplan add primary key (cod)");
    ejecutar("alter table estados add primary key (titulo, nodo, parte1)");
    ejecutar("alter table libroiva add primary key (pase)");
    ejecutar("alter table nuevoplan add primary key (codigo)");
    ejecutar("alter table periodos add primary key (ejercicio, codigo)");
    ejecutar("alter table planamortizaciones add primary key (cuenta_activo)");
    ejecutar("alter table plancontable add primary key (codigo)");
    ejecutar("alter table presupuesto add primary key (ejercicio, cuenta)");
    ejecutar("alter table saldossubcuenta add primary key (codigo)");
    ejecutar("alter table tiposiva add primary key (clave)");
    ejecutar("alter table var_as_modelo add primary key (asientomodelo, variable)");
    ejecutar("alter table vencimientos add primary key (num)");

    QMessageBox::information( 0 , QObject::tr("Actualización de base de datos"),
	QObject::tr("La operación se ha realizado"));

}

// actualizade22 no va a ser usado nunca con qsqlite
void basedatos::actualizade22 ()
{
 // actualiza para versión 2.2.1
 ejecutar("alter table var_as_modelo add column valor varchar(160) default ''");

 ejecutar("alter table det_as_modelo add column fecha_factura varchar(255) default ''");

 ejecutar("update configuracion set version='2.2.1'");

}

void basedatos::actualizade221 ()
{
 // actualiza para versión 2.3
 if (cualControlador()== MYSQL)
    {
     ejecutar("alter table datossubcuenta add column imagen text");
     ejecutar("update datossubcuenta set imagen=''");
    }
    else
       ejecutar("alter table datossubcuenta add column imagen text default ''");
 ejecutar("alter table configuracion add column fecha_constitucion date");
 ejecutar("alter table configuracion add column objeto varchar(255) default ''");
 ejecutar("alter table configuracion add column actividad varchar(255) default ''");
    if ( cualControlador() == SQLITE ) {
       ejecutar("alter table var_as_modelo add column guardar bool default 0;");
    }
    else {
        ejecutar("alter table var_as_modelo add column guardar bool default false;");
    }

    // cab_regulariz
 QString cadena = "CREATE TABLE cab_regulariz ("
             "codigo        varchar(40),"
             "descripcion   varchar(255),"
             "PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

    // regul_sec
    cadena = "CREATE TABLE regul_sec ("
             "codigo            varchar(40),"
             "orden             int,"
             "descripcion       varchar(255),"
             "cuenta_deudora    varchar(40),"
             "cuenta_acreedora  varchar(40),"
             "cuentas           varchar(40),"
             "condicion         varchar(255),"
             "PRIMARY KEY (codigo,orden) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

 ejecutar("update configuracion set version='2.3'");

 // lo que sigue sirve para implementar claves foráneas; no procede para SQLITE
 if ( cualControlador() == SQLITE || cualControlador() == MYSQL) return;

 ejecutar("alter table amortcontable add foreign key(ejercicio) references ejercicios(codigo)");
 // ejecutar("alter table amortcontable add foreign key(asiento) references diario(asiento)");
 ejecutar("create index ac_ejercicio_idx on amortcontable(ejercicio)");
 // ejecutar("create index ac_asiento_idx on amortcontable(asiento)");

 ejecutar("alter table amortfiscalycontable add foreign key(ejercicio) references ejercicios(codigo)");
 ejecutar("alter table amortfiscalycontable add foreign key(cuenta_activo) references plancontable(codigo)");
 ejecutar("alter table amortfiscalycontable add foreign key(cuenta_am_acum) references plancontable(codigo)");
 ejecutar("alter table amortfiscalycontable add foreign key(cuenta_gasto) references plancontable(codigo)");
 ejecutar("create index afc_ejercicio_idx on amortfiscalycontable(ejercicio)");
 ejecutar("create index afc_cuenta_activo_idx on amortfiscalycontable(cuenta_activo)");
 ejecutar("create index afc_cuenta_am_acum_idx on amortfiscalycontable(cuenta_am_acum)");
 ejecutar("create index afc_cuenta_gasto_idx on amortfiscalycontable(cuenta_gasto)");

// suponemos que esta tabla va a contener pocos registros, por lo que no indexamos el campo cuenta 
ejecutar("alter table borrador add foreign key(cuenta) references plancontable(codigo)");

// cuenta es clave primaria, por lo que ya está indexada
ejecutar("alter table datossubcuenta add foreign key(cuenta) references plancontable(codigo)");

// esta tabla tiene relativamente pocas filas, por lo que no indexamos
ejecutar("alter table det_as_modelo add foreign key(asientomodelo) references cab_as_modelo(asientomodelo)");

// esta tabla tiene relativamente pocas filas, por lo que no indexamos
ejecutar("alter table var_as_modelo add foreign key(asientomodelo) references cab_as_modelo(asientomodelo)");

// el índice cuenta ya se ha creado
ejecutar("alter table diario add foreign key(cuenta) references plancontable(codigo)");

// tabla con relativamente pocas filas; no indexamos
ejecutar("alter table estados add foreign key(titulo) references cabeceraestados(titulo)");

// pase forma parte la clave primaria, por lo que hay índice implícito
ejecutar("alter table libroiva add foreign key(pase) references diario(pase)");

// cuenta_activo forma parte la clave primaria, por lo que hay índice implícito
ejecutar("alter table planamortizaciones add foreign key(cuenta_activo) references plancontable(codigo)");
ejecutar("alter table planamortizaciones add foreign key(cuenta_gasto) references plancontable(codigo)");
ejecutar("create index plam_cuenta_gasto_idx on planamortizaciones(cuenta_gasto)");

// tabla con relativamente pocas filas; no indexamos
ejecutar("alter table amortpers add foreign key(cuenta) references plancontable(codigo)");
ejecutar("alter table amortpers add foreign key(ejercicio) references ejercicios(codigo)");

// codigo es clave primaria, por lo que hay índice implícito
ejecutar("alter table saldossubcuenta add foreign key(codigo) references plancontable(codigo)");

// pase_diario_vencimiento puede ser nulo
ejecutar("alter table vencimientos add foreign key(cta_ordenante) references plancontable(codigo)");
// ejecutar("alter table vencimientos add foreign key(cuenta_tesoreria) references plancontable(codigo)");
ejecutar("alter table vencimientos add foreign key(pase_diario_operacion) references diario(pase)");
ejecutar("create index vtos_cta_ordenante_idx on vencimientos(cta_ordenante)");
ejecutar("create index vtos_cta_tesoreria_idx on vencimientos(cuenta_tesoreria)");
ejecutar("create index vtos_cta_pase_operacion_idx on vencimientos(pase_diario_operacion)");

// en presupuesto, ejerciciobase y presupuestobase pueden no tener contenido
ejecutar("alter table presupuesto add foreign key(ejercicio) references ejercicios(codigo)");
ejecutar("alter table presupuesto add foreign key(cuenta) references plancontable(codigo)");
ejecutar("create index pres_ejercicio_idx on presupuesto(ejercicio)");
ejecutar("create index pres_cuenta_idx on presupuesto(cuenta)");


// esta tabla va a contener relativamente pocos registros (conciliación a mano)
// no vamos a indexar por ningún campo para clave foránea
ejecutar("alter table ajustes_conci add foreign key(cuenta) references plancontable(codigo)");

ejecutar("alter table cuenta_ext_conci add foreign key(cuenta) references plancontable(codigo)");
ejecutar("create index exconc_cuenta_idx on cuenta_ext_conci(cuenta)");

// tabla con pocos registros, no vamos a crear índice
ejecutar("alter table regul_sec add foreign key(codigo) references cab_regulariz(codigo)");

}


void basedatos::actualizade23 ()
{
 // actualiza para 2.4
 if ( cualControlador() == SQLITE )
    ejecutar("alter table configuracion add column analitica_parc bool default 0;");
   else ejecutar("alter table configuracion add column analitica_parc bool default false;");
 ejecutar("alter table configuracion add column ctas_analitica text;");
 ejecutar("alter table cabeceraestados add column horacalculo varchar(10);");

 if ( cualControlador() == SQLITE)
    {
     ejecutar("update cabeceraestados set fechacalculo=substr(fechacalculo,1,4)||'-'||substr(fechacalculo,6,2)||'-'||substr(fechacalculo,9,2);");
     ejecutar("update configuracion set fecha_constitucion=substr(fecha_constitucion,1,4)||'-'||substr(fecha_constitucion,6,2)||'-'||substr(fecha_constitucion,9,2);");
     ejecutar("update diario set fecha=substr(fecha,1,4)||'-'||substr(fecha,6,2)||'-'||substr(fecha,9,2);");
     ejecutar("update ejercicios set apertura=substr(apertura,1,4)||'-'||substr(apertura,6,2)||'-'||substr(apertura,9,2);");
     ejecutar("update ejercicios set cierre=substr(cierre,1,4)||'-'||substr(cierre,6,2)||'-'||substr(cierre,9,2);");
     ejecutar("update libroiva set fecha_fra=substr(fecha_fra,1,4)||'-'||substr(fecha_fra,6,2)||'-'||substr(fecha_fra,9,2);");
     ejecutar("update planamortizaciones set fecha_func=substr(fecha_func,1,4)||'-'||substr(fecha_func,6,2)||'-'||substr(fecha_func,9,2);");
     ejecutar("update vencimientos set fecha_operacion=substr(fecha_operacion,1,4)||'-'||substr(fecha_operacion,6,2)||'-'||substr(fecha_operacion,9,2);");
     ejecutar("update vencimientos set fecha_vencimiento=substr(fecha_vencimiento,1,4)||'-'||substr(fecha_vencimiento,6,2)||'-'||substr(fecha_vencimiento,9,2);");
     ejecutar("update periodos set inicio=substr(inicio,1,4)||'-'||substr(inicio,6,2)||'-'||substr(inicio,9,2);");
     ejecutar("update periodos set fin=substr(fin,1,4)||'-'||substr(fin,6,2)||'-'||substr(fin,9,2);");
     ejecutar("update ajustes_conci set fecha=substr(fecha,1,4)||'-'||substr(fecha,6,2)||'-'||substr(fecha,9,2);");
     ejecutar("update cuenta_ext_conci set fecha=substr(fecha,1,4)||'-'||substr(fecha,6,2)||'-'||substr(fecha,9,2);");
    }

    // tiposvenci
    QString cadena = "CREATE TABLE tiposvenci ("
             "codigo            varchar(40),"
             "descripcion       varchar(255),"
             "PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

// -----------------------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------------------

if ( cualControlador() == SQLITE )
   ejecutar("alter table datossubcuenta  add column venciasoc bool default 0;");
  else
    ejecutar("alter table datossubcuenta add column venciasoc bool default false;");

ejecutar("alter table datossubcuenta add column codvenci varchar(40) default '';");
ejecutar("alter table datossubcuenta add column tesoreria varchar(40) default '';");

if ( cualControlador() == SQLITE )
   ejecutar("alter table datossubcuenta add column ivaasoc bool default 0;");
  else
    ejecutar("alter table datossubcuenta add column ivaasoc bool default false;");

ejecutar("alter table datossubcuenta add column cuentaiva varchar(40) default '';");
ejecutar("alter table datossubcuenta add column tipoiva varchar(40) default '';");
ejecutar("alter table datossubcuenta add column conceptodiario varchar(155) default '';");


if ( cualControlador() == SQLITE )
   ejecutar("alter table periodos add column deshabilitado bool default 0;");
  else
    ejecutar("alter table periodos add column deshabilitado bool default false;");

// bloqueos_cta
cadena = "CREATE TABLE bloqueos_cta ("
         "codigo        varchar(80),"
         "PRIMARY KEY (codigo) )";
cadena = anadirMotor(cadena);
ejecutar(cadena);

ejecutar("update configuracion set version='2.4'");

// -----------------------------------------------------------------------------------------------------

}


void basedatos::actualizade24 ()
{
 // actualiza para 2.5
if ( cualControlador() != SQLITE )
   ejecutar("alter table datossubcuenta drop column nifcomunitario;");
 ejecutar("alter table datossubcuenta add column nifrprlegal varchar(80);");
 ejecutar("alter table datossubcuenta add column web varchar(80);");
 ejecutar("alter table datossubcuenta add column claveidfiscal varchar(80);");

 ejecutar("alter table libroiva add column fecha_operacion date;");
 ejecutar("alter table libroiva add column clave_operacion varchar(40);");
 ejecutar("alter table libroiva add column bi_costo numeric(14,2);");

 ejecutar("alter table libroiva add column rectificada varchar(80) default '';");
 ejecutar("alter table libroiva add column nfacturas int default 0;");
 ejecutar("alter table libroiva add column finicial varchar(80) default '';");
 ejecutar("alter table libroiva add column ffinal varchar(80) default '';");

 ejecutar("update libroiva set fecha_operacion=fecha_fra");

     // codigopais
    QString cadena = "CREATE TABLE codigopais ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

    // claveidpais
    cadena = "CREATE TABLE claveidfiscal ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

    // clave_op_expedidas
    cadena = "CREATE TABLE clave_op_expedidas ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

    // clave_op_recibidas
    cadena = "CREATE TABLE clave_op_recibidas ("
             "codigo         varchar(40),"
             "descripcion    varchar(254),"
             "PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

    intropaises();
    introclaveidfiscal();

    introclaveexpedidas();
    introclaverecibidas();

    // tabla borrador
    ejecutar("alter table borrador add column fecha_operacion varchar(40) default '';");
    ejecutar("alter table borrador add column clave_operacion varchar(40) default '';");
    ejecutar("alter table borrador add column biacoste varchar(40) default '';");

    ejecutar("alter table borrador add column prorrata_e varchar(255) default ''");

    ejecutar("alter table borrador add column fecha_op varchar(255) default ''");
    ejecutar("alter table borrador add column clave_op varchar(255) default ''");
    ejecutar("alter table borrador add column bicoste varchar(255) default ''");

    ejecutar("alter table det_as_modelo add column prorrata_e varchar(255) default ''");
    if ( cualControlador() == SQLITE )
        ejecutar("alter table det_as_modelo add column rectificativa bool default 0");
      else ejecutar("alter table det_as_modelo add column rectificativa bool default false");
    if ( cualControlador() == SQLITE )
        ejecutar("alter table det_as_modelo add column autofactura bool default 0");
      else ejecutar("alter table det_as_modelo add column autofactura bool default false");
    ejecutar("alter table det_as_modelo add column fecha_op varchar(255) default ''");
    ejecutar("alter table det_as_modelo add column clave_op varchar(255) default ''");
    ejecutar("alter table det_as_modelo add column bicoste varchar(255) default ''");

    ejecutar("alter table borrador add column rectificada     varchar(40) default ''");
    ejecutar("alter table borrador add column nfacturas       varchar(40) default ''");
    ejecutar("alter table borrador add column finicial        varchar(40) default ''");
    ejecutar("alter table borrador add column ffinal          varchar(40) default ''");

    ejecutar("update configuracion set version='2.5'");

}

void basedatos::actualizade25 ()
{
 // actualiza para 2.5.1

   if ( cualControlador() == SQLITE ) {
        ejecutar("alter table libroiva add column autofactura_no_ue        bool default 0;");
        ejecutar("alter table libroiva add column pr_servicios_ue          bool default 0;");
    }
    else {
        ejecutar("alter table libroiva add column autofactura_no_ue    bool default false;");
        ejecutar("alter table libroiva add column pr_servicios_ue      bool default false;");
    }

   ejecutar("alter table det_as_modelo add column rectificada  varchar(255) default '';");
   ejecutar("alter table det_as_modelo add column numfacts     varchar(255) default '';");
   ejecutar("alter table det_as_modelo add column facini       varchar(255) default '';");
   ejecutar("alter table det_as_modelo add column facfinal     varchar(255) default '';");

   ejecutar("update configuracion set version='2.5.1';");

}


void basedatos::actualizade251 ()
{
 // actualiza para 2.6
    ejecutar("alter table configuracion add column prox_documento bigint default 1");
    QString cadena="alter table configuracion add column ";
    if ( cualControlador() == SQLITE ) cadena += "gestion_usuarios bool default 0";
      else cadena += "gestion_usuarios bool default false";
    ejecutar(cadena);

    ejecutar("alter table diario add column copia_doc varchar(255) default ''");

    // crea tabla usuarios
    cadena = "CREATE TABLE usuarios ("
             "codigo         varchar(40) default '',"
             "nombre         varchar(254) default '',"
             "clave          varchar(40) default '',"
             "nif            varchar(40) default '',"
             "domicilio      varchar(80) default '',"
             "poblacion      varchar(80) default '',"
             "codigopostal   varchar(40) default '',"
             "provincia      varchar(80) default '',"
             "pais           varchar(80) default '',"
             "tfno           varchar(80) default '',"
             "email          varchar(80) default '',"
             "observaciones  varchar(255) default '',"
             "privilegios    varchar(255) default '',"
             "imagen         text,"
             "PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);


    cadena="insert into usuarios (codigo, nombre, clave, privilegios) "
           "values ('admin', 'Administrador', 'admin',"
           "'1111111111111111111111111111111111111111111111111111111')";
          // 1234567890123456789012345678901234567890123456789012345

    ejecutar(cadena);

    if ( cualControlador() == SQLITE )
        ejecutar ("alter table libroiva add column bien_inversion bool default 0");
    else
        ejecutar ("alter table libroiva add column bien_inversion bool default false");

    if ( cualControlador() == SQLITE )
        ejecutar ("alter table det_as_modelo add column bien_inversion bool default 0");
    else
        ejecutar ("alter table det_as_modelo add column bien_inversion bool default false");

    if ( cualControlador() == SQLITE )
        ejecutar ("alter table datossubcuenta add column procesavenci bool default 0"); // procesa vencimiento
       else ejecutar("alter table datossubcuenta add column procesavenci bool default false");

    if ( cualControlador() == SQLITE )
        ejecutar ("alter table cabeceraestados add column desglose_ctas bool default 0");
    else
        ejecutar ("alter table cabeceraestados add column desglose_ctas bool default false");


   ejecutar("alter table borrador add column pr_serv_ue      varchar(40) default ''");
   ejecutar("alter table borrador add column autofactura_noue varchar(40) default ''");
   ejecutar("alter table borrador add column bien_inversion   varchar(40) default ''");


   ejecutar("update configuracion set version='2.6'");

}

void basedatos::actualizade26 ()
{
 // actualiza para 2.7
    if ( cualControlador() == SQLITE )
        ejecutar("alter table libroiva add column op_no_sujeta bool default 0");
    else
        ejecutar("alter table libroiva add column op_no_sujeta bool default false");

    ejecutar("alter table libroiva add column afecto numeric(7,4) default 1");

    ejecutar("alter table borrador add column sujeto varchar(40) default ''");
    ejecutar("alter table borrador add column afecto varchar(40) default ''");
    ejecutar("alter table borrador add column fecha date");

    ejecutar("alter table det_as_modelo add column afecto varchar(255) default ''");

    if ( cualControlador() == SQLITE )
       ejecutar("alter table configuracion add column analitica_tabla bool default 0");
    else ejecutar("alter table configuracion add column analitica_tabla bool default false");


    // inputaciones automáticas para las cuentas auxiliares
    QString cadena = "CREATE TABLE input_cta (";
    cadena+="auxiliar    varchar(80),"
            "ci          varchar(40),"
          "imputacion  numeric(10,4),";
    if ( cualControlador() == SQLITE) {
    cadena += "registro integer primary key autoincrement";
     }
     else {
            cadena += "registro   serial";
          }
    if ( cualControlador() == SQLITE) cadena+=")";
       else cadena+=", primary key (registro))";

    ejecutar(cadena);

    cadena="alter table configuracion add column "
           "prox_num_ci bigint default 1";
    ejecutar(cadena);

    ejecutar("alter table diario add column clave_ci bigint default 0");


    cadena = "CREATE TABLE diario_ci ("
             "clave_ci       bigint,"
             "ci             varchar(80),"
             "importe        numeric(19,4),";
    if ( cualControlador() == SQLITE) {
    cadena += "registro integer primary key autoincrement";
    }
     else {
            cadena += "registro   serial";
          }
     if ( cualControlador() == SQLITE) cadena+=")";
        else cadena+=", primary key (registro))";
    cadena = anadirMotor(cadena);

    ejecutar(cadena);

    // NO es posible porque el campo clave_ci no tiene restricción a UNIQUE
    // en la  tabla diario
    /* if ( cualControlador() != SQLITE) {
       cadena="alter table diario_ci add foreign key(clave_ci) references diario(clave_ci)";
       ejecutar(cadena);
      } */
    cadena="create index clave_ci_idx on diario_ci(clave_ci)";
    ejecutar(cadena);

    cadena="alter table configuracion add column notas text";
    ejecutar(cadena);

    ejecutar("update configuracion set version='2.7'");

}

void basedatos::actualizade27 ()
{
 // actualiza para 2.8
    QString cadena = "CREATE TABLE series_fact ("
             "codigo  varchar(80),"
             "descrip varchar(254),"
             "proxnum bigint default 0,"
             "PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

    // tipos documentos a emitir
    cadena = "CREATE TABLE tipos_doc ("
             "codigo  varchar(80),"
             "descrip varchar(254) default '',"
             "serie   varchar(80) default '',"
             "pie1     varchar(254) default '',"
             "pie2     varchar(254) default '',"
             "moneda          varchar(10) default '',"
             "codigo_moneda   varchar(10) default '',";
             if ( cualControlador() == SQLITE ) {
                 cadena += "incluir_vto bool default 0,";
             }
             else {
                 cadena += "incluir_vto bool default false,";
             }
             cadena+="PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

    // referencias (facturación)
    cadena = "CREATE TABLE referencias ("
             "codigo  varchar(80),"
             "descrip varchar(254) default '',"
             "precio numeric(19,4) default 0,"
             "iva varchar(20) default '',"
             "ivare varchar(20) default '',"
             "cuenta varchar(80) default '',"
             "PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

    cadena="alter table tiposiva add column cuenta varchar(80) default ''";
    ejecutar(cadena);

    cadena="alter table configuracion add column tipo_ret_irpf numeric(7,4) default 0";
    ejecutar(cadena);

    if ( cualControlador() != SQLITE ) {
                 ejecutar("alter table configuracion drop column prox_asiento, "
                          "drop column numrel");
                 ejecutar("alter table ejercicios drop column primerasiento");
             }

    cadena="alter table ejercicios add column prox_asiento  bigint default 2";
    ejecutar(cadena);
    if ( cualControlador() == SQLITE )
        cadena = "alter table ejercicios add column primero_apertura bool default 1";
    else
        cadena = "alter table ejercicios add column primero_apertura bool default true";
    ejecutar(cadena);


    cadena="update usuarios set privilegios='";
    cadena+="11111111111111111111111111111111111111111111111111111111111111111111'";
    cadena+=" where codigo='admin'";
    ejecutar(cadena);

    ejecutar("alter table diario add column ejercicio varchar(80)");

    // habría que obtener una lista de ejercicios y después asignar a
    // cada ejercicio el número de asiento máximo en prox_asiento
    QStringList ejercicios = lista_cod_ejercicios();
    for (int i = 0; i < ejercicios.size(); ++i)
       {
        actu_ejercicio_diario(ejercicios.at(i));
        asigna_prox_asiento_ejercicio(ejercicios.at(i));
       }

    ejecutar("update configuracion set version='2.8'");

}


void basedatos::actualizade28()
{
    QString cadena = "drop TABLE tipos_doc";
    ejecutar (cadena);

    cadena = "drop TABLE facturas";
    ejecutar (cadena);

    cadena = "drop TABLE referencias";
    ejecutar (cadena);

    cadena = "drop TABLE lin_fact";
    ejecutar (cadena);

    // tipos documentos a emitir
    cadena = "CREATE TABLE tipos_doc ("
             "codigo  varchar(80),"
             "descrip varchar(254) default '',"
             "serie   varchar(80) default '',"
             "pie1     varchar(254) default '',"
             "pie2     varchar(254) default '',"
             "moneda          varchar(10) default '',"
             "codigo_moneda   varchar(10) default '',";
             if ( cualControlador() == SQLITE ) {
                 cadena += "contabilizable bool default 0,";
             }
             else {
                 cadena += "contabilizable bool default false,";
             }
             if ( cualControlador() == SQLITE ) {
                 cadena += "rectificativo bool default 0,";
             }
             else {
                 cadena += "rectificativo bool default false,";
             }
             cadena+="tipo_operacion int,"
             "documento   varchar(254) default '', "
             "cif_empresa varchar(254) default '', "
             "cif_cliente varchar(254) default '', "
             "cantidad    varchar(254) default '', "
             "referencia  varchar(254) default '', "
             "descripref  varchar(254) default '', "
             "precio      varchar(254) default '', "
             "totallin    varchar(254) default '', "
             "descuento   varchar(254) default '', "
             "bi          varchar(254) default '', "
             "tipoiva     varchar(254) default '', "
             "tipore      varchar(254) default '', "
             "cuota       varchar(254) default '', "
             "cuotare     varchar(254) default '', "
             "totalfac    varchar(254) default '', "
             "notas       varchar(254) default '', "
             "venci       varchar(254) default '', "
             "numero      varchar(254) default '', "
             "fecha       varchar(254) default '', "
             "cliente     varchar(254) default '', "
             "notastex    text , ";
             cadena+="imagen         text,";
             cadena+="PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);


    cadena = "CREATE TABLE referencias ("
             "codigo  varchar(80),"
             "descrip varchar(254) default '',"
             "precio numeric(19,4) default 0,"
             "iva varchar(20) default '',"
             "ivare varchar(20) default '',"
             "cuenta varchar(80) default '',"
             "PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);


    cadena = "create table facturas (";
    if (  cualControlador() == SQLITE) {
        cadena += "clave integer primary key autoincrement,";
    }
    else {
        cadena += "clave   serial,";
    }
    cadena += "serie   varchar(80),"
              "numero  bigint,"
              "cuenta  varchar(80),"
              "fecha     date,"
              "fecha_fac date,"
              "fecha_op  date,";
    if ( cualControlador() == SQLITE ) {
        cadena += "contabilizado bool default 0,";
       }
    else {
          cadena += "contabilizado bool default false,";
         }
    if ( cualControlador() == SQLITE ) {
        cadena += "contabilizable bool default 0,";
       }
    else {
          cadena += "contabilizable bool default false,";
         }
    if ( cualControlador() == SQLITE ) {
        cadena += "con_ret bool default 0,";
       }
    else {
          cadena += "con_ret bool default false,";
         }
    if ( cualControlador() == SQLITE ) {
        cadena += "con_re bool default 0,";
       }
    else {
          cadena += "con_re bool default false,";
         }
    if ( cualControlador() == SQLITE ) {
        cadena += "cerrado bool default 0,";
       }
    else {
          cadena += "cerrado bool default false,";
         }
    cadena+="tipo_ret  numeric(5,2),"
            "retencion numeric(14,2), "
            "tipo_doc varchar(80), "
            "notas text, "
            "pie1 varchar(254),"
            "pie2 varchar(254),"
            "pase_diario_cta bigint";
    cadena+=")";

    cadena = anadirMotor(cadena);
    ejecutar(cadena);


    // línea de facturas
    // línea de facturas
    cadena = "create table lin_fact ( ";
    if (cualControlador() == SQLITE) {
        cadena += "linea integer primary key autoincrement,";
    }
    else {
        cadena += "linea   serial,"; }
    cadena += "clave bigint, "  // por este campo se relaciona con cabecera
              "codigo       varchar(80),"
              "descripcion  varchar(254),"
              "cantidad     numeric(19,3),"
              "precio       numeric(19,4),"
              "clave_iva    varchar(40),"
              "tipo_iva     numeric(8,4),"
              "tipo_re      numeric(8,4),"
              "dto          numeric(8,4) ";
    if (cualControlador()!=SQLITE) cadena+=",PRIMARY KEY (linea) )";
       else cadena+=")";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);


    // tipos_automaticos
    cadena = "CREATE TABLE tipos_automaticos ("
             "codigo            varchar(40),"
             "descripcion       varchar(255),"
             "PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

//    if ( cualControlador() == SQLITE ) {
       cadena="insert into tipos_automaticos ( codigo,descripcion) "
             "values ('";
       cadena += tipo_asiento_general();
       cadena += "','";
       cadena += QObject::tr("TIPO GENERAL");
       cadena += "')";
       ejecutar(cadena);
//      }

    // cab_as_modelo
    cadena = "alter table cab_as_modelo add column tipo varchar(80) default ''";
    ejecutar(cadena);
    cadena = "alter table cab_as_modelo add column aib bool";
    ejecutar(cadena);
    cadena = "alter table cab_as_modelo add column autofactura_ue bool";
    ejecutar(cadena);
    cadena = "alter table cab_as_modelo add column autofactura_no_ue bool";
    ejecutar(cadena);
    cadena = "alter table cab_as_modelo add column eib bool";
    ejecutar(cadena);
    cadena = "alter table cab_as_modelo add column eib_servicios bool";
    ejecutar(cadena);
    if ( cualControlador() != SQLITE ) {
                 ejecutar("alter table det_as_modelo drop column autofactura");
             }

    cadena="update cab_as_modelo set tipo='";
    cadena += tipo_asiento_general();
    cadena += "'";
    ejecutar(cadena);


    cadena = "alter table diario add column ";
    if ( cualControlador() == SQLITE )
        cadena += "enlace       bool default 0";
    else
        cadena += "enlace       bool default false";
    ejecutar(cadena);


    ejecutar("update configuracion set version='2.9'");

    // Creamos nuevas claves foráneas
    if ( cualControlador() == SQLITE ) return; // no procede para SQLITE

    // claves foráneas para tipos_automaticos y facturación
/*    cadena="create index tipos_doc_serie_idx on tipos_doc(serie)";
    ejecutar(cadena);
    cadena="alter table tipos_doc add foreign key(serie) references series_fact(codigo)";
    ejecutar(cadena);
*/
    cadena="create index tipo_doc_factura_idx on facturas(tipo_doc)";
    ejecutar(cadena);
    cadena="alter table facturas add foreign key(tipo_doc) references tipos_doc(codigo)";
    ejecutar(cadena);

    cadena="create index codigo_lin_fact_idx on lin_fact(codigo)";
    ejecutar(cadena);
    cadena="alter table lin_fact add foreign key(codigo) references referencias(codigo)";
    ejecutar(cadena);

    cadena="alter table cab_as_modelo add foreign key(tipo) references tipos_automaticos(codigo)";
    ejecutar(cadena);


}

void basedatos::actualizade29()
{
    QString cadena = "alter table libroiva add column ";
    if (cualControlador() == SQLITE )
        cadena += "agrario bool default 0;";
      else
        cadena += "agrario bool default false;";
    ejecutar(cadena);

    cadena = "alter table libroiva add column ";
    cadena+="nombre varchar(80) default '';";
    ejecutar(cadena);

    cadena = "alter table libroiva add column ";
    cadena+="cif varchar(80) default '';";
    ejecutar(cadena);

    cadena = "alter table configuracion add column cuenta_ret_ing varchar(255)";
    ejecutar(cadena);

    cadena = "alter table det_as_modelo add column ";
     if ( cualControlador() == SQLITE )
       cadena += "agrario bool default 0;";
      else
          cadena += "agrario bool default false;";
    ejecutar(cadena);

    cadena = "alter table det_as_modelo add column ";
     cadena+="nombre varchar(255) default '';";
     ejecutar(cadena);

     cadena = "alter table det_as_modelo add column ";
     cadena+="cif varchar(255) default '';";
     ejecutar(cadena);

     // clave_op_retenciones
     cadena = "CREATE TABLE clave_op_retenciones ("
              "codigo         varchar(40),"
              "descripcion    varchar(254),"
              "PRIMARY KEY (codigo) )";
     cadena = anadirMotor(cadena);
     ejecutar(cadena);

     introclave_op_retenciones();

     // provincias
     cadena = "CREATE TABLE provincias ("
              "codigo         varchar(40),"
              "descripcion    varchar(254),"
              "PRIMARY KEY (codigo) )";
     cadena = anadirMotor(cadena);
     ejecutar(cadena);

     intro_provincias();

     cadena = "alter table datossubcuenta add column ";
     cadena+="pais_dat varchar(80)";
     ejecutar(cadena);

     cadena = "alter table datossubcuenta add column ";
     cadena+="tipo_ret numeric(5,2) default 0;";
     ejecutar(cadena);

     cadena = "alter table datossubcuenta add column ";
     cadena+="tipo_operacion_ret varchar(80)";
     ejecutar(cadena);

     cadena = "alter table datossubcuenta add column ";
     if (cualControlador() == SQLITE )
       cadena += "ret_arrendamiento bool default 0;"; // retención de arrendamiento
     else cadena += "ret_arrendamiento bool default false;";
     ejecutar(cadena);

     cadena = "alter table configuracion add column ";
     cadena += "provincia_def  varchar(255) default '';";
     ejecutar(cadena);


     // retenciones
     cadena = "CREATE TABLE retenciones ("
              "pase          bigint,"
              "cta_retenido  varchar(80),";
              if ( cualControlador() == SQLITE ) {
                  cadena += "arrendamiento        bool default 0,";
              }
              else {
                  cadena += "arrendamiento    bool default false,";
              }
              cadena+="clave_ret varchar(255),"

              "base_ret      numeric(14,2),"
              "tipo_ret      numeric(5,2),"
              "retencion     numeric(14,2),"
              "ing_cta       numeric(14,2),"
              "ing_cta_rep   numeric(14,2),"
              "nombre        varchar(255),"
              "cif           varchar(80),"
              "provincia     varchar(255),";
     cadena+="PRIMARY KEY (pase) )";
     cadena = anadirMotor(cadena);
     ejecutar(cadena);

     // ESTO no vale para SQLITE
     // pase forma parte la clave primaria, por lo que hay índice implícito
     cadena="alter table retenciones add foreign key(pase) references diario(pase)";
     if ( cualControlador() != SQLITE ) ejecutar(cadena);


     cadena="alter table det_as_modelo add column cta_retenido varchar(255) default '';";
     ejecutar(cadena);
     if (cualControlador() == SQLITE )
         cadena = "alter table det_as_modelo add column arrendamiento bool default 0;";
        else cadena = "alter table det_as_modelo add column arrendamiento bool default false;";
     ejecutar(cadena);
     cadena="alter table det_as_modelo add column  clave       varchar(255) default '';";
     ejecutar(cadena);
     cadena="alter table det_as_modelo add column base_ret    varchar(255) default '';";
     ejecutar(cadena);
     cadena="alter table det_as_modelo add column tipo_ret    varchar(255) default '';";
     ejecutar(cadena);
     cadena="alter table det_as_modelo add column retencion   varchar(255) default '';";
     ejecutar(cadena);
     cadena="alter table det_as_modelo add column ing_a_cta   varchar(255) default '';";
     ejecutar(cadena);
     cadena="alter table det_as_modelo add column ing_a_cta_rep varchar(255) default '';";
     ejecutar(cadena);
     cadena="alter table det_as_modelo add column nombre_ret    varchar(255) default '';";
     ejecutar(cadena);
     cadena="alter table det_as_modelo add column cif_ret      varchar(255) default '';";
     ejecutar(cadena);
     cadena="alter table det_as_modelo add column provincia    varchar(255) default '';";
     ejecutar(cadena);

     cadena="alter table tipos_doc add column totallineas    varchar(254) default ''; ";
     ejecutar(cadena);
     cadena="alter table tipos_doc add column lineas_doc  int;";
     ejecutar(cadena);
     cadena="alter table tipos_doc add column nombre_emisor     varchar(254) default '';";
     ejecutar(cadena);
     cadena="alter table tipos_doc add column domicilio_emisor  varchar(254) default '';";
     ejecutar(cadena);
     cadena="alter table tipos_doc add column cp_emisor         varchar(254) default '';";
     ejecutar(cadena);
     cadena="alter table tipos_doc add column poblacion_emisor  varchar(254) default '';";
     ejecutar(cadena);
     cadena="alter table tipos_doc add column provincia_emisor  varchar(254) default '';";
     ejecutar(cadena);
     cadena="alter table tipos_doc add column pais_emisor       varchar(254) default '';";
     ejecutar(cadena);
     cadena="alter table tipos_doc add column cif_emisor        varchar(254) default '';";
     ejecutar(cadena);
     cadena="alter table tipos_doc add column id_registral      varchar(254) default '';";
     ejecutar(cadena);

     cadena="alter table cab_as_modelo add column diario varchar(255) default '';";
     ejecutar(cadena);

     ejecutar("update configuracion set version='2.9.3'");

     cadena="update usuarios set privilegios='";
     cadena+="1111111111111111111111111111111111111111111111111111111111111111111111111'";
           // 1234567890123456789012345678901234567890123456789012345678901234567890123
     cadena+=" where codigo='admin'";
     ejecutar(cadena);

     QString cadquery = "update tipos_doc set ";
     cadquery+="nombre_emisor='";
     cadquery+=selectEmpresaconfiguracion();
     cadquery+="', domicilio_emisor='";
     cadquery+=domicilio();
     cadquery+="', cp_emisor='";
     cadquery+=cpostal();
     cadquery+="', poblacion_emisor='";
     cadquery+= ciudad();
     cadquery+="', provincia_emisor='";
     cadquery+= provincia();
     cadquery+="', cif_emisor='";
     cadquery+=cif();
     cadquery+="', id_registral='";
     cadquery+=idregistral();
     cadquery+="', lineas_doc= ";
     cadquery+="10";

     ejecutar(cadquery);


}

void basedatos::actualizade293()
{
/*    QString cadena="alter table diario add column fecha_reg date default '2000-01-01';";
    ejecutar(cadena);

    cadena="alter table diario add column hora_reg varchar(10) default '';";
    ejecutar(cadena); */

    QString cadena="alter table diario add column apunte_origen bigint default 0;";
    ejecutar(cadena);

    // apuntes borrados
    cadena = "CREATE TABLE apuntes_borrados ("
             "pase          bigint,"
             "fecha_reg     date, "
             "hora_reg      varchar(10),";
    cadena+="PRIMARY KEY (pase) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);


    // empresas consolidables
    cadena = "CREATE TABLE consolidables (";
    cadena += "codigo         int, ";       //código de la empresa 1 al 9
    cadena += "nombrebd       varchar(80),"; //nombre base de datos
    cadena += "descrip        varchar(80),"; // descripción de la conexión
    cadena += "usuario        varchar(80),"; //usuario base de datos
    cadena += "clave          varchar(80),"; //clave conexión
    cadena += "host           varchar(160),"; //host donde se encuentra la base de datos
    cadena += "puerto         varchar(80),"; //puerto de la conexión
    cadena += "controlador    varchar(80),"; //controlador de la conexión
    cadena += "ultimo_apunte  bigint,";      // último apunte conexión origen
    cadena += "fecha_actu     date       ,";    // fecha última actualización
    cadena += "hora_actu      varchar(10),";  //hora última actualización hhmmss
    cadena += "import_tipos_diario  bool,";  // importa tipos de diario no estándar
    cadena+="PRIMARY KEY (codigo) )";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

    ejecutar("update configuracion set version='2.9.3E'");

}

void basedatos::actualizade293E()
{
    QString cadena;
    if ( cualControlador() == SQLITE )
      {
       cadena="alter table bloqueos_cta add column enero bool default 0;";
       ejecutar(cadena);
       cadena = "alter table bloqueos_cta add column febrero bool default 0;";
       ejecutar(cadena);
       cadena = "alter table bloqueos_cta add column marzo bool default 0;";
       ejecutar(cadena);
       cadena = "alter table bloqueos_cta add column abril bool default 0;";
       ejecutar(cadena);
       cadena = "alter table bloqueos_cta add column mayo bool default 0;";
       ejecutar(cadena);
       cadena = "alter table bloqueos_cta add column junio bool default 0;";
       ejecutar(cadena);
       cadena = "alter table bloqueos_cta add column julio bool default 0;";
       ejecutar(cadena);
       cadena = "alter table bloqueos_cta add column agosto bool default 0;";
       ejecutar(cadena);
       cadena = "alter table bloqueos_cta add column septiembre bool default 0;";
       ejecutar(cadena);
       cadena = "alter table bloqueos_cta add column octubre bool default 0;";
       ejecutar(cadena);
       cadena = "alter table bloqueos_cta add column noviembre bool default 0;";
       ejecutar(cadena);
       cadena = "alter table bloqueos_cta add column diciembre bool default 0;";
       ejecutar(cadena);
      }
      else
          {
          cadena = "alter table bloqueos_cta add column enero bool default false;";
          ejecutar(cadena);
          cadena = "alter table bloqueos_cta add column febrero bool default false;";
          ejecutar(cadena);
          cadena = "alter table bloqueos_cta add column marzo bool default false;";
          ejecutar(cadena);
          cadena = "alter table bloqueos_cta add column abril bool default false;";
          ejecutar(cadena);
          cadena = "alter table bloqueos_cta add column mayo bool default false;";
          ejecutar(cadena);
          cadena = "alter table bloqueos_cta add column junio bool default false;";
          ejecutar(cadena);
          cadena = "alter table bloqueos_cta add column julio bool default false;";
          ejecutar(cadena);
          cadena = "alter table bloqueos_cta add column agosto bool default false;";
          ejecutar(cadena);
          cadena = "alter table bloqueos_cta add column septiembre bool default false;";
          ejecutar(cadena);
          cadena = "alter table bloqueos_cta add column octubre bool default false;";
          ejecutar(cadena);
          cadena = "alter table bloqueos_cta add column noviembre bool default false;";
          ejecutar(cadena);
          cadena = "alter table bloqueos_cta add column diciembre bool default false;";
          ejecutar(cadena);

          }
      // vencimientos asignados a cuentas (no auxiliares)
      cadena ="CREATE TABLE venci_cta (";
      cadena += "codigo varchar(40), ";
      cadena += "venci  varchar(40), ";
      cadena += "tesoreria  varchar(80) default '', ";
      if ( cualControlador() == SQLITE ) {
          cadena += "procesavenci bool default 0,";
         }
      else {
            cadena += "procesavenci bool default false,";
           }
      cadena += "vto_dia_fijo  int default 0, ";
      cadena+="PRIMARY KEY (codigo) )";
      cadena = anadirMotor(cadena);
      ejecutar(cadena);

      cadena="alter table datossubcuenta add column vto_dia_fijo int default 0;";
      ejecutar(cadena);


      cadena="update usuarios set privilegios='";
      cadena+="111111111111111111111111111111111111111111111111111111111111111111111111111'";
            // 123456789012345678901234567890123456789012345678901234567890123456789012345
      cadena+=" where codigo='admin'";
      ejecutar(cadena);

      ejecutar("update configuracion set version='2.9.4'");

}


void basedatos::actualizade294()        
{
    QSqlDatabase::database().transaction();

    if (cualControlador() == MYSQL)
       {
        ejecutar("SET AUTOCOMMIT=0");
        ejecutar("LOCK TABLE configuracion write, diario write");
       }
    if (cualControlador() == POSTGRES)
       {
         ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
         ejecutar("LOCK TABLE diario IN ACCESS EXCLUSIVE MODE");
       }

    ejecutar("alter table configuracion add column imagen text");

    ejecutar("alter table diario add column codfactura varchar(80) default ''");

    ejecutar("update configuracion set version='2.9.5'");

    QSqlDatabase::database().commit();
    if (cualControlador() == MYSQL)
       {
        ejecutar("unlock tables");
        ejecutar("SET AUTOCOMMIT=1");
       }

}


void basedatos::actualizade295()
{
   QSqlDatabase::database().transaction();

    if (cualControlador() == MYSQL)
       {
        ejecutar("SET AUTOCOMMIT=0");
        ejecutar("LOCK TABLE configuracion write, datossubcuenta write");
       }
    if (cualControlador() == POSTGRES)
       {
         ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
         ejecutar("LOCK TABLE datossubcuenta IN ACCESS EXCLUSIVE MODE");
       }


  ejecutar("alter table datossubcuenta add column cuenta_ret_asig varchar(40) default ''");

  if ( cualControlador() == SQLITE )
    ejecutar("alter table datossubcuenta add column es_ret_asig_arrend bool default 0");
  else
    ejecutar("alter table datossubcuenta add column es_ret_asig_arrend bool default false");

  ejecutar("alter table datossubcuenta add column tipo_ret_asig numeric(5,2) default 0");
  ejecutar("alter table datossubcuenta add column tipo_oper_ret_asig varchar(80)");

  ejecutar("update configuracion set version='2.9.6'");



    QSqlDatabase::database().commit();
    if (cualControlador() == MYSQL)
       {
        ejecutar("unlock tables");
        ejecutar("SET AUTOCOMMIT=1");
       }

}

void basedatos::actualizade297()
{
  ejecutar("alter table diario add column nrecepcion bigint default 0");

  QString cadena="alter table diario add column ";
if (cualControlador() == SQLITE )
    cadena += "revisado bool default 0";
else
    cadena += "revisado bool default false";
ejecutar(cadena);

 ejecutar("alter table ejercicios add column prox_nrecepcion bigint default 1");

 cadena="alter table configuracion add column ";
 if ( cualControlador() == SQLITE )
     cadena += "sec_recibidas bool default 0";
      else cadena += "sec_recibidas bool default false";
 ejecutar(cadena);

 cadena="update usuarios set privilegios='";
 cadena+="1111111111111111111111111111111111111111111111111111111111111111111111111111111'";
       // 1234567890123456789012345678901234567890123456789012345678901234567890123456789
 cadena+=" where codigo='admin'";
 ejecutar(cadena);

 ejecutar("update configuracion set version='2.9.7.3'");

}

void basedatos::actualizade2973()
{
    QString cadena="alter table datossubcuenta add column ";
    if (cualControlador() == SQLITE )
            cadena += "dom_bancaria bool default 0";
       else cadena += "dom_bancaria bool default false";
    ejecutar(cadena);

    cadena="alter table datossubcuenta add column IBAN     varchar(80);";
    ejecutar(cadena);
    cadena="alter table datossubcuenta add column nriesgo  varchar(80);";
    ejecutar(cadena);
    cadena="alter table datossubcuenta add column cedente1 varchar(80);";
    ejecutar(cadena);
    cadena="alter table datossubcuenta add column cedente2 varchar(80);";
    ejecutar(cadena);

    ejecutar("update configuracion set version='2.9.7.4'");

}

void basedatos::actualizade2974()
{

 QString cadena=" alter table vencimientos add column domiciliacion bigint default 0;";
 ejecutar(cadena);

 if ( cualControlador() == SQLITE ) {
     cadena = "alter table vencimientos add column impagado bool default 0;";
   }
  else {
        cadena = "alter table vencimientos add column impagado bool default false;";
     }
  ejecutar(cadena);

if ( cualControlador() == SQLITE ) {
  cadena = "alter table vencimientos add column domiciliable bool default 0;";
   }
 else {
  cadena = "alter table vencimientos add column domiciliable bool default false;";
 }
 ejecutar(cadena);

 cadena="alter table datossubcuenta add column BIC  varchar(80);";
 ejecutar(cadena);

 cadena="alter table datossubcuenta add column sufijo varchar(80);";
 ejecutar(cadena);

 ejecutar("alter table configuracion add column prox_domiciliacion bigint default 1;");

 ejecutar("alter table configuracion add column cod_ine_plaza varchar(40) default '';");

 // domiciliaciones
 cadena = "CREATE TABLE domiciliaciones ("
          "id_remesa    bigint,"
          "aux_banco    varchar(80),"  // Cuenta auxiliar correspondiente al banco
          "descrip      varchar(255)," // Campo con descripción de la domiciliación
          "ccc          varchar(80),"  // Código cuenta cliente
          "iban         varchar(80),"  // IBAN
          "sufijo       varchar(80),"  // Campo sufijo
          "cedente1     varchar(80),"  // Campo cedente1
          "cedente2     varchar(80),"  // Campo cedente2
          "riesgo       varchar(80),"  //  Número de riesgo
          "fecha_conf   date       ,"  // Fecha de confección
          "fecha_cargo  date       ,";  // Fecha de cargo
          if ( cualControlador() == SQLITE )
              cadena += "emitido19 bool default 0,";
          else
              cadena += "emitido19 bool default false,";
          if ( cualControlador() == SQLITE )
              cadena += "emitido58 bool default 0,";
          else
              cadena += "emitido58 bool default false,";
          if ( cualControlador() == SQLITE )
              cadena += "procesada bool default 0,";
          else
              cadena += "procesada bool default false,";

          cadena+=  "PRIMARY KEY (id_remesa) )";
 cadena = anadirMotor(cadena);
 ejecutar(cadena);
 ejecutar("update configuracion set version='2.9.7.5'");

}



void basedatos::actualizade2975()
{
#ifdef NOMACHINE
    QSqlDatabase::database().transaction();

     if (cualControlador() == POSTGRES)
        {
          ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
        }
#endif

// facturas predefinidas
QString cadena = "create table facturas_predef (";
cadena += "codigo  varchar(40),";
cadena += "descrip  varchar(254),";
cadena += "serie   varchar(80),";
if ( cualControlador() == SQLITE ) {
    cadena += "contabilizable bool default 0,";
   }
else {
      cadena += "contabilizable bool default false,";
     }
if ( cualControlador() == SQLITE ) {
    cadena += "con_ret bool default 0,";
   }
else {
      cadena += "con_ret bool default false,";
     }
if (cualControlador() == SQLITE ) {
    cadena += "con_re bool default 0,";
   }
else {
      cadena += "con_re bool default false,";
     }
cadena+="tipo_ret  numeric(5,2),"
        "tipo_doc varchar(80), "
        "notas text, "
        "pie1 varchar(254),"
        "pie2 varchar(254), ";
cadena+="primary key (codigo))";

cadena = anadirMotor(cadena);
ejecutar(cadena);


// línea de facturas predefinidas
cadena = "create table lin_fact_predef ( ";
if ( cualControlador() == SQLITE) {
    cadena += "linea integer primary key autoincrement,";
}
else {
    cadena += "linea   serial,"; }
cadena += "cod_fact     varchar(40), "  // por este campo se relaciona con cabecera
          "codigo       varchar(80),"
          "descripcion  varchar(254),"
          "cantidad     numeric(19,3),"
          "precio       numeric(19,4),"
          "clave_iva    varchar(40),"
          "tipo_iva     numeric(8,4),"
          "tipo_re      numeric(8,4),"
          "dto          numeric(8,4)";
if (cualControlador()!=SQLITE)
    cadena+=",PRIMARY KEY (linea) )";
   else cadena+=")";

cadena = anadirMotor(cadena);
ejecutar(cadena);

// actualizamos ecpn
cadena="delete from concepto_evpn_pymes";
ejecutar(cadena);
intro_conceptos_evpn();

// actualizamos cols_evpn_pymes
cadena="delete from cols_evpn_pymes";
ejecutar(cadena);
intro_cols_evpn_pymes();


// lotes facturas predefinidas
cadena = "create table lote_fact ( "
          "codigo       varchar(80),"
          "descripcion  varchar(255)";
if (cualControlador()!=SQLITE)
   cadena+=", PRIMARY KEY (codigo) )";
 else cadena+=")";

cadena = anadirMotor(cadena);
ejecutar(cadena);


// línea de lotes facturas predefinidas
cadena = "create table lin_lote_predef ( ";
if (cualControlador() == SQLITE) {
    cadena += "linea integer primary key autoincrement,";
}
else {
    cadena += "linea   serial,"; }
cadena += "codigo       varchar(80), "  // por este campo se relaciona con cabecera
          "cuenta       varchar(80), "
          "cuota        varchar(80) ";
if (cualControlador()!=SQLITE)
    cadena+=", PRIMARY KEY (linea) )";
   else cadena+=")";

cadena = anadirMotor(cadena);
ejecutar(cadena);



cadena ="CREATE TABLE iva_ret_cta (";
cadena += "codigo varchar(40), ";
if ( cualControlador() == SQLITE )
    cadena += "ivaasoc bool default 0,"; // vencimiento asociado
 else cadena += "ivaasoc bool default false,";
cadena+="cuentaiva      varchar(40),"
        "tipoiva        varchar(20),"
        "conceptodiario varchar(155),"
        "cuenta_ret_asig varchar(40) default '',";
if (cualControlador() == SQLITE )
  cadena += "es_ret_asig_arrend bool default 0,"; // vencimiento asociado
 else cadena += "es_ret_asig_arrend bool default false,";
cadena+="tipo_ret_asig numeric(5,2) default 0,"
        "tipo_oper_ret_asig varchar(80),";
cadena+="PRIMARY KEY (codigo) )";
cadena = anadirMotor(cadena);
ejecutar(cadena);



if (cualControlador() == SQLITE )
    cadena = "alter table libroiva add column exento_dcho_ded bool default 1;";
else
    cadena = "alter table libroiva add column exento_dcho_ded bool default false;";
ejecutar(cadena);


if (cualControlador() == SQLITE )
    cadena = "alter table libroiva add column isp_op_interiores bool default 0;";
else
    cadena = "alter table libroiva add column isp_op_interiores bool default false;";
ejecutar(cadena);


if (cualControlador() == SQLITE )
    cadena = "alter table libroiva add column importacion bool default 0;";
else
    cadena = "alter table libroiva add column importacion bool default false;";
ejecutar(cadena);

if ( cualControlador() == SQLITE )
    cadena = "alter table libroiva add column exportacion bool default 0;";
else
    cadena = "alter table libroiva add column exportacion bool default false;";
ejecutar(cadena);

ejecutar("update configuracion set version='2.9.7.6'");


#ifdef NOMACHINE
   QSqlDatabase::database().commit();
#endif

}

void basedatos::actualizade2976()
{

    QString cadena;

    if (cualControlador() == SQLITE )
        cadena = "alter table cab_as_modelo add column isp_op_interiores bool default 0;";
       else cadena = "alter table cab_as_modelo add column isp_op_interiores bool default false;";

    ejecutar(cadena);

    if (cualControlador() == SQLITE )
        cadena = "alter table cab_as_modelo add column importacion bool default 0;";
       else cadena = "alter table cab_as_modelo add column importacion bool default false;";

    ejecutar(cadena);

    if (cualControlador() == SQLITE )
       cadena = "alter table cab_as_modelo add column exportacion bool default 0;";
      else cadena = "alter table cab_as_modelo add column exportacion bool default false;";

    ejecutar(cadena);

    cadena="alter table configuracion add column ";
    if ( cualControlador() == SQLITE )
        cadena += "caja_iva bool default 0";
          else cadena += "caja_iva bool default false";
    ejecutar(cadena);

    cadena="alter table datossubcuenta add column ";
    if ( cualControlador() == SQLITE )
            cadena += "caja_iva bool default 0"; // criterio de caja en IVA
       else cadena += "caja_iva bool default false";
    ejecutar(cadena);

    ejecutar("alter table vencimientos add column fecha_realizacion date");
    ejecutar("alter table vencimientos add column medio_realizacion varchar(255) default ''");
    ejecutar("alter table vencimientos add column cuenta_bancaria varchar(255) default ''");
    cadena="alter table vencimientos add column ";
    if (cualControlador() == SQLITE ) {
        cadena += "forzar_liq_iva bool default 0";
    }
    else {
        cadena += "forzar_liq_iva bool default false";
    }
    ejecutar(cadena);
    ejecutar("alter table vencimientos add column liq_iva_fecha date");

    ejecutar("update vencimientos set fecha_realizacion=fecha_vencimiento where not pendiente");

    cadena="alter table libroiva add column ";
    if ( cualControlador() == SQLITE )
        cadena += "caja_iva  bool default 0;";
    else
        cadena += "caja_iva  bool default false;";
    ejecutar(cadena);
    ejecutar("alter table libroiva add column apunte_tot_factura bigint default 0");
    ejecutar("alter table libroiva add column total_factura numeric(14,2)");

    cadena="alter table domiciliaciones add column ";
    if (cualControlador() == SQLITE )
        cadena += "emitidosepa1914 bool default 0";
    else
        cadena += "emitidosepa1914 bool default false";
    ejecutar(cadena);

    cadena="update usuarios set privilegios='";
    cadena+="1111111111111111111111111111111111111111111111111111111111111111111111111111111111'";
          // 1234567890123456789012345678901234567890123456789012345678901234567890123456789
    cadena+=" where codigo='admin'";
    ejecutar(cadena);

    ejecutar("update configuracion set version='2.9.7.7'");

}

void basedatos::actualizade2978()
{
    ejecutar("alter table datossubcuenta add column ref_mandato varchar(80) default ''");
    ejecutar("alter table datossubcuenta add column firma_mandato date");
    ejecutar("update configuracion set version='2.9.7.9'");

}


void basedatos::actualizade2979()
{
   QString cadena;
   cadena = "create table condiciones(";
   if ( cualControlador() == SQLITE )
       cadena += "mostrar bool default 0, "; // vencimiento asociado
      else cadena += "mostrar bool default false, ";
   cadena+= " mensaje text,"
            " nombre varchar(255),"
            " cargo  varchar(255),"
            " organizacion varchar(255),";
   if ( cualControlador() == SQLITE )
     cadena += "acepta bool default 0 "; // vencimiento asociado
    else cadena += "acepta bool default false ";
   cadena+=");";
   ejecutar(cadena);

 ejecutar("update configuracion set version='2.9.8'");

}

void basedatos::actualizade298()
{

    QString cadena;
       cadena= "drop table condiciones;";
       ejecutar(cadena);

    cadena = "create table condiciones(";
    if ( cualControlador() == SQLITE) {
        cadena += "registro integer primary key autoincrement,";
    }
    else {
        cadena += "registro   serial,";
    }
    if ( cualControlador() == SQLITE )
        cadena += "mostrar bool default 0, "; // vencimiento asociado
       else cadena += "mostrar bool default false, ";
    cadena+= " mensaje text,"
             " fecha   date,"
             " hora    varchar(6),"
             " nombre varchar(255),"
             " cargo  varchar(255),"
             " organizacion varchar(255),";
    if ( cualControlador() == SQLITE )
      cadena += "acepta bool default 0 ";
     else cadena += "acepta bool default false ";
    cadena+=");";
    cadena = anadirMotor(cadena);
    ejecutar(cadena);

    ejecutar("alter table estados add column m1 numeric(14,2);");
    ejecutar("alter table estados add column m2 numeric(14,2);");
    ejecutar("alter table estados add column m3 numeric(14,2);");
    ejecutar("alter table estados add column m4 numeric(14,2);");
    ejecutar("alter table estados add column m5 numeric(14,2);");
    ejecutar("alter table estados add column m6 numeric(14,2);");
    ejecutar("alter table estados add column m7 numeric(14,2);");
    ejecutar("alter table estados add column m8 numeric(14,2);");
    ejecutar("alter table estados add column m9 numeric(14,2);");
    ejecutar("alter table estados add column m10 numeric(14,2);");
    ejecutar("alter table estados add column m11 numeric(14,2);");
    ejecutar("alter table estados add column m12 numeric(14,2);");


    ejecutar("update configuracion set version='2.9.8.1'");

}

void basedatos::actualizade2981()
{

  // NUEVOS CONCEPTOS ECPN
    ejecutar("delete from concepto_evpn_pymes");
    intro_conceptos_evpn();

  ejecutar("alter table regul_sec add column diario varchar(80) default ''");

  ejecutar("alter table borrador add column col32            varchar(40) default ''");
  ejecutar("alter table borrador add column col33            varchar(255) default ''");
  ejecutar("alter table borrador add column col34            varchar(40) default ''");
  ejecutar("alter table borrador add column col35            varchar(40) default ''");
  ejecutar("alter table borrador add column col36            varchar(40) default ''");
  ejecutar("alter table borrador add column col37            varchar(40) default ''");
  ejecutar("alter table borrador add column col38            varchar(40) default ''");
  ejecutar("alter table borrador add column col39            varchar(40) default ''");
  ejecutar("alter table borrador add column col40            varchar(40) default ''");
  ejecutar("alter table borrador add column col41            varchar(40) default ''");
  ejecutar("alter table borrador add column col42            varchar(40) default ''");
  ejecutar("alter table borrador add column col43            varchar(255) default ''");
  ejecutar("alter table borrador add column col44            varchar(40) default ''");
  ejecutar("alter table borrador add column col45            varchar(255) default ''");
  ejecutar("alter table borrador add column col46            varchar(40) default ''");
  ejecutar("alter table borrador add column col47            varchar(40) default ''");
  ejecutar("alter table borrador add column col48            varchar(40) default ''");
  ejecutar("alter table borrador add column col49            varchar(40) default ''");
  ejecutar("alter table borrador add column col50            varchar(40) default ''");

  ejecutar("update configuracion set version='2.9.8.2'");

}

void basedatos::actualizade2982()
{
    QString cadena= "drop table lote_fact;";
    ejecutar(cadena);

    cadena= "drop table lin_lote_predef;";
        ejecutar(cadena);

    // lotes facturas predefinidas
    cadena = "create table lote_fact ( "
              "codigo       varchar(80),"
              "descripcion  varchar(255),";
              if (cualControlador() == SQLITE ) {
                  cadena += "cuotas bool default 0,";
                 }
                 else {
                       cadena += "cuotas bool default false,";
                      }
    cadena += "cod_factura_predef  varchar(80), ";
    cadena += "presupuesto numeric(19,2) default 0, ";
    cadena+=" PRIMARY KEY (codigo) )";

    cadena = anadirMotor(cadena);
    ejecutar(cadena);



    // línea de lotes facturas predefinidas
    cadena = "create table lin_lote_predef ( ";
    if (cualControlador() == SQLITE) {
        cadena += "linea integer primary key autoincrement,";
    }
    else {
        cadena += "linea   serial,"; }
    cadena += "codigo       varchar(80), "  // por este campo se relaciona con cabecera
              "cuenta       varchar(80), "
              "importe      varchar(80), "
              "cuota        varchar(80) ";
    if (cualControlador()!=SQLITE)
        cadena+=", PRIMARY KEY (linea) )";
       else cadena+=")";

    cadena = anadirMotor(cadena);
    ejecutar(cadena);


    if (cualControlador()!=SQLITE)
       {
        cadena="alter table lin_lote_predef add foreign key(codigo) "
           "references lote_fact(codigo)";
        ejecutar(cadena);
       }


    cadena="alter table planamortizaciones add column ";
    if ( cualControlador() == SQLITE )
        cadena += "baja bool default 0;";
    else
        cadena += "baja bool default false;";
    ejecutar(cadena);

    cadena= "alter table planamortizaciones add column fecha_baja date;";
    ejecutar(cadena);

    cadena= "alter table planamortizaciones add column motivo_baja varchar(255);";
    ejecutar(cadena);

    cadena= "alter table planamortizaciones add column cuenta_proveedor varchar(40);";
    ejecutar(cadena);

  ejecutar("update configuracion set version='2.9.8.3'");

}


void basedatos::actualizade2983()
{
    ejecutar("alter table tiposiva add column cuenta_sop varchar(80)");
    ejecutar("update configuracion set version='2.9.8.4'");
}

void basedatos::actualizade2984()
{
    ejecutar("alter table configuracion add column cabecera347 varchar(255);");
    ejecutar("alter table configuracion add column cuerpo347 text;");
    ejecutar("alter table configuracion add column desglose347 bool;");
    ejecutar("alter table configuracion add column cuerpo2_347 text;");
    ejecutar("alter table configuracion add column pie347 text;");
    ejecutar("alter table configuracion add column saludo347 text;");
    ejecutar("alter table configuracion add column firma347 varchar(255);");

    ejecutar(mensajes_iniciales_347());

    ejecutar("update configuracion set version='2.9.8.5'");

}

void basedatos::actualizade2977()
{
    ejecutar("alter table configuracion add column cuenta_tesoreria  varchar(40) default ''");
    ejecutar("alter table configuracion add column cuenta_gasto_vto  varchar(40) default ''");

    ejecutar("alter table diario add column fecha_factura date");


    QString cadena;
    cadena="update configuracion set cuentasapagar='";
    cadena+="400,410,523,173', cuentasacobrar='";
    cadena+="43,44,253'";
    ejecutar(cadena);


    ejecutar("update diario set fecha_factura=fecha");

    ejecutar("update configuracion set version='2.9.7.8'");

}

void basedatos::actualizade296()
{
// clave_concepto_evpn
QString cadena = "CREATE TABLE concepto_evpn_pymes ("
         "codigo         varchar(40),"
         "descripcion    varchar(254),"
         "PRIMARY KEY (codigo) )";
cadena = anadirMotor(cadena);
ejecutar(cadena);

intro_conceptos_evpn();

ejecutar("alter table diario add column codigo_var_evpn_pymes varchar(160) default ''");

// clave_columnas_evpn
cadena = "CREATE TABLE cols_evpn_pymes ("
         "codigo         varchar(40),"
         "descripcion    varchar(254),"
         "cuentas    varchar(254),"
         "PRIMARY KEY (codigo) )";

cadena = anadirMotor(cadena);
ejecutar(cadena);

intro_cols_evpn_pymes();

ejecutar("alter table amortcontable add column as_enero       bigint default 0");
ejecutar("alter table amortcontable add column as_febrero     bigint default 0");
ejecutar("alter table amortcontable add column as_marzo       bigint default 0");
ejecutar("alter table amortcontable add column as_abril       bigint default 0");
ejecutar("alter table amortcontable add column as_mayo        bigint default 0");
ejecutar("alter table amortcontable add column as_junio       bigint default 0");
ejecutar("alter table amortcontable add column as_julio       bigint default 0");
ejecutar("alter table amortcontable add column as_agosto      bigint default 0");
ejecutar("alter table amortcontable add column as_septiembre  bigint default 0");
ejecutar("alter table amortcontable add column as_octubre     bigint default 0");
ejecutar("alter table amortcontable add column as_noviembre   bigint default 0");
ejecutar("alter table amortcontable add column as_diciembre   bigint default 0");


ejecutar("alter table amortfiscalycontable add column mes int default 0");

// tabla amoinv
cadena = "create table amoinv (";
if (cualControlador() == SQLITE) {
    cadena += "numero integer primary key autoincrement,";
}
else {
    cadena += "numero   serial,";
}
cadena += "cta_aa       varchar(40),"
          "cta_ag       varchar(40),"
          "concepto     varchar(255),"
          "fecha_inicio date,"
          "fecha_compra date,"
          "fecha_ult_amort date,"
          "coef         float8,"
          "valor_adq    numeric(14,2),"
          "valor_amortizado numeric(14,2),"
          "factura      varchar(255),"
          "proveedor    varchar(255))";
cadena = anadirMotor(cadena);
ejecutar(cadena);


ejecutar ("alter table configuracion add column cuentas_aa varchar(255) default '';");
ejecutar ("alter table configuracion add column cuentas_ag varchar(255) default '';");
if ( cualControlador() == SQLITE )
    ejecutar("alter table configuracion add column amoinv bool default 0;");
  else ejecutar("alter table configuracion add column amoinv bool default false;");


 ejecutar("drop table amortfiscalycontable");

  // amortfiscalycontable
  cadena = "CREATE TABLE amortfiscalycontable ("
           "ejercicio         varchar(40),"
           "mes               int default 0, "
           "cuenta_activo     varchar(40),"
           "codigo_activo     varchar(40),"
           "cuenta_am_acum    varchar(40),"
           "cuenta_gasto      varchar(40),"
           "dotacion_contable numeric(14,2),"
           "dotacion_fiscal   numeric(14,2),"
           "PRIMARY KEY (ejercicio, cuenta_activo, mes) )";
  cadena = anadirMotor(cadena);
  ejecutar(cadena);

  cadena="update usuarios set privilegios='";
  cadena+="111111111111111111111111111111111111111111111111111111111111111111111111111111'";
        // 123456789012345678901234567890123456789012345678901234567890123456789012345678
  cadena+=" where codigo='admin'";
  ejecutar(cadena);

 ejecutar("update configuracion set version='2.9.7'");
}

// devuelve la clave de la factura
int basedatos::nuevacabecerafactura(QString serie,
                                    QString numero,
                                    QString cuenta,
                                    QDate fecha_fac,
                                    QDate fecha_op,
                                    bool contabilizado,
                                    bool contabilizable,
                                    bool con_ret,
                                    bool re,
                                    QString tipo_ret,
                                    QString retencion,
                                    QString tipo_doc,
                                    QString notas,
                                    QString pie1,
                                    QString pie2,
                                    QString pase_diario_cta
                                    )
{
    QString cad1="INSERT into facturas (serie,numero,cuenta, fecha,fecha_fac,fecha_op,"
        "contabilizado,contabilizable, con_ret, con_re, tipo_ret, retencion, "
        "tipo_doc, notas, pie1, pie2, pase_diario_cta) VALUES(";
    cad1 += "'"+serie.left(-1).replace("'","''") +"', ";
    cad1 += numero + ", '";
    cad1 += cuenta + "', '";
    cad1 += QDate::currentDate().toString("yyyy-MM-dd") + "','";
    cad1 += fecha_fac.toString("yyyy-MM-dd") + "','";
    cad1 += fecha_op.toString("yyyy-MM-dd") + "',";
    if ( cualControlador() == SQLITE ) { // contabilizado
       cad1 += contabilizado ? "1": "0" ;
      }
    else {
           cad1 += contabilizado ? "true": "false" ;
         }
    cad1+=", ";
          if ( cualControlador() == SQLITE ) { // contabilizado
             cad1 += contabilizable ? "1": "0" ;
            }
          else {
                 cad1 += contabilizable ? "true": "false" ;
               }
    cad1+=", ";
         if ( cualControlador() == SQLITE ) { // con_ret
               cad1 += con_ret ? "1": "0" ;
               }
            else {
                  cad1 += con_ret ? "true": "false" ;
                 }
    cad1+=", ";
    if ( cualControlador() == SQLITE ) { // con_re
          cad1 += re ? "1": "0" ;
          }
       else {
             cad1 += re ? "true": "false" ;
            }
cad1+=", ";
    if (tipo_ret.isEmpty()) tipo_ret="0";
    cad1+=tipo_ret.left(-1).replace("'","''");
    cad1+=", ";
    if (retencion.isEmpty()) retencion="0";
    cad1+=retencion.left(-1).replace("'","''");
    cad1+=", '";
    cad1+=tipo_doc.left(-1).replace("'","''");
    cad1+="', '";
    cad1+=notas.left(-1).replace("'","''");
    cad1+="', '";
    cad1+=pie1.left(-1).replace("'","''");
    cad1+="', '";
    cad1+=pie2.left(-1).replace("'","''");
    cad1+="', ";
    cad1+=pase_diario_cta.left(-1).replace("'","''");
    cad1 += ")";
    ejecutar(cad1);

    // averiguamos campo clave del registro recién insertado
    cad1 = "select clave from facturas where serie='";
    cad1 += serie;
    cad1 += "' and numero=";
    cad1 += numero;
    QSqlQuery query=ejecutar(cad1);
    if (query.isActive())
       if (query.next()) return query.value(0).toInt();

    return 0;

}


void basedatos::transac_fich_import()
{
   if (cualControlador() == POSTGRES)
      {
       QSqlDatabase::database().transaction();
       ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
       ejecutar("LOCK TABLE ejercicios IN ACCESS EXCLUSIVE MODE");
      }
}

void basedatos::fintransac_fich_import()
{
  if (cualControlador() == POSTGRES)
    QSqlDatabase::database().commit();
}



// devuelve la clave de la factura
int basedatos::modificacabecerafactura(QString serie,
                                    QString numero,
                                    QString cuenta,
                                    QDate fecha_fac,
                                    QDate fecha_op,
                                    bool contabilizado,
                                    bool contabilizable,
                                    bool con_ret,
                                    bool re,
                                    QString tipo_ret,
                                    QString retencion,
                                    QString tipo_doc,
                                    QString notas,
                                    QString pie1,
                                    QString pie2,
                                    QString pase_diario_cta
                                    )
{
   // (serie,numero,cuenta, fecha,fecha_fac,fecha_op,"
   //         "contabilizado,contabilizable, con_ret, re, tipo_ret, retencion, "
   //         "tipo_doc, notas, pie1, pie2, pase_diario_cta)

    QString cad1="update facturas set cuenta='";
    cad1 += cuenta + "', ";
    cad1 += "fecha='" + QDate::currentDate().toString("yyyy-MM-dd") + "', ";
    cad1 += "fecha_fac='" + fecha_fac.toString("yyyy-MM-dd") + "', ";
    cad1 += "fecha_op='" + fecha_op.toString("yyyy-MM-dd") + "', ";
    if ( cualControlador() == SQLITE ) { // contabilizado
       cad1 += contabilizado ? "contabilizado =1": "contabilizado =0" ;
      }
    else {
           cad1 += contabilizado ? "contabilizado =true": "contabilizado =false" ;
         }
    cad1+=", ";
    if ( cualControlador() == SQLITE ) { // contabilizable
       cad1 += contabilizable ? "contabilizable= 1": "contabilizable= 0" ;
            }
          else {
                 cad1 += contabilizable ? "contabilizable =true": "contabilizable =false";
               }
    cad1+=", ";
    if ( cualControlador() == SQLITE ) { // con_ret
         cad1 += con_ret ? "con_ret= 1": "con_ret= 0" ;
         }
            else {
                  cad1 += con_ret ? "con_ret= true": "con_ret= false" ;
                 }
    cad1+=", ";
    if ( cualControlador() == SQLITE ) { // re
         cad1 += re ? "con_re= 1": "con_re= 0" ;
         }
            else {
                  cad1 += re ? "con_re= true": "con_re= false" ;
                 }
    cad1+=", ";
    if (tipo_ret.isEmpty()) tipo_ret="0";
    cad1+="tipo_ret="+tipo_ret.left(-1).replace("'","''");
    cad1+=", ";
    if (retencion.isEmpty()) retencion="0";
    cad1+="retencion=" + retencion.left(-1).replace("'","''");
    cad1+=", ";
    cad1+="tipo_doc='" + tipo_doc.left(-1).replace("'","''");
    cad1+="', ";
    cad1+="notas='"+ notas.left(-1).replace("'","''");
    cad1+="', ";
    cad1+="pie1='" + pie1.left(-1).replace("'","''");
    cad1+="', ";
    cad1+="pie2='"+ pie2.left(-1).replace("'","''");
    cad1+="', ";
    cad1+="pase_diario_cta="+pase_diario_cta.left(-1).replace("'","''");
    cad1 += " where serie='";
    cad1 += serie.left(-1).replace("'","''");
    cad1 += "' and numero=";
    cad1 += numero;

    ejecutar(cad1);

    // averiguamos campo clave del registro recién modificado
    cad1 = "select clave from facturas where serie='";
    cad1 += serie.left(-1).replace("'","''");
    cad1 += "' and numero=";
    cad1 += numero;
    QSqlQuery query=ejecutar(cad1);
    if (query.isActive())
       if (query.next()) return query.value(0).toInt();

    return 0;

}


void basedatos::modificacabecerafactura_predef(
                                            QString codigo,
                                            bool contabilizable,
                                            bool con_ret,
                                            bool re,
                                            QString tipo_ret,
                                            QString tipo_doc,
                                            QString notas,
                                            QString pie1,
                                            QString pie2
                                            )

{
    QString cad1="update facturas_predef set ";
    if ( cualControlador() == SQLITE ) { // contabilizable
       cad1 += contabilizable ? "contabilizable= 1": "contabilizable= 0" ;
            }
          else {
                 cad1 += contabilizable ? "contabilizable =true": "contabilizable =false";
               }
    cad1+=", ";
    if ( cualControlador() == SQLITE ) { // con_ret
         cad1 += con_ret ? "con_ret= 1": "con_ret= 0" ;
         }
            else {
                  cad1 += con_ret ? "con_ret= true": "con_ret= false" ;
                 }
    cad1+=", ";
    if ( cualControlador() == SQLITE ) { // re
         cad1 += re ? "con_re= 1": "con_re= 0" ;
         }
            else {
                  cad1 += re ? "con_re= true": "con_re= false" ;
                 }
    cad1+=", ";
    if (tipo_ret.isEmpty()) tipo_ret="0";
    cad1+="tipo_ret="+tipo_ret.left(-1).replace("'","''");
    cad1+=", ";
    cad1+="tipo_doc='" + tipo_doc.left(-1).replace("'","''");
    cad1+="', ";
    cad1+="notas='"+ notas.left(-1).replace("'","''");
    cad1+="', ";
    cad1+="pie1='" + pie1.left(-1).replace("'","''");
    cad1+="', ";
    cad1+="pie2='"+ pie2.left(-1).replace("'","''");
    cad1+="' ";
    cad1 += " where codigo='";
    cad1 += codigo.left(-1).replace("'","''");
    cad1 += "' ";

    ejecutar(cad1);

}







int basedatos::clave_doc_fac(QString serie,
                              QString numero)
{
// averiguamos campo clave del registro de cabecera facturas
QString cad1 = "select clave from facturas where serie='";
cad1 += serie.left(-1).replace("'","''");
cad1 += "' and numero=";
cad1 += numero;
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next()) return query.value(0).toInt();

return 0;
}


QSqlQuery basedatos::select_cabecera_doc (QString serie, QString numero) {
    // (serie,numero,cuenta, fecha,fecha_fac,fecha_op,"
    //         "contabilizado,contabilizable, con_re, con_ret, tipo_ret, retencion, "
    //         "tipo_doc, notas, pie1, pie2, pase_diario_cta
    QString cadquery = "select serie, numero, cuenta, fecha, fecha_fac, fecha_op, "
                       "contabilizado,contabilizable, con_ret, con_re, tipo_ret, retencion, "
                       "tipo_doc, notas, pie1, pie2, pase_diario_cta, clave,cerrado from facturas"
                       " where serie='";
    cadquery+=serie.left(-1).replace("'","''");
    cadquery+="' and numero=";
    cadquery+=numero.left(-1).replace("'","''");
    return ejecutar(cadquery);
}

void basedatos::update_contabilizado_doc (QString serie, QString numero, QString pasediario) {
    // (serie,numero,cuenta, fecha,fecha_fac,fecha_op,"
    //         "contabilizado,contabilizable, con_re, con_ret, tipo_ret, retencion, "
    //         "tipo_doc, notas, pie1, pie2, pase_diario_cta
    QString cad1="update facturas set cerrado=";
    if ( cualControlador() == SQLITE ) cad1+=" 1";
       else cad1+="true";
    cad1+= ", contabilizado=";
    if ( cualControlador() == SQLITE ) cad1+=" 1";
       else cad1+="true";
    cad1+= ", pase_diario_cta=";
    cad1+= pasediario;
    cad1 += " where serie='";
    cad1 += serie.left(-1).replace("'","''");
    cad1 += "' and numero=";
    cad1 += numero;
    ejecutar(cad1);
}


bool basedatos::pase_en_facturas(QString pase, QString *serie, QString *numero)
{

QString cad1 = "select serie, numero from facturas where pase_diario_cta=";
cad1 += pase.left(-1).replace("'","''");
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next())
    {
       *serie=query.value(0).toString();
       *numero=query.value(1).toString();
       return true;
    }
return false;
}


int basedatos::tipo_operacion_doc(QString codigo)
{

QString cad1 = "select tipo_operacion from tipos_doc where codigo='";
cad1 += codigo.left(-1).replace("'","''");
cad1 += "'";
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next()) return query.value(0).toInt();

return 0;
}


bool basedatos::tipo_doc_rectificativo(QString codigo)
{

QString cad1 = "select rectificativo from tipos_doc where codigo='";
cad1 += codigo.left(-1).replace("'","''");
cad1 += "'";
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next()) return query.value(0).toBool();

return false;
}


bool basedatos::doc_contabilizable(QString serie,
                              QString numero)
{

QString cad1 = "select contabilizable from facturas where serie='";
cad1 += serie.left(-1).replace("'","''");
cad1 += "' and numero=";
cad1 += numero;
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next()) return query.value(0).toBool();

return false;
}

bool basedatos::existe_fact(QString serie,
                            QString numero)
{
    QString cad1 = "select clave from facturas where serie='";
    cad1 += serie.left(-1).replace("'","''");
    cad1 += "' and numero=";
    cad1 += numero;
    QSqlQuery query=ejecutar(cad1);
    if (query.isActive())
       if (query.next()) return true;

    return false;

}


bool basedatos::existe_fact_predef(QString codigo)
{
    QString cad1 = "select codigo from facturas_predef where codigo='";
    cad1 += codigo.left(-1).replace("'","''");
    cad1 += "'";
    QSqlQuery query=ejecutar(cad1);
    if (query.isActive())
       if (query.next()) return true;

    return false;

}


bool basedatos::doc_cerrado(QString serie,
                              QString numero)
{
// averiguamos campo cerrado del registro de cabecera facturas
QString cad1 = "select cerrado from facturas where serie='";
cad1 += serie.left(-1).replace("'","''");
cad1 += "' and numero=";
cad1 += numero;
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next()) return query.value(0).toBool();

return false;
}

bool basedatos::doc_contabilizado(QString serie,
                              QString numero)
{
// averiguamos campo contabilizado del registro de cabecera facturas
QString cad1 = "select contabilizado from facturas where serie='";
cad1 += serie.left(-1).replace("'","''");
cad1 += "' and numero=";
cad1 += numero;
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next()) return query.value(0).toBool();

return false;
}

void basedatos::cierra_doc(QString serie,
                           QString numero)
{
   // (serie,numero,cuenta, fecha,fecha_fac,fecha_op,"
   //         "contabilizado,contabilizable, con_ret, re, tipo_ret, retencion, "
   //         "tipo_doc, notas, pie1, pie2, pase_diario_cta)

    QString cad1="update facturas set cerrado=";
    if ( cualControlador() == SQLITE ) cad1+=" 1";
       else cad1+="true";
    cad1 += " where serie='";
    cad1 += serie.left(-1).replace("'","''");
    cad1 += "' and numero=";
    cad1 += numero;
    ejecutar(cad1);

    // averiguamos campo clave del registro recién modificado
    ejecutar(cad1);

}



QString basedatos::doc_cliente(QString serie,
                              QString numero)
{
// averiguamos campo cuenta cliente del registro de cabecera facturas
QString cad1 = "select cuenta from facturas where serie='";
cad1 += serie.left(-1).replace("'","''");
cad1 += "' and numero=";
cad1 += numero;
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next()) return query.value(0).toString();

return QString();
}


QString basedatos::cuenta_ref(QString ref)
{
QString cad1 = "select cuenta from referencias where codigo='";
cad1 += ref.left(-1).replace("'","''");
cad1 += "'";
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next()) return query.value(0).toString();

return QString();
}



QSqlQuery basedatos::select_cab_facturas (QString filtro) {
    QString cadena="SELECT f.serie,f.numero,f.cuenta, p.descripcion, f.fecha_fac,"
                      "f.contabilizado, f.cerrado, "
                      "f.pase_diario_cta, f.tipo_doc"
                      " from facturas f, plancontable p ";
    if (filtro.length()>0)
       {
        cadena += filtro;
        cadena += " and p.codigo=f.cuenta";
       }
       else cadena += "where p.codigo=f.cuenta";
    cadena+=" order by clave desc";
    return ejecutar(cadena);
}

  // lin_fact
  //  "clave bigint, "  // por este campo se relaciona con cabecera
  //  "codigo       varchar(80),"
  //  "descripcion  varchar(254),"
  //  "cantidad     varchar(40),"
  //  "precio       numeric(19,4),"
  //  "clave_iva    varchar(40),"
  //  "tipo_iva     numeric(8,4),"
  //  "tipo_re      numeric(8,4)";


void basedatos::borralineas_doc (int clave)
{
    QString cadnum;
    cadnum.setNum(clave);
    QString cadena="DELETE from lin_fact where clave=";
    cadena+=cadnum;
    ejecutar(cadena);
}

void basedatos::borralineas_fac_predef (QString codigo)
{
    QString cadena="DELETE from lin_fact_predef where cod_fact='";
    cadena+=codigo;
    cadena+="'";
    ejecutar(cadena);
}


void basedatos::borra_cabecera_doc (QString serie,
                                    QString numero)
{
    QString cadena="DELETE from facturas where serie='";
    cadena += serie.left(-1).replace("'","''");
    cadena += "' and numero=";
    cadena += numero;
    ejecutar(cadena);
}


void basedatos::insert_lin_doc(int clave, QString codigo, QString descrip,
                               QString cantidad, QString precio, QString clave_iva,
                               QString tipo_iva, QString tipo_re, QString dto)
{
    QString cadena="insert into lin_fact ("
    "clave, codigo, descripcion, cantidad, precio, clave_iva, tipo_iva, tipo_re, dto) values (";
    QString cadnum; cadnum.setNum(clave);
    cadena+=cadnum;
    cadena += ", '";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "', '";
    cadena += descrip.left(-1).replace("'","''");
    cadena += "', ";
    cadena += cantidad.isEmpty() ? "0" : cantidad.left(-1).replace("'","''");
    cadena += ", ";
    cadena += precio.isEmpty() ? "0" : precio.left(-1).replace("'","''");
    cadena += ", '";
    cadena += clave_iva.left(-1).replace("'","''");
    cadena += "', ";
    cadena += tipo_iva.isEmpty() ? "0" :tipo_iva.left(-1).replace("'","''");
    cadena += ", ";
    cadena += tipo_re.isEmpty() ? "0" : tipo_re.left(-1).replace("'","''");
    cadena += ", ";
    cadena += dto.isEmpty() ? "0" : dto;
    cadena += ")";

    ejecutar(cadena);
}


void basedatos::insert_lin_doc_predef(QString cod_fact, QString codigo, QString descrip,
                               QString cantidad, QString precio, QString clave_iva,
                               QString tipo_iva, QString tipo_re, QString dto)
{
    QString cadena="insert into lin_fact_predef ("
    "cod_fact, codigo, descripcion, cantidad, precio, clave_iva, tipo_iva, tipo_re, dto) "
    "values ('";
    cadena+=cod_fact;
    cadena += "', '";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "', '";
    cadena += descrip.left(-1).replace("'","''");
    cadena += "', ";
    cadena += cantidad.isEmpty() ? "0" : cantidad.left(-1).replace("'","''");
    cadena += ", ";
    cadena += precio.isEmpty() ? "0" : precio.left(-1).replace("'","''");
    cadena += ", '";
    cadena += clave_iva.left(-1).replace("'","''");
    cadena += "', ";
    cadena += tipo_iva.isEmpty() ? "0" :tipo_iva.left(-1).replace("'","''");
    cadena += ", ";
    cadena += tipo_re.isEmpty() ? "0" : tipo_re.left(-1).replace("'","''");
    cadena += ", ";
    cadena += dto.isEmpty() ? "0" : dto;
    cadena += ")";

    ejecutar(cadena);
}



QSqlQuery basedatos::select_lin_doc (int clave) {
    QString cadena="SELECT "
                   "clave, codigo, descripcion, cantidad, precio, "
                   "clave_iva, tipo_iva, tipo_re, dto"
                   " from lin_fact "
                   "where clave=";
    QString cadnum; cadnum.setNum(clave);
    cadena += cadnum;
    return ejecutar(cadena);
}


QSqlQuery basedatos::select_lin_doc_predef (QString cod_predef) {
    QString cadena="SELECT "
                   "cod_fact, codigo, descripcion, cantidad, precio, "
                   "clave_iva, tipo_iva, tipo_re, dto"
                   " from lin_fact_predef "
                   "where cod_fact='";
    cadena += cod_predef;
    cadena += "'";
    return ejecutar(cadena);
}



QSqlQuery basedatos::select_iva_lin_doc (int clave) {
    QString cadena="SELECT "
                   "sum(cantidad*precio*(100-dto)/100), "
                   "clave_iva, max(tipo_iva), max(tipo_re) "
                   " from lin_fact "
                   "where clave=";
    QString cadnum; cadnum.setNum(clave);
    cadena += cadnum;
    cadena += " group by clave_iva";

    return ejecutar(cadena);
}

void basedatos::introclave_op_retenciones()
{
    QString cadena="insert into clave_op_retenciones (codigo,descripcion) values ";
    cadena+=QObject::tr("('G','G - Rendimientos de actividades económicas: actividades profesionales')");
    ejecutar(cadena);
    cadena="insert into clave_op_retenciones (codigo,descripcion) values ";
    cadena+=QObject::tr("('H','H- Rendimientos de actividades económicas: actividades agrícolas,"
                        " ganaderas y forestales y actividades empresariales en estimación "
                        "objetiva (art.95.6 reglamento)')");
    ejecutar(cadena);

}

void basedatos::intro_provincias()
{
    QString cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('--','No asignada')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('01','Araba/Álava')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('02','Albacete')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('03','Alicante')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('04','Almería')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('05','Ávila')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('06','Badajoz')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('07','Illes Balears')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('08','Barcelona')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('09','Burgos')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('10','Cáceres')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('11','Cádiz')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('12','Castellón')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('13','Ciudad Real')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('14','Córdoba')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('15','Coruña')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('16','Cuenca')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('17','Girona')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('18','Granada')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('19','Guadalajara')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('20','Gipuzkoa')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('21','Huelva')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('22','Huesca')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('23','Jaén')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('24','León')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('25','Lleida')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('26','La Rioja')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('27','Lugo')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('28','Madrid')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('29','Málaga')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('30','Murcia')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('31','Navarra')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('32','Ourense')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('33','Asturias')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('34','Palencia')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('35','Las Palmas')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('36','Pontevedra')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('37','Salamanca')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('38','S.C. Tenerife')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('39','Cantabria')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('40','Segovia')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('41','Sevilla')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('42','Soria')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('43','Tarragona')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('44','Teruel')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('45','Toledo')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('46','Valencia')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('47','Valladolid')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('48','Bizkaia')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('49','Zamora')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('50','Zaragoza')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('51','Ceuta')");
    ejecutar(cadena);

    cadena="insert into provincias (codigo,descripcion) values ";
    cadena+=QObject::tr("('52','Melilla')");
    ejecutar(cadena);

}

void basedatos::introclaveexpedidas()
{
QString cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("(' ','  - Operación habitual')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('A','A - Asiento resumen de facturas')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('B','B - Asiento resumen de tiques')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('C','C - Factura con varios asientos(varios tipo impositivos)')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('D','D - Factura rectificativa')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('E','E - IVA devengado pendiente de emitir factura')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('G','G - Régimen especial de grupo de entidades en IVA o IGIC')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('H','H - Régimen especial de oro de inversión')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('I','I - Inversión del Sujeto pasivo (ISP)')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('J','J - Tiques')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('K','K - Rectificación anotaciones registrales')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('M','M - IVA facturado pendiente de devengar (emitida factura)')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('N','N - Facturación de las prestaciones de servicios de agencias de viaje que actúan como mediadoras en nombre y por cuenta ajena')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('O','O - Factura emitida en sustitución de tiques facturados y declarados.')");
ejecutar(cadena);
cadena="insert into clave_op_expedidas (codigo,descripcion) values ";
cadena+=QObject::tr("('Q','Q - Operaciones a las que se aplique el régimen especial de bienes usados, ...')");
ejecutar(cadena);

}

void basedatos::introclaverecibidas()
{
QString cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("(' ','  - Operación habitual')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('A','A - Asiento resumen de facturas')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('B','B - Asiento resumen de tiques')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('C','C - Factura con varios asientos(varios tipo impositivos)')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('D','D - Factura rectificativa')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('F','F - Adquisiciones realizadas por las agencias de viajes directamente en interés del viajero (Régimen especial de agencias de viajes)')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('G','G - Régimen especial de grupo de entidades en IVA o IGIC')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('H','H - Régimen especial de oro de inversión')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('I','I - Inversión del Sujeto pasivo (ISP)')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('J','J - Tiques')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('K','K - Rectificación anotaciones registrales')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('L','L - Adquisiciones a comerciantes minoristas del IGIC')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('P','P - Adquisiciones intracomunitarias de bienes.')"); ejecutar(cadena);
cadena="insert into clave_op_recibidas (codigo,descripcion) values ";
cadena+=QObject::tr("('Q','Q - Operaciones a las que se aplique el régimen especial de bienes usados, ...')"); ejecutar(cadena);
}

void basedatos::introclaveidfiscal()
{
 // QString cadena="insert into claveidfiscal (codigo,descripcion) values ";
 //   cadena+=QObject::tr("('0','          ')"); ejecutar(cadena);
QString cadena="insert into claveidfiscal (codigo,descripcion) values ";
    cadena+=QObject::tr("('1','NIF')"); ejecutar(cadena);
cadena="insert into claveidfiscal (codigo,descripcion) values ";
    cadena+=QObject::tr("('2','NIF/IVA (NIF operador intracomunitario)')"); ejecutar(cadena);
cadena="insert into claveidfiscal (codigo,descripcion) values ";
    cadena+=QObject::tr("('3','Pasaporte')"); ejecutar(cadena);
cadena="insert into claveidfiscal (codigo,descripcion) values ";
    cadena+=QObject::tr("('4','Documento Oficial de identificación expedido por el país o territorio de residencia')");
    ejecutar(cadena);
cadena="insert into claveidfiscal (codigo,descripcion) values ";
    cadena+=QObject::tr("('5','Certificado de residencia fiscal')"); ejecutar(cadena);
cadena="insert into claveidfiscal (codigo,descripcion) values ";
    cadena+=QObject::tr("('6','Otro documento probatorio')"); ejecutar(cadena);
}

void basedatos::intro_conceptos_evpn()
{
  QString cadena="insert into concepto_evpn_pymes  (codigo,descripcion) values "
    ; cadena+=QObject::tr("('1','Resultado de la cuenta de pérdidas y ganancias')");
  ejecutar(cadena);

  cadena="insert into concepto_evpn_pymes  (codigo,descripcion) values "
    ; cadena+=QObject::tr("('2','Ingresos fiscales a distribuir en varios ejercicios')");
  ejecutar(cadena);

  cadena="insert into concepto_evpn_pymes  (codigo,descripcion) values "
    ; cadena+=QObject::tr("('3','Otros ingresos y gastos reconocidos en patrimonio neto')");
  ejecutar(cadena);

  cadena="insert into concepto_evpn_pymes  (codigo,descripcion) values "
    ; cadena+=QObject::tr("('4','Aumentos de capital')");
  ejecutar(cadena);
  cadena="insert into concepto_evpn_pymes  (codigo,descripcion) values "
    ; cadena+=QObject::tr("('5','Reducciones de capital')");
  ejecutar(cadena);
  cadena="insert into concepto_evpn_pymes  (codigo,descripcion) values "
    ; cadena+=QObject::tr("('6','Otras operaciones con socios o propietarios')");
  ejecutar(cadena);
  cadena="insert into concepto_evpn_pymes  (codigo,descripcion) values "
    ; cadena+=QObject::tr("('7','Movimientos Reserva Revalorización')");
  ejecutar(cadena);
  cadena="insert into concepto_evpn_pymes  (codigo,descripcion) values "
    ; cadena+=QObject::tr("('8','Otras variaciones del patrimonio neto')");
  ejecutar(cadena);

}

/* cadena = "CREATE TABLE cols_evpn_pymes ("
         "codigo         varchar(40),"
         "descripcion    varchar(254),"
         "cuentas    varchar(254),"
         "PRIMARY KEY (codigo) )"; */

void basedatos::intro_cols_evpn_pymes()
{
    QString cadena="insert into cols_evpn_pymes  (codigo,descripcion,cuentas) values ";
    cadena+="('1',"+QObject::tr("'Capital Escriturado'")+ ",'100,101,102')";
    ejecutar(cadena);

    cadena="insert into cols_evpn_pymes  (codigo,descripcion,cuentas) values ";
    cadena+="('2',"+QObject::tr("'Capital no exigido'")+ ",'103')";
    ejecutar(cadena);

    cadena="insert into cols_evpn_pymes (codigo,descripcion,cuentas) values ";
    cadena+="('3',"+QObject::tr("'Prima de emisión'")+ ",'110')";
    ejecutar(cadena);

    cadena="insert into cols_evpn_pymes (codigo,descripcion,cuentas) values ";
    cadena+="('4',"+QObject::tr("'Reservas'")+ ",'112,113,114,115,118,119')";
    ejecutar(cadena);

    cadena="insert into cols_evpn_pymes (codigo,descripcion,cuentas) values ";
    cadena+="('5',"+QObject::tr("'Acciones y participaciones en patrimonio propias'")+ ",'108,109')";
    ejecutar(cadena);

    cadena="insert into cols_evpn_pymes  (codigo,descripcion,cuentas) values ";
    cadena+="('6',"+QObject::tr("'Resultados de ejercicios anteriores'")+ ",'120,121')";
    ejecutar(cadena);

    cadena="insert into cols_evpn_pymes  (codigo,descripcion,cuentas) values ";
    cadena+="('7',"+QObject::tr("'Otras aportaciones de socios'")+ ",'104')";
    ejecutar(cadena);

    cadena="insert into cols_evpn_pymes  (codigo,descripcion,cuentas) values ";
    cadena+="('8',"+QObject::tr("'Resultado del ejercicio'")+ ",'129')";
    ejecutar(cadena);

    cadena="insert into cols_evpn_pymes  (codigo,descripcion,cuentas) values ";
    cadena+="('9',"+QObject::tr("'Dividendo a cuenta'")+ ",'557')";
    ejecutar(cadena);

    cadena="insert into cols_evpn_pymes  (codigo,descripcion,cuentas) values ";
    cadena+="('10',"+QObject::tr("'Ajustes de patrimonio neto'")+ ",'133,134,135,136,137')";
    ejecutar(cadena);

    cadena="insert into cols_evpn_pymes  (codigo,descripcion,cuentas) values ";
    cadena+="('11',"+QObject::tr("'Subvenciones, donaciones y legados recibidos'")+ ",'130,131,132')";
    ejecutar(cadena);

}





void basedatos::intropaises()
{
QString cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AF','AF AFGANISTÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AL','AL ALBANIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('DE','DE ALEMANIA (Incluida la Isla de Helgoland)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AD','AD ANDORRA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AO','AO ANGOLA (incluido Cabinda)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AI','AI ANGUILA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AQ','AQ ANTÁRTIDA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AG','AG ANTIGUA Y BARBUDA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AN','AN ANTILLAS NEERLANDESAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SA','SA ARABIA SAUDÍ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('DZ','DZ ARGELIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AR','AR ARGENTINA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AM','AM ARMENIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AW','AW ARUBA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AU','AU AUSTRALIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AT','AT AUSTRIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AZ','AZ AZERBAIYÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BS','BS BAHAMAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BH','BH BAHRÉIN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BD','BD BANGLADESH')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BB','BB BARBADOS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BE','BE BÉLGICA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BZ','BZ BELICE')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BJ','BJ BENÍN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BM','BM BERMUDAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BY','BY BIELORRUSIA (BELARÚS)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BO','BO BOLIVIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BA','BA BOSNIA-HERZEGOVINA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BW','BW BOTSUANA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BV','BV BOUVET, ISLA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BR','BR BRASIL')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BN','BN BRUNÉI (BRUNÉI DARUSSALAM)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BG','BG BULGARIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BF','BF BURKINA FASO (Alto Volta)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BI','BI BURUNDI')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('BT','BT BUTÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CV','CV CABO VERDE, REPÚBLICA DE')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('KY','KY CAIMÁN, ISLAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('KH','KH CAMBOYA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CM','CM CAMERÚN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CA','CA CANADÁ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CF','CF CENTROAFRICANA, REPÚBLICA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CC','CC COCOS, ISLA DE (KEELING)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CO','CO COLOMBIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('KM','KM COMORAS (Gran Comora, Anjouan y Mohéli) ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CG','CG CONGO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CD','CD CONGO, REPÚBLICA DEMOCRÁTICA DEL (Zaire)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CK','CK COOK, ISLAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('KP','KP COREA DEL NORTE (República Popular Democrática de Corea)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('KR','KR COREA DEL SUR (República de Corea)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CI','CI COSTA DE MARFIL')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CR','CR COSTA RICA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('HR','HR CROACIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CU','CU CUBA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TD','TD CHAD')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CZ','CZ CHECA, REPÚBLICA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CL','CL CHILE')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CN','CN CHINA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CY','CY CHIPRE')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('DK','DK DINAMARCA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('DM','DM DOMINICA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('DO','DO DOMINICANA, REPÚBLICA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('EC','EC ECUADOR (incluidas las Islas Galápagos) ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('EG','EG EGIPTO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AE','AE EMIRATOS ÁRABES UNIDOS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('ER','ER ERITREA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SK','SK ESLOVAQUIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SI','SI ESLOVENIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('ES','ES ESPAÑA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('US','US ESTADOS UNIDOS DE AMÉRICA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('EE','EE ESTONIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('ET','ET ETIOPÍA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('FO','FO FEROE, ISLAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PH','PH FILIPINAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('FI','FI FINLANDIA (Incluidas las Islas Aland)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('FJ','FJ FIYI')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('FR','FR FRANCIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GA','GA GABÓN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GM','GM GAMBIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GE','GE GEORGIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GS','GS GEORGIA DEL SUR Y LAS ISLAS SANDWICH DEL SUR')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GH','GH GHANA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GI','GI GIBRALTAR')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GD','GD GRANADA (incluidas las Islas Granadinas del Sur)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GR','GR GRECIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GL','GL GROENLANDIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GU','GU GUAM')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GT','GT GUATEMALA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GG','GG GUERNESEY (isla anglonormanda del Canal)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GN','GN GUINEA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GQ','GQ GUINEA ECUATORIAL')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GW','GW GUINEA-BISSAU')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GY','GY GUYANA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('HT','HT HAITÍ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('HM','HM HEARD Y MCDONALD, ISLAS ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('HN','HN HONDURAS (incluidas Islas del Cisne)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('HK','HK HONG-KONG')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('HU','HU HUNGRÍA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('IN','IN INDIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('ID','ID INDONESIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('IR','IR IRÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('IQ','IQ IRAQ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('IE','IE IRLANDA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('IM','IM ISLA DE MAN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('IS','IS ISLANDIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('IL','IL ISRAEL')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('IT','IT ITALIA (Incluido Livigno)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('JM','JM JAMAICA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('JP','JP JAPÓN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('JE','JE JERSEY (isla anglonormanda del Canal)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('JO','JO JORDANIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('KZ','KZ KAZAJSTÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('KE','KE KENIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('KG','KG KIRGUISTÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('KI','KI KIRIBATI')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('KW','KW KUWAIT')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('LA','LA LAOS (LAO)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('LS','LS LESOTHO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('LV','LV LETONIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('LB','LB LÍBANO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('LR','LR LIBERIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('LY','LY LIBIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('LI','LI LIECHTENSTEIN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('LT','LT LITUANIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('LU','LU LUXEMBURGO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('XG','XG LUXEMBURGO (Rtas. Soc. párrafo 1 prot. anexo Conv. doble imp.')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MO','MO MACAO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MK','MK MACEDONIA (Antigua República Yugoslava) ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MG','MG MADAGASCAR')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MY','MY MALASIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MW','MW MALAWI')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MV','MV MALDIVAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('ML','ML MALI')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MT','MT MALTA (Incluidos Gozo y Comino)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('FK','FK MALVINAS, ISLAS (FALKLANDS)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MP','MP MARIANAS DEL NORTE, ISLAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MA','MA MARRUECOS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MH','MH MARSHALL, ISLAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MU','MU MAURICIO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MR','MR MAURITANIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('YT','YT MAYOTTE (Gran Tierra y Pamandzi)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('UM','UM MENORES ALEJADAS DE LOS EE.UU, ISLAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MX','MX MÉXICO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('FM','FM MICRONESIA, FEDERACIÓN DE ESTADOS DE')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MD','MD MOLDAVIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MC','MC MÓNACO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MN','MN MONGOLIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('ME','ME MONTENEGRO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MS','MS MONTSERRAT')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MZ','MZ MOZAMBIQUE')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('MM','MM MYANMAR (Antigua Birmania)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NA','NA NAMIBIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NR','NR NAURU')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CX','CX NAVIDAD, ISLA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NP','NP NEPAL')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NI','NI NICARAGUA (incluidas las Islas del Maíz)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NE','NE NÍGER')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NG','NG NIGERIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NU','NU NIUE, ISLA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NF','NF NORFOLK, ISLA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NO','NO NORUEGA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NC','NC NUEVA CALEDONIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NZ','NZ NUEVA ZELANDA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('IO','IO OCÉANO ÍNDICO, TERRITORIO BRITÁNICO DEL ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('OM','OM OMÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('NL','NL PAÍSES BAJOS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PK','PK PAKISTÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PW','PW PALAU')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PA','PA PANAMÁ (incluida la antigua Zona del Canal) ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PG','PG PAPÚA NUEVA GUINEA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PY','PY PARAGUAY')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PE','PE PERÚ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PN','PN PITCAIRN (incluidas las Islas Henderson, Ducie y Oeno)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PF','PF POLINESIA FRANCESA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PL','PL POLONIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PT','PT PORTUGAL')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PR','PR PUERTO RICO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('QA','QA QATAR')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('GB','GB REINO UNIDO (Gran Bretaña e Irlanda del Norte)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('RW','RW RUANDA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('RO','RO RUMANÍA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('RU','RU RUSIA (FEDERACIÓN DE)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SB','SB SALOMÓN, ISLAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SV','SV SALVADOR, EL')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('WS','WS SAMOA (Samoa Occidental)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('AS','AS SAMOA AMERICANA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('KN','KN SAN CRISTÓBAL Y NIEVES (Saint Kitts y Nevis)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SM','SM SAN MARINO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PM','PM SAN PEDRO Y MIQUELÓN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('VC','VC SAN VICENTE Y LAS GRANADINAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SH','SH SANTA ELENA (incl.Isla Ascensión y Archip. Tristán Da Cuhna ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('LC','LC SANTA LUCÍA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('ST','ST SANTO TOMÉ Y PRÍNCIPE')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SN','SN SENEGAL')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SR','SR SERBIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SC','SC SEYCHELLES')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SL','SL SIERRA LEONA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SG','SG SINGAPUR')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SY','SY SIRIA (REPÚBLICA ÁRABE) ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SO','SO SOMALIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('LK','LK SRI LANKA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SZ','SZ SUAZILANDIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('ZA','ZA SUDÁFRICA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SD','SD SUDÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('SE','SE SUECIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('CH','CH SUIZA')"); ejecutar(cadena);
// cadena="insert into codigopais (codigo,descripcion) values " // El código SR está duplicado
// ; cadena+=QObject::tr("('SR','SR SURINAM')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TH','TH TAILANDIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TW','TW TAIWÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TZ','TZ TANZANIA (REPÚBLICA UNIDA DE)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TJ','TJ TAYIKISTÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('PS','PS TERRITORIO PALESTINO OCUPADO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TF','TF TIERRAS AUSTRALES FRANCESAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TP','TP TIMOR ESTE')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TG','TG TOGO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TK','TK TOKELAU, ISLAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TO','TO TONGA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TT','TT TRINIDAD Y TOBAGO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TN','TN TÚNEZ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TC','TC TURCAS Y CAICOS, ISLAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TM','TM TURKMENISTÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TR','TR TURQUÍA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('TV','TV TUVALU')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('UA','UA UCRANIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('UG','UG UGANDA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('UY','UY URUGUAY')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('UZ','UZ UZBEKISTÁN')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('VU','VU VANUATU')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('VA','VA VATICANO, CIUDAD DEL (Santa Sede)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('VE','VE VENEZUELA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('VN','VN VIETNAM')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('VG','VG VÍRGENES BRITÁNICAS, ISLAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('VI','VI VÍRGENES DE LOS EE.UU, ISLAS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('WF','WF WALLIS Y FUTUNA, ISLAS (incluida la Isla Alofi)')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('YE','YE YEMEN (Yemen del Norte y Yemen del Sur) ')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('DJ','DJ YIBUTI')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('ZM','ZM ZAMBIA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('ZW','ZW ZIMBABUE')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('QU','QU OTROS PAÍSES O TERRITORIOS NO RELACIONADOS')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('XB','XB BANCO CENTRAL EUROPEO')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('XU','XU INSTITUCIONES DE LA UNIÓN EUROPEA')"); ejecutar(cadena);
cadena="insert into codigopais (codigo,descripcion) values "
; cadena+=QObject::tr("('XN','XN ORG.INTER.DISTINTOS DE LAS INST. DE LA UE Y DEL BCO.CENT.EUR.')"); ejecutar(cadena);

}



// Devuelve la lista de los drivers
QStringList basedatos::drivers () {
    return QSqlDatabase::drivers();
}


// devuelve el saldo hasta la fecha
double basedatos::saldo_hasta_fecha (QString cuenta,QDate fecha) {
    QString cadquery = "select sum(debe-haber) from diario where ";
    if (cualControlador() == SQLITE) {
            cadquery += "cuenta like '"+ cuenta.left(-1).replace("'","''") +"%'";
        }
        else {
    	    cadquery += " position('";
    	    cadquery += cuenta.left(-1).replace("'","''");
    	    cadquery += "' in cuenta)=1";
        }
    QDate fechainicio=selectAperturaejercicios(selectEjerciciodelafecha(fecha));
    cadquery += " and fecha>='";
    cadquery+=fechainicio.toString("yyyy-MM-dd");
    cadquery += "' and fecha<'";
    cadquery+=fecha.toString("yyyy-MM-dd");
    cadquery+="' and diario != '";
    cadquery+= diario_cierre();
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDouble();
    }
    return 0;
}



//////////////////////////////
// Privadas
//////////////////////////////

// Ejecuta una sentencia sql
QSqlQuery basedatos::ejecutar (QString cadena) {
    QSqlQuery query;
    if ( !query.exec(cadena) ) {
        QMessageBox::critical ( 0 , QObject::tr("Base de datos") ,
                                QObject::tr("Error al ejecutar la sentencia:\n") +
                                cadena + "\n" + query.lastError().databaseText()
                                +"\n"+ QObject::tr("Controlador: ") + controlador() );
        error_consulta=true;
    }
    return query;
}

// Ejecuta una sentencia sql en otra conexión no default
QSqlQuery basedatos::ejecutar (QString cadena, QString db) {
    QSqlDatabase base = QSqlDatabase::database (db);
    // QMessageBox::information ( 0 , QObject::tr("Base de datos") , cadena );
    QSqlQuery query(base);
    if ( !query.exec(cadena) ) {
        QMessageBox::critical ( 0 , QObject::tr("Base de datos") ,
                                QObject::tr("Error al ejecutar la sentencia:\n")
                                + cadena + "\n" + query.lastError().databaseText()
                                +"\n"+ QObject::tr("Controlador: ") + base.driverName() );
        error_consulta=true;
    }
    return query;
}


// Comprueba la versión de las tablas
bool basedatos::comprobarVersion () {
    // comprobamos versión correcta
    if (versionbd()!=VERSION) {
        if (versionbd()!="2.0" && versionbd()!="2.1" && versionbd()!="2.2" && versionbd()!="2.2.1"
            && versionbd()!="2.3" && versionbd()!="2.4" && versionbd()!="2.5"
            && versionbd()!="2.5.1" && versionbd()!="2.6" && versionbd()!="2.7"
            && versionbd() !="2.8" && versionbd() !="2.9" && versionbd() !="2.9.3"
            && versionbd() !="2.9.3E" && versionbd() !="2.9.4"
            && versionbd() !="2.9.5" && versionbd() !="2.9.6"
            && versionbd() !="2.9.7" && versionbd() !="2.9.7.3"
            && versionbd() !="2.9.7.4" && versionbd() !="2.9.7.5"
            && versionbd() !="2.9.7.6" && versionbd() !="2.9.7.7"
            && versionbd() !="2.9.7.8" && versionbd() !="2.9.7.9"
            && versionbd() !="2.9.8" && versionbd() !="2.9.8.1"
            && versionbd() !="2.9.8.2" && versionbd() !="2.9.8.3"
            && versionbd() !="2.9.8.4") {
            QMessageBox::warning( 0 , QObject::tr("Apertura de base de datos"),
                QObject::tr("ERROR: versión de tablas incorrecta"));
            QSqlDatabase::database().close();
            return false;
        }
        else {
            if (QMessageBox::question(
                0 ,
                QObject::tr("ACTUALIZAR TABLAS"),
                QObject::tr("Las tablas pertenecen a la versión '") + versionbd() + QObject::tr("' ¿ Desea actualizarlas ?"),
                QObject::tr("&Sí"), QObject::tr("&No"),
                QString::null, 0, 1 ) ==0 ) {
                if (versionbd()=="2.0") actualizade20();
                if (versionbd()=="2.1") actualizade21();
                if (versionbd()=="2.2") actualizade22();
                if (versionbd()=="2.2.1") actualizade221();
                if (versionbd()=="2.3") actualizade23();
                if (versionbd()=="2.4") actualizade24();
                if (versionbd()=="2.5") actualizade25();
                if (versionbd()=="2.5.1") actualizade251();
                if (versionbd()=="2.6") actualizade26();
                if (versionbd()=="2.7") actualizade27();
                if (versionbd()=="2.8") actualizade28();
                if (versionbd()=="2.9") actualizade29();
                if (versionbd()=="2.9.3") actualizade293();
                if (versionbd()=="2.9.3E") actualizade293E();
                if (versionbd()=="2.9.4") actualizade294();
                if (versionbd()=="2.9.5") actualizade295();
                if (versionbd()=="2.9.6") actualizade296();
                if (versionbd()=="2.9.7") actualizade297();
                if (versionbd()=="2.9.7.3") actualizade2973();
                if (versionbd()=="2.9.7.4") actualizade2974();
                if (versionbd()=="2.9.7.5") actualizade2975();
                if (versionbd()=="2.9.7.6") actualizade2976();
                if (versionbd()=="2.9.7.7") actualizade2977();
                if (versionbd()=="2.9.7.8") actualizade2978();
                if (versionbd()=="2.9.7.9") actualizade2979();
                if (versionbd()=="2.9.8") actualizade298();
                if (versionbd()=="2.9.8.1") actualizade2981();
                if (versionbd()=="2.9.8.2") actualizade2982();
                if (versionbd()=="2.9.8.3") actualizade2983();
                actualizade2984();
            }
            else {
                QSqlDatabase::database().close();
                return false;
            }
        }
    }
    return true;
}

// Añade el tipo de motor que se utilizará en la base de datos
QString basedatos::anadirMotor (QString cadena, QString db) {
    if (db.isEmpty())
       {
        if (cualControlador() == MYSQL) return cadena + " ENGINE = InnoDB";
         else return cadena;
       }
       else
           {
            if (cualControlador(db) == MYSQL) return cadena + " ENGINE = InnoDB";
             else return cadena;
           }
}


// Añade el tipo de motor que se utilizará en la base de datos
QString basedatos::anadirMotor (QString cadena) {
        if (cualControlador() == MYSQL) return cadena + " ENGINE = InnoDB";
         else return cadena;
}

// Crea las database para MYSQL y POSTGRES
bool basedatos::crearDatabase (QString controlador, QString nombre, QString db) {
    QString cadena;

    cadena = "create database ";
    cadena += nombre;

    if (controlador=="QMYSQL3" || controlador=="QMYSQL")
    {
        cadena += " default character set utf8";
    }
    if ((controlador=="QPSQL7") || (controlador=="QPSQL") )
    {
        cadena += " encoding='UNICODE'";
    }

    return ejecutar(cadena,db).isActive();

}

void basedatos::haz_venci_procesado(QString numvenci, QString pase_diario_vencimiento)
{
  QString cadquery="update vencimientos set ";
  if(cualControlador() == SQLITE)
      cadquery+="pendiente=0, pase_diario_vencimiento=";
    else
       cadquery+="pendiente=FALSE, pase_diario_vencimiento=";
  cadquery+=pase_diario_vencimiento;
  cadquery+=" where num=";
  cadquery+=numvenci;
  ejecutar(cadquery);
}

// comprueba si el asiento posee pases vinculados con la tabla de vencimientos
bool basedatos::asiento_con_pases_vencimientos(QString elasiento, QString ejercicio)
{
 QString cadena="select pase from diario where asiento=";
 cadena+=elasiento;
 cadena+=" and ejercicio='";
 cadena+=ejercicio.left(-1).replace("'","''");
 cadena+="'";
 cadena+=" and pase in (select pase_diario_operacion from vencimientos)";
 QSqlQuery query=ejecutar(cadena);
 if (query.isActive())
    if (query.next()) return true;

 cadena="select pase from diario where asiento=";
 cadena+=elasiento;
 cadena+=" and ejercicio='";
 cadena+=ejercicio.left(-1).replace("'","''");
 cadena+="'";
 cadena+=" and pase in (select pase_diario_vencimiento from vencimientos)";
 query=ejecutar(cadena);
 if (query.isActive())
    if (query.next()) return true;

 return false;
}



// comprueba si el asiento posee pases vinculados con la tabla de facturas
bool basedatos::asiento_con_pases_factura(QString elasiento, QString ejercicio)
{
 QString cadena="select pase from diario where asiento=";
 cadena+=elasiento;
 cadena+=" and ejercicio='";
 cadena+=ejercicio.left(-1).replace("'","''");
 cadena+="'";
 cadena+=" and pase in (select pase_diario_cta from facturas)";
 QSqlQuery query=ejecutar(cadena);
 if (query.isActive())
    if (query.next()) return true;

 return false;
}

bool basedatos::asiento_con_enlace(QString elasiento, QString ejercicio)
{
 QString cadena="select enlace from diario where asiento=";
 cadena+=elasiento;
 cadena+=" and ejercicio='";
 cadena+=ejercicio.left(-1).replace("'","''");
 cadena+="'";
 QSqlQuery query=ejecutar(cadena);
 if (query.isActive())
    while (query.next())
       {
         if (query.value(0).toBool()) return true;
       }
 return false;
}


int basedatos::anchocuentas()
{
    QString cadquery = "SELECT anchocuentas from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

int basedatos::anchocuentas(QString db)
{
    QString cadquery = "SELECT anchocuentas from configuracion";
    QSqlQuery query = ejecutar(cadquery,db);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}

bool basedatos::cod_longitud_variable(QString db)
{
     return anchocuentas(db)==0;
}

bool basedatos::cod_longitud_variable()
{
     return anchocuentas()==0;
}

bool basedatos::codsubdivis2db (QString qcodigo,QString db) {
    // evaluamos si el código suministrado posee subcódigos 
    // que no sean subcuentas
    QString cadquery = "SELECT codigo from plancontable where ";

    if (cualControlador(db) == SQLITE) {
        cadquery += "length(codigo) > ";
        cadquery += "length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "') and substr(codigo,1,length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'))='";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'";
    }
    else {
        cadquery += "char_length(codigo) > ";
        cadquery += "char_length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "') and substring(codigo from 1 for char_length('";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'))='";
        cadquery += qcodigo.left(-1).replace("'","''");
        cadquery += "'";
    }

    if (!cod_longitud_variable(db))
    {
        if ( cualControlador(db) == SQLITE ) {
            cadquery += " and length(codigo)<";
            cadquery += QString::number( anchocuentas(db) );
        }
        else {
            cadquery += " and char_length(codigo)<";
            QString cadnum;
            cadnum.setNum(anchocuentas(db));
            cadquery += cadnum;
        }
    }
    else {
            cadquery += " and not auxiliar";
    }

    QSqlQuery query = ejecutar(cadquery,db);
    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;
}


QSqlQuery basedatos::select_codigo_descrip_no_aux_2db (QString db) {
    QString cadena="select codigo,descripcion from plancontable where ";
    if (! cod_longitud_variable(db))
       {
        if (cualControlador(db) == SQLITE) { cadena += "length(codigo)<"; }
            else { cadena += "char_length(codigo)<"; }
        QString cadnum;
        cadnum.setNum(anchocuentas(db));
        cadena += cadnum;
       }
      else 
    {
        cadena += "not auxiliar";
    }
    cadena += " order by codigo";
    return ejecutar(cadena,db);
}



QSqlQuery basedatos::select_codigo_descrip_aux_2db (QString db)
{
    QString cadena="select codigo,descripcion from plancontable where ";
    if (! cod_longitud_variable(db))
       {
        if (cualControlador(db) == SQLITE) { cadena += "length(codigo)="; }
            else { cadena += "char_length(codigo)="; }
        QString cadnum;
        cadnum.setNum(anchocuentas(db));
        cadena += cadnum;
       }
      else 
    {
        cadena += "auxiliar";
    }
    cadena += " order by codigo";
    return ejecutar(cadena,db);
}


bool basedatos::posee_cuenta_aux_2db (QString cta,QString db)
{
    QString cadena="select codigo,descripcion from plancontable where ";
    if (! cod_longitud_variable(db))
       {
        if (cualControlador(db) == SQLITE) { cadena += "length(codigo)="; }
            else { cadena += "char_length(codigo)="; }
        QString cadnum;
        cadnum.setNum(anchocuentas(db));
        cadena += cadnum;
       }
      else 
    {
        cadena += "auxiliar";
    }

   if (cualControlador(db) == SQLITE) {
        cadena += " and substr(codigo,1,length('";
        cadena += cta.left(-1).replace("'","''");
        cadena += "'))='";
        cadena += cta.left(-1).replace("'","''");
        cadena += "'";
    }
    else {
        cadena += " and substring(codigo from 1 for char_length('";
        cadena += cta.left(-1).replace("'","''");
        cadena += "'))='";
        cadena += cta.left(-1).replace("'","''");
        cadena += "'";
    }

    QSqlQuery query = ejecutar(cadena,db);

    if ( (query.isActive()) && (query.first()) )
    {
        return true;
    }
    return false;

}


// se utiliza para insertar información en la contabilidad consolidada
void basedatos::insert_diario_consolidacion (QString cadnumasiento, qlonglong pase,
                                             QDate fecha, QString cuenta, double debe,
                                             double haber, QString concepto,
                                             QString documento,
                                             QString diario, QString usuario,
                                             QString ejercicio) {
    QString cad1="INSERT into diario (asiento,pase,fecha,cuenta,debe,haber,concepto,"
        "documento,diario,usuario,ci, ejercicio) VALUES(";
    cad1 += cadnumasiento +",";
    QString cadnumpase;
    cadnumpase.setNum(pase);
    cad1 += cadnumpase +",'";
    cad1 += fecha.toString("yyyy-MM-dd") + "','";
    cad1 += cuenta;
    cad1 +="',";
    QString cadimporte;
    cadimporte.setNum(debe,'f',2);
    cad1 += cadimporte;
    cad1 += ",";
    cadimporte.setNum(haber,'f',2);
    cad1 += cadimporte;
    cad1 += ",'";
    cad1 += concepto.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += documento.left(-1).replace("'","''");
    cad1 += "','";
    cad1+=diario.left(-1).replace("'","''");
    cad1+="','";
    cad1+=usuario.left(-1).replace("'","''");
    cad1 += "','','";
    cad1 += ejercicio;
    cad1 += "')";
    ejecutar(cad1);
}


//
qlonglong basedatos::selectMaxpase_origen_entrefechas (QString cx, QDate inicio, QDate fin) {
    QString cadena = "select max(apunte_origen) from diario where fecha<='";
    cadena += fin.toString("yyyy-MM-dd");
    cadena += "' and fecha>='";
    cadena += inicio.toString("yyyy-MM-dd");
    cadena += "'";

    if (cualControlador() == SQLITE)
       {
        cadena+=" and cuenta like '%";
        cadena+=cx.left(-1).replace("'","''");
        cadena+="%'";
       }
     else
         {
          cadena+=" and position('";
          cadena+=cx.left(-1).replace("'","''");
          cadena+="' in upper(cuenta))>0";
         }

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) ) {
        return query.value(0).toLongLong();
    }
    return 0;
}

bool basedatos::ejercicio_vacio(QString ejercicio, QString cx)
{
  QString cadena = "select cuenta from diario where ejercicio='";
  cadena+=ejercicio;
  cadena+="'";

  if (cualControlador() == SQLITE)
     {
      cadena+=" and cuenta like '%";
      cadena+=cx.left(-1).replace("'","''");
      cadena+="%'";
     }
   else
       {
        cadena+=" and position('";
        cadena+=cx.left(-1).replace("'","''");
        cadena+="' in upper(descripcion))>0";
       }

  QSqlQuery query = ejecutar(cadena);
  if ( (query.isActive()) && (query.next()) ) return false;
  return true;
}

// se utiliza para insertar información en la contabilidad consolidada sincronizada
void basedatos::insert_diario_consolidacion_conex (QString cadnumasiento, qlonglong pase,
                                             QDate fecha, QString cuenta, double debe,
                                             double haber, QString concepto,
                                             QString documento,
                                             QString diario, QString usuario,
                                             QString ejercicio, qlonglong pase_origen) {
    QString cad1="INSERT into diario (asiento,pase,fecha,cuenta,debe,haber,concepto,"
        "documento,diario,usuario,ci, ejercicio, apunte_origen) VALUES(";
    cad1 += cadnumasiento +",";
    QString cadnumpase;
    cadnumpase.setNum(pase);
    cad1 += cadnumpase +",'";
    cad1 += fecha.toString("yyyy-MM-dd") + "','";
    cad1 += cuenta;
    cad1 +="',";
    QString cadimporte;
    cadimporte.setNum(debe,'f',2);
    cad1 += cadimporte;
    cad1 += ",";
    cadimporte.setNum(haber,'f',2);
    cad1 += cadimporte;
    cad1 += ",'";
    cad1 += concepto.left(-1).replace("'","''");
    cad1 += "','";
    cad1 += documento.left(-1).replace("'","''");
    cad1 += "','";
    cad1+=diario.left(-1).replace("'","''");
    cad1+="','";
    cad1+=usuario.left(-1).replace("'","''");
    cad1 += "','','";
    cad1 += ejercicio;
    cad1 += "',";
    QString cadnumpase_origen;
    cadnumpase_origen.setNum(pase_origen);
    cad1 += cadnumpase_origen;
    cad1+=")";
    ejecutar(cad1);
}


// abre segunda base de datos (no default)
bool basedatos::abre2db(QString nombredb,QString nombreusuario,QString clave,
                        QString host, QString controlador, QString puerto, QString base)
{
   QString nbd;
   if (controlador.contains("SQLITE"))
       {
        nbd=dirtrabajobd();
        nbd+=QDir::separator();
       }
   nbd+=nombredb.toLower();
   if (controlador.contains("SQLITE")) nbd=adapta(nbd);

   if (controlador.contains("SQLITE"))
        {
         if (!QFile::exists(nbd))
            {
             QMessageBox::warning(0, QObject::tr("KEME-Contabilidad"),
                   QObject::tr("No existe la base de datos especificada"));
             return false;
            }
        }

   QSqlDatabase segundaDB = QSqlDatabase::addDatabase( controlador,base);
   if ( segundaDB.isDriverAvailable (controlador) ) {
          if (!nombredb.isEmpty()) segundaDB.setDatabaseName( nbd );
          if (!nombreusuario.isEmpty()) segundaDB.setUserName( nombreusuario );
          if (!clave.isEmpty()) segundaDB.setPassword( clave );
          if (!host.isEmpty()) segundaDB.setHostName( host );
          if (!puerto.isEmpty()) segundaDB.setPort(puerto.toInt());
          if ( !segundaDB.open() )
             {
               QMessageBox::critical ( 0 , QObject::tr("Base de datos"),
                 QObject::tr("No se ha conseguido abrir la base de datos\n") );
               return false;
             }
          // La base de datos se ha abierto, ya podemos executar comandos
         }
        else
             {
                QMessageBox::critical( 0, QObject::tr("Apertura de base de datos"),
                 QObject::tr("IMPOSIBLE CARGAR BASE DE DATOS CON ")+controlador);
	         return false;
             }
 return true;
}


// información del diario de la base de datos no default
QSqlQuery basedatos::consulta_diario_db(QDate fechainicial, QDate fechafinal, QString base)
{
    QString consulta="select asiento,fecha,cuenta,debe,haber,concepto,documento,diario,usuario "
                     "from diario where fecha>='";
    consulta+=fechainicial.toString("yyyy-MM-dd");
    consulta+="' and fecha<='";
    consulta+=fechafinal.toString("yyyy-MM-dd");
    consulta+="'";
    return ejecutar(consulta,base);
}

// información del diario de la base de datos no default a partir de número de apunte
QSqlQuery basedatos::consulta_diario_db_ap_apunte(QDate fechainicial,
                                                  QDate fechafinal,
                                                  QString apunte,
                                                  QString base)
{
    QString consulta="select asiento, fecha, cuenta, debe, haber, concepto,"
                     "documento,diario,usuario,pase "
                     "from diario where fecha>='";
    consulta+=fechainicial.toString("yyyy-MM-dd");
    consulta+="' and fecha<='";
    consulta+=fechafinal.toString("yyyy-MM-dd");
    consulta+="' and pase>";
    consulta+=apunte.isEmpty() ? "0": apunte;
    return ejecutar(consulta,base);
}


// información número apuntes en diario de la base de datos no default
// a partir de número de apunte
int basedatos::consulta_diario_num_apuntes(QDate fechainicial,
                                                  QDate fechafinal,
                                                  QString apunte,
                                                  QString base)
{    
    QString consulta="select count(pase) "
                     "from diario where fecha>='";
    consulta+=fechainicial.toString("yyyy-MM-dd");
    consulta+="' and fecha<='";
    consulta+=fechafinal.toString("yyyy-MM-dd");
    consulta+="' and pase>";
    consulta+=apunte.isEmpty() ? "0": apunte;
    QSqlQuery query = ejecutar(consulta,base);
    if (query.isActive())
        if (query.next()) return query.value(0).toInt();
    return 0;
}



void basedatos::cierra_no_default(QString base)
{
  QSqlDatabase::removeDatabase(base);
}


QString basedatos::cadvalorestado(QString estado,QString nodo,QString ejercicio, bool parte1)
{
  QString consulta="select ejercicio1,ejercicio2 from cabeceraestados where titulo='";
  consulta+=estado;
  consulta+="'";
  bool primero=false;
  QSqlQuery query = ejecutar(consulta);
  if (query.first())
     {
      if (query.value(0).toString()==ejercicio) primero=true;
         else
             {
              if (query.value(1).toString()==ejercicio) primero=false;
                 else return "";
             }
     }
     else return "";
  consulta="select importe1,importe2 from estados where titulo='";
  consulta+=estado;
  consulta+="' and nodo='";
  consulta+=nodo;
  consulta+="'";
  if (parte1) consulta+=" and parte1";
    else consulta+=" and not parte1";
  query=ejecutar(consulta);
  if (query.first())
     {
      if (primero) return formateanumerosep(query.value(0).toDouble(),comadecimal,decimales);
         else return formateanumerosep(query.value(1).toDouble(),comadecimal,decimales);
     }
  return "";
}


bool basedatos::existecodigoregularizacion (QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descripcion from cab_regulariz where codigo = '"+ cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return true;
    }
    return false;
}

// Actualiza la descripción del código de regularización
void basedatos::guardacambiosregulariz (QString codigo,QString descripcion) {
    QString cadquery = "UPDATE cab_regulariz SET descripcion='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE codigo='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}


// Inserta datos en la tabla cab_regulariz
void basedatos::insertcab_regulariz (QString codigo,QString descripcion) {
    QString cadquery = "INSERT INTO cab_regulariz (codigo,descripcion) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}


// Borra un código de tipovenci
void basedatos::eliminatipovenci (QString qcodigo) {
    // ojo hay que eliminar primero las líneas de regularización
    ejecutar("delete from tiposvenci where codigo= '"+ qcodigo.left(-1).replace("'","''") +"'");
}

// Borra un código de tipos_automaticos
void basedatos::eliminatipo_automatico (QString qcodigo) {

    ejecutar("delete from tipos_automaticos where codigo= '"+ qcodigo.left(-1).replace("'","''") +"'");
}

bool basedatos::existecodigotipovenci (QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descripcion from tiposvenci where codigo = '"+ cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return true;
    }
    return false;
}

bool basedatos::existecodigotipo_automatico (QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descripcion from tipos_automaticos where codigo = '"+ cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return true;
    }
    return false;
}

// Actualiza la descripción del código de tipo vencimiento
void basedatos::guardacambiostipovenci (QString codigo,QString descripcion) {
    QString cadquery = "UPDATE tiposvenci SET descripcion='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE codigo='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}

// Actualiza la descripción del código de tipo automático
void basedatos::guardacambiostipo_automatico (QString codigo,QString descripcion) {
    QString cadquery = "UPDATE tipos_automaticos SET descripcion='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE codigo='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}

QStringList basedatos::listatiposdescripvenci () {
    QStringList listavenci;
    QString cadquery = "SELECT codigo,descripcion from tiposvenci order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if ( query.isActive() )
    {
      while (query.next())
        {
          QString cadena= query.value(0).toString();
          cadena+=" // ";
          cadena+=query.value(1).toString();
          listavenci << cadena;
        }
    }
    return listavenci;
}

QStringList basedatos::listaclaveidfiscal () {
    QStringList lista;
    QString cadquery = "SELECT codigo,descripcion from claveidfiscal order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if ( query.isActive() )
    {
      while (query.next())
        {
          QString cadena= query.value(0).toString();
          cadena+=" // ";
          cadena+=query.value(1).toString();
          lista << cadena;
        }
    }
    return lista;
}


QStringList basedatos::listapaises()
{
    QStringList lista;
    QString cadquery = "SELECT codigo,descripcion from codigopais order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if ( query.isActive() )
    {
      while (query.next())
        {
          QString cadena= query.value(1).toString();
          lista << cadena;
        }
    }
    return lista;
}

// Inserta datos en la tabla cab_regulariz
void basedatos::inserttipovenci (QString codigo,QString descripcion) {
    QString cadquery = "INSERT INTO tiposvenci (codigo,descripcion) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}


void basedatos::inserttipo_automatico (QString codigo,QString descripcion) {
    QString cadquery = "INSERT INTO tipos_automaticos (codigo,descripcion) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}


// Borra un código de regularización
void basedatos::eliminaregulariz (QString qcodigo) {
    // ojo hay que eliminar primero las líneas de regularización
    borra_regul_sec(qcodigo);
    ejecutar("delete from cab_regulariz where codigo= '"+ qcodigo.left(-1).replace("'","''") +"'");
}



// borra secuencia de regularización
void basedatos::borra_regul_sec(QString codigo)
{
  QString cadena="delete from regul_sec where codigo='";
  cadena+=codigo;
  cadena+="'";
  ejecutar(cadena);
}

// inserta registro de secuencia de regularización
void basedatos::inserta_reg_sec(QString codigo, int orden, QString descripcion,
                                QString cuenta_deudora,
                                QString cuenta_acreedora, QString cuentas,
                                QString condicion, QString diario)
{
  QSqlQuery query;

  query.prepare("INSERT INTO regul_sec (codigo, orden, descripcion,"
                "cuenta_deudora, cuenta_acreedora, cuentas, condicion, diario) "
                   "VALUES (:codigo, :orden, :descripcion,"
                   ":cuenta_deudora, :cuenta_acreedora, :cuentas, :condicion, :diario)");

  query.bindValue(":codigo", codigo);
  query.bindValue(":orden", orden);
  query.bindValue(":descripcion", descripcion );
  query.bindValue(":cuenta_deudora", cuenta_deudora );
  query.bindValue(":cuenta_acreedora", cuenta_acreedora );
  query.bindValue(":cuentas", cuentas);
  query.bindValue(":condicion", condicion);
  query.bindValue(":diario", diario);
  ejecuta(query);
  return;
}

// ejecuta query (no cadena)
QSqlQuery basedatos::ejecuta (QSqlQuery query) {
    if ( !query.exec() ) {
        QMessageBox::critical ( 0 , QObject::tr("Base de datos") ,
         QObject::tr("Error al ejecutar la sentencia:\n") + query.lastQuery ()  + "\n" +
         query.lastError().databaseText() +"\n"+ QObject::tr("Controlador: ") +
         QSqlDatabase::database().driverName() );
         error_consulta=true;
    }
    return query;
}

QSqlQuery basedatos::carga_regul_sec(QString codigo)
{
  QSqlQuery query;

  query.prepare("select descripcion,"
                "cuenta_deudora, cuenta_acreedora, cuentas, condicion, diario "
                   "from regul_sec where codigo= :codigo order by orden");

  query.bindValue(":codigo", codigo);

  return ejecuta(query);
}

// cols_evpn_pymes  (codigo,descripcion,cuentas)
QSqlQuery basedatos::carga_cols_evpn_pymes()
{
  QSqlQuery query;

  query.prepare("select codigo,descripcion,cuentas from cols_evpn_pymes order by codigo");
  return ejecuta(query);
}

void basedatos::borra_cols_evpn_pymes()
{
  ejecutar("delete from cols_evpn_pymes");
}


void basedatos::inserta_reg_cols_evpn_pymes(QString codigo, QString descripcion,
                                            QString cuentas)
{
  QString cadena="insert into cols_evpn_pymes (codigo, descripcion, cuentas) values ('";
  cadena+=codigo;
  cadena+="', '";
  cadena+=descripcion;
  cadena+="', '";
  cadena+=cuentas;
  cadena +="')";
  ejecutar (cadena);
}

double basedatos::calculasaldocuentas(QString cadenacuentas, QDate inicial, QDate final)
{
    QString cadena = "select ";
    cadena += "sum(debe), ";
    cadena += "sum(haber) ";
    cadena += "from diario where ";
    cadena += "fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and (";

    int partes = cadenacuentas.count(',');

    for (int veces=0; veces<=partes; veces++)
    {
        if (cualControlador() == SQLITE) {
            cadena += "cuenta like '"+ cadenacuentas.section(',',veces,veces).left(-1).replace("'","''") +"%'";
        }
        else {
            cadena += "position('";
            cadena += cadenacuentas.section(',',veces,veces).left(-1).replace("'","''");
            cadena += "' in cuenta)=1";
        }
        if (veces==partes) cadena+=")";
        else cadena+=" or ";
    }

  QSqlQuery query=ejecutar(cadena);
  if (query.first())
     return query.value(0).toDouble()-query.value(1).toDouble();
  return 0;
}

// inserta apunte en diario para regularización parcial
void basedatos::insertDiarioRegulParc (QString cadnumasiento, QString cadnumpase, QString cadfecha,
         QString cuenta, QString debe, QString haber, QString concepto,
         QString ejercicio, QString diario) {
    QString cad2 = "INSERT into diario (asiento, pase,fecha,cuenta, debe,haber,concepto,"
        "ejercicio, documento,diario,usuario) VALUES(";
    cad2 += cadnumasiento.left(-1).replace("'","''") +",";
    cad2 += cadnumpase.left(-1).replace("'","''") +",'";
    cad2 += cadfecha.left(-1).replace("'","''") +"','";
    cad2 += cuenta.left(-1).replace("'","''") +"',";
    cad2 += debe.left(-1).replace("'","''") +",";
    cad2 += haber.left(-1).replace("'","''");
    cad2 += ",'"+ concepto.left(-1).replace("'","''") +"','";
    cad2 += ejercicio.left(-1).replace("'","''") +"','";
    // cad2 += documento;
    cad2 += "','";
    cad2 += diario;
    if (cualControlador() == SQLITE) {
        cad2 += "','"+ usuario_sistema() +"')";
    }
    else {
        cad2 += "',CURRENT_USER)";
    }
    ejecutar(cad2);
}


// crea tablas copia de libroiva y vencimientos y borra las originales
void basedatos::proctablas_libroiva_venci()
{
 if (cualControlador() == POSTGRES)
  {
   ejecutar("DROP TABLE IF EXISTS libroiva2");
   ejecutar("DROP TABLE IF EXISTS vencimientos2");
   ejecutar("CREATE TABLE libroiva2 AS select * from libroiva");
   ejecutar("CREATE TABLE vencimientos2 AS select * from vencimientos");
   ejecutar("DELETE from libroiva");
   ejecutar("DELETE from vencimientos");
  }
 if (cualControlador() == MYSQL)
  {
   ejecutar("DROP TABLE IF EXISTS libroiva2");
   ejecutar("DROP TABLE IF EXISTS vencimientos2");
   ejecutar("CREATE TABLE libroiva2 select * from libroiva");
   ejecutar("CREATE TABLE vencimientos2 select * from vencimientos");
   ejecutar("DELETE from libroiva");
   ejecutar("DELETE from vencimientos");
  }
}


void basedatos::rettablas_libroiva_venci()
{
   ejecutar("INSERT INTO libroiva SELECT * FROM libroiva2");
   ejecutar("INSERT INTO vencimientos SELECT * FROM vencimientos2");
   ejecutar("DROP TABLE IF EXISTS libroiva2");
   ejecutar("DROP TABLE IF EXISTS vencimientos2");
}


void basedatos::updateLibroivapasepase2 (QString antiguo, QString nuevo) {
    QString cad2 = "update libroiva2 set pase=";
    cad2 += nuevo.left(-1).replace("'","''");
    cad2 += " where pase=";
    cad2 += antiguo.left(-1).replace("'","''");
    ejecutar(cad2);
}


// 
void basedatos::updateVencimientospase_diario_operacionpase_diario_operacion2(QString antiguo, QString nuevo) {
    QString cad2 = "update vencimientos2 set pase_diario_operacion=";
    cad2 += nuevo.left(-1).replace("'","''");
    cad2 += " where pase_diario_operacion=";
    cad2 += antiguo.left(-1).replace("'","''");
    ejecutar(cad2);
}

// 
void basedatos::updateVencimientospase_diario_vencimientopase_diario_vencimiento2 (QString antiguo, QString nuevo) {
    QString cad2 = "update vencimientos2 set pase_diario_vencimiento=";
    cad2 += nuevo.left(-1).replace("'","''");
    cad2 += " where pase_diario_vencimiento=";
    cad2 += antiguo.left(-1).replace("'","''");
    ejecutar(cad2);
}

QString basedatos::ejerciciodeasientoamort(QString asiento)
{
 QString cad="select ejercicio from amortcontable where asiento=";
 cad+=asiento;
  QSqlQuery query=ejecutar(cad);
  if (query.first())
     return query.value(0).toString();
  return "";
}

bool basedatos::esasientodeamort(QString asiento, QString ejercicio)
{
 QString cad="select ejercicio from amortcontable where asiento=";
 cad+=asiento;
 cad+= " and ejercicio='";
 cad+=ejercicio;
 cad+="'";
  QSqlQuery query=ejecutar(cad);
  if (query.first())
     return query.value(0).toString()==ejercicio.left(-1).replace("'","''");
  return false;
}


void basedatos::renum_amortiz (QString asientoA, QString asientoN, QString ejercicio) {
    QString cad2 = "update amortcontable set asiento=";
    cad2 += asientoN;
    cad2 += " where asiento=";
    cad2 += asientoA;
    cad2 += " and ejercicio='";
    cad2 += ejercicio.left(-1).replace("'","''");
    cad2 += "'";
  ejecutar(cad2);
}

bool basedatos::essqlite()
{
  return cualControlador() == SQLITE;
}


QSqlQuery basedatos::infocopiardepase (QString pase) {
    QSqlQuery query;
    query.prepare("select asiento,fecha,cuenta,concepto,debe,haber,"
                               "documento,ci from diario where pase="+ pase);
    return ejecuta(query);
}

bool basedatos::hayconceptoasociado(QString cuenta, QString &concepto)
{
    QSqlQuery query;
    query.prepare("select conceptodiario from datossubcuenta where cuenta= :cuenta");
    query.bindValue(":cuenta",cuenta);
    query=ejecuta(query);
    if ( (query.isActive()) && (query.first()) )
       {
        concepto=query.value(0).toString();
        if (concepto.length()>0) return true;
       }
    concepto.clear();
    return false;
}


bool basedatos::hayconceptoasociado_cta(QString cuenta, QString &concepto)
{
    /* "ivaasoc bool default 0,"
     "cuentaiva      varchar(40),"
     "tipoiva        varchar(20),"
     "conceptodiario varchar(155),"
     "cuenta_ret_asig varchar(40) default '',"
     "es_ret_asig_arrend bool "
     "tipo_ret_asig numeric(5,2) default 0,"
     "tipo_oper_ret_asig varchar(80)," */

    // cargamos primero todas las cuentas del grupo correspondiente
    // ojo venci es el código del vencimiento
   QString extract=cuenta.left(1);
   QString cad="select codigo, conceptodiario from iva_ret_cta where codigo like '";
   cad+=extract;
   cad+="%'";
   QStringList listacod, lista_concepto;

   QSqlQuery query=ejecutar(cad);
   if (query.isActive())
       while (query.next())
       {
        listacod<<query.value(0).toString();
        lista_concepto<<query.value(1).toString();
       }

   for (int veces=cuenta.length()-1; veces>0; veces--)
     {
        if (listacod.contains(cuenta.left(veces)))
         {
           concepto=lista_concepto.at(listacod.indexOf(cuenta.left(veces)));
           return true;
         }
      }
   return false;
}




bool basedatos::hayivaasociado(QString cuenta, QString &cuentaiva, QString &tipoiva)
{
    QSqlQuery query;
    query.prepare("select ivaasoc,cuentaiva,tipoiva from datossubcuenta where cuenta= :cuenta");
    query.bindValue(":cuenta",cuenta);
    query=ejecuta(query);
    if ( (query.isActive()) && (query.first()) )
       {
        if (query.value(0).toBool())
           {
            cuentaiva=query.value(1).toString();
            tipoiva=query.value(2).toString();
            if (!cuentaiva.isEmpty()) return true;
           }
       }
    cuentaiva.clear();
    tipoiva.clear();
    return false;
}

bool basedatos::hayivaasociado_cta(QString cuenta, QString &cuentaiva, QString &tipoiva)
{
    /* "ivaasoc bool default 0,"
     "cuentaiva      varchar(40),"
     "tipoiva        varchar(20),"
     "conceptodiario varchar(155),"
     "cuenta_ret_asig varchar(40) default '',"
     "es_ret_asig_arrend bool "
     "tipo_ret_asig numeric(5,2) default 0,"
     "tipo_oper_ret_asig varchar(80)," */

    // cargamos primero todas las cuentas del grupo correspondiente
    // ojo venci es el código del vencimiento
   QString extract=cuenta.left(1);
   QString cad="select codigo, cuentaiva, tipoiva from iva_ret_cta where codigo like '";
   cad+=extract;
   cad+="%'";
   QStringList listacod, lista_cuentaiva, lista_tipoiva;

   QSqlQuery query=ejecutar(cad);
   if (query.isActive())
       while (query.next())
       {
        listacod<<query.value(0).toString();
        lista_cuentaiva<<query.value(1).toString();
        lista_tipoiva<<query.value(2).toString();
       }

   for (int veces=cuenta.length()-1; veces>0; veces--)
     {
        if (listacod.contains(cuenta.left(veces)))
         {
           cuentaiva=lista_cuentaiva.at(listacod.indexOf(cuenta.left(veces)));
           tipoiva=lista_tipoiva.at(listacod.indexOf(cuenta.left(veces)));
           return true;
         }
      }
   return false;
}

bool basedatos::hayretasociada_cta(QString cuenta, QString *cuentaret, QString *ret, bool *arrend,
                               QString *tipo_oper_ret)
{
    /* "ivaasoc bool default 0,"
     "cuentaiva      varchar(40),"
     "tipoiva        varchar(20),"
     "conceptodiario varchar(155),"
     "cuenta_ret_asig varchar(40) default '',"
     "es_ret_asig_arrend bool "
     "tipo_ret_asig numeric(5,2) default 0,"
     "tipo_oper_ret_asig varchar(80)," */

    // cargamos primero todas las cuentas del grupo correspondiente
    // ojo venci es el código del vencimiento
   QString extract=cuenta.left(1);
   QString cad="select codigo, cuenta_ret_asig, tipo_ret_asig, es_ret_asig_arrend,"
               " tipo_oper_ret_asig from iva_ret_cta where codigo like '";
   cad+=extract;
   cad+="%'";
   QStringList listacod, lista_cuentaret, lista_ret, lista_arrend, lista_tipo_oper_ret;

   QSqlQuery query=ejecutar(cad);
   if (query.isActive())
       while (query.next())
       {
        listacod<<query.value(0).toString();
        lista_cuentaret<<query.value(1).toString();
        lista_ret<<query.value(2).toString();
        lista_arrend << (query.value(3).toBool() ? "1" : "0");
        lista_tipo_oper_ret << query.value(4).toString();
       }

   for (int veces=cuenta.length()-1; veces>0; veces--)
     {
        if (listacod.contains(cuenta.left(veces)))
         {
           if (lista_cuentaret.at(listacod.indexOf(cuenta.left(veces))).isEmpty())
               return false;
            *cuentaret=lista_cuentaret.at(listacod.indexOf(cuenta.left(veces)));
            *ret=lista_ret.at(listacod.indexOf(cuenta.left(veces)));
            *arrend=lista_arrend.at(listacod.indexOf(cuenta.left(veces)))=="1";
            *tipo_oper_ret=lista_tipo_oper_ret.at(listacod.indexOf(cuenta.left(veces)));
           return true;
         }
      }
   return false;

}

/*
bool basedatos::hay_venci_cta_asociado(QString cuenta, QString *cadena_venci,
                                       QString *cuenta_tesoreria, bool *procesavenci,
                                       int *dia_vto_fijo)
{
  // cargamos primero todas las cuentas del grupo correspondiente
  // ojo venci es el ćodigo del vencimiento
 QString extract=cuenta.left(1);
 QString cad="select codigo, tesoreria from venci_cta where codigo like '";
 cad+=extract;
 cad+="%'";
 QStringList listacod, lista_tesor;

 QSqlQuery query=ejecutar(cad);
 if (query.isActive())
     while (query.next())
     {
      listacod<<query.value(0).toString();
      lista_tesor<<query.value(1).toString();
     }
 for (int veces=cuenta.length()-1; veces>0; veces--)
   {
      if (listacod.contains(cuenta.left(veces)))
       {
         if (!lista_tesor.at(listacod.indexOf(cuenta.left(veces))).isEmpty())
          {
             QSqlQuery q =datos_venci_asignado_cta(cuenta.left(veces));
             if (q.isActive())
                 if (q.next())
                 {
                   *cadena_venci=descrip_tiposvenci(q.value(0).toString());
                   *cuenta_tesoreria=q.value(1).toString();
                   *procesavenci=q.value(2).toBool();
                   *dia_vto_fijo=q.value(3).toInt();
                   return true;
                 }
          }
       }
   }
 return false;
}
*/

void basedatos::datos_cuenta_banco(QString cuenta, QString *ccc, QString *iban,
                                   QString *cedente1, QString *cedente2, QString *nriesgo,
                                   QString *sufijo)
{

    QSqlQuery query;
    query.prepare("select ccc, iban, cedente1, cedente2, nriesgo, sufijo "
                  "from datossubcuenta where cuenta= :cuenta");
    query.bindValue(":cuenta",cuenta);
    query=ejecuta(query);
    if ( (query.isActive()) && (query.first()) )
       {
        if (!query.value(0).toString().isEmpty())
           {
            *ccc=query.value(0).toString();
            *iban=query.value(1).toString();
            *cedente1=query.value(2).toString();
            *cedente2=query.value(3).toString();
            *nriesgo=query.value(4).toString();
            *sufijo=query.value(5).toString();
           }
       }
    return;
}

bool basedatos::hayretasociada(QString cuenta, QString *cuentaret, QString *ret, bool *arrend,
                               QString *tipo_oper_ret)
{
    QSqlQuery query;
    query.prepare("select cuenta_ret_asig, tipo_ret_asig, es_ret_asig_arrend, "
                  "tipo_oper_ret_asig from datossubcuenta where cuenta= :cuenta");
    query.bindValue(":cuenta",cuenta);
    query=ejecuta(query);
    if ( (query.isActive()) && (query.first()) )
       {
        if (!query.value(0).toString().isEmpty())
           {
            *cuentaret=query.value(0).toString();
            *ret=query.value(1).toString();
            *arrend=query.value(2).toBool();
            *tipo_oper_ret=query.value(3).toString();
            if (esauxiliar(*cuentaret) && (*ret).toDouble()>0.00001) return true;
           }
       }
    (*cuentaret).clear();
    (*ret).clear();
    (*tipo_oper_ret).clear();
    return false;
}



bool basedatos::hayivaasociado_cta_iva(QString cuenta, QString &tipoiva)
{
    QSqlQuery query;
    query.prepare("select ivaasoc,cuentaiva,tipoiva from datossubcuenta where cuenta= :cuenta");
    query.bindValue(":cuenta",cuenta);
    query=ejecuta(query);
    if ( (query.isActive()) && (query.first()) )
       {
        if (query.value(0).toBool())
           {
            tipoiva=query.value(2).toString();
            if (query.value(1).toString().isEmpty()) return true;
           }
       }
    tipoiva.clear();
    return false;
}


bool basedatos::hay_ret_asociada_cta_ret(QString cuenta, double *tipo_ret,
                                         QString *tipo_operacion_ret,
                                         bool *ret_arrendamiento)
{
    QSqlQuery query;
    query.prepare("select tipo_ret, tipo_operacion_ret, ret_arrendamiento "
                   "from datossubcuenta where cuenta= :cuenta");
    query.bindValue(":cuenta",cuenta);
    query=ejecuta(query);
    if ( (query.isActive()) && (query.first()) )
       {
        if (query.value(0).toDouble()>0.001)
           {
            *tipo_ret=query.value(0).toDouble();
            *tipo_operacion_ret=query.value(1).toString();
            *ret_arrendamiento=query.value(2).toBool();
            return true;
           } else return false;
       }
    return false;

}


bool basedatos::hayvenciasociado(QString cuenta)
{
 //   QSqlQuery query;
    QString cadena="select venciasoc from datossubcuenta where cuenta='";
    cadena+=cuenta;
    cadena+="'";
//    query.prepare(cadena);
    // query.bindValue(":cuenta",cuenta);

  QSqlQuery query=ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
       {
        if (query.value(0).toBool())
           {
            return true;
           }
       }
    return false;
}

bool basedatos::cargadatospase(QString pase, QString &cuenta,QDate &fecha,
                            QString &importedebe,
                            QString &importehaber, QString &documento)
{
    QSqlQuery query;
    query.prepare("select cuenta,fecha,debe,haber,documento from diario where pase= :pase");
    query.bindValue(":pase",pase);
    query=ejecuta(query);
    if ( query.isActive() && query.first() )
       {
        cuenta=query.value(0).toString();
        fecha=query.value(1).toDate();
        importedebe=query.value(2).toString();
        importehaber=query.value(3).toString();
        documento=query.value(4).toString();
        return true;
       }
    return false;
}


bool basedatos::pase_en_vencimientos(QString pase)
{
 QString cadena="select pase_diario_operacion from vencimientos where pase_diario_operacion=";
 cadena+=pase;
 QSqlQuery query=ejecutar(cadena);
 if (query.isActive())
    if (query.next()) return true;

 cadena="select pase_diario_vencimiento from vencimientos where pase_diario_vencimiento=";
 cadena+=pase;
 query=ejecutar(cadena);
 if (query.isActive())
    if (query.next()) return true;

 return false;
}


QString basedatos::cadvenci_subcuenta (QString cuenta) {
    QString cadquery = "SELECT tiposvenci.descripcion from datossubcuenta, tiposvenci ";
    cadquery += "where datossubcuenta.cuenta = '";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "' and tiposvenci.codigo=datossubcuenta.codvenci";
   QSqlQuery query=ejecutar(cadquery);
   if (query.isActive())
     if (query.next()) return query.value(0).toString();
  return "";
}


int basedatos::dia_fijo_vto_subcuenta (QString cuenta) {
    QString cadquery = "SELECT vto_dia_fijo from datossubcuenta ";
    cadquery += "where cuenta = '";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "'";
   QSqlQuery query=ejecutar(cadquery);
   if (query.isActive())
     if (query.next()) return query.value(0).toInt();
  return 0;
}


bool basedatos::cuenta_dom_bancaria (QString cuenta) {
    QString cadquery = "SELECT dom_bancaria from datossubcuenta ";
    cadquery += "where cuenta = '";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "'";
   QSqlQuery query=ejecutar(cadquery);
   if (query.isActive())
     if (query.next()) return query.value(0).toBool();
  return false;
}


QString basedatos::cta_tesoreria_venci (QString cuenta) {
    QString cadquery = "SELECT tesoreria from datossubcuenta ";
    cadquery += "where cuenta = '";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "'";
   QSqlQuery query=ejecutar(cadquery);
   if (query.isActive())
     if (query.next()) return query.value(0).toString();
  return "";
}

QString basedatos::asientodepase(QString pase)
{
    QString cadquery = "SELECT asiento from diario ";
    cadquery += "where pase = ";
    cadquery += pase;
   QSqlQuery query=ejecutar(cadquery);
   if (query.isActive())
     if (query.next()) return query.value(0).toString();
  return "";

}


void basedatos::borra_bloqueo_cta(QString cuenta)
{
  QString cadquery = "DELETE from bloqueos_cta where codigo='";
  cadquery+=cuenta;
  cadquery+="'";
  ejecutar(cadquery);
}

bool basedatos::existe_bloqueo_cta_gen(QString cuenta)
{
    QString cadquery = "select codigo from bloqueos_cta where codigo='";
    cadquery+=cuenta;
    cadquery+="'";
    QSqlQuery query=ejecutar(cadquery);
    if (query.isActive())
       if (query.next()) return true;
    return false;

}

bool basedatos::existe_bloqueo_cta(QString cuenta, QDate fecha)
{
  int mes=fecha.month();
  if (mes==0) mes=1;
  QString cadquery = "select codigo from bloqueos_cta where codigo='";
  cadquery+=cuenta;
  cadquery+="' and ";
  switch(mes)
   {
   case 1 : cadquery += "enero";
       break;
   case 2 : cadquery += "febrero";
       break;
   case 3 : cadquery += "marzo";
       break;
   case 4 : cadquery += "abril";
       break;
   case 5 : cadquery += "mayo";
       break;
   case 6 : cadquery += "junio";
       break;
   case 7 : cadquery += "julio";
       break;
   case 8 : cadquery += "agosto";
       break;
   case 9 : cadquery += "septiembre";
       break;
   case 10 : cadquery += "octubre";
       break;
   case 11 : cadquery += "noviembre";
       break;
   case 12 : cadquery += "diciembre";
       break;
   }

  QSqlQuery query=ejecutar(cadquery);
  if (query.isActive())
     if (query.next()) return true;
  return false;
}

void basedatos::bloqueos_mes_cta(QString cuenta, bool *enero, bool *febrero, bool *marzo,
                                 bool *abril, bool *mayo, bool *junio, bool *julio,
                                 bool *agosto, bool *septiembre, bool *octubre,
                                 bool *noviembre, bool *diciembre)
{
    QString cadquery = "select enero, febrero, marzo, abril, mayo, junio, julio,"
                       "agosto, septiembre, octubre, noviembre, diciembre "
                       "from bloqueos_cta where codigo='";
    cadquery+=cuenta;
    cadquery+="'";
    QSqlQuery query=ejecutar(cadquery);
    if (query.isActive())
       if (query.next())
        {
         *enero= query.value(0).toBool();
         *febrero= query.value(1).toBool();
         *marzo= query.value(2).toBool();
         *abril= query.value(3).toBool();
         *mayo= query.value(4).toBool();
         *junio= query.value(5).toBool();
         *julio= query.value(6).toBool();
         *agosto= query.value(7).toBool();
         *septiembre= query.value(8).toBool();
         *octubre= query.value(9).toBool();
         *noviembre= query.value(10).toBool();
         *diciembre= query.value(11).toBool();
        }
}


void basedatos::ed_bloqueos_mes_cta(QString cuenta, bool enero, bool febrero, bool marzo,
                                 bool abril, bool mayo, bool junio, bool julio,
                                 bool agosto, bool septiembre, bool octubre,
                                 bool noviembre, bool diciembre)
{
    // suponemos que el codigo de la cuenta ya existe
    QString cad = "update bloqueos_cta set enero=";
    if ( cualControlador() == SQLITE )
     {
        cad+= enero ? "1" : "0";
        cad+= ", febrero=";
        cad+= febrero ? "1" : "0";
        cad+= ", marzo=";
        cad+= marzo ? "1" : "0";
        cad+= ", abril=";
        cad+= abril ? "1" : "0";
        cad+= ", mayo=";
        cad+= mayo ? "1" : "0";
        cad+= ", junio=";
        cad+= junio ? "1" : "0";
        cad+= ", julio=";
        cad+= julio ? "1" : "0";
        cad+= ", agosto=";
        cad+= agosto ? "1" : "0";
        cad+= ", septiembre=";
        cad+= septiembre ? "1" : "0";
        cad+= ", octubre=";
        cad+= octubre ? "1" : "0";
        cad+= ", noviembre=";
        cad+= noviembre ? "1" : "0";
        cad+= ", diciembre=";
        cad+= diciembre ? "1" : "0";
      }
      else
          {
           cad+= enero ? "true" : "false";
           cad+= ", febrero=";
           cad+= febrero ? "true" : "false";
           cad+= ", marzo=";
           cad+= marzo ? "true" : "false";
           cad+= ", abril=";
           cad+= abril ? "true" : "false";
           cad+= ", mayo=";
           cad+= mayo ? "true" : "false";
           cad+= ", junio=";
           cad+= junio ? "true" : "false";
           cad+= ", julio=";
           cad+= julio ? "true" : "false";
           cad+= ", agosto=";
           cad+= agosto ? "true" : "false";
           cad+= ", septiembre=";
           cad+= septiembre ? "true" : "false";
           cad+= ", octubre=";
           cad+= octubre ? "true" : "false";
           cad+= ", noviembre=";
           cad+= noviembre ? "true" : "false";
           cad+= ", diciembre=";
           cad+= diciembre ? "true" : "false";
          }
    cad+= " where codigo='"+cuenta;
    cad+="'";
    ejecutar(cad);
}


void basedatos::insert_bloqueo_cta (QString cuenta)
{
    QString cad = "INSERT into bloqueos_cta (codigo) VALUES('";
    cad+=cuenta.left(-1).replace("'","''");
    cad+="')";
    ejecutar(cad);
}

bool basedatos::existe_venci_cta(QString cuenta)
{
    QString cadquery = "select codigo from venci_cta where codigo='";
    cadquery+=cuenta;
    cadquery+="'";
    QSqlQuery query=ejecutar(cadquery);
    if (query.isActive())
       if (query.next()) return true;
    return false;

}

bool basedatos::existe_iva_ret_cta(QString cuenta)
{
    QString cadquery = "select codigo from iva_ret_cta where codigo='";
    cadquery+=cuenta;
    cadquery+="'";
    QSqlQuery query=ejecutar(cadquery);
    if (query.isActive())
       if (query.next()) return true;
    return false;

}

void basedatos::borra_venci_cta(QString cuenta)
{
  QString cadquery = "DELETE from venci_cta where codigo='";
  cadquery+=cuenta;
  cadquery+="'";
  ejecutar(cadquery);
}

void basedatos::borra_iva_ret_cta(QString cuenta)
{
  QString cadquery = "DELETE from iva_ret_cta where codigo='";
  cadquery+=cuenta;
  cadquery+="'";
  ejecutar(cadquery);
}



QString basedatos::tipo_venci_asignado_cta(QString cuenta)
{
    QString cadquery = "select venci from venci_cta where codigo='";
    cadquery+=cuenta;
    cadquery+="'";
    QSqlQuery query=ejecutar(cadquery);
    if (query.isActive())
       if (query.next()) return query.value(0).toString();
    return QString();
}

bool basedatos::hay_venci_cta_asociado_ver(QString cuenta)
{
    QString cadena_venci, cuenta_tesoreria;
    bool procesavenci;
    int dia_vto_fijo;
    return hay_venci_cta_asociado(cuenta, &cadena_venci,
                           &cuenta_tesoreria, &procesavenci,
                           &dia_vto_fijo);
}

bool basedatos::hay_venci_cta_asociado(QString cuenta, QString *cadena_venci,
                                       QString *cuenta_tesoreria, bool *procesavenci,
                                       int *dia_vto_fijo)
{
  // cargamos primero todas las cuentas del grupo correspondiente
  // ojo venci es el ćodigo del vencimiento
 QString extract=cuenta.left(1);
 QString cad="select codigo, tesoreria from venci_cta where codigo like '";
 cad+=extract;
 cad+="%'";
 QStringList listacod, lista_tesor;

 QSqlQuery query=ejecutar(cad);
 if (query.isActive())
     while (query.next())
     {
      listacod<<query.value(0).toString();
      lista_tesor<<query.value(1).toString();
     }
 for (int veces=cuenta.length()-1; veces>0; veces--)
   {
      if (listacod.contains(cuenta.left(veces)))
       {
         if (!lista_tesor.at(listacod.indexOf(cuenta.left(veces))).isEmpty())
          {
             QSqlQuery q =datos_venci_asignado_cta(cuenta.left(veces));
             if (q.isActive())
                 if (q.next())
                 {
                   *cadena_venci=descrip_tiposvenci(q.value(0).toString());
                   *cuenta_tesoreria=q.value(1).toString();
                   *procesavenci=q.value(2).toBool();
                   *dia_vto_fijo=q.value(3).toInt();
                   return true;
                 }
          }
       }
   }
 return false;
}

QString basedatos::descrip_tiposvenci(QString codigo)
{
    QString cadena="select descripcion from tiposvenci where codigo='";
    cadena+=codigo;
    cadena+="'";
    QSqlQuery q=ejecutar(cadena);
    if (q.isActive())
        if (q.next())
            return q.value(0).toString();
    return QString();
}


QSqlQuery basedatos::datos_venci_asignado_cta(QString cuenta)
{
    QString cadquery = "select venci, tesoreria, procesavenci, vto_dia_fijo "
                       "from venci_cta where codigo='";
    cadquery+=cuenta;
    cadquery+="'";
    return ejecutar(cadquery);
}

QSqlQuery basedatos::datos_iva_ret_asignado_cta(QString cuenta)
{

/* "ivaasoc bool default 0,"
 "cuentaiva      varchar(40),"
 "tipoiva        varchar(20),"
 "conceptodiario varchar(155),"
 "cuenta_ret_asig varchar(40) default '',"
 "es_ret_asig_arrend bool "
 "tipo_ret_asig numeric(5,2) default 0,"
 "tipo_oper_ret_asig varchar(80)," */

    QString cadquery = "select ivaasoc, cuentaiva, tipoiva, conceptodiario, "
                       "cuenta_ret_asig, es_ret_asig_arrend, tipo_ret_asig, "
                       "tipo_oper_ret_asig "
                       "from iva_ret_cta where codigo='";
    cadquery+=cuenta;
    cadquery+="'";
    return ejecutar(cadquery);
}


void basedatos::actu_venci_asignado_cta(QString cuenta, QString tipo,
                                               QString cta_tesoreria, bool procesavenci,
                                               int vto_dia_fijo)
{
    QString cadquery = "update venci_cta set venci='";
                       //venci, tesoreria, procesavenci, vto_dia_fijo "
                       //"from venci_cta where codigo='";
    cadquery+=tipo.left(-1).replace("'","''");
    cadquery+="', tesoreria='";
    cadquery+=cta_tesoreria.left(-1).replace("'","''");
    cadquery+="', procesavenci= ";
    if (cualControlador() == SQLITE) cadquery+=procesavenci ? "1" : "0";
       else cadquery+=procesavenci ? "true" : "false";
    cadquery+=", vto_dia_fijo= ";
    QString cadnum; cadnum.setNum(vto_dia_fijo);
    cadquery+=cadnum;
    cadquery+=" where codigo='";
    cadquery+=cuenta;
    cadquery+="'";
    ejecutar(cadquery);
    return;
}


void basedatos::actu_iva_ret_asignado_cta(QString cuenta, bool ivaasoc,
                                          QString cuentaiva, QString tipoiva,
                                          QString conceptodiario,
                                          QString cuenta_ret_asig,
                                          bool es_ret_asig_arrend,
                                          QString tipo_ret_asig,
                                          QString tipo_oper_ret_asig)
{
    /* "ivaasoc bool default 0,"
     "cuentaiva      varchar(40),"
     "tipoiva        varchar(20),"
     "conceptodiario varchar(155),"
     "cuenta_ret_asig varchar(40) default '',"
     "es_ret_asig_arrend bool "
     "tipo_ret_asig numeric(5,2) default 0,"
     "tipo_oper_ret_asig varchar(80)," */

    QString cadquery = "update iva_ret_cta set "
                       "ivaasoc=";
    if (cualControlador() == SQLITE) cadquery+=ivaasoc ? "1" : "0";
        else cadquery+=ivaasoc ? "true" : "false";
    cadquery+=", cuentaiva='";
    cadquery+=cuentaiva.left(-1).replace("'","''");
    cadquery+="', tipoiva='";
    cadquery+=tipoiva.left(-1).replace("'","''");
    cadquery+="', ";
    cadquery+="conceptodiario='";
    cadquery+=conceptodiario.left(-1).replace("'","''");
    cadquery+="', ";
    cadquery+="cuenta_ret_asig='";
    cadquery+=cuenta_ret_asig.left(-1).replace("'","''");
    cadquery+="', es_ret_asig_arrend=";
    if (cualControlador() == SQLITE) cadquery+=es_ret_asig_arrend ? "1" : "0";
        else cadquery+=es_ret_asig_arrend ? "true" : "false";
    cadquery+=", tipo_ret_asig=";
    cadquery+=tipo_ret_asig.left(-1).replace("'","''");
    cadquery+=", tipo_oper_ret_asig='";
    cadquery+=tipo_oper_ret_asig.left(-1).replace("'","''");
    cadquery+="'";
    cadquery+="where codigo='";
    cadquery+=cuenta;
    cadquery+="'";
    ejecutar(cadquery);
    return;
}



void basedatos::insert_venci_cta (QString cuenta, QString venci)
{
    QString cad = "INSERT into venci_cta (codigo, venci) VALUES('";
    cad+=cuenta.left(-1).replace("'","''");
    cad+="', '";
    cad+=venci.left(-1).replace("'","''");
    cad+="')";
    ejecutar(cad);
}

void basedatos::insert_iva_ret_cta (QString cuenta)
{
    QString cad = "INSERT into iva_ret_cta (codigo) VALUES('";
    cad+=cuenta.left(-1).replace("'","''");
    cad+="')";
    ejecutar(cad);
}




void basedatos::update_venci_cta (QString cuenta, QString venci)
{
    QString cad = "update venci_cta set venci='";
    cad+=venci.left(-1).replace("'","''");
    cad+="' where codigo='";
    cad+=cuenta;
    cad+="'";
    ejecutar(cad);
}


bool basedatos::fecha_periodo_bloqueado(QDate fecha)
{
  QString cad = "select inicio,fin,deshabilitado from periodos";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     while (query.next())
      {
        if (fecha>=query.value(0).toDate() && fecha <=query.value(1).toDate() && query.value(2).toBool())
            return true;
      }
  return false;
}

bool basedatos::asiento_en_periodo_bloqueado(QString elasiento,QString ejercicio)
{
  QString cad = "select fecha from diario where asiento=";
  cad+=elasiento;
  cad+=" and ejercicio='";
  cad+=ejercicio.left(-1).replace("'","''");;
  cad+="'";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
      if (query.next())
        {
          if (fecha_periodo_bloqueado(query.value(0).toDate())) return true;
        }
  return false;
}

bool basedatos::asiento_con_cuentas_bloqueadas(QString elasiento,QString ejercicio)
{
  QString cad="select cuenta from diario where asiento=";
  cad+=elasiento;
  cad+=" and ejercicio='";
  cad+=ejercicio.left(-1).replace("'","''");;
  cad+="'";
  QDate fecha=selectFechadiarioasiento(elasiento, ejercicio);
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
      while (query.next())
        {
         if (existe_bloqueo_cta(query.value(0).toString(),fecha)) return true;
        }
  return false;
}


bool basedatos::int_asientos_per_bloqueado (QString asientoinicial,QString asientofinal,
                                            QString ejercicio) {
    QString cadena = "select fecha from diario where asiento>="+
                     asientoinicial.left(-1).replace("'","''");
    cadena += " and asiento<="+ asientofinal.left(-1).replace("'","''");
    cadena += " and ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);

    if (query.isActive()) {
        while (query.next())
        {
         if (fecha_periodo_bloqueado(query.value(0).toDate())) return true;
        }
    }
    return false;
}


bool basedatos::int_asientos_con_enlace (QString asientoinicial,QString asientofinal,
                                            QString ejercicio) {
    QString cadena = "select enlace from diario where asiento>="+
                     asientoinicial.left(-1).replace("'","''");
    cadena += " and asiento<="+ asientofinal.left(-1).replace("'","''");
    cadena += " and ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);

    if (query.isActive()) {
        while (query.next())
        {
         if (query.value(0).toBool()) return true;
        }
    }
    return false;
}


bool basedatos::int_asientos_cuentas_bloqueadas (QString asientoinicial,QString asientofinal,
                                                 QString ejercicio) {
    QString cadena = "select cuenta,fecha from diario where asiento>="+ asientoinicial.left(-1).replace("'","''");
    cadena += " and asiento<="+ asientofinal.left(-1).replace("'","''");
    cadena += " and ejercicio='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);

    if (query.isActive()) {
        while (query.next())
        {
            if (existe_bloqueo_cta(query.value(0).toString(),
                                   query.value(1).toDate())) return true;
        }
    }
    return false;
}


bool basedatos::periodo_bloqueado_entre_fechas(QDate inicial, QDate final)
{
  QString cad = "select inicio,fin,deshabilitado from periodos";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     while (query.next())
      {
        if (inicial>=query.value(0).toDate() && inicial <=query.value(1).toDate() && query.value(2).toBool())
            return true;
        if (final>=query.value(0).toDate() && final <=query.value(1).toDate() && query.value(2).toBool())
            return true;
      }
  return false;
}


bool basedatos::hayperiodosbloqueados()
{
  QString cad = "select inicio,fin,deshabilitado from periodos where deshabilitado";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     if (query.next())
      {
       return true;
      }
  return false;
}


QString basedatos::documento_pase(QString pase)
{
  QString cad = "select documento from diario where pase=";
  cad+=pase;
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     if (query.next())
      {
       return query.value(0).toString();
      }
  return "";
}


// Actualiza el diario a partir de cuenta
void basedatos::updateDiariocipase (QString codigo, QString apunte) {
    QString cadena = "update diario set ci='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "' where pase=";
    cadena += apunte.left(-1).replace("'","''");
    ejecutar(cadena);
}


QStringList basedatos::listaoperaciones_recibidas()
{
    QStringList lista;
    QString cadquery = "SELECT codigo,descripcion from clave_op_recibidas order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if ( query.isActive() )
    {
      while (query.next())
        {
          QString cadena= query.value(1).toString();
          lista << cadena;
        }
    }
    return lista;
}


QStringList basedatos::listaoperaciones_expedidas()
{
    QStringList lista;
    QString cadquery = "SELECT codigo,descripcion from clave_op_expedidas order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if ( query.isActive() )
    {
      while (query.next())
        {
          QString cadena= query.value(1).toString();
          lista << cadena;
        }
    }
    return lista;
}

QStringList basedatos::listaoperaciones_ret()
{
    QStringList lista;
    QString cadquery = "SELECT codigo,descripcion from clave_op_retenciones order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if ( query.isActive() )
    {
      while (query.next())
        {
          QString cadena= query.value(1).toString();
          lista << cadena;
        }
    }
    return lista;
}


QStringList basedatos::lista_concepto_evpn()
{
    QStringList lista;
    QString cadquery = "SELECT codigo,descripcion from concepto_evpn_pymes order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if ( query.isActive() )
    {
      while (query.next())
        {
          QString cadena= query.value(0).toString() +"-"+query.value(1).toString();
          lista << cadena;
        }
    }
    return lista;
}

QStringList basedatos::listacodprovincias()
{
    QStringList lista;
    QString cadquery = "SELECT codigo,descripcion from provincias order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if ( query.isActive() )
    {
      while (query.next())
        {
          QString cadena= query.value(0).toString() +"-"+query.value(1).toString();
          lista << cadena;
        }
    }
    return lista;
}


void basedatos::agregados340(QDate inicial, QDate final, int *nregistros,
                             double *sumabi, double *sumacuota, double *itotal)
{
    QString cadquery;
        cadquery="select count(l.pase),sum(l.base_iva),sum(l.cuota_iva) from libroiva l, diario d "
                         "where d.fecha>='";
        cadquery+=inicial.toString("yyyy-MM-dd");
        cadquery+="' and d.fecha<='";
        cadquery+=final.toString("yyyy-MM-dd");
        cadquery+="' and l.pase=d.pase";

    // cadquery+= " and not (not soportado and aib and autofactura)";


    cadquery+=" and not (not soportado and (aib or autofactura or autofactura_no_ue "
         "or isp_op_interiores) )";


    // QMessageBox::information( 0, QObject::tr("base de datos"),cadquery);
    QSqlQuery query = ejecutar(cadquery);
    if ( query.isActive() )
    {
      if (query.next())
        {
          *nregistros = query.value(0).toInt();
          *sumabi = query.value(1).toDouble();
          *sumacuota = query.value(2).toDouble();
          *itotal=*sumabi+*sumacuota;
        }
    }

}


QSqlQuery basedatos::datoslibroiva(QDate inicial, QDate final)
{
    QSqlQuery query;
    QString cad="select s.cif, s.claveidfiscal, s.nifrprlegal, s.razon, s.pais, "
                "l.clave_operacion, l.fecha_fra, l.fecha_operacion, l.tipo_iva, "
                "l.base_iva, l.cuota_iva, l.bi_costo, l.nfacturas, l.finicial, l.ffinal,"
                "l.rectificada, l.tipo_re, d.documento, d.fecha, l.soportado, d.asiento, l.prorrata from "
                "diario d, libroiva l, datossubcuenta s where "
                "l.cuenta_fra=s.cuenta and d.pase=l.pase and ";

        cad+="d.fecha>='";
        cad+=inicial.toString("yyyy-MM-dd");
        cad+="' and d.fecha<='";
        cad+=final.toString("yyyy-MM-dd");
        cad+="'";

        cad+=" and not (not soportado and (aib or autofactura or autofactura_no_ue "
             "or isp_op_interiores) )";
    cad+=" order by d.fecha, d.asiento";

    // QMessageBox::information( 0, QObject::tr("base de datos"),cad);

    query.prepare(cad);
    return ejecuta(query);
}


//
void basedatos::updateDiarioCI (QString por, QString origen, bool fechas,
                                QDate inicial, QDate final) {


    // update diario_ci set ci='SE3' where ci in (select diario_ci.ci from diario,diario_ci
    // where diario.fecha='2013-01-01');
    if (basedatos::instancia()->analitica_tabla() && basedatos::instancia()->essqlite())
       {
        QString cadena="update diario_ci set ci='";
        cadena += por.left(-1).replace("'","''");
        cadena += "' where ci in (select diario_ci.ci from diario,diario_ci";
        cadena += " where diario_ci.ci='";
        cadena += origen.left(-1).replace("'","''");
        cadena += "'";
        if (fechas)
        {
            cadena += " and diario.fecha>='";
            cadena += inicial.toString("yyyy-MM-dd");
            cadena += "' and diario.fecha<='";
            cadena += final.toString("yyyy-MM-dd");
            cadena += "'";
        }
        cadena+=")";
        ejecutar(cadena);
        return;
       }


    if (basedatos::instancia()->analitica_tabla())
       {
        QString cadena="update diario_ci set ci='";
        cadena += por.left(-1).replace("'","''");
        cadena += "' from diario where diario_ci.ci='";
        cadena += origen.left(-1).replace("'","''");
        cadena += "'";
        if (fechas)
        {
            cadena += " and diario.fecha>='";
            cadena += inicial.toString("yyyy-MM-dd");
            cadena += "' and diario.fecha<='";
            cadena += final.toString("yyyy-MM-dd");
            cadena += "'";
        }
        cadena+=" and diario_ci.clave_ci=diario.clave_ci";
        ejecutar(cadena);
        return;
       }

    QString cadena = "update diario set ci='";
    cadena += por.left(-1).replace("'","''");
    cadena += "' where ci='";
    cadena += origen.left(-1).replace("'","''");
    cadena += "'";

    if (fechas)
    {
        cadena += " and fecha>='";
        cadena += inicial.toString("yyyy-MM-dd");
        cadena += "' and fecha<='";
        cadena += final.toString("yyyy-MM-dd");
        cadena += "'";
    }
    ejecutar(cadena);
}


// Datos del próximo copia documento
qlonglong basedatos::select_prox_doc () {
    QSqlQuery query = ejecutar("select prox_documento from configuracion");
    qlonglong vnum = 0;
    if ( (query.isActive()) &&(query.next()) )
    {
        vnum = query.value(0).toLongLong();
        if (vnum == 0)
            vnum = 1;
    }
    else vnum = 1;
    return vnum;
}

// Datos de la próxima domiciliación
qlonglong basedatos::select_prox_domiciliacion () {
    QSqlQuery query = ejecutar("select prox_domiciliacion from configuracion");
    qlonglong vnum = 0;
    if ( (query.isActive()) &&(query.next()) )
    {
        vnum = query.value(0).toLongLong();
        if (vnum == 0)
            vnum = 1;
    }
    else vnum = 1;
    return vnum;
}


// Actualiza el prox_documento de configuracion
void basedatos::updateConfiguracionprox_documento (QString cadnumpase) {
    ejecutar("update configuracion set prox_documento="+ cadnumpase.left(-1).replace("'","''"));
}


// Actualiza el prox_domiciliacion de configuracion
void basedatos::updateConfiguracionprox_domiciliacion (QString cadnum) {
    ejecutar("update configuracion set prox_domiciliacion="+ cadnum.left(-1).replace("'","''"));
}


// Actualiza campo copia_doc en diario
void basedatos::updatecopia_docdiario (QString cadnumasiento, QString copia_doc,
                                       QString ejercicio) {
    QString cadena;
    cadena="update diario set copia_doc='";
    cadena+=copia_doc.left(-1).replace("'","''");
    cadena+="' where asiento=";
    cadena+=cadnumasiento;
    cadena+=" and ejercicio='";
    cadena+=ejercicio.left(-1).replace("'","''");
    cadena+="'";
    ejecutar(cadena);
}

// Devuelve campo copia_doc en diario
QString basedatos::copia_docdiario (QString cadnumasiento, QString ejercicio) {
    QString doc;
    QString cadquery = "select copia_doc from diario where asiento="+cadnumasiento;
    cadquery+=" and ejercicio='";
    cadquery+=ejercicio.left(-1).replace("'","''");
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( query.isActive() )
    {
      if (query.next())
        {
          doc= query.value(0).toString();
        }
    }
    return doc;

}

// Borra campo copia_doc en diario
void basedatos::borra_docdiario (QString cadnumasiento) {
    ejecutar("update diario set copia_doc='' where asiento="+cadnumasiento);
}


QSqlQuery basedatos::select_codigo_nombre_usuarios()
{
    QSqlQuery query;
    QString cad="select  codigo, nombre from usuarios order by codigo;";
    query.prepare(cad);
    return ejecuta(query);
}


bool basedatos::existecodigousuario(QString contenido)
{
  QString cad = "select codigo from usuarios where codigo='";
  cad+=contenido.left(-1).replace("'","''");
  cad+="'";
  QSqlQuery query=ejecutar(cad);
  if (query.isActive())
     if (query.next())
      {
       return true;
      }
  return false;
}

void basedatos::adcodigousuario(QString contenido)
{
   QString cad="INSERT into usuarios (codigo) VALUES('";
   cad+=contenido.left(-1).replace("'","''");
   cad+="')";
   ejecutar(cad);
 }


void basedatos::borrausuario(QString contenido)
{
   QString cad="DELETE from usuarios where codigo='";
   cad+=contenido.left(-1).replace("'","''");
   cad+="';";
   ejecutar(cad);
 }


void basedatos::grabarusuario(QString codigo, QString nombre, QString clave, QString nif,
                              QString domicilio, QString poblacion, QString codigopostal,
                              QString provincia, QString pais, QString tfno, QString email,
                              QString observaciones, QString privilegios)
{
    /*
             "codigo         varchar(40) default '',"
             "nombre         varchar(254) default '',"
             "clave          varchar(40) default '',"
             "nif            varchar(40) default '',"
             "domicilio      varchar(80) default '',"
             "poblacion      varchar(80) default '',"
             "codigopostal   varchar(40) default '',"
             "provincia      varchar(80) default '',"
             "pais           varchar(80) default '',"
             "tfno           varchar(80) default '',"
             "email          varchar(80) default '',"
             "observaciones  varchar(255) default '',"
             "privilegios    varchar(255) default '',"
             "imagen         text,"
     */


   QString cad="update usuarios set nombre='";
   cad+=nombre.left(-1).replace("'","''");
   cad+="', clave='";
   cad+=clave.left(-1).replace("'","''");
   cad+="', nif='";
   cad+=nif.left(-1).replace("'","''");
   cad+="', domicilio='";
   cad+=domicilio.left(-1).replace("'","''");
   cad+="', poblacion='";
   cad+=poblacion.left(-1).replace("'","''");
   cad+="', codigopostal='";
   cad+=codigopostal.left(-1).replace("'","''");
   cad+="', provincia='";
   cad+=provincia.left(-1).replace("'","''");
   cad+="', pais='";
   cad+=pais.left(-1).replace("'","''");
   cad+="', tfno='";
   cad+=tfno.left(-1).replace("'","''");
   cad+="', email='";
   cad+=email.left(-1).replace("'","''");
   cad+="', observaciones='";
   cad+=observaciones.left(-1).replace("'","''");
   cad+="', privilegios='";
   cad+=privilegios.left(-1).replace("'","''");
   cad+="' where codigo='";
   cad+=codigo.left(-1).replace("'","''");
   cad+="'";
   ejecutar(cad);
 }


void basedatos::grabaimagenusuario(QString codigo, QString fototexto)
{
  QString cadquery = "UPDATE usuarios ";
  cadquery +="set imagen = '";
  cadquery += fototexto;
  cadquery += "' where codigo = '"+ codigo +"'";
  ejecutar(cadquery);
}


void basedatos::grabaimagenlogo(QString codigo, QString fototexto)
{
  QString cadquery = "UPDATE tipos_doc ";
  cadquery +="set imagen = '";
  cadquery += fototexto;
  cadquery += "' where codigo = '"+ codigo +"'";
  ejecutar(cadquery);
}


void basedatos::graba_config_imagenlogo(QString fototexto)
{
  QString cadquery = "UPDATE configuracion ";
  cadquery +="set imagen = '";
  cadquery += fototexto;
  cadquery += "'";
  ejecutar(cadquery);
}



QSqlQuery basedatos::recuperarusuario(QString codigo)
{
    QString cadquery = "select nombre, clave, nif, domicilio, poblacion,"
                       "codigopostal, provincia, pais, tfno, email, observaciones,"
                       "privilegios, imagen from usuarios where codigo='";
    cadquery+=codigo;
    cadquery+="';";
    return ejecutar(cadquery);
}

void basedatos::actu_privilegios_admin(QString cadena)
{
    QString cad="update usuarios set privilegios='";
    cad+=cadena;
    cad+="' where usuario='admin'";
    ejecutar(cad);

}


// Devuelve si se usa o no numeración relativa
bool basedatos::gestiondeusuarios() {
    QSqlQuery query = ejecutar("select gestion_usuarios from configuracion");
    if ( (query.isActive()) && (query.first()) )
            return query.value(0).toBool();
    return false;
}

bool basedatos::claveusuariook(QString usuario, QString clave)
{
    QSqlQuery query = ejecutar("select clave from usuarios where codigo='"+usuario+"'");
    if ( (query.isActive()) && (query.first()) )
            return query.value(0).toString()==clave;
    return false;
}

QString basedatos::privilegios(QString usuario)
{
    QSqlQuery query = ejecutar("select privilegios from usuarios where codigo='"+usuario+"'");
    if ( (query.isActive()) && (query.first()) )
            return query.value(0).toString();
    return QString();
}


QString basedatos::imagenusuario(QString codigo)
{
    QString cadquery = "select imagen from usuarios where codigo='";
    cadquery+=codigo;
    cadquery+="';";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
            return query.value(0).toString();
    return QString();
}


QSqlQuery basedatos::select_usuarios (QString db) {
    QString cadena = "select codigo, nombre, clave, nif, domicilio, poblacion,"
                       "codigopostal, provincia, pais, tfno, email, observaciones,"
                       "privilegios, imagen from usuarios";
    return ejecutar(cadena,db);
}


// Devuelve el saldo de una cuenta auxiliar en un ejercicio y diario determinado
double basedatos::saldocuentaendiarioejercicio (QString cuenta,QString ejercicio,QString diario) {
    QString cadquery = "select sum(debe-haber) from diario where cuenta='";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "' and fecha>='";
    cadquery += inicioejercicio(ejercicio).toString("yyyy-MM-dd");
    cadquery += "' and fecha<='";
    cadquery += finejercicio(ejercicio).toString("yyyy-MM-dd");
    cadquery += "' and diario='";
    cadquery += diario;
    cadquery += "'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDouble();
    }
    return 0;
}



// Devuelve el saldos de cuentas auxiliares en un ejercicio y diario determinado
QSqlQuery basedatos::saldo_auxs_endiarioejercicio (QString ejercicio,QString diario,
                                                bool intervalo, QString inicial, QString final) {
    QString cadquery = "select cuenta, sum(debe-haber) from diario where ";
    cadquery += "fecha>='";
    cadquery += inicioejercicio(ejercicio).toString("yyyy-MM-dd");
    cadquery += "' and fecha<='";
    cadquery += finejercicio(ejercicio).toString("yyyy-MM-dd");
    cadquery += "' and diario='";
    cadquery += diario;
    cadquery += "' ";
    if (intervalo)
       {
        cadquery+="and cuenta>='";
        cadquery+=inicial;
        cadquery+="' and cuenta<='";
        cadquery+=final;
        cadquery+="' ";
       }
    cadquery+="group by cuenta order by cuenta";
    // QMessageBox::warning( 0, QObject::tr("base de datos"),cadquery);
   return ejecutar(cadquery);
}


// devuelve el saldo hasta la fecha
QSqlQuery basedatos::saldo_auxs_hasta_fecha (QDate fecha,
                                             bool intervalo,
                                             QString inicial, QString final) {
    QString cadquery = "select cuenta, sum(debe-haber) from diario where ";
    QDate fechainicio=selectAperturaejercicios(selectEjerciciodelafecha(fecha));
    cadquery += "fecha>='";
    cadquery+=fechainicio.toString("yyyy-MM-dd");
    cadquery += "' and fecha<'";
    cadquery+=fecha.toString("yyyy-MM-dd");
    cadquery+="' and diario != '";
    cadquery+= diario_cierre();
    cadquery+="' ";
    if (intervalo)
       {
        cadquery+="and cuenta>='";
        cadquery+=inicial;
        cadquery+="' and cuenta<='";
        cadquery+=final;
        cadquery+="' ";
       }
    cadquery+="group by cuenta order by cuenta";
    // QMessageBox::warning( 0, QObject::tr("base de datos"),cadquery);
    return ejecutar(cadquery);
}


QSqlQuery basedatos::sumas_aux_dh ( QDate desde,
                                    QDate hasta,
                                    QString filtro,
                                    bool intervalo,
                                    QString inicial, QString final,
                                    bool analitica,
                                    QString ci)

{
    QString cadena;
    cadena = "select cuenta,sum(debe),sum(haber) from diario" ;

    cadena+=" where (fecha>='";
    cadena+=desde.toString("yyyy-MM-dd");
    cadena+="' and fecha<='";
    cadena+=hasta.toString("yyyy-MM-dd");
    cadena+="')";

    cadena += " and (";
    cadena += filtro;
    cadena += ")";

    if (analitica && ci.length()>0)
    {
        cadena += " and ";
        cadena += filtroci(ci,false);
    }
    if (intervalo)
       {
        cadena+=" and cuenta>='";
        cadena+=inicial;
        cadena+="' and cuenta<='";
        cadena+=final;
        cadena+="' ";
       }
    cadena+="group by cuenta order by cuenta";
    // QMessageBox::warning( 0, QObject::tr("base de datos"),cadena);

    return ejecutar(cadena);
}



// Devuelve datos de cuenta debe y haber para una cuenta en unas fechas con analitica-tabla
QSqlQuery basedatos::sumas_aux_dh_analitica_tabla (QDate fechaini,
                                                 QDate fechafin,
                                                 QString ci,
                                                 QString filtro,
                                                 bool intervalo,
                                                 QString inicial, QString final) {

    QString cadena;
    cadena = "select diario.cuenta, sum(diario.debe/abs(diario.debe+diario.haber)*diario_ci.importe), ";
    cadena+= "sum(diario.haber/abs(diario.debe+diario.haber)*diario_ci.importe) " ;
    cadena+= " from diario, diario_ci where ";
    cadena+= "diario_ci.clave_ci = diario.clave_ci and ";

    cadena += " diario.fecha>='";
    cadena += fechaini.toString("yyyy-MM-dd");
    cadena += "' and diario.fecha<='";
    cadena += fechafin.toString("yyyy-MM-dd");
    cadena += "'";

    if (intervalo)
       {
        cadena+=" and diario.cuenta>='";
        cadena+=inicial;
        cadena+="' and diario.cuenta<='";
        cadena+=final;
        cadena+="' ";
       }
    cadena +=" and (";
    cadena += filtro;
    cadena += ")";
;

    if (ci.length() > 0)
    {
        cadena += " and ";
        cadena += filtroci(ci,true);
    }

    cadena+=" group by diario.cuenta order by diario.cuenta";

    return ejecutar(cadena);
}


// Devuelve el saldo de una cuenta auxiliar en un ejercicio y diario determinado
double basedatos::saldocuenta_princi_endiarioejercicio (QString cuenta,QString ejercicio,
                                                        QString diario) {
    QString cadquery = "select sum(debe-haber) from diario where ";
    if (cualControlador() == SQLITE) {
            cadquery += "cuenta like '"+ cuenta.left(-1).replace("'","''") +"%'";
        }
        else {
            cadquery += "position('";
            cadquery += cuenta.left(-1).replace("'","''");
            cadquery += "' in cuenta)=1";
        }

    cadquery += " and fecha>='";
    cadquery += inicioejercicio(ejercicio).toString("yyyy-MM-dd");
    cadquery += "' and fecha<='";
    cadquery += finejercicio(ejercicio).toString("yyyy-MM-dd");
    cadquery += "' and diario='";
    cadquery += diario;
    cadquery += "'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDouble();
    }
    return 0;
}





bool basedatos::hayprocvenci(QString cuenta)
{
 //   QSqlQuery query;
    QString cadena="select procesavenci from datossubcuenta where cuenta='";
    cadena+=cuenta;
    cadena+="'";
//    query.prepare(cadena);
    // query.bindValue(":cuenta",cuenta);

  QSqlQuery query=ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
       {
        if (query.value(0).toBool())
           {
            return true;
           }
       }
    return false;
}


// comprueba si el asiento es un vencimiento procesado
bool basedatos::asiento_es_vencimiento_procesado(QString elasiento, QString ejercicio,
                                                 QString *qpase)
{
 QString cadena="select pase from diario where asiento=";
 cadena+=elasiento;
 cadena+=" and ejercicio='";
 cadena+=ejercicio;
 cadena+="'";
 cadena+=" and pase in (select pase_diario_vencimiento from vencimientos)";
 QSqlQuery query=ejecutar(cadena);
 if (query.isActive())
    if (query.next())
      {
        *qpase=query.value(0).toString();
        return true;
      }

 return false;
}


// Devuelve num vencimiento a partir de pase vto procesado
QString basedatos::numvto_paseprocesado(QString qpase)
{
 QString valor;
 QString cadena="select num from vencimientos where pase_diario_vencimiento=";
 cadena+=qpase;
 QSqlQuery query=ejecutar(cadena);
 if (query.isActive())
    if (query.next())
      {
        valor=query.value(0).toString();
      }

 return valor;
}


void basedatos::cambia_clave_usuario(QString codigo,QString clave)
{
    /*
             "codigo         varchar(40) default '',"
             "nombre         varchar(254) default '',"
             "clave          varchar(40) default '',"
             "nif            varchar(40) default '',"
             "domicilio      varchar(80) default '',"
             "poblacion      varchar(80) default '',"
             "codigopostal   varchar(40) default '',"
             "provincia      varchar(80) default '',"
             "pais           varchar(80) default '',"
             "tfno           varchar(80) default '',"
             "email          varchar(80) default '',"
             "observaciones  varchar(255) default '',"
             "privilegios    varchar(255) default '',"
             "imagen         text,"
     */


   QString cad="update usuarios set clave='";
   cad+=clave.left(-1).replace("'","''");
   cad+="' where codigo='";
   cad+=codigo.left(-1).replace("'","''");
   cad+="'";
   ejecutar(cad);
 }


// Devuelve si se ha generado ya el asiento de apertura
bool basedatos::aperturaendiarioenfechas (QString cadinicioej, QString cadfinej) {
    QString cadena = "select count(pase) from diario where fecha>='";
    cadena += cadinicioej;
    cadena += "' and fecha<='";
    cadena += cadfinej;
    cadena += "' and diario='";
    cadena += diario_apertura();
    cadena += "'";
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.next()) )
    {
        return query.value(0).toInt()>0;
    }
    return false;
}


QString basedatos::clavegastos()
{
  QString rdo;
  QString cadena = "select clave_gastos from configuracion";
  QSqlQuery query = ejecutar(cadena);
  if ( (query.isActive()) && (query.next()) )
     {
      return query.value(0).toString();
     }
  return rdo;

}


QString basedatos::claveingresos()
{

  QString rdo;
  QString cadena = "select clave_ingresos from configuracion";
  QSqlQuery query = ejecutar(cadena);
  if ( (query.isActive()) && (query.next()) )
     {
      return query.value(0).toString();
     }
  return rdo;
}


QSqlQuery basedatos::listacuentasauxiliares(QString filtro)
{
    int anchocuentas=selectAnchocuentasconfiguracion();
    QString cadena;
    if (anchocuentas>0)
        {
           if (es_sqlite()) cadena="length(codigo)=";
           else cadena="char_length(codigo)=";
           QString cadnum;
           cadnum.setNum(selectAnchocuentasconfiguracion());
           cadena+=cadnum;
        }
        else {
               if (es_sqlite()) { cadena = "auxiliar = 1"; }
                 else {  cadena="auxiliar"; }
             }

    QString cadquery = "select codigo, descripcion from plancontable where ";
    cadquery+=cadena;
    cadquery+=" and ( ";
    cadquery+=filtro;
    cadquery+=" ) order by codigo;";
    // QMessageBox::information( 0, "Consulta",cadquery);
    return ejecutar(cadquery);
}



bool basedatos::existe_input_cuenta (QString ci, QString cta) {
    QString cadena = "select ci from input_cta where ci='";
    cadena += ci.left(-1).replace("'","''");
    cadena += "' and auxiliar='";
    cadena += cta.left(-1).replace("'","''");
    cadena += "'";

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive())&& (query.first()) ) return true;
    return false;
}


void basedatos::updateCi_input_cta (QString asignacion, QString cuenta, QString ci) {
    QString cadquery = "UPDATE input_cta SET imputacion=";
    cadquery += asignacion.left(-1).replace("'","''");
    cadquery += " WHERE auxiliar='";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "' and ci='";
    cadquery += ci.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}


void basedatos::insertCi_input_cta (QString cuenta, QString ci, QString asignacion) {
    QString cadquery = "insert into input_cta (auxiliar,ci,imputacion) values ('";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += ci.left(-1).replace("'","''");
    cadquery += "',";
    cadquery += asignacion.left(-1).replace("'","''");
    cadquery += ")";
    ejecutar(cadquery);
}

void basedatos::clona_input(QString origen, QString destino)
{
  QString cadena="delete from input_cta where auxiliar='";
  cadena+=destino;
  cadena+="'";
  ejecutar(cadena);
  cadena="select ci,imputacion from input_cta where auxiliar='";
  cadena+=origen;
  cadena+="'";
  QSqlQuery q=ejecutar(cadena);
  if (q.isActive())
      while (q.next())
      {
       insertCi_input_cta(destino,q.value(0).toString(),q.value(1).toString());
      }
}


QSqlQuery basedatos::selectSumasignacion100input_cta (QString cuenta) {
    QString cadena = "select sum(imputacion)*100 from input_cta where auxiliar='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "'";
    return ejecutar(cadena);
}


// Indica si es contabilidad analítica con desglose en tabla
bool basedatos::analitica_tabla () {
    QSqlQuery query = ejecutar("select analitica_tabla from configuracion");
    if ( (query.isActive()) && (query.next()) )
        return query.value(0).toBool();
    return false;
}



QSqlQuery basedatos::ci_input(QString cuenta)
{
    QString cadquery = "select ci, imputacion from input_cta where auxiliar='";
    cadquery+=cuenta;
    cadquery+="'";
    cadquery+=" order by ci;";
    // QMessageBox::information( 0, "Consulta",cadquery);
    return ejecutar(cadquery);
}

bool basedatos::ci_input_ok (QString cuenta) {
    QString cadquery="select sum(imputacion) from input_cta where auxiliar='";
    cadquery+=cuenta;
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.next()) )
       {
        if (query.value(0).toDouble()>1.0001 || query.value(0).toDouble()<0.9999)
           return false;
        else return true;
       }
    return false;
}


qlonglong basedatos::prox_num_ci()
{
    QString cadena = "select prox_num_ci from configuracion";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
            return query.value(0).toLongLong();
    return 0;
}


void basedatos::incrementa_prox_num_ci()
{
    QString cadena = "update configuracion set prox_num_ci=prox_num_ci+1";
    ejecutar(cadena);
}

void basedatos::inserta_diario_ci(QString cadproxci, QString ci, QString importe)
{
  QString cadena="insert into diario_ci (clave_ci, ci, importe) values (";
  cadena+=cadproxci.left(-1).replace("'","''");
  cadena+=", '";
  cadena+=ci.left(-1).replace("'","''");
  cadena+="', ";
  cadena+=importe.left(-1).replace("'","''");
  cadena+=")";
  ejecutar(cadena);
}


// Borra de la tabla diario_ci registros con clave_ci suministrada
void basedatos::deletediarioci_clave (QString clave) {
    ejecutar("delete from diario_ci where clave_ci="+ clave);
}

QSqlQuery basedatos::carga_de_tabla_ci(QString qclave_ci)
{
    QString cadquery = "select ci, importe from diario_ci where clave_ci='";
    cadquery+=qclave_ci;
    cadquery+="'";
    // QMessageBox::information( 0, "Consulta",cadquery);
    return ejecutar(cadquery);
}


QSqlQuery basedatos::cons_diario_ci (QDate inicial, QDate final, QString filtro) {
    // diario
    QString cadena = "select diario.fecha, diario.asiento, diario.concepto,"
                     "diario.debe/abs(diario.debe+diario.haber)*diario_ci.importe,"
                     "diario.haber/abs(diario.debe+diario.haber)*diario_ci.importe,"
                     "diario.documento,diario.cuenta,diario_ci.ci "
                     "from diario, diario_ci where "
                     "diario_ci.clave_ci = diario.clave_ci "
                     "and diario.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena +="' and diario.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena +="'";
    if (filtro.length()>0)
    {
        cadena+=" and ";
        cadena+=filtro;
    }

    // cadena+="' and cuenta='";
    // cadena+=ui.subcuentalineEdit->text();
    cadena+=" order by diario.fecha,diario.pase";
    return ejecutar(cadena);
}


qlonglong basedatos::count_regs_diario_ci (QDate inicial, QDate final, QString filtro) {
    QString cadena = "select count(diario_ci.clave_ci) from diario, diario_ci where "
                     "diario_ci.clave_ci = diario.clave_ci "
                     "and diario.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and diario.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "'";
    if (filtro.length() > 0)
    {
        cadena += " and ";
        cadena += filtro;
    }
    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toLongLong();
    }
    return 0;
}


QSqlQuery basedatos::cons_diario_ci_tex (QDate inicial, QDate final, QString filtro) {
    // diario
    QString cadena = "select diario.fecha, diario.asiento, diario.concepto, "
                     "diario.cuenta, diario_ci.ci, "
                     "diario.debe/(diario.debe+diario.haber)*diario_ci.importe, "
                     "diario.haber/(diario.debe+diario.haber)*diario_ci.importe, "
                     "diario.documento "
                     "from diario, diario_ci where "
                     "diario_ci.clave_ci = diario.clave_ci "
                     "and diario.fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena +="' and diario.fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena +="'";
    if (filtro.length()>0)
    {
        cadena+=" and ";
        cadena+=filtro;
    }

    // cadena+="' and cuenta='";
    // cadena+=ui.subcuentalineEdit->text();
    cadena+=" order by diario.fecha,diario.pase";
    return ejecutar(cadena);
}

QSqlQuery basedatos::lista_inputs_cuenta(QString cuenta, QDate fecha_ini,
                                        QDate fecha_fin, bool esdebe)
{
 QString cadena="select c.ci, sum(c.importe) from diario d, diario_ci c where "
                "d.fecha<='";
 cadena+=fecha_fin.toString("yyyy-MM-dd");
 cadena+="' and d.fecha>='";
 cadena+=fecha_ini.toString("yyyy-MM-dd");
 cadena+="' and d.cuenta='";
 cadena+=cuenta;
 cadena+="' ";
 if (esdebe)
     cadena+="and (d.debe>0 or d.haber<0) ";
   else
       cadena+="and (d.haber>0 or d.debe<0) ";
cadena+="and c.clave_ci=d.clave_ci  group by c.ci order by c.ci;";
return ejecutar(cadena);
}

// Devuelve datos de cuenta debe y haber para una cuenta en unas fechas con analitica-tabla
QSqlQuery basedatos::debehaber_ci (QString codigo, QDate fechaini,
                                   QDate fechafin, QString subcadena, QString ci) {
    QString cadena = "select diario.cuenta, "
                     "sum(diario.debe/abs(diario.debe+diario.haber)*diario_ci.importe)- "
                     "sum(diario.haber/abs(diario.debe+diario.haber)*diario_ci.importe)"
                     " from diario, diario_ci where "
                     "diario_ci.clave_ci = diario.clave_ci and ";
    if (cualControlador() == SQLITE) {
        cadena += "diario.cuenta like '"+ codigo.left(-1).replace("'","''") +"%'";
    }
    else {
        cadena += "position('";
        cadena += codigo.left(-1).replace("'","''");
        cadena += "' in diario.cuenta)=1";
    }
    cadena += " and fecha>='";
    cadena += fechaini.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += fechafin.toString("yyyy-MM-dd");
    cadena += "'";

    cadena += subcadena;

    if (ci.length() > 0)
    {
        cadena += " and ";
        cadena += filtroci(ci,true);
    }

    cadena+=" group by cuenta";

    return ejecutar(cadena);
}


QSqlQuery basedatos::pase_bi_libroiva()
{
    QString cadquery = "select pase, base_iva, cuota_iva, fecha_fra, soportado from libroiva;";
    return ejecutar(cadquery);
}

double basedatos::anotacion_pase(QString pase)
{
    QString cadena = "select debe-haber from diario where pase=";
    cadena+=pase;
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
            return query.value(0).toDouble();
    return 0;
}


QSqlQuery basedatos::ctaord_pase_vtos()
{
    QString cadquery = "select cta_ordenante, pase_diario_operacion, fecha_operacion from vencimientos;";
    return ejecutar(cadquery);
}


QSqlQuery basedatos::descuadres_diario()
{
    QString cadquery = "select asiento, fecha, sum(debe) as sumadebe,sum(haber) as sumahaber "
                       "from diario group by asiento,fecha having "
                       "(sum(debe)-sum(haber)<-0.001 or sum(debe)-sum(haber)>0.001);";
    return ejecutar(cadquery);
}



QString basedatos::cuenta_pase(QString pase)
{
    QString cadena = "select cuenta from diario where pase=";
    cadena+=pase;
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
            return query.value(0).toString();
    return QString();
}


QString basedatos::notas()
{
    QString cadena = "select notas from configuracion";
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
            return query.value(0).toString();
    return QString();
}



void basedatos::grabarnotas(QString notas)
{

   QString cad="update configuracion set notas='";
   cad+=notas.left(-1).replace("'","''");
   cad+="'";
   ejecutar(cad);
 }


QSqlQuery basedatos::pase_libroiva()
{
    QString cadquery = "select pase from libroiva;";
    return ejecutar(cadquery);
}


QDate basedatos::fecha_pase_diario(QString pase)
{
    QString cadena = "select fecha from diario where pase=";
    cadena+=pase;
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
            return query.value(0).toDate();
    return QDate();

}

void basedatos::borra_reg_libroiva(QString pase)
{
   QString cad="delete from libroiva where pase=";
   cad+=pase;
   ejecutar(cad);
 }


QSqlQuery basedatos::num_pase_vencimientos()
{
    QString cadquery = "select num, pase_diario_operacion from vencimientos;";
    return ejecutar(cadquery);
}


QSqlQuery basedatos::num_pase_retenciones()
{
    QString cadquery = "select pase from retenciones;";
    return ejecutar(cadquery);
}



// devuelve clave y fechas de facturas
QSqlQuery basedatos::clave_fechafac_facturas()
{
    QString cadquery = "select clave, fecha_fac from facturas;";
    return ejecutar(cadquery);
}


// devuelve clave y fechas de facturas
QSqlQuery basedatos::borra_lin_fact_clave(QString clave)
{
    QString cadquery = "delete from lin_fact where clave=";
    cadquery+=clave;
    return ejecutar(cadquery);
}


// devuelve clave y fechas de facturas
QSqlQuery basedatos::borra_facturas_clave(QString clave)
{
    QString cadquery = "delete from facturas where clave=";
    cadquery+=clave;
    return ejecutar(cadquery);
}


void basedatos::borra_reg_vencimientos(QString num)
{
   QString cad="delete from vencimientos where num=";
   cad+=num;
   ejecutar(cad);
 }


void basedatos::borra_reg_retenciones(QString pase)
{
   QString cad="delete from retenciones where pase=";
   cad+=pase;
   ejecutar(cad);
 }


void basedatos::borra_ej_presupuesto(QString ejercicio)
{
   QString cad="delete from presupuesto where ejercicio='";
   cad+=ejercicio.left(-1).replace("'","''");
   cad+="'";
   ejecutar(cad);
 }


void basedatos::borra_ej_ajustesconci(QDate inicial, QDate final)
{
    QString cad="delete from ajustes_conci where fecha>='";
    cad+=inicial.toString("yyyy-MM-dd");
    cad+="' and fecha<='";
    cad+=final.toString("yyyy-MM-dd");
    cad+="'";
    ejecutar(cad);
}


void basedatos::borra_ej_cuenta_ext_conci(QDate inicial, QDate final)
{
    QString cad="delete from cuenta_ext_conci where fecha>='";
    cad+=inicial.toString("yyyy-MM-dd");
    cad+="' and fecha<='";
    cad+=final.toString("yyyy-MM-dd");
    cad+="'";
    ejecutar(cad);
}


QSqlQuery basedatos::clave_ci_diario_ci()
{
    QString cadquery = "select clave_ci from diario_ci;";
    return ejecutar(cadquery);
}


QDate basedatos::fecha_clave_ci_diario(QString clave_ci)
{
    QString cadena = "select fecha from diario where clave_ci=";
    cadena+=clave_ci;
    QSqlQuery query = ejecutar(cadena);

    if ( (query.isActive()) && (query.first()) )
            return query.value(0).toDate();
    return QDate();

}


void basedatos::borra_clave_ci_diario_ci(QString clave_ci)
{
   QString cad="delete from diario_ci where clave_ci=";
   cad+=clave_ci;
   ejecutar(cad);
 }


void basedatos::borra_diario_entrefechas(QDate inicial, QDate final)
{
    QString cad="delete from diario where fecha>='";
    cad+=inicial.toString("yyyy-MM-dd");
    cad+="' and fecha<='";
    cad+=final.toString("yyyy-MM-dd");
    cad+="'";
    ejecutar(cad);
}


void basedatos::delete_input_cta (QString cuenta, QString ci) {
    QString cadquery = "delete from input_cta where auxiliar='";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "' and ci='";
    cadquery += ci.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}


bool basedatos::existecodigoserie (QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descrip from series_fact where codigo = '"+
                       cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return true;
    }
    return false;
}


// Actualiza la descripción del código de la serie facturación
void basedatos::guardacambiosserie (QString codigo,QString descripcion) {
    QString cadquery = "UPDATE series_fact SET descrip='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE codigo='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}


// Inserta datos en la tabla serie facturación
void basedatos::insertserie (QString codigo,QString descripcion) {
    QString cadquery = "INSERT INTO series_fact (codigo,descrip) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}


// Borra un código de serie
void basedatos::eliminaserie (QString qcodigo) {
    ejecutar("delete from series_fact where codigo= '"+ qcodigo.left(-1).replace("'","''") +"'");
}


bool basedatos::existecodigoserienum (QString cadena,QString *qdescrip, qlonglong *num) {
    QString cadquery = "SELECT codigo,descrip,proxnum from series_fact where codigo = '"+
                       cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        *num=query.value(2).toLongLong();
        return true;
    }
    return false;
}

void basedatos::actualizaserie(QString codigo, QString descrip, QString proxdoc)
{
  QString cadquery= "update series_fact set descrip='";
  cadquery+=descrip.left(-1).replace("'","''");
  cadquery+="' where codigo='";
  cadquery+=codigo;
  cadquery+="'";
  ejecutar(cadquery);

  cadquery= "update series_fact set proxnum=";
  cadquery+=proxdoc.left(-1).replace("'","''");
  cadquery+=" where codigo='";
  cadquery+=codigo;
  cadquery+="'";
  ejecutar(cadquery);

}


bool basedatos::existecodigo_fact_predef (QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descrip from facturas_predef where codigo = '"+
                       cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return true;
    }
    return false;
}

bool basedatos::existecodigo_lote (QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descripcion from lote_fact where codigo = '"+
                       cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return true;
    }
    return false;
}



bool basedatos::existecodigotipos_doc (QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descrip from tipos_doc where codigo = '"+
                       cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return true;
    }
    return false;
}

QSqlQuery basedatos::datos_cabecera_fact_predef(QString codigo)
{
    QString cadquery = "select descrip, serie, contabilizable, con_ret,"
                       "con_re, tipo_ret, tipo_doc, notas, pie1, pie2 "
                       "from facturas_predef where codigo='";
    cadquery+=codigo;
    cadquery+="'";
    return ejecutar(cadquery);
}


// Actualiza la descripción del código de facturas_predef
void basedatos::guardacambios_facturas_predef (QString codigo,QString descripcion) {
            QString cadquery = "UPDATE facturas_predef SET descrip='";
            cadquery += descripcion.left(-1).replace("'","''");
            cadquery += "' WHERE codigo='";
            cadquery += codigo.left(-1).replace("'","''");
            cadquery += "'";
            ejecutar(cadquery);
        }

// Actualiza la descripción del código de lote
void basedatos::guardacambios_lote (QString codigo,QString descripcion) {
            QString cadquery = "UPDATE lote_fact SET descripcion='";
            cadquery += descripcion.left(-1).replace("'","''");
            cadquery += "' WHERE codigo='";
            cadquery += codigo.left(-1).replace("'","''");
            cadquery += "'";
            ejecutar(cadquery);
        }


// Inserta datos en la tabla facturas_predef
void basedatos::insertfacturas_predef (QString codigo,QString descripcion) {
    QString cadquery = "INSERT INTO facturas_predef (codigo,descrip) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}


// Inserta datos en la tabla lote_fact
void basedatos::insertfacturas_lote (QString codigo,QString descripcion) {
    QString cadquery = "INSERT INTO lote_fact (codigo,descripcion) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}


// Actualiza la descripción del código de tipo documento
void basedatos::guardacambiostipos_doc (QString codigo,QString descripcion) {
    QString cadquery = "UPDATE tipos_doc SET descrip='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE codigo='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}


// Inserta datos en la tabla tipos documento
void basedatos::inserttipos_doc (QString codigo,QString descripcion) {
    QString cadquery = "INSERT INTO tipos_doc (codigo,descrip) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}


// Borra un código de facturas_predef
void basedatos::elimina_factura_predef (QString qcodigo) {
    ejecutar("delete from facturas_predef where codigo= '"+ qcodigo.left(-1).replace("'","''") +"'");
}


// Borra un código de serie
void basedatos::eliminatipo_doc (QString qcodigo) {
    ejecutar("delete from tipos_doc where codigo= '"+ qcodigo.left(-1).replace("'","''") +"'");
}


// Borra facturas predefinidas
void basedatos::eliminafra_predefinida (QString qcodigo) {
    // primero eliminar líneas
    ejecutar("delete from lin_fact_predef where codigo= '"+ qcodigo.left(-1).replace("'","''") +"'");

    // luego eliminar cabecera

    ejecutar("delete from facturas_predef where codigo= '"+ qcodigo.left(-1).replace("'","''") +"'");
}


// Borra facturas predefinidas
void basedatos::eliminalote_fact (QString qcodigo) {
    // primero eliminar líneas
    ejecutar("delete from lin_lote_predef where codigo= '"+ qcodigo.left(-1).replace("'","''") +"'");

    // luego eliminar cabecera

    ejecutar("delete from lote_fact where codigo= '"+ qcodigo.left(-1).replace("'","''") +"'");
}


QStringList basedatos::listacodseries()
{
    QStringList listaseries;
    QString cadquery = "SELECT codigo from series_fact order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if  (query.isActive() )
        while (query.next())
         {
          listaseries << query.value(0).toString();
         }
   return listaseries;
}

QStringList basedatos::listacoddocs()
{
    QStringList listadocs;
    QString cadquery = "SELECT codigo from tipos_doc order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if (query.isActive())
        while (query.next())
         {
          listadocs << query.value(0).toString();
         }
   return listadocs;
}


QStringList basedatos::lista_cod_predef()
{
    QStringList listacods;
    QString cadquery = "SELECT codigo from facturas_predef order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if (query.isActive())
        while (query.next())
         {
          listacods << query.value(0).toString();
         }
   return listacods;
}



QString basedatos::imagendoc(QString codigo)
{
    QString cadquery = "SELECT imagen from tipos_doc where codigo='";
    cadquery+=codigo;
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    QString imagen;
    if ( (query.isActive()) && (query.first()) )
    {
     imagen=query.value(0).toString();
    }
    return imagen;
}


QString basedatos::logo_empresa()
{
    QString cadquery = "SELECT imagen from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    QString imagen;
    if ( (query.isActive()) && (query.first()) )
    {
     imagen=query.value(0).toString();
    }
    return imagen;
}



void basedatos::carga_tipo_doc(QString codigo,
                               QString *descrip,
                               QString *serie,
                               QString *pie1,
                               QString *pie2,
                               QString *moneda,
                               QString *codigo_moneda,
                               bool *contabilizable,
                               bool *rectificativo,
                               int *tipo_operacion,
                               QString *documento,
                               QString *cantidad,
                               QString *referencia,
                               QString *descripref,
                               QString *precio,
                               QString *totallin,
                               QString *bi,
                               QString *tipoiva,
                               QString *tipore,
                               QString *cuota,
                               QString *cuotare,
                               QString *totalfac,
                               QString *notas,
                               QString *venci,
                               QString *notastex,
                               QString *cif_empresa,
                               QString *cif_cliente,
                               QString *numero,
                               QString *fecha,
                               QString *cliente,
                               QString *descuento,
                               QString *totallineas,
                               QString *lineas_doc,
                               QString *nombre_emisor,
                               QString *domicilio_emisor,
                               QString *cp_emisor,
                               QString *poblacion_emisor,
                               QString *provincia_emisor,
                               QString *pais_emisor,
                               QString *cif_emisor,
                               QString *id_registral,
                               QString *imagen)
{

    QString cadquery = "SELECT descrip, serie, pie1, pie2, moneda,"
                       "codigo_moneda, contabilizable, rectificativo, "
                       "tipo_operacion,"
                       "documento, "
                       "cantidad, "
                       "referencia, "
                       "descripref, "
                       "precio, "
                       "totallin, "
                       "bi, "
                       "tipoiva, "
                       "tipore, "
                       "cuota, "
                       "cuotare, "
                       "totalfac, "
                       "notas, "
                       "venci, notastex, cif_empresa, cif_cliente, "
                       "numero, fecha, cliente, descuento, "
                       "totallineas, lineas_doc, nombre_emisor, domicilio_emisor, "
                       "cp_emisor, poblacion_emisor, provincia_emisor, pais_emisor, "
                       "cif_emisor, id_registral, "
                       "imagen from tipos_doc where codigo='";
    cadquery+=codigo;
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
     *descrip=query.value(0).toString();
     *serie=query.value(1).toString();
     *pie1=query.value(2).toString();
     *pie2=query.value(3).toString();
     *moneda=query.value(4).toString();
     *codigo_moneda=query.value(5).toString();
     *contabilizable=query.value(6).toBool();
     *rectificativo=query.value(7).toBool();
     *tipo_operacion=query.value(8).toInt();
     *documento=query.value(9).toString();
     *cantidad=query.value(10).toString();
     *referencia=query.value(11).toString();
     *descripref=query.value(12).toString();
     *precio=query.value(13).toString();
     *totallin=query.value(14).toString();
     *bi=query.value(15).toString();
     *tipoiva=query.value(16).toString();
     *tipore=query.value(17).toString();
     *cuota=query.value(18).toString();
     *cuotare=query.value(19).toString();
     *totalfac=query.value(20).toString();
     *notas=query.value(21).toString();
     *venci=query.value(22).toString();
     *notastex=query.value(23).toString();
     *cif_empresa=query.value(24).toString();
     *cif_cliente=query.value(25).toString();
     *numero=query.value(26).toString();
     *fecha=query.value(27).toString();
     *cliente=query.value(28).toString();
     *descuento=query.value(29).toString();
     *totallineas=query.value(30).toString();
     *lineas_doc=query.value(31).toString();
     *nombre_emisor=query.value(32).toString();
     *domicilio_emisor=query.value(33).toString();
     *cp_emisor=query.value(34).toString();
     *poblacion_emisor=query.value(35).toString();
     *provincia_emisor=query.value(36).toString();
     *pais_emisor=query.value(37).toString();
     *cif_emisor=query.value(38).toString();
     *id_registral=query.value(39).toString();
     *imagen=query.value(40).toString();
    }
}

bool basedatos::es_euro_tipo_doc(QString codigo)
{
  QString cadquery="select codigo_moneda from tipos_doc where codigo='";
  cadquery+=codigo;
  cadquery+="'";
  QSqlQuery query = ejecutar(cadquery);
  if ( (query.isActive()) && (query.first()) )
    {
     if (query.value(0).toString()=="EUR")
        return true;
    }
  return false;
}

void basedatos::msjs_tipo_doc(QString codigo,
                               QString *documento,
                               QString *cantidad,
                               QString *referencia,
                               QString *descripref,
                               QString *precio,
                               QString *totallin,
                               QString *bi,
                               QString *tipoiva,
                               QString *tipore,
                               QString *cuota,
                               QString *cuotare,
                               QString *totallineas,
                               QString *totalfac,
                               QString *notas,
                               QString *venci,
                               QString *cif_empresa,
                               QString *cif_cliente,
                               QString *numero,
                               QString *fecha,
                               QString *cliente,
                               QString *descuento)
{
    QString cadquery = "SELECT "
                       "documento, "
                       "cantidad, "
                       "referencia, "
                       "descripref, "
                       "precio, "
                       "totallin, "
                       "bi, "
                       "tipoiva, "
                       "tipore, "
                       "cuota, "
                       "cuotare, "
                       "totallineas, "
                       "totalfac, "
                       "notas, "
                       "venci, cif_empresa, cif_cliente, "
                       "numero, fecha, cliente, descuento "
                       "from tipos_doc where codigo='";
    cadquery+=codigo;
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
     *documento=query.value(0).toString();
     *cantidad=query.value(1).toString();
     *referencia=query.value(2).toString();
     *descripref=query.value(3).toString();
     *precio=query.value(4).toString();
     *totallin=query.value(5).toString();
     *bi=query.value(6).toString();
     *tipoiva=query.value(7).toString();
     *tipore=query.value(8).toString();
     *cuota=query.value(9).toString();
     *cuotare=query.value(10).toString();
     *totallineas=query.value(11).toString();
     *totalfac=query.value(12).toString();
     *notas=query.value(13).toString();
     *venci=query.value(14).toString();
     *cif_empresa=query.value(15).toString();
     *cif_cliente=query.value(16).toString();
     *numero=query.value(17).toString();
     *fecha=query.value(18).toString();
     *cliente=query.value(19).toString();
     *descuento=query.value(20).toString();
    }
}



void basedatos::datos_empresa_tipo_doc(QString codigo,
                                       QString *nombre_emisor,
                                       QString *domicilio_emisor,
                                       QString *cp_emisor,
                                       QString *poblacion_emisor,
                                       QString *provincia_emisor,
                                       QString *pais_emisor,
                                       QString *cif_emisor,
                                       QString *id_registral
                                       )
{
    QString cadquery = "SELECT "
                       "nombre_emisor, "
                       "domicilio_emisor, "
                       "cp_emisor, "
                       "poblacion_emisor, "
                       "provincia_emisor, "
                       "pais_emisor, "
                       "cif_emisor, "
                       "id_registral "
                       "numero, fecha, cliente, descuento "
                       "from tipos_doc where codigo='";
    cadquery+=codigo;
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
     *nombre_emisor=query.value(0).toString();
     *domicilio_emisor=query.value(1).toString();
     *cp_emisor=query.value(2).toString();
     *poblacion_emisor=query.value(3).toString();
     *provincia_emisor=query.value(4).toString();
     *pais_emisor=query.value(5).toString();
     *cif_emisor=query.value(6).toString();
     *id_registral=query.value(7).toString();
    }
}


QString basedatos::descrip_doc(QString codigo)
{
    QString cadquery = "SELECT descrip "
                       "from tipos_doc where codigo='";
    cadquery+=codigo;
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return QString();
}


int basedatos::lineas_doc(QString codigo)
{
    QString cadquery = "SELECT lineas_doc "
                       "from tipos_doc where codigo='";
    cadquery+=codigo;
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toInt();
    }
    return 0;
}


void basedatos::guarda_tipo_doc(QString codigo,
                               QString descrip,
                               QString serie,
                               QString pie1,
                               QString pie2,
                               QString moneda,
                               QString codigo_moneda,
                               bool contabilizable,
                               bool rectificativo,
                               QString tipo_operacion,
                               QString documento,
                               QString cantidad,
                               QString referencia,
                               QString descripref,
                               QString precio,
                               QString totallin,
                               QString bi,
                               QString tipoiva,
                               QString tipore,
                               QString cuota,
                               QString cuotare,
                               QString totalfac,
                               QString notas,
                               QString venci,
                               QString notastex,
                               QString cif_empresa,
                               QString cif_cliente,
                               QString numero,
                               QString fecha,
                               QString cliente,
                               QString descuento,
                               QString totallineas,
                               QString lineas_doc,  // tipo int
                               QString nombre_emisor,
                               QString domicilio_emisor,
                               QString cp_emisor,
                               QString poblacion_emisor,
                               QString provincia_emisor,
                               QString pais_emisor,
                               QString cif_emisor,
                               QString id_registral
                               )
{

  QString cadquery = "update tipos_doc set descrip='";
  cadquery+=descrip.left(-1).replace("'","''");
  cadquery+="', serie='";
  cadquery+=serie.left(-1).replace("'","''");
  cadquery+="', pie1='";
  cadquery+=pie1.left(-1).replace("'","''");
  cadquery+="', pie2='";
  cadquery+=pie2.left(-1).replace("'","''");
  cadquery+="', moneda='";
  cadquery+=moneda.left(-1).replace("'","''");
  cadquery+="', codigo_moneda='";
  cadquery+=codigo_moneda.left(-1).replace("'","''");
  cadquery+="', contabilizable=";
  if ( cualControlador() == SQLITE )
      cadquery+=contabilizable ? "1":"0";
    else
        cadquery+=contabilizable ? "true":"false";
    cadquery+=", rectificativo=";
    if ( cualControlador() == SQLITE )
        cadquery+=rectificativo ? "1":"0";
      else
          cadquery+=rectificativo ? "true":"false";

  cadquery+=", tipo_operacion=";
  cadquery+=tipo_operacion;

  cadquery+=", documento='";
  cadquery+=documento.left(-1).replace("'","''");
  cadquery+="', cantidad='";
  cadquery+=cantidad.left(-1).replace("'","''");
  cadquery+="', referencia='";
  cadquery+=referencia.left(-1).replace("'","''");
  cadquery+="', descripref='";
  cadquery+=descripref.left(-1).replace("'","''");
  cadquery+="', precio='";
  cadquery+=precio.left(-1).replace("'","''");
  cadquery+="', totallin='";
  cadquery+=totallin.left(-1).replace("'","''");
  cadquery+="', bi='";
  cadquery+=bi.left(-1).replace("'","''");
  cadquery+="', tipoiva='";
  cadquery+=tipoiva.left(-1).replace("'","''");
  cadquery+="', tipore='";
  cadquery+=tipore.left(-1).replace("'","''");
  cadquery+="', cuota='";
  cadquery+=cuota.left(-1).replace("'","''");
  cadquery+="', cuotare='";
  cadquery+=cuotare.left(-1).replace("'","''");
  cadquery+="', totalfac='";
  cadquery+=totalfac.left(-1).replace("'","''");
  cadquery+="', notas='";
  cadquery+=notas.left(-1).replace("'","''");
  cadquery+="', venci='";
  cadquery+=venci.left(-1).replace("'","''");
  cadquery+="', notastex='";
  cadquery+=notastex.left(-1).replace("'","''");
  cadquery+="', cif_empresa='";
  cadquery+=cif_empresa.left(-1).replace("'","''");
  cadquery+="', cif_cliente='";
  cadquery+=cif_cliente.left(-1).replace("'","''");
  cadquery+="', numero='";
  cadquery+=numero.left(-1).replace("'","''");
  cadquery+="', fecha='";
  cadquery+=fecha.left(-1).replace("'","''");
  cadquery+="', cliente='";
  cadquery+=cliente.left(-1).replace("'","''");
  cadquery+="', descuento='";
  cadquery+=descuento.left(-1).replace("'","''");
  cadquery+="',";

  cadquery+=" totallineas='";
  cadquery+=totallineas.left(-1).replace("'","''");
  cadquery+="', lineas_doc=";
  cadquery+= lineas_doc.isEmpty() ? "0" : lineas_doc;  // tipo int
  cadquery+=", nombre_emisor='";
  cadquery+=nombre_emisor.left(-1).replace("'","''");
  cadquery+="', domicilio_emisor='";
  cadquery+=domicilio_emisor.left(-1).replace("'","''");
  cadquery+="', cp_emisor='";
  cadquery+=cp_emisor.left(-1).replace("'","''");
  cadquery+="', poblacion_emisor='";
  cadquery+= poblacion_emisor.left(-1).replace("'","''");
  cadquery+="', provincia_emisor='";
  cadquery+=provincia_emisor.left(-1).replace("'","''");
  cadquery+="', pais_emisor='";
  cadquery+= pais_emisor.left(-1).replace("'","''");
  cadquery+="', cif_emisor='";
  cadquery+=cif_emisor.left(-1).replace("'","''");
  cadquery+="', id_registral='";
  cadquery+=id_registral.left(-1).replace("'","''");
  cadquery+="' ";

  cadquery+=" where codigo='";
  cadquery+=codigo;
  cadquery+="'";
  ejecutar(cadquery);
}

int basedatos::tipo_operacion_tipo_doc(QString codigo)
{
    int tipo_operacion=0;
    QString cadquery = "SELECT "
                       "tipo_operacion"
                       " from tipos_doc where codigo='";
    cadquery+=codigo;
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
     tipo_operacion=query.value(0).toInt();
    }
    return tipo_operacion;
}

bool basedatos::existecodigoref (QString cadena,QString *qdescrip) {
    QString cadquery = "SELECT codigo,descrip from referencias where codigo = '"+
                       cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *qdescrip = query.value(1).toString();
        return true;
    }
    return false;
}


void basedatos::guardacambiosref (QString codigo,QString descripcion) {
    QString cadquery = "UPDATE referencias SET descrip='";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "' WHERE codigo='";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "'";
    ejecutar(cadquery);
}

void basedatos::insertref (QString codigo,QString descripcion) {
    QString cadquery = "INSERT INTO referencias (codigo,descrip) VALUES ('";
    cadquery += codigo.left(-1).replace("'","''");
    cadquery += "','";
    cadquery += descripcion.left(-1).replace("'","''");
    cadquery += "')";
    ejecutar(cadquery);
}


void basedatos::eliminaref (QString qcodigo) {
    ejecutar("delete from referencias where codigo= '"+ qcodigo.left(-1).replace("'","''") +"'");
}


void basedatos::carga_ref(QString codigo,
               QString *descrip,
               QString *tipoiva,
               QString *tipoivare,
               QString *cuenta,
               double *pvp)
{
    QString cadquery = "SELECT descrip, iva, ivare, precio, cuenta"
                       " from referencias where codigo='";
    cadquery+=codigo;
    cadquery+="'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
     *descrip=query.value(0).toString();
     *tipoiva=query.value(1).toString();
     *tipoivare=query.value(2).toString();
     *pvp=query.value(3).toDouble();
     *cuenta=query.value(4).toString();
    }

}

void basedatos::guarda_ref(QString codigo,
                QString descrip,
                QString tipoiva,
                QString tipoivare,
                QString cuenta,
                QString pvp)
{
    QString cadquery = "update referencias set descrip='";
    cadquery+=descrip.left(-1).replace("'","''");
    cadquery+="', cuenta='";
    cadquery+=cuenta.left(-1).replace("'","''");
    cadquery+="', iva='";
    cadquery+=tipoiva.left(-1).replace("'","''");
    cadquery+="', ivare='";
    cadquery+=tipoivare.left(-1).replace("'","''");
    cadquery+="', ";
    cadquery+="precio=";
    cadquery+=convapunto(pvp).left(-1).replace("'","''");
    cadquery+=" where codigo='";
    cadquery+=codigo;
    cadquery+="'";
    ejecutar(cadquery);

}


int basedatos::cuentatipoiva (QString cadena,QString *cuenta, bool soportado) {
    QString cadquery = ! soportado ? "SELECT clave,cuenta from tiposiva where clave = '"
                                 : "SELECT clave,cuenta_sop from tiposiva where clave = '";
    cadquery += cadena.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        *cuenta = query.value(1).toString();
        return 1;
    }
    return 0;
}


void basedatos::updatecuentatipoiva (QString clave,QString cuenta) {
    QString cadquery = "UPDATE tiposiva SET";
    cadquery += " cuenta='"+ cuenta.left(-1).replace("'","''");
    cadquery += "' WHERE clave='"+ clave.left(-1).replace("'","''") +"'";
    ejecutar(cadquery);
}

void basedatos::updatecuentatipoiva_soportado (QString clave,QString cuenta) {
    QString cadquery = "UPDATE tiposiva SET";
    cadquery += " cuenta_sop='"+ cuenta.left(-1).replace("'","''");
    cadquery += "' WHERE clave='"+ clave.left(-1).replace("'","''") +"'";
    ejecutar(cadquery);
}


void basedatos::carga_irpf(QString *cuenta,
               double *tipo)
{
    QString cadquery = "SELECT cuenta_ret_irpf, tipo_ret_irpf"
                       " from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
     *cuenta=query.value(0).toString();
     *tipo=query.value(1).toDouble();
    }

}


void basedatos::actuconfig_irpf (QString cuenta,QString valor) {
    QString cadquery = "UPDATE configuracion SET";
    cadquery += " cuenta_ret_irpf='"+ cuenta.left(-1).replace("'","''");
    cadquery += "', tipo_ret_irpf=" + valor.left(-1).replace("'","''");
    ejecutar(cadquery);
}


void basedatos::insertadatosaccesorios_cplus(QString cuenta, QString razon, QString nif,
                                            QString domicilio, QString poblacion,
                                            QString provincia, QString codpostal,
                                            QString telef, QString fax, QString email)
{
    QString cadquery = "insert into datossubcuenta (cuenta,razon, cif,";
    cadquery += "domicilio, poblacion, provincia, codigopostal,";
    cadquery += "tfno, fax, email) ";
    cadquery += "values('";
    cadquery += cuenta.left(-1).replace("'","''")+ "', '";
    cadquery += razon.left(-1).replace("'","''")+ "', '";
    cadquery += nif.left(-1).replace("'","''")+ "', '";
    cadquery += domicilio.left(-1).replace("'","''")+ "', '";
    cadquery += poblacion.left(-1).replace("'","''")+ "', '";
    cadquery += provincia.left(-1).replace("'","''")+ "', '";
    cadquery += codpostal.left(-1).replace("'","''")+ "', '";
    cadquery += telef.left(-1).replace("'","''")+ "', '";
    cadquery += fax.left(-1).replace("'","''")+ "', '";
    cadquery += email.left(-1).replace("'","''")+ "')";
    ejecutar(cadquery);
}

// Guarda los datos accesorios de una subcuenta
void basedatos::guardadatosaccesorios2 (QString cuenta, QString razon, QString ncomercial,
                                        QString cif,
    QString nifrprlegal, QString domicilio, QString poblacion,
    QString codigopostal, QString provincia, QString pais, QString pais_dat,
    QString tfno, QString fax, QString email, QString observaciones,
    QString ccc, QString cuota,
    bool venciasoc, QString tipovenci, QString cuentatesor,
    QString web, QString codclaveidfiscal, bool procvto, int diasfijo,
    bool dom_bancaria, QString IBAN, QString BIC,
    QString fototexto)

  {
    QString qdescrip;
    QString caddiasfijo; caddiasfijo.setNum(diasfijo);
    if (!existecodigoplan(cuenta,&qdescrip)) return;

    QString cadquery = "SELECT cuenta from datossubcuenta where cuenta = '"+ cuenta.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) &&  (query.first()) )
    {
        cadquery = "UPDATE datossubcuenta ";
        cadquery += "set razon = '"+ razon.left(-1).replace("'","''") +"', ";
        cadquery += "nombrecomercial = '"+ ncomercial.left(-1).replace("'","''") +"', ";
        cadquery += "cif = '"+ cif.left(-1).replace("'","''") +"', ";
        cadquery += "nifrprlegal = '"+ nifrprlegal.left(-1).replace("'","''") +"', ";
        cadquery += "domicilio = '"+ domicilio.left(-1).replace("'","''") +"', ";
        cadquery += "poblacion = '"+ poblacion.left(-1).replace("'","''") +"', ";
        cadquery += "codigopostal = '"+ codigopostal.left(-1).replace("'","''") +"', ";
        cadquery += "provincia = '"+ provincia.left(-1).replace("'","''") +"', ";
        cadquery += "pais = '"+ pais.left(-1).replace("'","''") +"', ";
        cadquery += "pais_dat = '"+ pais_dat.left(-1).replace("'","''") +"', ";
        cadquery += "tfno = '"+ tfno.left(-1).replace("'","''") +"', ";
        cadquery += "fax = '"+ fax.left(-1).replace("'","''") +"', ";
        cadquery += "email = '"+ email.left(-1).replace("'","''") +"', ";
        cadquery += "observaciones = '"+ observaciones.left(-1).replace("'","''") +"', ";
        cadquery += "web = '"+ web.left(-1).replace("'","''") +"',";
        cadquery += "claveidfiscal = '"+ codclaveidfiscal.left(-1).replace("'","''") +"',";
        cadquery += "ccc = '"+ ccc.left(-1).replace("'","''") +"',";
        cadquery += "cuota = '"+ cuota.left(-1).replace("'","''") +"', ";
        if ( cualControlador() == SQLITE ) cadquery += venciasoc ? "venciasoc = 1," : "venciasoc = 0,";
           else cadquery += venciasoc ? "venciasoc = true," : "venciasoc = false,";
        cadquery+= "codvenci='";
        cadquery+= tipovenci.left(-1).replace("'","''") +"', ";
        cadquery+= "tesoreria='";
        cadquery+= cuentatesor.left(-1).replace("'","''") +"', ";
        cadquery+= "vto_dia_fijo= ";
        cadquery+= caddiasfijo;
        cadquery+= ", ";
        if ( cualControlador() == SQLITE ) cadquery += procvto ? "procesavenci = 1 " : "procesavenci = 0 ";
           else cadquery += procvto ? "procesavenci = true " : "procesavenci = false ";
        cadquery+=", ";
        if ( cualControlador() == SQLITE )
            cadquery += dom_bancaria ? "dom_bancaria = 1," : "dom_bancaria = 0,";
           else cadquery += dom_bancaria ? "dom_bancaria = true," : "dom_bancaria = false,";
        cadquery += " IBAN='";
        cadquery += IBAN;
        cadquery += "', BIC='";
        cadquery += BIC;
        cadquery += "' ";
        cadquery += "where cuenta = '"+ cuenta.left(-1).replace("'","''") +"'";
            // QMessageBox::warning( 0, QObject::tr("sentencia"),cadquery);
        ejecutar(cadquery);
    }
    else
    {
        cadquery = "insert into datossubcuenta (cuenta,razon,nombrecomercial,cif,";
        cadquery += "nifrprlegal,domicilio,poblacion,codigopostal,";
        cadquery += "provincia,pais,tfno,fax,email,observaciones,ccc,cuota,venciasoc,"
                    "codvenci,tesoreria,web,claveidfiscal,"
                    "procesavenci, vto_dia_fijo, dom_bancaria, IBAN, BIC) ";
        cadquery += "values('";
        cadquery += cuenta.left(-1).replace("'","''")+ "', '";
        cadquery += razon.left(-1).replace("'","''")+ "', '";
        cadquery += ncomercial.left(-1).replace("'","''")+ "', '";
        cadquery += cif.left(-1).replace("'","''")+ "', '";
        cadquery += nifrprlegal.left(-1).replace("'","''")+ "', '";
        cadquery += domicilio.left(-1).replace("'","''")+ "', '";
        cadquery += poblacion.left(-1).replace("'","''")+ "', '";
        cadquery += codigopostal.left(-1).replace("'","''")+ "', '";
        cadquery += provincia.left(-1).replace("'","''")+ "', '";
        cadquery += pais.left(-1).replace("'","''")+ "', '";
        cadquery += tfno.left(-1).replace("'","''")+ "', '";
        cadquery += fax.left(-1).replace("'","''")+ "', '";
        cadquery += email.left(-1).replace("'","''")+ "', '";
        cadquery += observaciones.left(-1).replace("'","''")+ "','";
        cadquery += ccc.left(-1).replace("'","''")+ "','";
        cadquery += cuota.left(-1).replace("'","''")+ "',";

        if ( cualControlador() == SQLITE ) cadquery += venciasoc ? "1, '" : "0, '";
           else cadquery += venciasoc ? "true,'" : "false, '";
        cadquery+= tipovenci.left(-1).replace("'","''") +"', '";
        cadquery+= cuentatesor.left(-1).replace("'","''") +"', '";
        cadquery+=web.left(-1).replace("'","''") +"','";
        cadquery+=codclaveidfiscal.left(-1).replace("'","''") + "',";
        if ( cualControlador() == SQLITE ) cadquery += procvto ? "1" : "0";
           else cadquery += procvto ? "true" : "false";
        cadquery+=", ";
        cadquery+=caddiasfijo;
        cadquery+=", ";
        if ( cualControlador() == SQLITE )
               cadquery += dom_bancaria ? "1," : "0,";
              else cadquery += dom_bancaria ? "true," : "false,";
        cadquery+=" '";
        cadquery+=IBAN;
        cadquery+="', '";
        cadquery+=BIC;
        cadquery+="'";

        cadquery+=")";
        ejecutar(cadquery);
    }
   // guardamos ahora la foto
        cadquery = "UPDATE datossubcuenta ";
        cadquery +="set imagen = '";
        cadquery += fototexto;
        cadquery += "' where cuenta = '"+ cuenta.left(-1).replace("'","''") +"'";
        ejecutar(cadquery);
}



void basedatos::datosnumeracion_ejercicio (QString ejercicio,
                                           QString *prox_asiento,
                                           bool *primero_apertura) {
    QString cadquery = "SELECT prox_asiento, primero_apertura from ejercicios "
                       "where codigo = '"+ ejercicio.left(-1).replace("'","''") +"'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) &&  (query.first()) )
      {
       *prox_asiento=query.value(0).toString();
       *primero_apertura=query.value(1).toBool();
      }
}

void basedatos::pasanumeracion_ejercicio (QString ejercicio,
                                          QString prox_asiento,
                                          bool primero_apertura) {
    QString cadena="update ejercicios set prox_asiento=";
    cadena += prox_asiento.replace("'","''");
    cadena +=", primero_apertura=";
    if ( cualControlador() == SQLITE ) cadena += primero_apertura ? "1" : "0";
       else cadena += primero_apertura ? "true" : "false";
    cadena += " where codigo='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}

void basedatos::pasa_prox_nrecepcion_ejercicio (QString ejercicio,
                                          QString prox_nrecepcion) {
    QString cadena="update ejercicios set prox_nrecepcion=";
    cadena += prox_nrecepcion.replace("'","''");
    cadena += " where codigo='";
    cadena += ejercicio.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}


bool basedatos::diario_vacio()
{
    QSqlQuery query = ejecutar("select count(pase) from diario");
    if ( (query.isActive()) &&  (query.first()) )
      {
        return query.value(0).toInt()<= 0;
      }
    return true;
}



QDate basedatos::maxfecha_diario () {
    QString cadena="select max(fecha) from diario";

    QSqlQuery query = ejecutar(cadena);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toDate();
    }
    else return QDate();
}

QStringList basedatos::lista_cod_ejercicios()
{
    QStringList listacod;
    QString cadquery = "SELECT codigo from ejercicios order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if  (query.isActive() )
        while (query.next())
         {
          listacod << query.value(0).toString();
         }
   return listacod;
}

void basedatos::actu_ejercicio_diario(QString ejercicio)
{
    // averiguamos fecha de apertura y de cierre
    QDate apertura = selectAperturaejercicios(ejercicio);
    QDate cierre = selectCierreejercicios(ejercicio);

    // actualizamos campo de ejercicio en diario
    QString cadquery = "update diario set ejercicio='";
    cadquery+=ejercicio + "' where fecha>='";
    cadquery+=apertura.toString("yyyy-MM-dd");
    cadquery+="' and fecha <='";
    cadquery+=cierre.toString("yyyy-MM-dd");
    cadquery+="'";
    ejecutar(cadquery);
}


void basedatos::asigna_prox_asiento_ejercicio(QString ejercicio)
{
    // averiguamos último asiento en ejercicio
    QString cadena= "select max(asiento) from diario where ejercicio='";
    cadena+=ejercicio;
    cadena += "'";
    qlonglong proxasiento=0;
    QSqlQuery query = ejecutar(cadena);
    if  (query.isActive() )
        if (query.next())
         {
           proxasiento = query.value(0).toLongLong()+1;
         }
    if (proxasiento==0) return;
    QString cadnum; cadnum.setNum(proxasiento);
    cadena="update ejercicios set prox_asiento=";
    cadena+=cadnum;
    cadena+=" where codigo='";
    cadena+=ejercicio;
    cadena+="'";
    ejecutar(cadena);
}


// para bloquear las tablas para actualización/adición de facturas
void basedatos::bloquea_para_facturas()
{
 // para desbloquear - llamar a desbloquea_y_commit()
 if (cualControlador() == MYSQL)
    {
     ejecutar("SET AUTOCOMMIT=0");
     ejecutar("LOCK TABLE series_fact write, facturas write, lin_fact write, "
              "tiposiva write"); // falta tabla detalle
    }
 if (cualControlador() == POSTGRES)
    {
      ejecutar("LOCK TABLE series_fact IN ACCESS EXCLUSIVE MODE");
    }
}



qlonglong basedatos::proxnum_serie(QString serie)
{
  // devuelve próximo número de serie de facturación
  // incrementa la serie en 1
    qlonglong proxnum=0;
    QString cadena="select proxnum from series_fact where codigo='";
    cadena+=serie;
    cadena+="'";
    QSqlQuery query = ejecutar(cadena);
    if  (query.isActive() )
        if (query.next())
         {
           proxnum = query.value(0).toLongLong();
         }
    QString cadnum; cadnum.setNum(proxnum+1);
    cadena="update series_fact set proxnum=";
    cadena+=cadnum;
    cadena+=" where codigo='";
    cadena+=serie;
    cadena+="'";
    ejecutar(cadena);

    return proxnum;
}


// Devuelve datos de tipos automaticos ordenados
QStringList basedatos::tipos_automaticos () {
    QStringList lista;
    QString cadquery = "SELECT codigo,descripcion from tipos_automaticos order by codigo";
    QSqlQuery query = ejecutar(cadquery);
    if (query.isActive())
        while (query.next())
           {
             lista << query.value(0).toString();
           }
    return lista;
}


QString basedatos::cuenta_tipo_iva(QString clave)
{
   QString cadena="select cuenta from tiposiva where clave='";
   cadena+=clave;
   cadena+="'";
   QSqlQuery query = ejecutar(cadena);
   if (query.isActive())
       if (query.next())
          {
            return query.value(0).toString();
          }
   return QString();
}


QString basedatos::cuenta_tipo_iva_sop(QString clave)
{
   QString cadena="select cuenta_sop from tiposiva where clave='";
   cadena+=clave;
   cadena+="'";
   QSqlQuery query = ejecutar(cadena);
   if (query.isActive())
       if (query.next())
          {
            return query.value(0).toString();
          }
   return QString();
}


// elimina contabilizado y el registro de apunte del diario para la tabla facturas
void basedatos::quitacontab_fra_de_pase (QString pase) {
    QString cadena = "update facturas set pase_diario_cta=0, contabilizado=";

    if ( cualControlador() == SQLITE ) {
        cadena += "0";
    }
    else {
        cadena += "FALSE";
    }
    cadena += ", cerrado=";
              if ( cualControlador() == SQLITE ) {
                  cadena += "0";
              }
              else {
                  cadena += "FALSE";
              }

    cadena += " where pase_diario_cta="+ pase.left(-1).replace("'","''");
    ejecutar(cadena);
}


QString basedatos::descrip_pais (QString codigo)
{
    QString codseg=codigo;
    if (codigo.contains(' ')) codseg=codigo.section(" ",0,0).trimmed();
    QStringList lista=listapaises();
    for (int veces=0; veces<lista.size(); veces++)
        if (lista.at(veces).section(' ',0,0).trimmed()==codseg)
            return lista.at(veces).section(' ',1,1).trimmed();
    return QString();
}


QString basedatos::tipo_operacion_ret(QString cuenta)
{
// averiguamos tipo retención de cuenta
QString cad1 = "select tipo_operacion_ret from datossubcuenta where cuenta='";
cad1 += cuenta.left(-1).replace("'","''");
cad1 += "';";
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next()) return query.value(0).toString();

return QString();
}


double basedatos::tipo_ret_cta(QString cuenta)
{
// averiguamos tipo retención de cuenta
QString cad1 = "select tipo_ret from datossubcuenta where cuenta='";
cad1 += cuenta.left(-1).replace("'","''");
cad1 += "';";
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next()) return query.value(0).toDouble();

return 0;
}


bool basedatos::ret_arrendamiento(QString cuenta)
{
// averiguamos tipo retención de cuenta
QString cad1 = "select ret_arrendamiento from datossubcuenta where cuenta='";
cad1 += cuenta.left(-1).replace("'","''");
cad1 += "';";
QSqlQuery query=ejecutar(cad1);
if (query.isActive())
   if (query.next()) return query.value(0).toBool();

return false;
}


/* cadena = "CREATE TABLE retenciones ("
         "pase          bigint,"
         "cta_retenido  varchar(80),";
         if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
             cadena += "arrendamiento        bool default 0,";
         }
         else {
             cadena += "arrendamiento    bool default false,";
         }
         cadena+="clave_ret varchar(255),"

         "base_ret      numeric(14,2),"
         "tipo_ret      numeric(5,2),"
         "retencion     numeric(14,2),"
         "ing_cta       numeric(14,2),"
         "ing_cta_rep   numeric(14,2),"
         "nombre        varchar(255),"
         "cif           varchar(80),"
         "provincia     varchar(255),";
cadena+="PRIMARY KEY (pase) )"; */


//
void basedatos::insert_retencion (QString pase,
                                  QString cta_retenido, bool ret_arrendamiento,
                                  QString clave, QString base_percepciones,
                                  QString tipo_ret, QString retencion,
                                  QString ing_cta, QString ing_cta_repercutido,
                                  QString nombre_ret, QString cif_ret, QString provincia) {
    QString cad1="INSERT into retenciones (pase, cta_retenido, arrendamiento, clave_ret, ";
    cad1 += "base_ret, tipo_ret, retencion, ing_cta, ing_cta_rep, nombre, cif, provincia )"
            " VALUES(";
    cad1 += pase.left(-1).replace("'","''") +",'";
    cad1 += cta_retenido.left(-1).replace("'","''") +"', ";
    if (cualControlador() == SQLITE)
        cad1+= ret_arrendamiento ? "1" : "0";
      else
          cad1+= ret_arrendamiento ? "true" : "false";
    cad1+=", '";
    cad1 += clave.left(-1).replace("'","''") +"', ";
    if (base_percepciones.isEmpty()) base_percepciones="0";
    cad1 += convapunto(base_percepciones) +", ";
    if (tipo_ret.length()==0) tipo_ret= "0";
    cad1 += convapunto(tipo_ret);
    cad1 += ",";
    if (retencion.isEmpty()) retencion="0";
    cad1 += convapunto(retencion) + ", ";
    if (ing_cta.isEmpty()) ing_cta="0";
    cad1 += convapunto(ing_cta);
    cad1 += ", ";
    if (ing_cta_repercutido.isEmpty()) ing_cta_repercutido="0";
    cad1 += convapunto(ing_cta_repercutido).left(-1).replace("'","''");
    cad1 += " ,'";
    cad1 += nombre_ret.left(-1).replace("'","''");
    cad1 += "', '";
    cad1 += cif_ret.left(-1).replace("'","''");
    cad1 += "', '";
    cad1 += provincia.left(-1).replace("'","''");
    cad1+="') ";
    ejecutar(cad1);
}

// devuelve consulta registro de un pase determinado en retenciones
QSqlQuery basedatos::datos_ret_pase (QString cadpase) {
    QString consulta = "SELECT pase, cta_retenido, arrendamiento, clave_ret, ";
    consulta += "base_ret, tipo_ret, retencion, ing_cta, ing_cta_rep, nombre, cif, provincia ";
    consulta += "from retenciones where pase=";
    consulta += cadpase.left(-1).replace("'","''");
    return ejecutar(consulta);
}




QSqlQuery basedatos::retenciones_agrup (QDate fechaini, QDate fechafin,
                                        bool soloarrendamientos,
                                        bool excluyearrendamientos) {

/*    cadena = "CREATE TABLE diario ("
             "asiento       bigint,"
             "pase          bigint,"
             "fecha         date,"
             "cuenta        varchar(40),"
             "debe          numeric(14,2),"
             "haber         numeric(14,2),"
             "concepto      varchar(255),"
             "documento     varchar(255),"
              "ejercicio        varchar(80),"
              "PRIMARY KEY (pase) )"; */

/*    cadena = "CREATE TABLE retenciones ("
             "pase          bigint,"
             "cta_retenido  varchar(80),";
             if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
                 cadena += "arrendamiento        bool default 0,";
             }
             else {
                 cadena += "arrendamiento    bool default false,";
             }
             cadena+="clave_ret varchar(255),"

             "base_ret      numeric(14,2),"
             "tipo_ret      numeric(5,2),"
             "retencion     numeric(14,2),"
             "ing_cta       numeric(14,2),"
             "ing_cta_rep   numeric(14,2),"
             "nombre        varchar(255),"
             "cif           varchar(80),"
             "provincia     varchar(255),"; */

    // cuenta retención, arrendamiento, clave, r.especie, base, retención, ing_cta, ing_cta_rep

    QString cadquery = "select d.cuenta, r.arrendamiento, r.clave_ret, sum(r.base_ret), "
                       "sum(r.retencion), sum(r.ing_cta), sum(r.ing_cta_rep) "
                       "from retenciones r, diario d "
                       "where d.pase= r.pase and d.fecha>='";
    cadquery+=fechaini.toString("yyyy-MM-dd");
    cadquery+="' and d.fecha<='";
    cadquery+=fechafin.toString("yyyy-MM-dd");
    cadquery+="' ";
    cadquery+= soloarrendamientos ? "and arrendamiento " : "";
    cadquery+= excluyearrendamientos ? "and not arrendamiento " : "";
    cadquery+="group by d.cuenta, r.arrendamiento, r.clave_ret order by d.cuenta";
    return ejecutar(cadquery);

}


QSqlQuery basedatos::listaret (QDate fechaini, QDate fechafin,
                                        bool soloarrendamientos,
                                        bool excluyearrendamientos) {
    // apunte, cta_ret, cta_retenido, arrendamiento, especie,
    // clave, base, retención, ing_cta, ing_cta_repercutido, nombre, cif, provincia
    QString cadquery = "select d.pase, d.cuenta, r.cta_retenido, r.arrendamiento, "
                       "r.clave_ret, r.base_ret, "
                       "r.retencion, r.ing_cta, r.ing_cta_rep, r.nombre, r.cif, r.provincia "
                       "from retenciones r, diario d "
                       "where d.pase= r.pase and d.fecha>='";
    cadquery+=fechaini.toString("yyyy-MM-dd");
    cadquery+="' and d.fecha<='";
    cadquery+=fechafin.toString("yyyy-MM-dd");
    cadquery+="' ";
    cadquery+= soloarrendamientos ? "and arrendamiento " : "";
    cadquery+= excluyearrendamientos ? "and not arrendamiento " : "";
    cadquery+="order by d.pase";
    return ejecutar(cadquery);
}

QSqlQuery basedatos::listaretagrup (QDate fechaini, QDate fechafin,
                                        bool soloarrendamientos,
                                        bool excluyearrendamientos) {
    // apunte, cta_ret, cta_retenido, arrendamiento, especie,
    // clave, base, retención, ing_cta, ing_cta_repercutido, nombre, cif, provincia
    QString cadquery = "select '', d.cuenta, r.cta_retenido, r.arrendamiento, "
                       "r.clave_ret, sum(r.base_ret), "
                       "sum(r.retencion), sum(r.ing_cta), sum(r.ing_cta_rep), "
                       "r.nombre, r.cif, r.provincia "
                       "from retenciones r, diario d "
                       "where d.pase= r.pase and d.fecha>='";
    cadquery+=fechaini.toString("yyyy-MM-dd");
    cadquery+="' and d.fecha<='";
    cadquery+=fechafin.toString("yyyy-MM-dd");
    cadquery+="' ";
    cadquery+= soloarrendamientos ? "and arrendamiento " : "";
    cadquery+= excluyearrendamientos ? "and not arrendamiento " : "";
    cadquery+="group by d.cuenta, r.cta_retenido, r.arrendamiento, r.clave_ret, "
              "r.nombre, r.cif, r.provincia ";
    cadquery+="order by d.cuenta";
    return ejecutar(cadquery);
}


// devuelve -1 si no hay registros
int basedatos::primer_venci_pte_de_pase(QString apunte)
{
  QString cadena= "select num from vencimientos where pase_diario_operacion=";
  cadena+=apunte;
  cadena+=" and pendiente and not anulado";
  cadena += " order by fecha_vencimiento";

  QSqlQuery query=ejecutar(cadena);
  if (query.isActive())
     if (query.next()) return query.value(0).toInt();

  return -1;

}

void basedatos::actualiza_prox_asiento_ejercicios()
{
   QString cadena="select ejercicio, max(asiento) from diario group by ejercicio";
   QSqlQuery query=ejecutar(cadena);
   if (query.isActive())
      while (query.next())
       {
        QString cadnum;
        cadnum.setNum(query.value(1).toInt()+1);
        update_ejercicio_prox_asiento(query.value(0).toString(), cadnum);

       }
}

void basedatos::actualiza_prox_pase()
{
   QString cadena="select max(pase) from diario;";
    // updateConfiguracionprox_pase (QString cadnumpase)
   QSqlQuery query=ejecutar(cadena);
   if (query.isActive())
      if (query.next())
       {
         QString cadnum;
         cadnum.setNum(query.value(0).toInt()+1);
         updateConfiguracionprox_pase (cadnum);
       }
}

void basedatos::insert_datossubcuenta_import(QString codigo, QString razon,
                                  QString nombrecomercial, QString cif,
                                  QString web, QString domicilio,
                                  QString poblacion, QString cp,
                                  QString provincia, QString telefono,
                                  QString pais, QString fax,
                                  QString email, QString observaciones,
                                  QString ccc, QString iban,
                                  QString bic, QString sufijo,
                                  QString domiciliable,
                                  QString ref_mandato,
                                  QDate firma_mandato)

{
   bool esdomiciliable=domiciliable=="1";
   QString cadquery= "insert into datossubcuenta (cuenta, razon, nombrecomercial, cif,";
            cadquery += "web, domicilio, poblacion, codigopostal,";
            cadquery += "provincia,tfno, pais_dat, fax, email,observaciones,";
            cadquery += "ccc, iban, bic, sufijo, dom_bancaria, ref_mandato, firma_mandato) ";
            cadquery += "values('";
            cadquery += codigo.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += razon.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += nombrecomercial.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += cif.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += web.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += domicilio.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += poblacion.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += cp.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += provincia.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += telefono.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += pais.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += fax.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += email.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += observaciones.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += ccc.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += iban.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += bic.left(-1).replace("'","''");
            cadquery += "', '";
            cadquery += sufijo.left(-1).replace("'","''");
            cadquery += "', ";
            if (cualControlador() == SQLITE)
                cadquery+= esdomiciliable ? "1" : "0";
              else
                  cadquery+= esdomiciliable ? "true" : "false";
            cadquery+=", '";
            cadquery+=ref_mandato;
            cadquery+="', '";
            cadquery+=firma_mandato.toString("yyyy-MM-dd");
            cadquery+="'";
            cadquery += ")";
   ejecutar(cadquery);
}


void basedatos::update_datossubcuenta_import(QString codigo, QString razon,
                                  QString nombrecomercial, QString cif,
                                  QString web, QString domicilio,
                                  QString poblacion, QString cp,
                                  QString provincia, QString telefono,
                                  QString pais, QString fax,
                                  QString email, QString observaciones,
                                  QString ccc, QString iban,
                                  QString bic, QString sufijo,
                                  QString domiciliable,
                                  QString ref_mandato,
                                  QDate firma_mandato)

{
   bool esdomiciliable=domiciliable=="1";
   QString cadquery= "update datossubcuenta set razon='";
            //, razon, nombrecomercial, cif,";
            //cadquery += "web, domicilio, poblacion, codigopostal,";
            //cadquery += "provincia,tfno, pais, fax, email,observaciones,";
            //cadquery += "ccc, iban, bic, sufijo, dom_bancaria) ";
            //cadquery += "values('";
            cadquery += razon.left(-1).replace("'","''");
            cadquery += "', nombrecomercial='";
            cadquery += nombrecomercial.left(-1).replace("'","''");
            cadquery += "', cif='";
            cadquery += cif.left(-1).replace("'","''");
            cadquery += "', web='";
            cadquery += web.left(-1).replace("'","''");
            cadquery += "', domicilio='";
            cadquery += domicilio.left(-1).replace("'","''");
            cadquery += "', poblacion='";
            cadquery += poblacion.left(-1).replace("'","''");
            cadquery += "', codigopostal='";
            cadquery += cp.left(-1).replace("'","''");
            cadquery += "', provincia='";
            cadquery += provincia.left(-1).replace("'","''");
            cadquery += "', tfno='";
            cadquery += telefono.left(-1).replace("'","''");
            cadquery += "', pais='";
            cadquery += pais.left(-1).replace("'","''");
            cadquery += "', fax='";
            cadquery += fax.left(-1).replace("'","''");
            cadquery += "', email='";
            cadquery += email.left(-1).replace("'","''");
            cadquery += "', observaciones='";
            cadquery += observaciones.left(-1).replace("'","''");
            cadquery += "', ccc='";
            cadquery += ccc.left(-1).replace("'","''");
            cadquery += "', iban='";
            cadquery += iban.left(-1).replace("'","''");
            cadquery += "', bic='";
            cadquery += bic.left(-1).replace("'","''");
            cadquery += "', sufijo='";
            cadquery += sufijo.left(-1).replace("'","''");
            cadquery += "', dom_bancaria=";
            if (cualControlador() == SQLITE)
                cadquery+= esdomiciliable ? "1" : "0";
              else
                  cadquery+= esdomiciliable ? "true" : "false";
            cadquery += ", ref_mandato='";
            cadquery += ref_mandato;
            cadquery += "', firma_mandato='";
            cadquery += firma_mandato.toString("yyyy-MM-dd");
            cadquery += "'";
            cadquery += " where cuenta='";
            cadquery += codigo;
            cadquery += "'";
   ejecutar(cadquery);
}




void basedatos::insert_libroiva_import(
        QString apunte_diario,
        QString cuenta_base_iva,
        QString base_imponible,
        QString clave_iva,
        QString tipo_iva,
        QString tipo_recargo,
        QString cuota,
        QString cuenta_factura,
        QDate fecha_factura,
        bool soportado,
        bool aib,
        bool ais,
        bool adq_serv_extranjero,
        bool prest_serv_ue,
        bool bien_inversion,
        bool eib,
        QDate fecha_operacion,
        bool exportacion
        )
{


     QString cadquery= "insert into libroiva (pase, cta_base_iva, base_iva, clave_iva,";
             cadquery += "tipo_iva, tipo_re, cuota_iva, cuenta_fra,";
             cadquery += "fecha_fra,soportado, aib, autofactura, autofactura_no_ue,";
             cadquery += "pr_servicios_ue, bien_inversion, eib, fecha_operacion, "
                         "exento_dcho_ded, exportacion) ";
             cadquery += "values(";
             cadquery += apunte_diario.left(-1).replace("'","''");
             cadquery += ",'";
             cadquery += cuenta_base_iva.left(-1).replace("'","''");
             cadquery += "',";
             cadquery += base_imponible.left(-1).replace("'","''");
             cadquery += ",'";
             cadquery += clave_iva.left(-1).replace("'","''");
             cadquery += "',";
             cadquery += tipo_iva.left(-1).replace("'","''");
             cadquery += ",";
             cadquery += tipo_recargo.left(-1).replace("'","''");
             cadquery += ",";
             cadquery += cuota.left(-1).replace("'","''");
             cadquery += ",'";
             cadquery += cuenta_factura.left(-1).replace("'","''");
             cadquery += "','";

             QString cadfecha=fecha_factura.toString("yyyy-MM-dd");
             cadquery += cadfecha;
             cadquery += "',";

             if ( cualControlador() == SQLITE )
                {
                 cadquery+= (soportado ? "1" : "0");
                 cadquery += ",";
                 cadquery+= (aib ? "1" : "0");
                 cadquery += ",";
                 cadquery+= (ais ? "1" : "0");
                 cadquery += ",";
                 cadquery+= (adq_serv_extranjero ? "1" : "0");
                 cadquery += ",";
                 cadquery+= (prest_serv_ue ? "1" : "0");
                 cadquery += ",";
                 cadquery+= (bien_inversion ? "1" : "0");
                 cadquery += ",";
                 cadquery+= (eib ? "1" : "0");
               }
               else
                    {
                     cadquery+=(soportado ? "true" : "false");
                     cadquery += ",";
                     cadquery+=(aib ? "true" : "false");
                     cadquery += ",";
                     cadquery+=(ais ? "true" : "false");
                     cadquery += ",";
                     cadquery+=(adq_serv_extranjero ? "true" : "false");
                     cadquery += ",";
                     cadquery+=(prest_serv_ue ? "true" : "false");
                     cadquery += ",";
                     cadquery+=(bien_inversion ? "true" : "false");
                     cadquery += ",";
                     cadquery+=(eib ? "true" : "false");
                    }
             cadquery+=", '";
             cadquery+=fecha_operacion.toString("yyyy-MM-dd");
             cadquery+="',";
             if ( cualControlador() == SQLITE )
                {
                  cadquery+=" 1, ";
                  cadquery+= exportacion ? "1":"0";
                }
                else
                    {
                      cadquery+=" true, ";
                      cadquery+= exportacion ? "true":"false";
                    }
             cadquery+=")";
             ejecutar(cadquery);

}


void basedatos::insertavenci_import(QString numvencimiento,
                            QString cuenta_ordenante,
                            QDate fecha_operacion,
                            QString importe,
                            QString cuenta_tesoreria,
                            QDate fecha_vencimiento,
                            bool derecho,
                            bool pendiente,
                            QString apunte_diario,
                            QString apunte_vto,
                            bool domiciliable
                            )
{

    /* "CREATE TABLE vencimientos ("
     "num                     bigint,"
     "cta_ordenante           varchar(40),"
     "fecha_operacion         date,"
     "importe                 numeric(14,2),"
     "cuenta_tesoreria        varchar(40),"
     "fecha_vencimiento       date,"
     "derecho                 bool,"
     "pendiente               bool,"
     "anulado                 bool,"
     "pase_diario_operacion   bigint,"
     "pase_diario_vencimiento bigint,"
     "concepto                varchar(160),"
     "usuario                 varchar(80),"
     "PRIMARY KEY (num) ) */

    QString cadquery= "insert into vencimientos (num, cta_ordenante, fecha_operacion, "
                      "importe, cuenta_tesoreria, fecha_vencimiento, derecho, pendiente,"
                      "anulado, pase_diario_operacion, pase_diario_vencimiento, domiciliable) values (";
    cadquery+=numvencimiento.left(-1).replace("'","''");
    cadquery+=",'";
    cadquery+=cuenta_ordenante.left(-1).replace("'","''");
    cadquery+="','";
    cadquery+=fecha_operacion.toString("yyyy-MM-dd");
    cadquery+="',";
    cadquery+=importe.left(-1).replace("'","''");
    cadquery+=",'";
    cadquery+=cuenta_tesoreria.left(-1).replace("'","''");
    cadquery+="','";
    cadquery+=fecha_vencimiento.toString("yyyy-MM-dd");
    cadquery+="',";
    if ( cualControlador() == SQLITE )
       {
        cadquery+= (derecho ? "1" : "0");
        cadquery += ",";
        cadquery+= (pendiente ? "1" : "0");
        cadquery += ",";
        cadquery += "0";
        cadquery += ",";
       }
       else
           {
            cadquery+=(derecho ? "true" : "false");
            cadquery += ",";
            cadquery+=(pendiente ? "true" : "false");
            cadquery += ",";
            cadquery += "false";
            cadquery += ",";
           }
    cadquery+= apunte_diario.left(-1).replace("'","''");
    cadquery+=", ";
    cadquery+= apunte_vto.left(-1).replace("'","''");
    cadquery+=", ";
    if ( cualControlador() == SQLITE )
       {
        cadquery+= (domiciliable ? "1" : "0");
       }
       else
           {
            cadquery+=(domiciliable ? "true" : "false");
           }
    cadquery+=")";
    ejecutar(cadquery);

}


// Indica si existe un código de ejercicio
bool basedatos::existe_codigo_ejercicio (QString cadena) {
    QString cadquery = "SELECT codigo from ejercicios where codigo = '";
    cadquery += cadena.left(-1).replace("'","''") + "'";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
        return true;
    return false;
}


bool basedatos::existen_datos_accesorios (QString cuenta) {
  QString cadquery = "SELECT cuenta from datossubcuenta where cuenta = '"+
                   cuenta.left(-1).replace("'","''") +"'";
  QSqlQuery query = ejecutar(cadquery);
  if ( (query.isActive()) &&  (query.first()) )
    {
      return true;
    }
  return false;
}

bool basedatos::hayerror_consulta()
{
  return error_consulta;
}

void basedatos::reset_error_consulta()
{
  error_consulta=false;
}


QSqlQuery basedatos::selec_diario_vr(QString pase)
{

  QString cadena="select asiento, debe, haber, cuenta, fecha from diario where pase=";
  cadena+=pase;
  return ejecutar(cadena);
}

// devuelve consulta con códigos de evpn en diario correspondiente a lista de cuentas
QSqlQuery basedatos::select_cods_evpn_pymes_diario (QString ejercicio,
                                                    QString cadenacuentas) {
    QString cadena = "select codigo_var_evpn_pymes,";
    cadena += "sum(haber), ";
    cadena += "sum(debe) ";

      cadena += "from diario where diario!='";
      cadena += diario_apertura();
      cadena += "' and diario!='";
      cadena += diario_cierre();
    cadena += "' and ejercicio='";
    cadena += ejercicio;
    cadena += "' and (";


    int partes = cadenacuentas.count(',');

    for (int veces=0; veces<=partes; veces++)
    {
        if (cualControlador() == SQLITE) {
            cadena += "cuenta like '"+ cadenacuentas.section(',',veces,veces).left(-1).replace("'","''") +"%'";
        }
        else {
            cadena += "position('";
            cadena += cadenacuentas.section(',',veces,veces).left(-1).replace("'","''");
            cadena += "' in cuenta)=1";
        }
        if (veces==partes) cadena+=")";
        else cadena+=" or ";
    }

    cadena+=" group by codigo_var_evpn_pymes order by codigo_var_evpn_pymes";
    // QMessageBox::information( 0, "consulta",cadena);
    return ejecutar(cadena);
}


// devuelve consulta diario valor apertura correspondiente a lista de cuentas
QSqlQuery basedatos::select_apertura_diario (QString ejercicio,
                                             QString cadenacuentas) {
    QString cadena = "select ";
    cadena += "sum(haber), ";
    cadena += "sum(debe) ";

    cadena += "from diario where diario='";
    cadena += diario_apertura();
    cadena += "' and ejercicio='";
    cadena += ejercicio;
    cadena += "' and (";


    int partes = cadenacuentas.count(',');

    for (int veces=0; veces<=partes; veces++)
    {
        if (cualControlador() == SQLITE) {
            cadena += "cuenta like '"+ cadenacuentas.section(',',veces,veces).left(-1).replace("'","''") +"%'";
        }
        else {
            cadena += "position('";
            cadena += cadenacuentas.section(',',veces,veces).left(-1).replace("'","''");
            cadena += "' in cuenta)=1";
        }
        if (veces==partes) cadena+=")";
        else cadena+=" or ";
    }
    // QMessageBox::warning( 0, QObject::tr("consulta"), cadena);

    return ejecutar(cadena);
}

void basedatos::asignadiarios_segun_concepto()
{
  QString cadena="update diario set diario='";
  cadena+=diario_apertura();
  cadena+="' where ";
  if (cualControlador() == SQLITE)
      cadena += "diario like 'Asiento de Apertura%'";
     else
         cadena+="position('Asiento de Apertura' in concepto )=1";
  ejecutar(cadena);

  cadena="update diario set diario='";
  cadena+=diario_regularizacion();
  cadena+="' where ";
  if (cualControlador() == SQLITE)
      cadena += "diario like 'Asiento de Regularizaci%'";
     else
         cadena+="position('Asiento de Regularizaci' in concepto )=1";
  ejecutar(cadena);

  cadena="update diario set diario='";
  cadena+=diario_cierre();
  cadena+="' where ";
  if (cualControlador() == SQLITE)
      cadena += "diario like 'Asiento de Cierre%'";
     else
         cadena+="position('Asiento de Cierre' in concepto )=1";
  ejecutar(cadena);


}



void basedatos::borra_apunte_consolidada(QString apunte_origen, int codigo)
{
    QString cadnum;
    cadnum.setNum(codigo);
    cadnum=cadnum.trimmed();
    cadnum="C"+cadnum;
    QString cad="delete from diario where apunte_origen=";
    cad+=apunte_origen;
    cad+= " and cuenta like '%";
    cad+= cadnum;
    cad+= "%'";
    ejecutar(cad);
}

void basedatos::borra_datos_consolidada(int codigo)
{
    QString cadnum;
    cadnum.setNum(codigo);
    cadnum=cadnum.trimmed();
    cadnum="C"+cadnum;
    QString cad="delete from diario where ";
    cad+= "cuenta like '%";
    cad+= cadnum;
    cad+= "%'";
    ejecutar(cad);

    cad="delete from saldossubcuenta where codigo like '%";
    cad+=cadnum;
    cad+="%'";
    ejecutar(cad);

    cad="delete from plancontable where codigo like '%";
    cad+=cadnum;
    cad+="%'";
    ejecutar(cad);
}

QSqlQuery basedatos::apuntes_borrados(QDate desdefecha, QString base)
{
    QDate fecha=desdefecha;
    fecha.addDays(-1);
    QString consulta="select pase from apuntes_borrados "
                     "where fecha_reg>='";
    consulta+=fecha.toString("yyyy-MM-dd");
    consulta+="'";
    return ejecutar(consulta,base);
}


QSqlQuery basedatos::select_consolidables()
{
  QString cadena="select codigo, nombrebd, descrip, fecha_actu, hora_actu, usuario, clave, host, "
         "puerto, controlador, ultimo_apunte, import_tipos_diario from consolidables order by codigo";
  return ejecutar(cadena);
}

void basedatos::borra_consolidables()
{
    QString cadquery = "delete from consolidables";
    ejecutar(cadquery);
}

void basedatos::inserta_reg_consolidable(QString controlador, QString basedatos,
                                         QString usuario, QString clave, QString host,
                                         QString puerto, int codigo,
                                         QString descripcion, bool infodiario,
                                         QDate fecha_actu, QString hora_actu,
                                         long ultimo_apunte)
{
  QString cadena;
  cadena="insert into consolidables (codigo, nombrebd, descrip, "
         "fecha_actu, hora_actu, usuario, clave, host, "
         "puerto, controlador, ultimo_apunte, import_tipos_diario) "
         "values (";
  QString cadcodigo; cadcodigo.setNum(codigo);
  QString cadultimo_apunte; cadultimo_apunte.setNum(ultimo_apunte);
  cadena+=cadcodigo;
  cadena+=", '";
  cadena+=basedatos;
  cadena+="', '";
  cadena+=descripcion;
  cadena+="', '";
  cadena+=fecha_actu.toString("yyyy-MM-dd");
  cadena +="', '";
  cadena += hora_actu;
  cadena +="', '";
  cadena+=usuario;
  cadena+="', '";
  cadena+=clave;
  cadena+="', '";
  cadena+=host;
  cadena+="', '";
  cadena+=puerto;
  cadena+="', '";
  cadena+=controlador;
  cadena+="', ";
  cadena+=cadultimo_apunte;
  cadena+=", ";
  if ( cualControlador() == SQLITE )
      cadena+= (infodiario ? "1" : "0");
     else
          cadena+=(infodiario ? "true" : "false");

  cadena+=" )";
  ejecutar(cadena);

}


void basedatos::marca_apunte_revisado(qlonglong asiento, QString ejercicio, bool marcado)
{
   QString cadasiento; cadasiento.setNum(asiento);
   QString cadena="update diario set revisado=";
   if ( cualControlador() == SQLITE )
       cadena+= (!marcado ? "1" : "0");
      else
           cadena+=(!marcado ? "true" : "false");
   cadena+=" where asiento= ";
   cadena+=cadasiento;
   cadena+=" and ejercicio='";
   cadena+=ejercicio;
   cadena+="'";
   ejecutar(cadena);
}



// devuelve consulta para filtrar por vencimientos en domiciliacion
QSqlQuery basedatos::select_venci_filtrados ( QString importe1, QString importe2,
                                              QString cuenta1, QString cuenta2, bool venci,
                                              QDate venci1, QDate venci2, bool oper,
                                              QDate op1, QDate op2,
                                              QString cta_banco, int orden)
{
    QString cadena = "select ";
    cadena += "v.num, v.concepto, v.fecha_operacion, v.importe, ";
    cadena += "v.fecha_vencimiento, v.cta_ordenante, p.descripcion, d.documento ";

    cadena += "from vencimientos v, diario d, plancontable p where ";
    cadena += "d.pase=v.pase_diario_operacion and ";
    cadena += "p.codigo=v.cta_ordenante";

    if (!importe2.isEmpty())
      {
        cadena += " and v.importe>=";
        cadena += importe1.toDouble()>0.001 ? importe1 : "0";
        cadena += " and v.importe<=";
        cadena += importe2.toDouble()>0.001 ? importe2 : "0";
      }

    if (!cuenta1.isEmpty() && !cuenta2.isEmpty())
       {
        cadena += " and v.cta_ordenante>='";
        cadena += cuenta1.left(-1).replace("'","''") +"'";
        cadena += " and v.cta_ordenante<='";
        cadena += cuenta2.left(-1).replace("'","''") +"'";
       }

    if (venci)
      {
        cadena += " and fecha_vencimiento>='";
        cadena += venci1.toString("yyyy-MM-dd");
        cadena += "' and fecha_vencimiento<='";
        cadena += venci2.toString("yyyy-MM-dd");
        cadena +="'";
      }

    if (oper)
      {
        cadena += " and fecha_operacion>='";
        cadena += op1.toString("yyyy-MM-dd");
        cadena += "' and fecha_operacion<='";
        cadena += op2.toString("yyyy-MM-dd");
        cadena +="'";
      }

    if (!cta_banco.isEmpty())
      {
        cadena += " and v.cuenta_tesoreria='";
        cadena += cta_banco.left(-1).replace("'","''") +"'";

      }

    cadena+=" and derecho and domiciliacion=0 and pendiente and domiciliable order by ";
    switch (orden)
     {
     case 0: cadena+="v.num"; break;
     case 1: cadena+="v.concepto"; break;
     case 2: cadena+="v.fecha_operacion"; break;
     case 3: cadena+="v.importe"; break;
     case 4: cadena+="v.fecha_vencimiento"; break;
     case 5: cadena+="v.cta_ordenante"; break;
     case 6: cadena+="p.descripcion"; break;
     case 7: cadena+="d.documento"; break;
     }
    // QMessageBox::warning( 0, QObject::tr("filtrar"),cadena);
    return ejecutar(cadena);
}


void basedatos::transac_bloquea_config()
{
    QSqlDatabase::database().transaction();
    if (cualControlador() == MYSQL)
       {
        ejecutar("SET AUTOCOMMIT=0");
        ejecutar("LOCK TABLE configuracion write");
       }
    if (cualControlador() == POSTGRES)
       {
         ejecutar("LOCK TABLE configuracion IN ACCESS EXCLUSIVE MODE");
       }
}


void basedatos::pasa_domiciliacion_vto(QString num, QString domiciliacion)
{
   QString cadena="update vencimientos set domiciliacion=";
   cadena+=domiciliacion;
   cadena+=" where num=";
   cadena+=num;
   ejecutar(cadena);
}



void basedatos::guardadomiciliacion(QString id_remesa, QString aux_banco, QString descrip,
                               QString ccc, QString iban, QString sufijo, QString cedente1,
                               QString cedente2, QString riesgo, QDate fecha_conf,
                               QDate fecha_cargo,
                               bool emitido19, bool emitido58, bool emitidosepa1914,
                               bool procesada)
{
  // ¿ existe la remesa ?
  bool existe=false;
  QString cadena="select id_remesa from domiciliaciones where id_remesa=";
  cadena+=id_remesa.left(-1).replace("'","''");
  QSqlQuery q = ejecutar(cadena);
  if (q.isActive())
      if (q.next()) existe=true;

  if (existe)
     {
       // hacer un update
       cadena="update domiciliaciones set ";
       cadena+="aux_banco='"+aux_banco.left(-1).replace("'","''")+"', ";  // Cuenta auxiliar correspondiente al banco
       cadena+="descrip='"+descrip.left(-1).replace("'","''")+"', "; // Campo con descripción de la remesa
       cadena+="ccc='"+ccc.left(-1).replace("'","''")+"', ";  // Código cuenta cliente
       cadena+="iban='"+iban.left(-1).replace("'","''")+"', "; // IBAN
       cadena+="sufijo='" + sufijo.left(-1).replace("'","''") +"', "; // Campo sufijo
       cadena+="cedente1='" + cedente1.left(-1).replace("'","''") + "', ";  // Campo cedente1
       cadena+="cedente2='" + cedente2.left(-1).replace("'","''") + "', ";  // Campo cedente2
       cadena+="riesgo='" + riesgo.left(-1).replace("'","''") + "', ";  //  Número de riesgo
       cadena+="fecha_conf='" + fecha_conf.toString("yyyy-MM-dd") + "', ";    // Fecha de confección de la remesa
       cadena+="fecha_cargo='" + fecha_cargo.toString("yyyy-MM-dd") + "', ";  // Fecha de cargo
       if ( cualControlador() == SQLITE )
         {cadena += "emitido19="; cadena+=(emitido19 ? "1, " : "0, ");}
       else
         {cadena += "emitido19="; cadena+= (emitido19 ? "true, " : "false, ");}
       if ( cualControlador() == SQLITE )
         { cadena += "emitido58="; cadena+=(emitido58 ? "1, ": "0, ");}
       else
         { cadena += "emitido58="; cadena+=(emitido58 ? "true, " : "false, ");}

       if ( cualControlador() == SQLITE )
         { cadena += "emitidosepa1914="; cadena+=(emitidosepa1914 ? "1, ": "0, ");}
       else
         { cadena += "emitidosepa1914="; cadena+=(emitidosepa1914 ? "true, " : "false, ");}

       if (cualControlador() == SQLITE )
         { cadena += "procesada="; cadena+=(procesada ? "1 " : "0 "); }
       else
         {cadena += "procesada="; cadena+=(procesada ? "true " : "false ");}

       cadena += "where id_remesa=";
       cadena += id_remesa;
       ejecutar(cadena);
       return;
     }

  // insertar registro

 cadena="insert into domiciliaciones (id_remesa, aux_banco, descrip, ccc, iban, "
        "sufijo, cedente1, cedente2, riesgo, fecha_conf, fecha_cargo, "
        "emitido19, emitido58, emitidosepa1914, procesada) values ( ";
 cadena+=id_remesa;
 cadena+=", '";
 cadena+=aux_banco.left(-1).replace("'","''");
 cadena+="', '";
 cadena+=descrip.left(-1).replace("'","''");
 cadena+="', '";
 cadena+=ccc.left(-1).replace("'","''");
 cadena+="', '";
 cadena+=iban.left(-1).replace("'","''");
 cadena+="', '";
 cadena+=sufijo.left(-1).replace("'","''");
 cadena+="', '";
 cadena+=cedente1.left(-1).replace("'","''");
 cadena+="', '";
 cadena+=cedente2.left(-1).replace("'","''");
 cadena+="', '";
 cadena+=riesgo.left(-1).replace("'","''");
 cadena+="', '";
 cadena+=fecha_conf.toString("yyyy-MM-dd");
 cadena+="', '";
 cadena+=fecha_cargo.toString("yyyy-MM-dd");;
 cadena+="', ";
 if ( cualControlador() == SQLITE )
     cadena += (emitido19 ? "1, " : "0, ");
 else
     cadena += (emitido19 ? "true, " : "false, ");
 if ( cualControlador() == SQLITE )
     cadena += (emitido58 ? "1, ": "0, ");
 else
     cadena += (emitido58 ? "true, " : "false, ");

 if ( cualControlador() == SQLITE )
     cadena += (emitidosepa1914 ? "1, ": "0, ");
 else
     cadena += (emitidosepa1914 ? "true, " : "false, ");

 if (cualControlador() == SQLITE )
     cadena += (procesada ? "1 " : "0 ");
 else
     cadena += (procesada ? "true " : "false ");
 cadena+=");";

 ejecutar(cadena);
}

/*
CREATE TABLE domiciliaciones ("
           "id_remesa    bigint,"
           "aux_banco    varchar(80),"  // Cuenta auxiliar correspondiente al banco
           "descrip      varchar(255)," // Campo con descripción de la remesa
           "ccc          varchar(80),"  // Código cuenta cliente
           "iban         varchar(80),"  // IBAN
           "sufijo       varchar(80),"  // Campo sufijo
           "cedente1     varchar(80),"  // Campo cedente1
           "cedente2     varchar(80),"  // Campo cedente2
           "riesgo       varchar(80),"  //  Número de riesgo
           "fecha_conf   date       ,"  // Fecha de confección de la remesa
           "fecha_cargo  date       ,";  // Fecha de cargo
           if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
               cadena += "emitido19 bool default 0,";
           else
               cadena += "emitido19 bool default false,";
           if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
               cadena += "emitido58 bool default 0,";
           else
               cadena += "emitido58 bool default false,";
           if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE )
               cadena += "procesada bool default 0,";
           else
               cadena += "procesada bool default false,";

           cadena+=  "PRIMARY KEY (id_remesa) )";
 */

QSqlQuery basedatos::carga_domiciliacion(QString id)
{
    QString cadena="select aux_banco, descrip, ccc, iban, "
           "sufijo, cedente1, cedente2, riesgo, fecha_conf, fecha_cargo, "
           "emitido19, emitido58, emitidosepa1914, procesada "
           "from domiciliaciones where id_remesa=";
    cadena+=id;

    return ejecutar(cadena);
}

void basedatos::elimina_domiciliacion(QString id)
{
   QString cadena;
   cadena="update vencimientos set domiciliacion=0 where domiciliacion=";
   cadena+=id;
   ejecutar(cadena);

   cadena="delete from domiciliaciones where id_remesa=";
   cadena+=id;
   ejecutar(cadena);

}


int basedatos::cuenta_regs_domiciliacion(QString id)
{
    QString cadena="select count(num) "
                   "from vencimientos where "
                   "domiciliacion=";
    cadena+=id;
    QSqlQuery q=ejecutar(cadena);
    if (q.isActive())
        if (q.next()) return q.value(0).toInt();
    return 0;
}


QSqlQuery basedatos::datos_domiciliacion(QString id)
{
    QString cadena="select v.num, v.cta_ordenante, v.importe, v.fecha_operacion,"
                   "v.fecha_vencimiento, d.documento, datos.razon, datos.cif,"
                   "datos.ccc, datos.IBAN, v.pendiente, v.impagado, v.concepto, datos.bic, "
                   "datos.domicilio, datos.codigopostal, datos.poblacion, datos.provincia, "
                   "datos.pais, datos.ref_mandato, datos.firma_mandato "
                   "from vencimientos v, diario d, datossubcuenta datos where "
                   "d.pase=v.pase_diario_operacion and datos.cuenta=d.cuenta "
                   "and v.domiciliacion=";
    cadena+=id;
    // QMessageBox::warning( 0, "Domiciliaciones",cadena);
    return ejecutar(cadena);

}

QString basedatos::config_codigo_ine_plaza()
{
   QSqlQuery q = ejecutar("select cod_ine_plaza from configuracion");
   if (q.isActive())
       if (q.next()) return q.value(0).toString();
   return QString();
}

void basedatos::marca_vto_impagado(QString num, bool impagado)
{
  QString cadena="update vencimientos set impagado=";
  if (cualControlador() == SQLITE )
      cadena += (impagado ? "1 " : "0 ");
  else
      cadena += (impagado ? "true " : "false ");
  cadena+="where num=";
  cadena+=num;
  ejecutar(cadena);
}

bool basedatos::cuenta_domiciliable(QString cuenta)
{
  QString cadena="select dom_bancaria from datossubcuenta where cuenta='";
  cadena+=cuenta;
  cadena+="'";
  QSqlQuery q = ejecutar(cadena);
  if (q.isActive())
      if (q.next()) return q.value(0).toBool();
  return false;

}


void basedatos::borra_tabla_apuntes_borrados()
{
    QString cadena="delete from apuntes_borrados";
    ejecutar(cadena);
}


int basedatos::registros_plan()
{
  int numero=0;
  QSqlQuery q=ejecutar("select count(codigo) from plancontable");
  if (q.isActive())
      if (q.next()) numero=q.value(0).toInt();
  return numero;
}

bool basedatos::hay_criterio_caja_aux(QString codigo)
{
  QString cadena="select caja_iva from datossubcuenta where cuenta='";
  cadena+=codigo;
  cadena+="'";
  QSqlQuery q=ejecutar(cadena);
  if(q.isActive())
      if (q.next())
          return q.value(0).toBool();
  return false;
}


QSqlQuery basedatos::registros_libro_facturas(QDate inicio, QDate final)
{
  QString cadena="select d.asiento, l.pase, l.cuenta_fra, l.base_iva+l.cuota_iva, "
                 "l.soportado, l.aib, l.eib, l.autofactura, l.autofactura_no_ue, "
                 "l.pr_servicios_ue, l.isp_op_interiores, l.importacion, "
                 "l.exportacion, d.fecha, l.nombre, l.cif from libroiva l, diario d where d.fecha>='";
  cadena+=inicio.toString("yyyy-MM-dd");
  cadena+="' and d.fecha<='";
  cadena+=final.toString("yyyy-MM-dd");
  cadena+="' and d.pase=l.pase order by d.fecha";
  return ejecutar(cadena);
}


bool basedatos::mostrar_advert()
{
  QString cadena="select mostrar from condiciones";
  QSqlQuery q=ejecutar(cadena);
  if (q.isActive())
      if (q.next())
          return q.value(0).toBool();
  return false;
}

void basedatos::datos_advert(QString *mensaje, QString *nombre, QString *cargo,
                             QString *organizacion)
{
    QString cadena="select mensaje, nombre, cargo, organizacion from condiciones order by registro";
    QSqlQuery q=ejecutar(cadena);
    if (q.isActive())
        if (q.next())
           {
             *mensaje=q.value(0).toString();
             *nombre=q.value(1).toString();
             *cargo=q.value(2).toString();
             *organizacion=q.value(3).toString();
           }
    return;
}


void basedatos::pasa_datos_advert(QString mensaje, QString nombre, QString cargo,
                             QString organizacion, bool acepta)
{

  QString cadena="update condiciones set mensaje='";
  cadena+=mensaje.left(-1).replace("'","''");
  cadena+="', nombre='";
  cadena+=nombre.left(-1).replace("'","''");
  cadena+="', cargo='";
  cadena+=cargo.left(-1).replace("'","''");
  cadena+="', organizacion='";
  cadena+=organizacion;
  cadena+="', acepta=";
  if (cualControlador() == SQLITE )
      cadena += (acepta ? "1 " : "0 ");
  else
      cadena += (acepta ? "true " : "false ");
  cadena+=", mostrar=";
  if (cualControlador() == SQLITE )
      cadena += (acepta ? "0 " : "1 ");
  else
      cadena += (acepta ? "false " : "true ");
  cadena+=", fecha='";
  cadena+=QDate().currentDate().toString("yyyy-MM-dd");
  cadena+="', hora='";
  cadena+=QTime().currentTime().toString("hhmmss");
  cadena+="'";
  ejecutar(cadena);
}


void basedatos::actualiza_cabecera_lote_fact(QString codigo, bool cuotas,
                                             QString cod_factura_predef,
                                             QString presupuesto)
{
  QString cadena="update lote_fact set cuotas=";
  if (cualControlador() == SQLITE) cadena+=cuotas ? "1":"0";
    else cadena+=cuotas ? "true" : "false";
  cadena+=", cod_factura_predef='";
  cadena+=cod_factura_predef.left(-1).replace("'","''");
  cadena+="', presupuesto=";
  double npresupuesto=presupuesto.toDouble();
  QString cadpresupuesto; cadpresupuesto.setNum(npresupuesto,'f',2);
  cadena+=cadpresupuesto;
  cadena+=" where codigo='";
  cadena+=codigo.left(-1).replace("'","''");
  cadena+="'";
  ejecutar(cadena);

}

void basedatos::borra_lins_lote_predef(QString codigo)
{
  QString cadena="delete from lin_lote_predef where codigo='";
  cadena+=codigo.left(-1).replace("'","''");
  cadena+="'";
  ejecutar(cadena);
}


void basedatos::inserta_lin_lote_predef(QString codigo, QString cuenta, QString importe,
                                        QString cuota)
{
  QString cadena="insert into lin_lote_predef (codigo, cuenta, importe, cuota) values ('";
  cadena+=codigo.left(-1).replace("'","''");
  cadena+="', '";
  cadena+=cuenta.left(-1).replace("'","''");
  cadena+="', '";
  cadena+=importe.left(-1).replace("'","''");
  cadena+="', '";
  cadena+=cuota.left(-1).replace("'","''");
  cadena+="')";
  ejecutar (cadena);
}
/*
    // lotes facturas predefinidas
    cadena = "create table lote_fact ( "
              "codigo       varchar(80),"
              "descripcion  varchar(255),";
              if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE ) {
                  cadena += "cuotas bool default 0,";
                 }
                 else {
                       cadena += "cuotas bool default false,";
                      }
    cadena += "cod_factura_predef  varchar(80), ";
    cadena += "presupuesto numeric(19,2) default 0, ";
    cadena+=" PRIMARY KEY (codigo) )";

    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

    // línea de lotes facturas predefinidas
    cadena = "create table lin_lote_predef ( ";
    if ( ( segunda ? cualControlador(qbase) : cualControlador()) == SQLITE) {
        cadena += "linea integer primary key autoincrement,";
    }
    else {
        cadena += "linea   serial,"; }
    cadena += "codigo       varchar(80), "  // por este campo se relaciona con cabecera
              "cuenta       varchar(80), "
              "importe      varchar(80), "
              "cuota        varchar(80) ";
    if (( segunda ? cualControlador(qbase) : cualControlador())!=SQLITE)
        cadena+=", PRIMARY KEY (linea) )";
       else cadena+=")";

    if (segunda) cadena = anadirMotor(cadena,qbase); else cadena = anadirMotor(cadena);
    if (segunda) ejecutar(cadena,qbase); else ejecutar(cadena);

*/

QSqlQuery basedatos::cabecera_lote_fact(QString codigo)
{
    QString cadena="select cuotas, cod_factura_predef, presupuesto from lote_fact "
                   "where codigo='";
    cadena+=codigo.left(-1).replace("'","''");
    cadena+="'";
    return ejecutar(cadena);
}

QSqlQuery basedatos::lineas_lin_lote_predef(QString codigo)
{
    QString cadena="select cuenta, importe, cuota from lin_lote_predef where codigo='";
    cadena+=codigo.left(-1).replace("'","''");
    cadena+="' order by linea";
    return ejecutar(cadena);
}


QSqlQuery basedatos::select_facturas_predef(QString codigo)
{
    QString cadena="select f.descrip, t.serie, f.contabilizable, f.con_ret, f.con_re, f.tipo_ret, "
                   "f.tipo_doc, f.notas, f.pie1, f.pie2 from facturas_predef f, "
                   "tipos_doc t where f.codigo='";
    cadena+=codigo.left(-1).replace("'","''");
    cadena+="' and t.codigo=f.tipo_doc";
    return ejecutar(cadena);

}

QSqlQuery basedatos::select_lin_facturas_predef(QString codigo)
{
    QString cadena="select linea, codigo, descripcion, cantidad, precio, clave_iva, tipo_iva, "
                   "tipo_re, dto "
                   "from lin_fact_predef where cod_fact='";
    cadena+=codigo.left(-1).replace("'","''");
    cadena+="' order by linea";
    return ejecutar(cadena);

}

void basedatos::update_cuenta_gasto_vto_config(QString codigo)
{
    QString cadena = "update configuracion set cuenta_gasto_vto='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}


QString basedatos::selec_cuenta_gasto_vto_config()
{
   QString cadena="select cuenta_gasto_vto from configuracion";
   QSqlQuery q=ejecutar(cadena);
   if (q.isActive())
       if (q.next()) return q.value(0).toString();
   return QString();
}


// Devuelve el nombre la cuenta tesorería por defecto para configuración
QString basedatos::select_cuenta_tesoreria_configuracion () {
    QString cadquery = "SELECT cuenta_tesoreria from configuracion";
    QSqlQuery query = ejecutar(cadquery);
    if ( (query.isActive()) && (query.first()) )
    {
        return query.value(0).toString();
    }
    return "";
}

void basedatos::update_cuenta_tesoreria_config(QString codigo)
{
    QString cadena = "update configuracion set cuenta_tesoreria='";
    cadena += codigo.left(-1).replace("'","''");
    cadena += "'";
    ejecutar(cadena);
}


bool basedatos::hay_reg_duplicado_diario(QString cuenta, QString documento)
{
    QString cad="select documento from diario where cuenta='";
    cad+=cuenta.left(-1).replace("'","''");
    cad+="' and documento='";
    cad+=documento.left(-1).replace("'","''");
    cad+="'";
    QSqlQuery query = ejecutar(cad);
    if ( (query.isActive()) && (query.first()) )
        return true;

    return false;

}


QString basedatos::apuntes_no_libroiva(bool soportado)
{
    QString respuesta=soportado ? QObject::tr("APUNTES DE IVA SOPORTADO\n") : QObject::tr("APUNTES DE IVA REPERCUTIDO\n");
      respuesta+="\n"+soportado ? QObject::tr("-----------------------------------------------\n\n") :
                                  QObject::tr("------------------------------------------------------\n\n");
    QSqlQuery q=ejecutar(soportado ? "select cuenta_iva_soportado from configuracion" :
                                     "select cuenta_iva_repercutido from configuracion");
    QString cuenta_iva;
    if (q.isActive())
        if (q.next()) cuenta_iva=q.value(0).toString();
    if (cuenta_iva.isEmpty()) return QString();

    QList<int> apuntes_libro_iva;

    q=ejecutar("select pase from libroiva");
    if (q.isActive())
        while (q.next())
          {
            apuntes_libro_iva << q.value(0).toInt();
          }

    if (apuntes_libro_iva.isEmpty()) return QString();

    QString cadena="select asiento, pase, fecha, cuenta, concepto, debe, haber from diario where ";
    if (cualControlador() == SQLITE) {
        cadena += "cuenta like '"+ cuenta_iva +"%' ";
    }
    else {
        cadena += "position('";
        cadena += cuenta_iva.left(-1).replace("'","''");
        cadena += "' in cuenta)=1";
    }

    q=ejecutar(cadena);
    if (q.isActive())
        while (q.next())
          {
             QApplication::processEvents();
             if (!apuntes_libro_iva.contains(q.value(1).toInt()))
             {
                 respuesta+=q.value(0).toString();
                 respuesta+="\t";
                 respuesta+=q.value(2).toDate().toString("dd-MM-yyyy");
                 respuesta+="\t";
                 respuesta+=q.value(3).toString();
                 respuesta+="\t";
                 respuesta+=q.value(4).toString();
                 respuesta+="\t";
                 if (q.value(5).toDouble()>0.001 || q.value(5).toDouble()<-0.001)
                    {
                     respuesta+=QObject::tr("(D)");
                     respuesta+="\t";
                     respuesta+=q.value(5).toString();
                    }
                    else
                         {
                          respuesta+=QObject::tr("(H)");
                          respuesta+="\t";
                          respuesta+=q.value(6).toString();
                         }
                 respuesta+="\n\n";
             }
          }

    return respuesta;
}



QString basedatos::apuntes_descuadre_libroiva()
{
    QString respuesta=QObject::tr("APUNTES DE IVA SOPORTADO\n\n");
         respuesta+=QObject::tr("\nAsiento\tFecha\tCuenta\tDebe\tHaber\tCuota\n\n");
        QString cadena="select d.asiento, d.fecha, d.cuenta, d.debe, d.haber, l.cuota_iva from diario d, libroiva l ";
        cadena+="where d.pase=l.pase and l.soportado and tipo_iva>0 and "
                "(l.cuota_iva-d.debe+d.haber>0.01 or l.cuota_iva-d.debe+d.haber<-0.01)";
        QSqlQuery q=ejecutar(cadena);
        if (q.isActive())
            while (q.next())
              {
                respuesta+=q.value(0).toString();
                respuesta+="\t";
                respuesta+=q.value(1).toString();
                respuesta+="\t";
                respuesta+=q.value(2).toString();
                respuesta+="\t";
                respuesta+=q.value(3).toString();
                respuesta+="\t";
                respuesta+=q.value(4).toString();
                respuesta+="\t";
                respuesta+=q.value(5).toString();
                respuesta+="\n\n";
              }


        respuesta+=QObject::tr("\n\n\nAPUNTES DE IVA REPERCUTIDO\n\n");
        respuesta+=QObject::tr("\nAsiento\tFecha\tCuenta\tDebe\tHaber\tCuota\n\n");

            cadena="select d.asiento, d.fecha, d.cuenta, d.debe, d.haber, l.cuota_iva from diario d, libroiva l ";
            cadena+="where d.pase=l.pase and not l.soportado and tipo_iva>0 and "
                    "(l.cuota_iva+d.debe-d.haber>0.01 or l.cuota_iva+d.debe-d.haber<-0.01)";
            q=ejecutar(cadena);
            if (q.isActive())
                while (q.next())
                  {
                    respuesta+=q.value(0).toString();
                    respuesta+="\t";
                    respuesta+=q.value(1).toString();
                    respuesta+="\t";
                    respuesta+=q.value(2).toString();
                    respuesta+="\t";
                    respuesta+=q.value(3).toString();
                    respuesta+="\t";
                    respuesta+=q.value(4).toString();
                    respuesta+="\t";
                    respuesta+=q.value(5).toString();
                    respuesta+="\n\n";
                  }

    return respuesta;
}


QSqlQuery basedatos::datos_carta347()
{
   return ejecutar("select cabecera347, cuerpo347, desglose347, cuerpo2_347, pie347, saludo347, firma347 from configuracion");
}

void basedatos::actu_config_carta347(QString cabecera347, QString cuerpo347, bool desglose347,
                                     QString cuerpo2_347, QString pie347, QString saludo347, QString firma347)
{
   QString cadena="update configuracion set cabecera347='";
   cadena+=cabecera347.left(-1).replace("'","''");
   cadena+="', cuerpo347='";
   cadena+=cuerpo347;
   cadena+="', desglose347=";
   if (cualControlador() == SQLITE)
       cadena+=desglose347 ? "1" : "0";
      else cadena+=desglose347 ? "true" : "false";
   cadena+=", cuerpo2_347='";
   cadena+=cuerpo2_347;
   cadena+="', pie347='";
   cadena+=pie347;
   cadena+="', saludo347='";
   cadena+=saludo347;
   cadena+="', firma347='";
   cadena+=firma347;
   cadena+="'";
   ejecutar(cadena);
}

QStringList basedatos::listacuentas()
{
    int anchoctas=anchocuentas();
    QStringList lista;
    QSqlQuery q=ejecutar("select codigo from plancontable order by codigo");
    if (q.isActive())
        while (q.next())
          {
            if (q.value(0).toString().length()==anchoctas) continue;
            lista << q.value(0).toString();
          }
    return lista;
}


QStringList basedatos::listacuentas_aux()
{
    int anchoctas=anchocuentas();
    QStringList lista;
    QSqlQuery q=ejecutar("select codigo from plancontable order by codigo");
    if (q.isActive())
        while (q.next())
          {
            if (q.value(0).toString().length()!=anchoctas) continue;
            lista << q.value(0).toString();
          }
    return lista;
}

void basedatos::reemplaza_aux_plan(QString origen, QString por)
{
   QString cadena="update plancontable set codigo='";
   cadena+=por;
   cadena+="' where codigo='";
   cadena+=origen;
   cadena+="'";
   ejecutar(cadena);
}


QSqlQuery basedatos::datos_venci_fechas(QString cuenta_tesor, QDate fechaini, QDate fechafin, int margen)
{
    QString cad = "select "
                  "num, "
                  "importe, "
                  "fecha_vencimiento, "
                  "derecho "
                  "from vencimientos where cuenta_tesoreria='";
    cad+=cuenta_tesor.left(-1).replace("'","''");
    cad+="' and fecha_vencimiento>='";
    QDate ini=fechaini.addDays(-margen);
    cad+=ini.toString("yyyy-MM-dd");
    cad+="' and fecha_vencimiento<='";
    QDate fin=fechafin.addDays(margen);
    cad+=fin.toString("yyyy-MM-dd");
    cad+="' and pendiente and not anulado";
    return ejecutar(cad);

}


QSqlQuery basedatos::datos_ext_conci_fechas(QString cuenta, QDate inicial, QDate final)
{
    QString cadena = "select numero, fecha, debe,haber "
        "from cuenta_ext_conci where cuenta='";
    cadena += cuenta.left(-1).replace("'","''");
    cadena += "' and fecha>='";
    cadena += inicial.toString("yyyy-MM-dd");
    cadena += "' and fecha<='";
    cadena += final.toString("yyyy-MM-dd");
    cadena += "' and not conciliado order by numero";
    return ejecutar(cadena);

}


QSqlQuery basedatos::select_apuntes_analitica_diario (QString cuenta,QDate fechaini, QDate fechafin) {
    QString cadquery = "select asiento, pase, clave_ci, debe+haber from diario where cuenta='";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "' and fecha>='";
    cadquery += fechaini.toString("yyyy-MM-dd");
    cadquery += "' and fecha<='";
    cadquery += fechafin.toString("yyyy-MM-dd");
    cadquery += "'";
    return ejecutar(cadquery);
}


int basedatos::registros_select_apuntes_analitica_diario (QString cuenta,QDate fechaini, QDate fechafin) {
    QString cadquery = "select count(pase) from diario where cuenta='";
    cadquery += cuenta.left(-1).replace("'","''");
    cadquery += "' and fecha>='";
    cadquery += fechaini.toString("yyyy-MM-dd");
    cadquery += "' and fecha<='";
    cadquery += fechafin.toString("yyyy-MM-dd");
    cadquery += "'";
    QSqlQuery q=ejecutar(cadquery);
    if (q.isActive())
        if (q.next()) return q.value(0).toInt();
    return 0;
}
