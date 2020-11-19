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
        }
        if (isset($_GET['idPersona'])) {
            $idPersona = $_GET['idPersona'];
        }
        if (isset($_GET['idCT'])) {
            $idCT = $_GET['idCT'];
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
                                <i class="material-icons left">date_range</i><span id="filtroFecha"></span>
                            </a>
                        </li>
                        <li>
                            <a href="../aquaoperario"><i class="material-icons left">account_circle</i>Operario</a>
                        </li>
                        <li>
                            <a href="controlEquipos.php" class="controlEquiposMenu"><i class="material-icons left">directions_car</i>Control equipos </a>
                        </li>
                        <li>
                            <a href="index.php"><i class="material-icons left">list</i>Control PS</a>
                        </li>
                        <li>
                            <a href="#modalPausa" class="modal-trigger"><i class="material-icons left">pause</i>Asignar pausa</a>
                        </li>
                        <li>
                            <a href="index.php?logout=1"><i class="material-icons left">exit_to_app</i><?php echo $_COOKIE['AquaTokenNOMBRE']; ?></a>
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
                            <a href="../aquaoperario"><i class="material-icons left">account_circle</i>Operario</a>
                        </li>
                        <li>
                            <a href="index.php"><i class="material-icons left">list</i>Control PS</a>
                        </li>
                        <li>
                            <a href="#modalPausa" class="modal-trigger"><i class="material-icons left">pause</i>Asignar pausa</a>
                        </li>
                        <li>
                            <a href="index.php?logout=1"><i class="material-icons left">exit_to_app</i><?php echo $_COOKIE['AquaTokenNOMBRE']; ?></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <main>
            <div class="section">
                <div class="row">
                    <?php
                    if ($idContacto !== 'null') {
                        $listContactosSistema = getContent($urlAPI . "/equipos/get_personas_from_temporal/" . $idContacto);
                        $totalPersonas = ($listContactosSistema->codigo == 200) ? 0 : count((array) $listContactosSistema->contenido);
                    } else {
                        $totalPersonas = 0;
                    }

                    $comparativaContacto = array();
                    if ($idContacto != 'null') {
                        $comparativaContacto[0] = getContent($urlAPI . "/equipos/contactos/temporal/get/id/" . $idContacto)->contenido->{1};
                        $comparativaContacto[1] = getContent($urlAPI . "/equipos/contactos/persona/" . $idPersona)->contenido->{1};
                        $comparativaContacto[0]->cabecera = 'Contacto temporal';
                        $comparativaContacto[1]->cabecera = 'Persona' . iraPersonaAquaplus($idPersona);
                        echo '<input type="hidden" id="relacion" value="1">';
                        echo '<input type="hidden" id="idContacto1" value="' . $idContacto . '">';
                        echo '<input type="hidden" id="idContacto2" value="' . $idPersona . '">';
                    } else {
                        $arrayPersonas = explode(",", $idPersona);
                        sort($arrayPersonas);
                        $comparativaContacto[0] = getContent($urlAPI . "/equipos/contactos/persona/" . $arrayPersonas[0])->contenido->{1};
                        $comparativaContacto[1] = getContent($urlAPI . "/equipos/contactos/persona/" . $arrayPersonas[1])->contenido->{1};
                        $comparativaContacto[0]->cabecera = 'Persona 1' . iraPersonaAquaplus($arrayPersonas[0]);
                        $comparativaContacto[1]->cabecera = 'Persona 2' . iraPersonaAquaplus($arrayPersonas[1]);
                        echo '<input type="hidden" id="relacion" value="2">';
                        echo '<input type="hidden" id="idContacto1" value="' . $arrayPersonas[0] . '">';
                        echo '<input type="hidden" id="idContacto2" value="' . $arrayPersonas[1] . '">';
                    }

                    echo '<input type="hidden" id="idPS" value="' . $idPS . '">';
                    echo '<input type="hidden" id="idContactoTemporal" value="' . $idCT . '">';

                    echo "<div class='col s2'><div class='collection z-depth-2 optiscroll'>
                    <a href='contactoComprobar.php?idPS=$idPS&idContacto=$idCT' class='collection-item active''>Volver PS $idPS</a>
                    </div>
                    </div>
                    <div class='col s10'><div class='collection z-depth-2 optiscroll columnHeight'>";
                    echo "<div class='col s12'><h5><a href='http://app.aqualyt.net/sf/action.php?SOURCE=PS2&amp;ACTION=SHOWINITIAL&amp;LOAD_PS=$idPS' target='_blank'>PS $idPS</a></h5></div>";
                    // codeOut($comparativaContacto);
                    echo '<table class="striped centered">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>' . $comparativaContacto[0]->cabecera . '</th>
                                        <th>' . $comparativaContacto[1]->cabecera . '</th>
                                    </tr>
                                    </thead>
                                    <form id="comparativaContactos">
                                    <tbody>
                                        ' . (($totalPersonas < 2) ? trContactos($comparativaContacto, 'id') : '') . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'nombre') : trContactos($comparativaContacto, 'nombre')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'apellido1') : trContactos($comparativaContacto, 'apellido1')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'apellido2') : trContactos($comparativaContacto, 'apellido2')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'telef1') : trContactos($comparativaContacto, 'telef1')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'telef2') : trContactos($comparativaContacto, 'telef2')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'movil') : trContactos($comparativaContacto, 'movil')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'email') : trContactos($comparativaContacto, 'email')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'tipo_calle') : trContactos($comparativaContacto, 'tipo_calle')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'calle') : trContactos($comparativaContacto, 'calle')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'numero_calle') : trContactos($comparativaContacto, 'numero_calle')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'piso') : trContactos($comparativaContacto, 'piso')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'poblacion') : trContactos($comparativaContacto, 'poblacion')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'provincia') : trContactos($comparativaContacto, 'provincia')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'cp') : trContactos($comparativaContacto, 'cp')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'observaciones') : trContactos($comparativaContacto, 'observaciones')) . '
                                        ' . (($totalPersonas < 2) ? trComparaContactos($comparativaContacto, 'dni') : trContactos($comparativaContacto, 'dni')) . '
                                        ' . trSelectContactosCreador($comparativaContacto) . '
                                        ' . trSelectFechasCreacion($comparativaContacto) . '
                                        ' . trSelectContactosModificador($comparativaContacto) . '
                                        ' . trSelectFechasModificacion($comparativaContacto) . '
                                    </tbody>
                                    </form>
                                </table>
                            <div class="right-align" id="compararContactosBtn">
                                <a href="#modalDesvincular" class="btn modal-trigger">Desvincular contactos</a>
                                ' . (($totalPersonas == 0) ? '<a href="#modalUnir1" class="unir btn modal-trigger"><i class="material-icons right">chevron_left</i>Traspasar a Persona 1</a>' : '') . '
                                ' . (($totalPersonas == 0) ? '<a href="#modalUnir2" class="unir btn modal-trigger"><i class="material-icons left">chevron_right</i>Traspasar a Persona 2</a>' : '') . '
                                ' . (($totalPersonas == 1) ? '<a href="#modalUnir3" class="unir btn modal-trigger"><i class="material-icons left">chevron_right</i>Traspasar a Persona</a>' : '') . '
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
        <div id="modalDesvincular" class="modal">
            <div class="modal-content">
                <h4>Desvincular contactos</h4>
                <p>Al aceptar se desvincularán los contactos y no aparecerán como relacionados.</p>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cancelar</a>
                <a href="#!" id="desvincularContactos" class="modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
            </div>
        </div>
        <div id="modalUnir1" class="modal">
            <div class="modal-content">
                <h4>Actualizar Persona 1</h4>
                <p>Al aceptar se actualizará los datos de la Persona 1 con la información seleccionada, es necesario revisar que se han seleccionado todos los campos ya que de lo contrario la información se eliminará.
                    <br /><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cancelar</a>
                <a href="#!" idPersona1="<?php echo $comparativaContacto[0]->id ?>" idPersona2="<?php echo $comparativaContacto[1]->id ?>" class="unirContactos modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
            </div>
        </div>
        <div id="modalUnir2" class="modal">
            <div class="modal-content">
                <h4>Actualizar Persona 2</h4>
                <p>Al aceptar se actualizará los datos de la Persona 2 con la información seleccionada, es necesario revisar que se han seleccionado todos los campos ya que de lo contrario la información se eliminará.
                    <br /><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cancelar</a>
                <a href="#!" idPersona1="<?php echo $comparativaContacto[1]->id ?>" idPersona2="<?php echo $comparativaContacto[0]->id ?>" class="unirContactos modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
            </div>
        </div>
        <div id="modalUnir3" class="modal">
            <div class="modal-content">
                <h4>Actualizar Persona</h4>
                <p>Al aceptar se actualizará los datos de la Persona con la información seleccionada, es necesario revisar que se han seleccionado todos los campos ya que de lo contrario la información se eliminará.
                    <br /><strong>Esta acción no se puede deshacer.</strong></p>
                <p class="avisoUnchecked">Los siguientes campos del formulario no se han marcada y su información se perderá:</p>
                <ul class="unchecked">
                </ul>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat">Cancelar</a>
                <a href="#!" id="unirPersonaContacto" idPersona="<?php echo $comparativaContacto[1]->id ?>" idContacto="<?php echo $comparativaContacto[0]->id ?>" class="modal-action modal-close waves-effect waves-green btn-flat">Aceptar</a>
            </div>
        </div>
    <?php
        include 'scripts.php';
    } ?>
    <script src="js/fn-contactos.js"></script>
</body>

</html>