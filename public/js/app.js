// Reports
const select = document.getElementById("reports-select");
let option = select.addEventListener('change', changeReportCard);
const reportCards = document.getElementsByClassName("report-wrap");
const sectionHeaders = ["Total expenses this year", "Expenses by category", "Top 10 expenses this month"];
const sectionHeader = document.getElementById("section-header");

function changeReportCard(e) {
    const v = e.target.value;

    reportCards[v-1].classList.replace("hide" , "show");
    sectionHeader.innerHTML = sectionHeaders[v-1];
    for ($i = 0; $i < reportCards.length; $i++) {
        if ($i != (v-1)) {
            reportCards[$i].classList.replace("show" , "hide");
        }
    }
}

