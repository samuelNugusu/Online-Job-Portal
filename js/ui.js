//Example: Add a click listener to a button that shows an alert
document.addEventListener("DOMContentLoaded", function() {
  const alertButton = document.getElementById("showAlertButton");
  if (alertButton) {
    alertButton.addEventListener("click", function() {
      alert("This is a sample alert!");
    });
  }
});