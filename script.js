
function searchMedicine() {
    const input = document.getElementById("medicineSearch");
    const resultsBox = document.getElementById("searchResults");
    const medicine = input.value.trim();

    if (medicine === "") {
        resultsBox.innerHTML =
            '<div class="alert alert-warning">Please enter a medicine name.</div>';
        input.focus();
        return;
    }

    resultsBox.innerHTML =
        '<div class="text-center p-4">Searching medicines...</div>';

    fetch("search_results.php?medicine=" + encodeURIComponent(medicine))
        .then(response => {
            if (!response.ok) {
                throw new Error("Search request failed.");
            }
            return response.text();
        })
        .then(html => {
            resultsBox.innerHTML = html;

            // Scroll to the results below the search bar
            resultsBox.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        })
        .catch(error => {
            console.error(error);
            resultsBox.innerHTML =
                '<div class="alert alert-danger">' +
                'Unable to search right now. Please try again.' +
                '</div>';
        });
}

// Allow Enter key to search
document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("medicineSearch");

    if (input) {
        input.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                searchMedicine();
            }
        });
    }
});

function mfChooseCategory(medicineName) {
    const input = document.getElementById("medicineSearch");

    if (!input) return;

    if (!medicineName) {
        input.focus();
        input.scrollIntoView({ behavior: "smooth", block: "center" });
        return;
    }

    input.value = medicineName;

    if (typeof searchMedicine === "function") {
        searchMedicine();
    }
}
