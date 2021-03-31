console.log('plugin main.js loaded ;)')

function toggle_by_class_name(cls) {
    if (cls) {
        jQuery('tbody tr').show().filter(':not(.'+cls+')').hide()
    }
}

function selectForfaitTimeCheck(value)
{
    console.log(value);
    if(value){
        var optionValueTime = document.getElementById("forfait"+value);
        console.log(optionValueTime.dataset.time)

        var inputTime = document.getElementById("taskTimeInput")
        var labelTime = document.getElementById("taskTimeLabel")
        var submitTask = document.getElementById("addTaskSubmit")
        inputTime.setAttribute("max", optionValueTime.dataset.time)
        labelTime.textContent += " (Max "+optionValueTime.dataset.time+")"

        if (optionValueTime.dataset.time === '00:00') {
            submitTask.style.pointerEvents = 'none'
            submitTask.setAttribute("value", "IMPOSSIBLE")
        }
    }
}

function toggleLists(id) {
    var forfaitButton = document.getElementsByClassName('forfait-button')
    var taskList = document.getElementsByClassName('main-tasks-container')

    for (var i = 0; i < taskList.length; i++) {
        for (var i2 = 0; i2 < forfaitButton.length; i++) {
            if (forfaitButton[i].classList.contains(id)) {
                forfaitButton[i].classList.toggle('activeButton')
            }
            if (taskList[i].classList.contains(id)) {
                taskList[i].classList.toggle('displayBlock')
            }
        }
    }
}

function displayOverviewTableRows() {
    jQuery(".overview-forfaits-btn .forfait-custom-btn").click(function(){
        jQuery('.activeButton').not(this).removeClass('activeButton');
        jQuery(this).toggleClass('activeButton');

        let elementID = jQuery(this).attr("id")
        jQuery('tbody tr').show().filter(':not(.'+elementID+')').hide()
        jQuery('.selected-forfait-datas').show().filter(':not(.'+elementID+')').hide()
    })
}

function closeFormAlert() {
    var forfaitAlertBloc = document.getElementById('forfaitAlertBloc')
    var closeButton = document.getElementById('forfaitAlertClose')

    if(forfaitAlertBloc) {
        closeButton.addEventListener('click', function() {
            forfaitAlertBloc.style.display = 'none'
        })
    }
}

jQuery(document).ready( function () {
    closeFormAlert()
    displayOverviewTableRows()
})