<?php header('Content-Type: text/html; charset=UTF-8');?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0" />
    <title>Aqualyt - Parrilla</title>
    <?php include 'styles.php';?>
</head>

<body>
<?php

    include 'urlAPI.php';
    include 'checkToken.php';

    ini_set("allow_url_fopen", 1);

function get_content($URL)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $URL);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

const AQUATOKEN = "AquaToken";
const AQUATOKENNOMBRE = "AquaTokenNOMBRE";
const AQUATOKENID = "AquaTokenID";

$errorAcceso = false;

if (isset($_POST['formSent'])) {
    $formSent = $_POST['formSent'];
} else {
    $formSent = false;
}

if ((isset($_GET['logout'])) && $_GET['logout']) {
    setcookie(AQUATOKEN, "0", time() - 1, '/');
    setcookie(AQUATOKENNOMBRE, "0", time() - 1, '/');
    setcookie(AQUATOKENID, "0", time() - 1, '/');
    header("Refresh:0; url=index.php");
}

if ($formSent) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $url = $urlAPI.'/oficina/auth?acc=' . $usuario . '&pw=' . $password;

    $json = get_content($url);
    $obj = json_decode($json);

    if ($obj->codigo == 100) {
        // la duración de las cookies es de 12 horas (43200 segundos)
        setcookie(AQUATOKEN, $obj->contenido->TOKEN, time() + 43200, '/');
        setcookie(AQUATOKENNOMBRE, $obj->contenido->EMPLEADO_NOMBRE, time() + 43200, '/');
        setcookie(AQUATOKENID, $obj->contenido->EMPLEADO_ID, time() + 43200, '/');
        header("Refresh:0");
    } else {
        $errorAcceso = true;
    }
}

