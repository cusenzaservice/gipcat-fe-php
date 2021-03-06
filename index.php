<?php
/// PAGE INFO ///
$pageid = 0;
$friendlyname = "Home";
$level = 0;
$jsdeps = array('bootstrap-bundle', 'feathericons', 'jquery', 'toastr');
/// PAGE INFO ///

require_once './init.php';
require_once './lib/pagetools.php';

openPage($pageid, $friendlyname, $level);
printInterventionsModals();
printInterventionsNBModals();
?>

<?php //if($_SESSION["permissionType"] < 3){ ?>
<!-- DO NOT CALL ANYMORE MODAL -->
<div class="modal fade" id="doNotCallModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Non chiamare più</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Sei sicuro di voler disabilitare le chiamate per l'installazione n&ordm; <strong id="dnc.title"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <span data-feather="x-octagon"></span>
                    Annulla
                </button>
                <button type="button" class="btn btn-danger" onclick="editInstallationDNC_AJAX(document.getElementById('dnc.title').textContent)">
                    <span data-feather="slash"></span>
                    Disabilita
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ANNOTATIONS MODAL -->
<div class="modal fade" id="annotationsModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Annotazioni per la chiamata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <input id="am.idIntervention" type="hidden" value="">
                    <div class="mb-2 mt-2">
                        <label for="am.associatedCallNote" class="form-label visually-hidden">Annotazioni</label>
                        <textarea class="form-control" id="am.associatedCallNote" rows="5"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <span data-feather="x-octagon"></span>
                    Annulla
                </button>
                <button type="button" class="btn btn-success" onclick="editInterventionCN_AJAX()">
                    <span data-feather="save"></span>
                    Salva 
                </button>
            </div>
        </div>
    </div>
</div>

<!-- POSTPONE CALL MODAL -->
<div class="modal fade" id="postponeCallModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Posticipazione chiamata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <input id="pcm.idIntervention" type="hidden" value="">
                    <div class="mb-2">
                        <label for="pcm.associatedCallPosticipationDate" class="form-label">Posticipa a:</label>
                        <input type="date" class="form-control" id="pcm.associatedCallPosticipationDate">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <span data-feather="x-octagon"></span>
                    Annulla
                </button>
                <button type="button" class="btn btn-success" onclick="editInterventionPCM_AJAX()">
                    <span data-feather="save"></span>
                    Salva 
                </button>
            </div>
        </div>
    </div>
</div>
<?php //} ?>

