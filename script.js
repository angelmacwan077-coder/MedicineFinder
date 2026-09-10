function searchMedicine() {

    const medicine = document.getElementById("medicineSearch").value.trim();

    if (medicine === "") {

        alert("Please enter a medicine name.");

        return;
    }

    alert(
        "You searched for: " +
        medicine +
        "\n\nMedicine search and pharmacy availability will be connected to the PHP + MySQL backend in the next step."
    );
}


/* Allow Enter key to search */

document.getElementById("medicineSearch").addEventListener("keypress", function(event) {

    if (event.key === "Enter") {

        searchMedicine();

    }

});