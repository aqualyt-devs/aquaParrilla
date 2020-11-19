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
    <?php include 'styles.php';?>
</head>

<body>
<?php 
    include 'urlAPI.php';
    include 'checkToken.php';

    if ((!isset($_COOKIE["AquaToken"])) || (checkToken($urlAPI)->codigo==200)) {
        header("Refresh:0; url=index.php");
    } else {
        $idPS = null;
        $idContacto = null;

        if (isset($_GET['idPS'])) {
            $idPS = $_GET['idPS'];
        }
        if (isset($_GET['idContacto'])) {
            $idContacto = $_GET['idContacto'];
        }
        function getContent($url){
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "token: ".$_COOKIE['AquaToken']
                ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            
            return json_decode($response);
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
                            <i class="material-icons left">account_circle</i>AquaOperario</a>
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
                            <i class="material-icons left">account_circle</i>AquaOperario</a>
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
                        $listContactos = getContent($urlAPI."/equipos/contactos/temporal/get");
                        $arrayContactos = array();
                        if ($listContactos->codigo == 100) {
                            foreach ($listContactos->contenido as $contacto) {
                                if (!isset($arrayContactos[$contacto->ps])) {$arrayContactos[$contacto->ps] = array();}
                                $arrayContactos[$contacto->ps][$contacto->id] = $contacto;
                            }
                        }

                        ksort($arrayContactos);

                        echo '<div class="col s2"><div class="collection z-depth-2 optiscroll columnHeight">';
                        foreach($arrayContactos as $key => $value){
                            echo "<a href='controlContactos.php?idPS=$key' class='collection-item";
                            if ($idPS == $key) {
                                echo ' active';
                            }
                            echo "'>PS $key</a>";
                        }
                        echo '</div></div>';

                        if ($idPS !== null) {
                            echo '<div class="col s10"><div class="collection z-depth-2 optiscroll columnHeight">';
                            if ($idContacto!==null) {
                                echo '<div class="row">
                                <div class="col s12">
                                    <h5>Validación contacto</h5>
                                </div>
                                <div class="section">
                                    <form class="col s12">
                                        <div class="row">
                                            <div class="input-field col s4">
                                                <input id="nombre" type="text" value="'.$arrayContactos[$idPS][$idContacto]->nombre.'">
                                                <label for="nombre">Nombre</label>
                                            </div>
                                            <div class="input-field col s4">
                                                <input id="apellido1" type="text" value="'.$arrayContactos[$idPS][$idContacto]->apellido1.'">
                                                <label for="apellido1">Apellido 1</label>
                                            </div>
                                            <div class="input-field col s4">
                                                <input id="apellido2" type="text" value="'.$arrayContactos[$idPS][$idContacto]->apellido2.'">
                                                <label for="apellido2">Apellido 2</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="input-field col s3">
                                                <input id="telf" type="text" value="'.$arrayContactos[$idPS][$idContacto]->telf.'">
                                                <label for="telf">Teléfono</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input id="movil" type="text" value="'.$arrayContactos[$idPS][$idContacto]->movil.'">
                                                <label for="movil">Móvil</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input id="telef2" type="text" value="'.$arrayContactos[$idPS][$idContacto]->telef2.'">
                                                <label for="telef2">Teléfono 2</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input id="email" type="text" value="'.$arrayContactos[$idPS][$idContacto]->email.'">
                                                <label for="email">Email</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="input-field col s1">
                                                <input id="tipo_calle" type="text" value="'.$arrayContactos[$idPS][$idContacto]->tipo_calle.'">
                                                <label for="tipo_calle">Tipo</label>
                                            </div>
                                            <div class="input-field col s4">
                                                <input id="calle" type="text" value="'.$arrayContactos[$idPS][$idContacto]->calle.'">
                                                <label for="calle">Calle</label>
                                            </div>
                                            <div class="input-field col s1">
                                                <input id="numero_calle" type="text" value="'.$arrayContactos[$idPS][$idContacto]->numero_calle.'">
                                                <label for="numero_calle">Nº</label>
                                            </div>
                                            <div class="input-field col s1">
                                                <input id="piso" type="text" value="'.$arrayContactos[$idPS][$idContacto]->piso.'">
                                                <label for="piso">Piso</label>
                                            </div>
                                            <div class="input-field col s1">
                                                <input id="cp" type="text" value="'.$arrayContactos[$idPS][$idContacto]->cp.'">
                                                <label for="cp">CP</label>
                                            </div>
                                            <div class="input-field col s2">
                                                <input id="poblacion" type="text" value="'.$arrayContactos[$idPS][$idContacto]->poblacion.'">
                                                <label for="poblacion">Población</label>
                                            </div>
                                            <div class="input-field col s2">
                                                <input id="provincia" type="text" value="'.$arrayContactos[$idPS][$idContacto]->provincia.'">
                                                <label for="provincia">Provincia</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="input-field col s3">
                                                <input id="nombreOrganismo" type="text" value="'.$arrayContactos[$idPS][$idContacto]->nombreOrganismo.'">
                                                <label for="nombreOrganismo">Organismo</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input id="idOrganismo" type="text" value="'.$arrayContactos[$idPS][$idContacto]->idOrganismo.'">
                                                <label for="idOrganismo">idOrganismo</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input id="nombreTipoContactoOrganismo" type="text" value="'.$arrayContactos[$idPS][$idContacto]->nombreTipoContactoOrganismo.'">
                                                <label for="nombreTipoContactoOrganismo">nombreTipoContactoOrganismo</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input id="idTipoContactoOrganismo" type="text" value="'.$arrayContactos[$idPS][$idContacto]->idTipoContactoOrganismo.'">
                                                <label for="idTipoContactoOrganismo">idTipoContactoOrganismo</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="input-field col s3">
                                                <input id="idLugar" type="text" value="'.$arrayContactos[$idPS][$idContacto]->idLugar.'">
                                                <label for="idLugar">idLugar</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input id="direccionLugar" type="text" value="'.$arrayContactos[$idPS][$idContacto]->direccionLugar.'">
                                                <label for="direccionLugar">direccionLugar</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input id="nombreTipoContactoLugar" type="text" value="'.$arrayContactos[$idPS][$idContacto]->nombreTipoContactoLugar.'">
                                                <label for="nombreTipoContactoLugar">nombreTipoContactoLugar</label>
                                            </div>
                                            <div class="input-field col s3">
                                                <input id="idTipoContactoLugar" type="text" value="'.$arrayContactos[$idPS][$idContacto]->idTipoContactoLugar.'">
                                                <label for="idTipoContactoLugar">idTipoContactoLugar</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="input-field col s12">
                                                <input id="observaciones" type="text" value="'.$arrayContactos[$idPS][$idContacto]->observaciones.'">
                                                <label for="observaciones">observaciones</label>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="input-field col s4">
                                                <input disabled id="fecha" type="text" value="'.$arrayContactos[$idPS][$idContacto]->fecha.'">
                                                <label for="fecha">Fecha</label>
                                            </div>
                                            <div class="input-field col s4">
                                                <input disabled id="nombreCreador" type="text" value="'.$arrayContactos[$idPS][$idContacto]->nombreCreador.'">
                                                <label for="nombreCreador">Creador</label>
                                            </div>
                                            <div class="input-field col s4">
                                                <input disabled id="from_persona" type="text" value="'.$arrayContactos[$idPS][$idContacto]->from_persona.'">
                                                <label for="from_persona">from_persona</label>
                                            </div>
                                        </div>
                                        <button class="btn waves-effect waves-light" type="submit" name="action">
                                            <i class="material-icons left">save</i>Guardar
                                        </button>
                                        <a class="waves-effect waves-light btn red darken-1"><i class="material-icons left">delete_forever</i>Eliminar</a>
                                    </form>
                                </div>
                                </div>';
                                // echo '<pre>';
                                // print_r($arrayContactos[$idPS][$idContacto]);
                                // echo '</pre>';
                            } else {
                                echo '<table class="striped centered">
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
                                foreach($arrayContactos[$idPS] as $key => $value){
                                    $address = array($value->calle, $value->numero_calle, $value->piso, $value->cp, $value->poblacion, $value->provincia);
                                    echo "<tr class='clickable-row' data-href='url://'>
                                        <td>$value->nombre</td>
                                        <td>$value->apellido1 $value->apellido2</td>
                                        <td>$value->telf</td>
                                        <td>$value->movil</td>
                                        <td>$value->email</td>
                                        <td>".join(', ', array_filter($address))."</td>
                                        <td>$value->observaciones</td>
                                        <td>$value->nombreCreador</td>
                                        <td>$value->fecha</td>
                                        <td><a href='controlContactos.php?idPS=$idPS&idContacto=$value->id' class='modal-trigger btn-flat actionBtn mantenimientoInstalacion'><i class='material-icons'>assignment_turned_in</i></a></td>
                                    </tr>";
                                }
                                echo '</tbody>
                                </table>';
                            }
                            echo '</div></div>';
                        }
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
}?>
</body>

</html>