if ((!isset($_COOKIE[AQUATOKEN])) || (checkToken($urlAPI)->codigo==200)) {?>
    <div class="navbar-fixed">
        <nav class="blue darken-3" role="navigation">
            <div class="nav-wrapper">
                <a id="logo-container" href="index.php" class="brand-logo"><img src="imgs/logo_negativo.png"></a>
            </div>
        </nav>
    </div>
    <main>
        <div class="section">
            <div class="row">
                <form action="index.php" class="col offset-s3 s6" method="post">
                    <div class="row center-align">
                        <img src="imgs/logo.jpg" alt="">
                    </div>
                    <?php if ($errorAcceso) {?>
                    <div class="row">
                        <div class="col s12">
                            <div class="card-panel red lighten-4">
                                <span class="red-text darken-4">Error de acceso, comprueba el nombre de usuario y contraseña.</span>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                    <div class="row">
                        <div class="input-field col s12">
                        <input id="usuario" name="usuario" type="text" class="validate">
                        <label for="usuario">Usuario</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                        <input id="password" name="password" type="password" class="validate">
                        <label for="password">Password</label>
                        </div>
                    </div>
                    <div class="row">
                    <input type="hidden" name="formSent" value="true">
                    <button class="btn waves-effect waves-light" type="submit" name="action">Entrar
                        <i class="material-icons right">send</i>
                    </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="js/materialize.min.js"></script>
<?php } else {
    ?>
    <div class="navbar-fixed">
        <nav class="blue darken-3" role="navigation">
            <div class="nav-wrapper">
                <a id="logo-container" href="index.php" class="brand-logo">
                    <img src="imgs/logo_negativo.png">
                </a>
                <a href="#" data-activates="mobileMenu" class="button-collapse"><i class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    <li id="mostrarNota" style="display:none;">
                        <a class="modal-trigger" href="#modal1"><i class="material-icons left">chat</i>Notas</a></li>
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
                        <a href="contactoControl.php">
                            <i class="material-icons left">people</i>Contactos</a>
                    </li>
                    <li>
                        <a href="controlEquipos.php" class="controlEquiposMenu">
                            <i class="material-icons left">directions_car</i>Control equipos </a>
                    </li>
                    <li>
                        <a href="#modalPausa" class="modal-trigger">
                            <i class="material-icons left">pause</i>Asignar pausa</a>
                    </li>
                    <li>
                        <a href="index.php?logout=1">
                            <i class="material-icons left">exit_to_app</i><?php echo $_COOKIE[AQUATOKENNOMBRE]; ?></a>
                    </li>
                </ul>
                <ul class="side-nav" id="mobileMenu">
                    <li id="mostrarNota" style="display:none;">
                        <a class="modal-trigger" href="#modal1"><i class="material-icons left">chat</i>Notas</a></li>
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
                        <a href="contactoControl.php">
                            <i class="material-icons left">people</i>Contactos</a>
                    </li>
                    <li>
                        <a href="controlEquipos.php" class="controlEquiposMenu">
                            <i class="material-icons left">directions_car</i>Control equipos </a>
                    </li>
                    <li>
                        <a href="#modalPausa" class="modal-trigger">
                            <i class="material-icons left">pause</i>Asignar pausa</a>
                    </li>
                    <li>
                        <a href="index.php?logout=1">
                            <i class="material-icons left">exit_to_app</i><?php echo $_COOKIE[AQUATOKENNOMBRE]; ?></a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <main>
        <div class="section">
            <div class="row">
                <div class="col s2">
                    <form id="filtros">
                        <ul class="collapsible" data-collapsible="accordion">
                            <li>
                                <div class="collapsible-header">Ordenación</div>
                                <div class="collapsible-body radioOrdenacion"></div>
                            </li>
                            <li>
                                <div class="collapsible-header">Tipos</div>
                                <div class="collapsible-body radioTipos"></div>
                            </li>
                            <li>
                                <div class="collapsible-header active">Estados</div>
                                <div class="collapsible-body radioEstados"></div>
                            </li>
                            <li>
                                <div class="collapsible-header">Naturaleza</div>
                                <div class="collapsible-body radioNaturaleza"></div>
                            </li>
                            <li>
                                <div class="collapsible-header">Urgencia</div>
                                <div class="collapsible-body radioUrgencia"></div>
                            </li>
                        </ul>
                        <a href="#" class="btn" id="filtrarBtn">Filtrar</a>
                        <a href="#modalLeyenda" class="btn modal-trigger" id="filtrarBtn">?</a>
                    </form>
                </div>

                <div class="col s10">
                    <div class="col s4">
                        <ul class="collection z-depth-2 optiscroll columnHeight itemPSList" id="PSList">
                            <li class="collection-item input-field">
                                <input id="buscador" placeholder="Buscar" type="text">
                            </li>
                            <li class="collection-item itemPSFilter avatar yellow lighten-5" idPS="0">
                                <i class="material-icons circle grey">filter_list</i>
                                <span class="title">Filtrado por:</span>
                                <ul id='listaFiltros'></ul>
                            </li>
                            <li class="collection-item itemPSAll avatar" idPS="0">
                                <i class="circle grey countPS"></i>
                                <span class="title">Ver todas las PS</span>
                                <p></p>
                            </li>
                        </ul>
                    </div>
                    <div class="col s8" id="mapaBig">
                        <div id="map" class="mapa z-depth-2"></div>
                    </div>
                    <div class="col s3" id="equiposList">
                        <div class="progress" id="loadingEquipos">
                            <div class="indeterminate"></div>
                        </div>
                        <ul class="collection z-depth-2 optiscroll columnHeight" id="equiposListCol">

                        </ul>
                    </div>
                    <div class="col s5" id="mapaSmall">
                        <div class="col s12 columnHeight optiscroll">
                            <div id="map2" class="mapa z-depth-2"></div>
                            <div class="card z-depth-2" id="infoPSEquipo">
                                <div class="card-content">
                                    <span class="card-title">PS
                                        <span id="PSID"></span>
                                    </span>
                                    <p>
                                        <span id="direccion"></span>
                                        <span id="avisoPS"></span>
                                        <span id="llegadaPS"></span>
                                        <span id="tiempoTeorico"></span>
                                        <span id="finalizacionPS"></span>
                                        <br /> Distancia:
                                        <span id="distancia"></span>
                                    </p>
                                </div>
                                <div class="card-action">
                                    <div class="row">
                                        <a class="col s6 modal-trigger asignaPS" href="#modalAsignar">Asignar PS</a>
                                        <a class="col s6 modal-trigger cancelarPS red-text text-darken-2" href="#modalCancelar">Cancelar asignación</a>
                                        <a class="col s6 modal-trigger cambioEquipoPS blue-text text-darken-2" href="#modalCambioEquipo">Cambio equipo asignado</a>
                                        <a class="col s6 modal-trigger cancelarPSCola purple-text text-darken-2" href="#modalCancelar">Cancelar cola</a>
                                        <a class="col s6 blue-grey-text text-darken-2 bloqueoText" href="#">Equipo bloqueado</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>
    </main>
    <!-- Modals -->
    <div id="modalPS" class="modal modal-fixed-footer">
        <div class="modal-content">
            <h4>
                <span id="ModalPSTitle"></span>
                <span class="equipoPre"></span>
            </h4>
            <div class="row">
                <div class="col s8">
                    <h5>Información de PS</h5>
                    <div class="row">
                        <div class="col s3">
                            <strong>Estado:</strong>
                        </div>
                        <div class="col s3">
                            <span id="ModalEstado"></span>
                        </div>
                        <div class="col s3">
                            <strong>Naturaleza:</strong>
                        </div>
                        <div class="col s3">
                            <span id="ModalNaturaleza"></span>
                        </div>
                        <div class="col s3">
                            <strong>Tipo:</strong>
                        </div>
                        <div class="col s3">
                            <span id="ModalTipo"></span>
                        </div>
                        <div class="col s3">
                            <strong>Urgencia:</strong>
                        </div>
                        <div class="col s3">
                            <span id="ModalUrgencia"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s3">
                            <strong>Dirección:</strong>
                        </div>
                        <div class="col s9">
                            <span id="ModalDireccion"></span>
                        </div>
                        <div class="col s3">
                            <strong>Dirección GPS:</strong>
                        </div>
                        <div class="col s9">
                            <span id="ModalDireccionGPS"></span>
                        </div>
                        <div class="col s3">
                            <strong>Solicitante:</strong>
                        </div>
                        <div class="col s9">
                            <span id="ModalSolicitante"></span>
                        </div>
                        <div class="col s3">
                            <strong>Agente:</strong>
                        </div>
                        <div class="col s9">
                            <span id="ModalAgente"></span>
                        </div>
                    </div>
                    <ul class="collapsible collapsibleModal" data-collapsible="accordion">
                        <li>
                            <div class="collapsible-header">Problema</div>
                            <div class="collapsible-body">
                                <span id="ModalProblema"></span>
                            </div>
                        </li>
                        <li id="collapsibleModalNotas">
                            <div class="collapsible-header">Notas</div>
                            <div class="collapsible-body">
                                <span id="ModalNotas"></span>
                            </div>
                        </li>
                        <li id="collapsibleModalNotaVital">
                            <div class="collapsible-header">Nota vital</div>
                            <div class="collapsible-body">
                                <span id="ModalNotaVital"></span>
                            </div>
                        </li>
                    </ul>
                    <div class="row">
                        <div class="switch" id="switchNota">
                            <label>
                                Enviar nota
                                <input checked type="checkbox">
                                <span class="lever"></span>
                            </label>
                        </div>
                        <div class="switch" id="switchNotaVital">
                            <label>
                                Enviar nota vital
                                <input checked type="checkbox">
                                <span class="lever"></span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s4">
                            <a href="#modalAnular" class="modal-trigger col s12 waves-effect waves-light red btn" id="anularPS">Anular PS</a>
                        </div>
                        <div class="col s4">
                            <a href="#modalNoRealizada" class="modal-trigger col s12 waves-effect waves-light orange btn" id="noRealizadaPS">PS No realizada</a>
                        </div>
                        <div class="col s4">
                            <a href="#modalCambioFecha" class="modal-trigger col s12 waves-effect waves-light blue btn" id="cambioFechaPS">Cambiar fecha PS</a>
                        </div>
                    </div>
                    <div class="row" id="actionPack">
                        <div class="col s4">
                            <a href="#modalAnularPack" class="modal-trigger col s12 waves-effect waves-light red btn" id="anularPSPack">Anular Pack PS</a>
                        </div>
                        <div class="col s4">
                            <a href="#modalNoRealizadaPack" class="modal-trigger col s12 waves-effect waves-light orange btn" id="noRealizadaPSPack">Pack PS No realizada</a>
                        </div>
                        <div class="col s4">
                            <a href="#modalCambioFechaPack" class="modal-trigger col s12 waves-effect waves-light blue btn" id="cambioFechaPSPack">Cambiar fecha Pack PS</a>
                        </div>
                    </div>
                </div>
                <div class="col s4">
                    <h5>Mapa</h5>
                    <div id="PSMapLocation"></div>
                    <div id="locationField">
                        <input id="autocomplete" placeholder="Enter your address" type="text"></input>
                    </div>
                    <input type="hidden" name="idLugar" id="idLugar">
                    <input type="hidden" name="lugarLat" id="lugarLat">
                    <input type="hidden" name="lugarLong" id="lugarLong">
                    <a href="#" class="waves-effect waves-light btn" id="actualizarDireccion">Actualizar dirección</a>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat ">Cerrar</a>
        </div>
    </div>
    <div id="modalAsignar" class="modal">
        <div class="modal-content">
            <h4>Asignar PS
                <span class="idPSAsignar"></span> al 
                <span class="idEquipoAsignar"></span>
            </h4>
            <p>¿Quieres asignar la PS
                <span class="idPSAsignar"></span> al 
                <span class="idEquipoAsignar"></span>?</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn" id="asignarPSaEquipo">Asignar</a>
        </div>
    </div>
    <div id="modalCambioFecha" class="modal">
        <div class="modal-content">
            <h4>Cambiar la fecha de la PS
                <span class="idPSAsignar"></span>
            </h4>
            <p>Selecciona la nueva fecha para la PS:</p>
            <div class="row">
                <div class="input-field col s6">
                    <label for="nuevaFechaPS">Nueva fecha</label>
                    <input type="text" class="datepickerNuevaFecha" id="nuevaFechaPS">
                </div>
                <div class="input-field col s6">
                    <label for="nuevaHoraPS">Nueva hora</label>
                    <input type="text" class="timepickerNuevaHora" id="nuevaHoraPS">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn" id="modificarFecha">Modificar</a>
        </div>
    </div>
    <div id="modalCambioFechaPack" class="modal">
        <div class="modal-content">
            <h4>Cambiar la fecha del Pack PS
                <span class="idPSAsignar"></span>
            </h4>
            <p>Selecciona la nueva fecha para la PS:</p>
            <div class="row">
                <div class="input-field col s6">
                    <label for="nuevaFechaPSPack">Nueva fecha</label>
                    <input type="text" class="datepickerNuevaFecha" id="nuevaFechaPSPack">
                </div>
                <div class="input-field col s6">
                    <label for="nuevaHoraPSPack">Nueva hora</label>
                    <input type="text" class="timepickerNuevaHora" id="nuevaHoraPSPack">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn" id="modificarFechaPack">Modificar</a>
        </div>
    </div>
    <div id="modalCancelar" class="modal">
        <div class="modal-content">
            <h4>Cancelar PS
                <span class="idPSCancelar"></span> asignada al 
                <span class="idEquipoCancelar"></span>
            </h4>
            <p>¿Quieres cancelar la PS
                <span class="idPSCancelar"></span> asignada al 
                <span class="idEquipoCancelar"></span>?</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn red" id="desasignarPSaEquipo">Cancelar</a>
        </div>
    </div>
    <div id="modalCambioEquipo" class="modal">
        <div class="modal-content">
            <h4>Cambiar Equipo asignado a la PS
                <span class="idPSCambio"></span> asignada al 
                <span class="idEquipoCambio"></span>
            </h4>
            <div class="row">
                <form action="#" class="col s12" id="cambioForm">
                    <div class="col s4 input-field">
                        <select id="equiposSelectCambio">
                            <option value="" disabled selected>Selecciona un equipo:</option>
                        </select>
                        <input type="hidden" id="idPSaCambiar" value="">
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn" id="cambiarEquipoPS">Cambiar</a>
        </div>
    </div>
    <div id="modalAnular" class="modal">
        <div class="modal-content">
            <h4>Anular PS
                <span class="idPSAnular"></span>
            </h4>
            <p>¿Quieres anular la PS
                <span class="idPSAnular"></span>?</p>
            <div class="row">
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s8">
                            <textarea id="anularPStext" class="materialize-textarea"></textarea>
                            <label for="anularPStext">Motivo de anulación</label>
                        </div>
                        <div class="col s4">
                            <input type="checkbox" name="siguientePS" value="siguiente" id="siguientePS" />
                            <label for="siguientePS">¿Crear la siguiente PS?</label>
                            <div class="input-field col s12">
                                <select id="SelectFiguraRechazado">
                                  <option value="" disabled selected>Selecciona la figura</option>
                                  <option value="1">Cliente</option>
                                  <option value="2">Despacho</option>
                                  <option value="3">Técnico</option>
                                </select>
                             </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn red" id="anularPSConfirmar">Confirmar Anulación</a>
        </div>
    </div>
    <div id="modalAnularPack" class="modal">
        <div class="modal-content">
            <h4>Anular Pack PS
                <span class="idPSAnular"></span>
            </h4>
            <p>¿Quieres anular la PS
                <span class="idPSAnular"></span>?</p>
            <div class="row">
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s8">
                            <textarea id="anularPSPacktext" class="materialize-textarea"></textarea>
                            <label for="anularPSPacktext">Motivo de anulación</label>
                        </div>
                        <div class="col s4">
                            <input type="checkbox" name="siguientePSPack" value="siguiente" id="siguientePSPack" />
                            <label for="siguientePSPack">¿Crear la siguiente PS?</label>
                            <div class="input-field col s12">
                                <select id="SelectFiguraRechazado">
                                  <option value="" disabled selected>Selecciona la figura</option>
                                  <option value="1">Cliente</option>
                                  <option value="2">Despacho</option>
                                  <option value="3">Técnico</option>
                                </select>
                             </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn red" id="anularPSPackConfirmar">Confirmar Anulación</a>
        </div>
    </div>
    <div id="modalNoRealizada" class="modal">
        <div class="modal-content">
            <h4>PS no realizada
                <span class="idPSNoRealizada"></span>
            </h4>
            <p>¿Quieres marcar la PS
                <span class="idPSNoRealizada"></span> como no realizada?</p>
            <div class="row">
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s8">
                            <textarea id="noRealizadaPStext" class="materialize-textarea"></textarea>
                            <label for="noRealizadaPStext">Motivo de no realización</label>
                        </div>
                        <div class="col s4">
                            <input type="checkbox" name="siguientePSNoRealizada" value="siguiente" id="siguientePSNoRealizada" />
                            <label for="siguientePSNoRealizada">¿Crear la siguiente PS?</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn red" id="noRealizadaPSConfirmar">Confirmar No Realizada</a>
        </div>
    </div>
    <div id="dialog-message" title="PS creada">
        <p>
          <label id="MessageConfirmation" class="dialogo"></label>
        </p>
    </div>
    <div id="modalNoRealizadaPack" class="modal">
        <div class="modal-content">
            <h4>Pack PS no realizada
                <span class="idPSNoRealizada"></span>
            </h4>
            <p>¿Quieres marcar la PS
                <span class="idPSNoRealizada"></span> como no realizada?</p>
            <div class="row">
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s8">
                            <textarea id="noRealizadaPSPacktext" class="materialize-textarea"></textarea>
                            <label for="noRealizadaPSPacktext">Motivo de no realización</label>
                        </div>
                        <div class="col s4">
                            <input type="checkbox" name="siguientePSPackNoRealizada" value="siguiente" id="siguientePSPackNoRealizada" />
                            <label for="siguientePSPackNoRealizada">¿Crear la siguiente PS?</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn red" id="noRealizadaPSPackConfirmar">Confirmar No Realizada</a>
        </div>
    </div>
    <div id="dialog-message" title="PS creada">
        <p>
          <label id="MessageConfirmation" class="dialogo"></label>
        </p>
    </div>
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
    <div id="modalLeyenda" class="modal">
        <div class="modal-content">
            <h4>Colores de las PS</h4>
            <table class="centered">
                <thead>
                    <tr>
                        <th>Color izquierda</th>
                        <th>Color icono</th>
                        <th>Color fondo</th>
                        <th>Color derecha</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="grey white-text">Anulado</td>
                        <td class="grey white-text">Repetición</td>
                        <td class="red white-text">Tiempo límite
                            < 15 minutos</td>
                                <td class="green darken-3 white-text">Servicio</td>
                    </tr>
                    <tr>
                        <td class="pink lighten-4 white-text">En servicio</td>
                        <td class="red white-text">Repetición Instalación</td>
                        <td class="deep-orange lighten-1 white-text">Tiempo límite
                            < 30 minutos</td>
                                <td class="blue white-text">Consulta</td>
                    </tr>
                    <tr>
                        <td class="orange white-text">No realizado</td>
                        <td class="blue white-text">Puntual</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="green darken-3 white-text">Realizado</td>
                        <td><i class="small material-icons">place</i> Dirección introducida</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="blue darken-4 white-text">Pendiente</td>
                        <td><i class="small material-icons">gps_off</i> Dirección no introducida</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="yellow">Trámite</td>
                        <td><i class="material-icons">priority_high</i> Mantenimiento</td>
                        <td><i class="material-icons">whatshot</i> Urgente</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cerrar</a>
        </div>
    </div>
    <div id="modal1" class="modal bottom-sheet">
        <div class="modal-content">
            <h4 id="notaParrilla"></h4>
            <div class="row">
                <form class="col s12">
                <div class="row">
                    <div class="input-field col s12">
                    <textarea id="textoNotas" class="materialize-textarea"></textarea>
                    <label for="textoNotas">Texto notas</label>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="waves-effect waves-green btn-flat" id="enviarNotas">Enviar</a>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat red-text">Cerrar</a>
        </div>
    </div>
    <?php
include 'scripts.php';
}
?>
</body>

</html>