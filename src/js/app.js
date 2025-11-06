import $ from "jquery";
window.jQuery = $;
window.$ = $;

// POPPER.JS (requerido por Bootstrap 4)
import Popper from "popper.js";
window.Popper = Popper;

// BOOTSTRAP 4
import "bootstrap";

// DATATABLES
import "datatables.net";
import "datatables.net-bs4";
import "datatables.net-responsive";
import "datatables.net-responsive-bs4";

// SELECT2
import "select2";

// SWEETALERT2
import Swal from "sweetalert2";
window.Swal = Swal;

// CHART.JS
import Chart from "chart.js/auto";
window.Chart = Chart;

// INTRO.JS
import introJs from "intro.js";
window.introJs = introJs;

// QUITAR ESTA LÍNEA:
// import "./api/usuarios-permisos.js";

// CSS
import "bootstrap/dist/css/bootstrap.min.css";
import "@fortawesome/fontawesome-free/css/all.min.css";
import "datatables.net-bs4/css/dataTables.bootstrap4.min.css";
import "datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css";
import "select2/dist/css/select2.min.css";
import "sweetalert2/dist/sweetalert2.min.css";
import "intro.js/introjs.css";

// CSS PERSONALIZADO
import "../scss/app.scss";

document.addEventListener("DOMContentLoaded", (e) => {
  const dropdown = document.querySelector(".dropdown-menu");
  if (dropdown) {
    dropdown.style.margin = 0;
  }

  let items = document.querySelectorAll(".nav-link");
  items.forEach((item) => {
    if (item.href == location.href) {
      item.classList.add("active");
      if (item.classList.contains("dropdown-item")) {
        item.parentElement.parentElement.previousElementSibling.classList.add(
          "active"
        );
      }
    }
  });
});

document.onreadystatechange = () => {
  switch (document.readyState) {
    case "loading":
      break;
    case "interactive":
      document.getElementById("bar")
        ? (document.getElementById("bar").style.width = "35%")
        : null;
      break;

    case "complete":
      document.getElementById("bar")
        ? (document.getElementById("bar").style.width = "100%")
        : null;
      setTimeout(() => {
        document.getElementById("bar")
          ? (document.getElementById("bar").parentElement.style.display =
              "none")
          : null;
      }, 1000);
      break;
  }
};
