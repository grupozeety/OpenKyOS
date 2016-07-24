
function changeSrc() {
document.getElementById("myframe").src = "http://54.173.20.195:8080/SpagoBI/servlet/AdapterHTTP?NEW_SESSION=false&user_id=biadmin&PAGE=ExecuteBIObjectPage&TITLE_VISIBLE=FALSE&TOOLBAR_VISIBLE=FALSE&OBJECT_LABEL=status&ROLE=/spagobi/admin&RUN_ANYWAY=TRUE&IGNORE_SUBOBJECTS_VIEWPOINTS_SNAPSHOTS=TRUE&SBI_EXECUTION_ID=80c58405358111e6ac7f9b949d9c3bf4&LIGHT_NAVIGATOR_ID=60a48806-6238-40f1-bd5d-36e25781d3dd&isFromCross=false&SBI_ENVIRONMENT=DOCBROWSER#ost_ticket";
}

$(document).ready(changeSrc);
