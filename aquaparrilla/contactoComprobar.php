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
                    <input type="hidden" name="idPS" id="idPS" value="<?php echo $idPS; ?>">
                    <input type="hidden" name="idContactoTemporal" id="idContactoTemporal" value="<?php echo $idContacto; ?>">
                    <?php
                    $listContactosSistema = getContent($urlAPI . "/equipos/get_personas_from_temporal/" . $idContacto);
                    $contactoTemporal = current((array) getContent($urlAPI . "/equipos/contactos/temporal/get/id/" . $idContacto)->contenido);
                    $totalPersonas = ($listContactosSistema->codigo == 200) ? 0 : count((array) $listContactosSistema->contenido);

                    $address = array($contactoTemporal->calle, $contactoTemporal->numero_calle, $contactoTemporal->piso, $contactoTemporal->cp, $contactoTemporal->poblacion, $contactoTemporal->provincia);

                    echo "<div class='col s2'><div class='collection z-depth-2 optiscroll columnHeight'>
                            <a href='contactoControl.php?idPS=$idPS' class='collection-item active''>PS $idPS</a>
                        </div></div>";
                    echo '<div class="col s10"><div class="collection z-depth-2 optiscroll columnHeight">';
                    echo "<div class='col s12'><h5><a href='http://app.aqualyt.net/sf/action.php?SOURCE=PS2&amp;ACTION=SHOWINITIAL&amp;LOAD_PS=$idPS' target='_blank'>PS $idPS</a></h5></div>";
                    echo '<div class="col s8"><h4>Contacto nuevo</h4></div><div class="col s4 right-align"><a href="#" class="btn" id="compararContactos">Comparar contactos</a></div>';
                    echo "<table class='striped centered'>
                                <thead>
                                  <tr>
                                      <th></th>
                                      <th>Nombre</th>
                                      <th>Apellidos</th>
                                      <th>Teléfono</th>
                                      <th>Móvil</th>
                                      <th>Email</th>
                                      <th>Dirección</th>
                                      <th>Observaciones</th>
                                      <th>Creado por</th>
                                      <th>fecha</th>
                                      <th></th>
                                  </tr>
                                </thead>
                        
                                <tbody>
                                <tr class='clickable-row'>
                                    <td><a href='editarContacto.php?idContacto=$contactoTemporal->id&idPS=$idPS' class='btn-flat editContacto'><i class='material-icons'>edit</i></a></td>
                                    <td>$contactoTemporal->nombre</td>
                                    <td>$contactoTemporal->apellido1 $contactoTemporal->apellido2</td>
                                    <td>$contactoTemporal->telef1</td>
                                    <td>$contactoTemporal->movil</td>
                                    <td>$contactoTemporal->email</td>
                                    <td>" . join(', ', array_filter($address)) . "</td>
                                    <td>$contactoTemporal->observaciones</td>
                                    <td>$contactoTemporal->nombreCreador</td>
                                    <td>$contactoTemporal->fecha</td>
                                    <td>
                                        <a href='#' class='btn-flat' idContacto='$contactoTemporal->id' id='selectContactoTemporal'><i class='material-icons'>check_box_outline_blank</i></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>";
                    if ($listContactosSistema->codigo != 200) {
                        echo '<div class="col s12"><h4>Personas en sistema</h4></div>
                                <table class="striped centered">
                                    <thead>
                                      <tr>
                                          <th>Nombre</th>
                                          <th>Apellidos</th>
                                          <th>Teléfono</th>
                                          <th>Móvil</th>
                                          <th>Email</th>
                                          <th>Dirección</th>
                                          <th>Observaciones</th>
                                          <th>Creado por</th>
                                          <th>fecha</th>
                                          <th></th>
                                      </tr>
                                    </thead>
                                    <tbody>';
                        foreach ($listContactosSistema->contenido as $key => $value) {
                            $address = array($value->calle, $value->numero_calle, $value->piso, $value->cp, $value->poblacion, $value->provincia);
				$irAPersonaAquaplus = iraPersonaAquaplus($value->id);
                            echo "<tr class='clickable-row'>
                                <td>$value->nombre <br/> $irAPersonaAquaplus</td>
                                <td>$value->apellido1 $value->apellido2</td>
                                <td>$value->telef1</td>
                                <td>$value->movil</td>
                                <td>$value->email</td>
                                <td>" . join(', ', array_filter($address)) . "</td>
                                <td>$value->observaciones</td>
                                <td>$value->nombreCreador</td>
                                <td>$value->fecha_creacion</td>
                                <td>
                                    <a href='#' class='btn-flat selectContacto' idContacto='$value->id'><i class='material-icons'>check_box_outline_blank</i></a>
                                </td>
                            </tr>";
                        }
                        echo '</tbody>
                                    </table>';
                    }
                    echo '</div></div>';
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
    <?php
        include 'scripts.php';
    } ?>
    <script src="js/fn-contactos.js"></script>
</body>

</html>