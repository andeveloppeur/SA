var divDiag = document.getElementById("chartdiv");

divDiag.addEventListener("click", sijeDoubleClikDev);
divDiag.addEventListener("click", sijeDoubleClikRef);
divDiag.addEventListener("click", sijeDoubleClikData);



var diagramdevPres = "";
var diagramdevAbs = "";

var diagramrefPres = "";
var diagramrefAbs = "";

var diagramdataPres = "";
var diagramdataAbs = "";
var jour = "";
var jourR = "";

function sijeDoubleClikDev() {
    ///////////////////////----dev---///////////////////////////
    diagramdevAbs = document.getElementById("id-455");
    if (diagramdevAbs) { diagramdevAbs.addEventListener("click", devAbsclicker); }
    jour = document.getElementById("jourR");
    jourR = jour.getAttribute("class");

    function devAbsclicker() {
        document.location.href = "presence.php?promo=Dev Web&statut=absents&laDate=" + jourR;

    }

    diagramdevPres = document.getElementById("id-413");
    if (diagramdevPres) { diagramdevPres.addEventListener("click", devPresclicker); }

    function devPresclicker() {
        document.location.href = "presence.php?promo=Dev Web&statut=present&laDate=" + jourR;

    }
    ///////////////////////----Fin dev---///////////////////////////
}

function sijeDoubleClikRef() {
    ///////////////////////----Ref Dig---///////////////////////////
    diagramrefAbs = document.getElementById("id-469");
    if (diagramrefAbs) { diagramrefAbs.addEventListener("click", refAbsclicker); }
    jour = document.getElementById("jourR");
    jourR = jour.getAttribute("class");

    function refAbsclicker() {
        document.location.href = "presence.php?promo=Ref Dig&statut=absents&laDate=" + jourR;

    }

    diagramrefPres = document.getElementById("id-427");
    if (diagramrefPres) { diagramrefPres.addEventListener("click", refPresclicker); }

    function refPresclicker() {
        document.location.href = "presence.php?promo=Ref Dig&statut=present&laDate=" + jourR;

    }
    ///////////////////////----Fin Ref Dig---///////////////////////////
}

function sijeDoubleClikData() {
    ///////////////////////----data art---///////////////////////////
    diagramdataAbs = document.getElementById("id-483");
    if (diagramdataAbs) { diagramdataAbs.addEventListener("click", dataAbsclicker); }
    jour = document.getElementById("jourR");
    jourR = jour.getAttribute("class");

    function dataAbsclicker() {
        document.location.href = "presence.php?promo=Data Art&statut=absents&laDate=" + jourR;

    }

    diagramdataPres = document.querySelector("#id-441");
    diagramdataPres.addEventListener("click", dataPresclicker);

    function dataPresclicker() {
        document.location.href = "presence.php?promo=Data Art&statut=present&laDate=" + jourR;

    }
    ///////////////////////----Fin data art---///////////////////////////
}