<table class="table table-bordered mt-4">
    <thead>
        <tr>
            <?php if($_SESSION["permissionType"] > 2){ ?>
                <th class="col col-md-6">
                    <h5 class="h5t">Chiamate in sospeso</h5>
                </th>
            <?php } ?>
            <th class="col col-md-6">
                <?php if($_SESSION["permissionType"] > 2){ ?>
                    <div class="row">
                        <div class="col col-md-8">
                            <h5 class="h5t">Calendario del giorno</h5>
                        </div>
                        <div class="col col-md-1">
                            <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#createInterventionNBModal">
                                <span data-feather="plus"></span>
                            </button>
                        </div>
                        <div class="col col-md-3">
                            <input type="date" class="form-control" id="calendar_date" 
                                <?php if (isset($_GET["date"]) && !empty($_GET["date"])) {
                                    echo 'value="' . htmlspecialchars($_GET["date"], ENT_QUOTES) . '"';
                                } else {
                                    echo 'value="' . date("Y-m-d") . '"';
                                } ?> 
                            aria-label="Ricerca">
                        </div>
                    </div>
                <?php } else { ?>
                    <input type="date" class="form-control" id="calendar_date" 
                        <?php if (isset($_GET["date"]) && !empty($_GET["date"])) {
                            echo 'value="' . htmlspecialchars($_GET["date"], ENT_QUOTES) . '"';
                        } else {
                            echo 'value="' . date("Y-m-d") . '"';
                        } ?> 
                    aria-label="Ricerca">
                <?php } ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php if($_SESSION["permissionType"] > 2){ ?>
                <td id="callScrollListContainer">
                    <br><br><br>
                    <center>
                        <button type="button" onclick="caricaChiamateAJAX()" class="btn btn-outline-dark">
                            <span data-feather="download-cloud"></span>
                            Carica Chiamate
                        </button>
                    </center>
                </td>
            <?php } ?>
            <td>
                <div class="scrollable-list">
                    <div class="callListSep">
                        <hr>
                            <center>interventi con anagrafica</center>
                        <hr>
                    </div>
                    <?php 
                    if (isset($_GET["date"]) && !empty($_GET["date"])){
                        $date = $con->real_escape_string($_GET["date"]);
                    }else{
                        $date = date("Y-m-d");
                    }
                    $intq = 
                    "SELECT
                        interventions.idIntervention,
                        interventions.idInstallation,
                        installations.idCustomer,
                        interventions.interventionType,
                        DATE_FORMAT(interventions.interventionDate,'%H:%i') interventionTimeStart,
                        DATE_FORMAT(
                            interventions.interventionDate + INTERVAL interventions.interventionDuration MINUTE,
                            '%H:%i'
                        ) interventionTimeEnd,
                        interventions.interventionState,
                        installations.installationAddress,
                        installations.installationCity,
                        installations.heaterBrand,
                        installations.heater,
                        installations.installationType,
                        customers.businessName,
                        users.userName,
                        users.legalName,
                        users.legalSurname,
                        users.color
                    FROM
                        interventions
                    INNER JOIN installations ON(
                            interventions.idInstallation = installations.idInstallation
                        )
                    INNER JOIN customers ON(
                            installations.idCustomer = customers.idCustomer
                        )
                    LEFT JOIN users ON(
                            interventions.assignedTo = users.userName
                        )
                    WHERE
                        interventions.interventionDate LIKE '%$date%'
                    ORDER BY 
                        interventions.interventionDate ASC;
                    ";
                    $interventionsToday = $con->query($intq);
                    if($con->affected_rows < 1) {
                        ?><br>
                        <center><h5>Nessun intervento<br>per la giornata selezionata</h5></center>
                        <br> <?php
                    } else {
                        while ($intervention = $interventionsToday->fetch_assoc()) {
                            printInterventionsCard($intervention);
                        }
                    }
                    ?>

                    <div class="callListSep">
                        <hr>
                            <center>interventi generici</center>
                        <hr>
                    </div>

                    <?php 
                    if (isset($_GET["date"]) && !empty($_GET["date"])){
                        $date = $con->real_escape_string($_GET["date"]);
                    }else{
                        $date = date("Y-m-d");
                    }
                    $intqNB = 
                    "SELECT
                        interventions_nb.idIntervention,
                        DATE_FORMAT(interventions_nb.interventionDate,'%H:%i') interventionTimeStart,
                        DATE_FORMAT(
                            interventions_nb.interventionDate + INTERVAL interventions_nb.interventionDuration MINUTE,
                            '%H:%i'
                        ) interventionTimeEnd,
                        interventions_nb.interventionState,
                        interventions_nb.footNote,
                        users.userName,
                        users.legalName,
                        users.legalSurname,
                        users.color
                    FROM
                        interventions_nb
                    LEFT JOIN users ON(
                            interventions_nb.assignedTo = users.userName
                        )
                    WHERE
                        interventions_nb.interventionDate LIKE '%$date%'
                    ORDER BY 
                        interventions_nb.interventionDate ASC;
                    ";
                    $interventionsNBToday = $con->query($intqNB);
                    if($con->affected_rows < 1) {
                        ?><br>
                        <center><h5>Nessun intervento generico<br>per la giornata selezionata</h5></center>
                        <br> <?php
                    } else {
                        while ($interventionNB = $interventionsNBToday->fetch_assoc()) {
                            printInterventionsNBCard($interventionNB);
                        }
                    }
                    ?>

                </div>
            </td>
        </tr>
    </tbody>
</table>

<?php
closePage($level, $jsdeps, "home.js");
