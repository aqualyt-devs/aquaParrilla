<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0" />
    <title>Aqualyt - Parrilla</title>
    <?php include 'styles.php'; ?>
</head>

<body>
    <?php
    include 'urlAPI.php';
    include 'checkToken.php';

    if ((!isset($_COOKIE["AquaToken"])) || (checkToken($urlAPI)->codigo == 200)) {
        header("Refresh:0; url=index.php");
    } else {
        include 'functions.php';
        $idPS = null;
        $idContacto = null;

        if (isset($_GET['idPS'])) {
            $idPS = $_GET['idPS'];
        }
        if (isset($_GET['idContacto'])) {
            $idContacto = $_GET['idContacto'];
        } else {
            header("Refresh:0; url=./contactoControl.php?idPS=" . $idPS);
        }

    ?>
        <div class="navbar-fixed">
            <nav class="blue darken-3" role="navigation">
                <div class="nav-wrapper">
                    <a id="logo-container" href="index.php" class="brand-logo">
                        <img src="imgs/logo_negativo.png">
                    </a>
                    <a href="#" data-activates="mobileMenu" class="button-collapse"><i class="material-icons">menu</i></a>
                    <ul class="right hide-on-med-and-down">
                        <li>
                            <a href="#" class="datepicker" data-value="" id="selectorFecha">
                                <i class="material-icons left">date_range</i>
                                <span id="filtroFecha"></span>
                            </a>
                        </li>
                        <li>
                            <a href="../aquaoperario">
                                <i class="material-icons left">account_circle</i>Operario</a>
                        </li>
                        <li>
                            <a href="controlEquipos.php" class="controlEquiposMenu">
                                <i class="material-icons left">directions_car</i>Control equipos </a>
                        </li>
                        <li>
                            <a href="index.php">
                                <i class="material-icons left">list</i>Control PS</a>
                        </li>
                        <li>
                            <a href="#modalPausa" class="modal-trigger">
                                <i class="material-icons left">pause</i>Asignar pausa</a>
                        </li>
                        <li>
                            <a href="index.php?logout=1">
                                <i class="material-icons left">exit_to_app</i><?php echo $_COOKIE['AquaTokenNOMBRE']; ?></a>
                        </li>
                    </ul>
                    <ul class="side-nav" id="mobileMenu">
                        <li>
                            <a href="#" class="datepicker" data-value="" id="selectorFecha">
                                <i class="material-icons left">date_range</i>
                                <span id="filtroFechaMobile"></span>
                            </a>
                        </li>
                        <li>
                            <a href="../aquaoperario">
                                <i class="material-icons left">account_circle</i>Operario</a>
                        </li>
                        <li>
                            <a href="index.php">
                                <i class="material-icons left">list</i>Control PS</a>
                        </li>
                        <li>
                            <a href="#modalPausa" class="modal-trigger">
                                <i class="material-icons left">pause</i>Asignar pausa</a>
                        </li>
                        <li>
                            <a href="index.php?logout=1">
                                <i class="material-icons left">exit_to_app</i><?php echo $_COOKIE['AquaTokenNOMBRE']; ?></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <main>
            <div class="section">
                <div class="row">
                    <?php
                    $infoContacto = getContent($urlAPI . "/equipos/contactos/temporal/get/id/" . $idContacto)->contenido->{1};
                    $direccionContactoSistema = getContent($urlAPI . "/equipos/direccion/ps/" . $idPS)->contenido->{1};

                    $arrayLugarPS = objectToArray(getContent($urlAPI . "/equipos/get_lugar_ps/ps/" . $idPS)->contenido);
                    $arrayOrganismoPS = objectToArray(getContent($urlAPI . "/equipos/get_organismos_ps/ps/" . $idPS)->contenido);
                    $arrayTipoCalle = objectToArray(getContent($urlAPI . "/equipos/keyvalue/tipo_calle/")->contenido);
                    $arrayTipoContactoLugar = objectToArray(getContent($urlAPI . "/equipos/keyvalue/tipo_contacto_lugar")->contenido);
                    $arrayTipoContactoOrganismo = objectToArray(getContent($urlAPI . "/equipos/keyvalue/tipo_contacto_organismo")->contenido);

                    $direccionContactoSistemaString = $arrayTipoCalle[$direccionContactoSistema->tipo_calle] . ' ' . $direccionContactoSistema->calle . ' ' . $direccionContactoSistema->num_calle . ', ' . $direccionContactoSistema->piso . ' ' . $direccionContactoSistema->cp . ' ' . $direccionContactoSistema->poblacion . ' ' . $direccionContactoSistema->provincia;
                    if (strlen($direccionContactoSistemaString) > 10) {
                        $direccionContactoSistemaStringBtn = ' Dirección: ' . $direccionContactoSistemaString . ' <a href="#modalCopiarDireccion" class="modal-trigger">Copiar dirección</a>';
                    }

                    echo '<div class="col s2"><div class="collection z-depth-2 optiscroll">';
                    echo "<a href='contactoComprobar.php?idPS=$idPS&idContacto=$idContacto' class='collection-item active''>Volver PS $idPS</a>";
                    echo '</div></div>
                        <div class="col s10"><div class="collection z-depth-2 optiscroll columnHeight">
                        <div class="row">';
                    echo "<div class='col s12'><h5><a href='http://app.aqualyt.net/sf/action.php?SOURCE=PS2&amp;ACTION=SHOWINITIAL&amp;LOAD_PS=$idPS' target='_blank'>PS $idPS</a></h5></div>";
                    echo '<div class="col s12">
                                    <h4>Editar contacto <small class="direccionContacto">' . $direccionContactoSistemaStringBtn . '</small></h4>
                                </div>
                                <div class="section">
                                <input type="hidden" id="idPS" value="' . $idPS . '">
                                    <form id="infoContacto" class="col s12">
                                        <div class="row">
                                            <div class="input-field col s3">
                                                <input name="nombre" id="nombre" type="text" value="' . $infoContacto->nombre . '">
                                                <label for="nombre">Nombre</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input name="apellido1" id="apellido1" type="text" value="' . $infoContacto->apellido1 . '">
                                                <label for="apellido1">Apellido 1</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input name="apellido2" id="apellido2" type="text" value="' . $infoContacto->apellido2 . '">
                                                <label for="apellido2">Apellido 2</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input name="dni" id="dni" type="text" value="' . $infoContacto->dni . '">
                                                <label for="dni">DNI</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="input-field col s3">
                                                <input name="telef1" id="telef1" type="text" value="' . $infoContacto->telef1 . '">
                                                <label for="telef1">Teléfono</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input name="movil" id="movil" type="text" value="' . $infoContacto->movil . '">
                                                <label for="movil">Móvil</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input name="telef2" id="telef2" type="text" value="' . $infoContacto->telef2 . '">
                                                <label for="telef2">Teléfono 2</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input name="email" id="email" type="text" value="' . $infoContacto->email . '">
                                                <label for="email">Email</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="input-field col s1">' . makeSelect($arrayTipoCalle, 'Tipo calle', 'tipo_calle', $infoContacto->tipo_calle) . '</div>
                                            <div class="input-field col s4">
                                                <input name="calle" id="calle" type="text" value="' . $infoContacto->calle . '">
                                                <label for="calle">Calle</label>
                                            </div>
                                            <div class="input-field col s1">
                                                <input name="numero_calle" id="numero_calle" type="text" value="' . $infoContacto->numero_calle . '">
                                                <label for="numero_calle">Nº</label>
                                            </div>
                                            <div class="input-field col s1">
                                                <input name="piso" id="piso" type="text" value="' . $infoContacto->piso . '">
                                                <label for="piso">Piso</label>
                                            </div>
                                            <div class="input-field col s1">
                                                <input name="cp" id="cp" type="text" value="' . $infoContacto->cp . '">
                                                <label for="cp">CP</label>
                                            </div>
                                            <div class="input-field col s2">
                                                <input name="poblacion" id="poblacion" type="text" value="' . $infoContacto->poblacion . '">
                                                <label for="poblacion">Población</label>
                                            </div>
                                            <div class="input-field col s2">
                                                <input name="provincia" id="provincia" type="text" value="' . $infoContacto->provincia . '">
                                                <label for="provincia">Provincia</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="input-field col s3">' . makeSelect($arrayOrganismoPS, 'Organismo', 'idOrganismo', $infoContacto->idOrganismo) . '</div>
                                            <div class="input-field col s3">' . makeSelect($arrayTipoContactoOrganismo, 'Tipo contacto organismo', 'idTipoContactoOrganismo', $infoContacto->idTipoContactoOrganismo) . '</div>
                                            <div class="input-field col s3">' . makeSelect($arrayLugarPS, 'Lugar', 'idLugar', $infoContacto->idLugar) . '</div>
                                            <div class="input-field col s3">' . makeSelect($arrayTipoContactoLugar, 'Tipo contacto lugar', 'idTipoContactoLugar', $infoContacto->idTipoContactoLugar) . '</div>
                                        </div>

                                        <div class="row">
                                            <div class="input-field col s12">
                                                <input name="observaciones" id="observaciones" type="text" value="' . $infoContacto->observaciones . '">
                                                <label for="observaciones">observaciones</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="input-field col s4">
                                                <input disabled id="fecha" type="text" value="' . $infoContacto->fecha . '">
                                                <label for="fecha">Fecha</label>
                                            </div>
                                            <div class="input-field col s4">
                                                <input disabled id="nombreCreador" type="text" value="' . $infoContacto->nombreCreador . '">
                                                <label for="nombreCreador">Creador</label>
                                            </div>
                                            <div class="input-field col s4">
                                                <input disabled id="from_persona" type="text" value="' . $infoContacto->from_persona . '">
                                                <label for="from_persona">from_persona</label>
                                            </div>
                                        </div>
                                        <a href="#modalGuardar" class="btn waves-effect waves-light modal-trigger"><i class="material-icons left">cloud_upload</i>Actualizar</a>
                                        <a href="#modalCrear" class="btn waves-effect waves-light modal-trigger"><i class="material-icons left">account_box</i>Crear persona</a>
                                        <a href="#modalBorrar" class="waves-effect waves-light btn modal-trigger red darken-1"><i class="material-icons left">delete_forever</i>Eliminar</a>
                                    </form>
                                </div>
                                </div>
                            </div></div>';
                    ?>
                </div>
            </div>
            <br>
            <br>
        </main>
        <!-- Modals -->
        <div id="modalPausa" class="modal">
            <div class="modal-content">
                <h4>Asignar pausa</h4>
                <div class="row">
                    <form action="#" class="col s12" id="pausaForm">
                        <div class="col s3 input-field">
                            <input type="text" class="datepickerPausa" placeholder="Fecha" id="datePickerPausa">
                            <label for="datePickerPausa">Fecha</label>
                        </div>
                        <div class="col s3 input-field">
                            <input type="text" class="timepicker" placeholder="Hora" id="timePickerPausa">
                            <label for="timePickerPausa">Hora</label>
                        </div>
                        <div class="col s3 input-field">
                            <select id="equiposSelect">
                                <option value="" disabled selected>Selecciona un equipo:</option>
                            </select>
                        </div>
                        <div class="" style="margin-top: 0px;">
                            <p>
                                <input name="tipoComida" type="radio" id="bocata" value="bocata" checked />
                                <label for="bocata">Bocata</label>
                            </p>
                            <p>
                                <input name="tipoComida" type="radio" id="comida" value="comida" />
                                <label for="comida">Comida</label>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
                <a href="#!" class="modal-action modal-close waves-effect waves-green btn" id="pausarBtn">Asignar</a>
            </div>
        </div>
        <div id="modalGuardar" class="modal">
            <div class="modal-content">
                <h4>Guardar cambios</h4>
                <p>Al aceptar se guardarán los cambios efectuados en el contacto temporal.</p>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cancelar</a>
                <a href="#!" id="updateContacto" idContacto="<?php echo $idContacto ?>" class="modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
            </div>
        </div>
        <div id="modalCrear" class="modal">
            <div class="modal-content">
                <h4>Crear persona</h4>
                <p>Al aceptar se generará una nueva persona en el sistema con los datos del contacto temporal.</p>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cancelar</a>
                <a href="#!" id="saveContacto" idContacto="<?php echo $idContacto ?>" class="modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
            </div>
        </div>
        <div id="modalBorrar" class="modal">
            <div class="modal-content">
                <h4>Borrar contacto temporal</h4>
                <p>Al aceptar se eliminará por completo el contacto temporal.</p>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cancelar</a>
                <a href="#!" id="deleteContacto" idContacto="<?php echo $idContacto ?>" class="modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
            </div>
        </div>
        <div id="modalCopiarDireccion" class="modal">
            <div class="modal-content">
                <h4>Copiar dirección</h4>
                <p>Al aceptar se sustituirán todos los campos de la dirección por: <?php echo $direccionContactoSistemaString ?></p>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cancelar</a>
                <a href="#!" id="copiarDireccion" class="modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
            </div>
        </div>
    <?php
        include 'scripts.php';
    } ?>
    <script src="js/fn-contactos.js"></script>
</body>

</html>