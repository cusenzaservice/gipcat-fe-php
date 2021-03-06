var version_ann;
var version_pcm;
var version;

document.getElementById("calendar_date").addEventListener('input', (event) => {
    if(document.getElementById("calendar_date").value == new Date().toISOString().substring(0, 10)){
        // remove query string
        location.href = "./";
    }else{
        location.href = "./?date=" + document.getElementById("calendar_date").value;
    }
});

var doNotCallModal = document.getElementById('doNotCallModal');
if (doNotCallModal != null) {
    doNotCallModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var idInstallation = button.getAttribute('data-bs-dncIid');
        var modalContent = document.getElementById('dnc.title');
        modalContent.textContent = idInstallation;
        $.ajax({
            type: "GET",
            url: './installations/ajax_get.php',
            data: { "idInstallation": idInstallation },
            success: function (dataget) {
                version = dataget['version'];
            },
            error: function (data) {
                toastr.error(data.responseText);
            }
        });
    });
}

function editInstallationDNC_AJAX(idInstallation){
    $.ajax({
        type: "POST",
        url: './installations/ajax_edit.php',
        data: {
            "idInstallation": idInstallation,
            "version": version,
            "toCall": 0
        },
        success: function (data) {
            successReload();
        },
        error: function (data) {
            toastr.error(data.responseText);
        }
    });
    version = "";
}

var annotationsModal = document.getElementById('annotationsModal');
if (annotationsModal != null) {
    annotationsModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var idIntervention = button.getAttribute('data-bs-amIid');
        $.ajax({
            type: "GET",
            url: './lib/int/ajax_get.php',
            data: { "idIntervention": idIntervention },
            success: function (dataget) {
                version_ann = dataget['version'];
                document.getElementById('am.idIntervention').value = dataget['idIntervention'];
                document.getElementById('am.associatedCallNote').value = dataget['associatedCallNote'];
            },
            error: function (data) {
                toastr.error(data.responseText);
            }
        });
    });
}

function editInterventionCN_AJAX(){
    $.ajax({
        type: "POST",
        url: './lib/int/ajax_edit.php',
        data: {
            "idIntervention": document.getElementById('am.idIntervention').value,
            "version": version_ann,
            "associatedCallNote": document.getElementById('am.associatedCallNote').value
        },
        success: function (data) {
            successReload();
        },
        error: function (data) {
            toastr.error(data.responseText);
        }
    });
    version_ann = "";
}

var postponeCallModal = document.getElementById('postponeCallModal');
if (postponeCallModal != null) {
    postponeCallModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var idIntervention = button.getAttribute('data-bs-pcmIid');
        $.ajax({
            type: "GET",
            url: './lib/int/ajax_get.php',
            data: { "idIntervention": idIntervention },
            success: function (dataget) {
                version_pcm = dataget['version'];
                document.getElementById('pcm.idIntervention').value = dataget['idIntervention'];
                document.getElementById('pcm.associatedCallPosticipationDate').value = dataget['associatedCallPosticipationDate'];
            },
            error: function (data) {
                toastr.error(data.responseText);
            }
        });
    });
}

function editInterventionPCM_AJAX(){
    if(document.getElementById('pcm.associatedCallPosticipationDate').value == ''){
        toastr.error("Compila il campo");
    }
    else{
        $.ajax({
            type: "POST",
            url: './lib/int/ajax_edit.php',
            data: {
                "idIntervention": document.getElementById('pcm.idIntervention').value,
                "version": version_pcm,
                "associatedCallPosticipationDate": document.getElementById('pcm.associatedCallPosticipationDate').value
            },
            success: function (data) {
                successReload();
            },
            error: function (data) {
                toastr.error(data.responseText);
            }
        });
        version_pcm = "";
    }
}

function caricaChiamateAJAX(){
    document.getElementById("callScrollListContainer").innerHTML =
    `<br><br><br>
    <center>
    <h5>Caricamento chiamate<br>in corso...</h5>
    <br>
    <div class="spinner-border text-dark" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    </center>`;
    // caricamento chiamate AJAX
    $.ajax({
        type: "GET",
        url: './lib/ajax_htmlchiamate.php',
        success: function (dataget) {
            // caricamento
            document.getElementById("callScrollListContainer").innerHTML = dataget;
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            feather.replace({ 'aria-hidden': 'true' })
        },
        error: function (data) {
            toastr.error(data.responseText);
        }
    });
}