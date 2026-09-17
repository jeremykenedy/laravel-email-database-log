(function () {
  "use strict";

  var root = document.documentElement;
  var select = document.getElementById("el-theme");
  var system = window.matchMedia("(prefers-color-scheme: dark)");
  var preference = root.getAttribute("data-email-log-theme") || "system";

  function apply(value) {
    if (["light", "dark", "system"].indexOf(value) === -1) return;
    preference = value;
    var resolved =
      value === "system" ? (system.matches ? "dark" : "light") : value;
    root.setAttribute("data-email-log-theme", value);
    root.setAttribute("data-bs-theme", resolved);
    root.classList.toggle("dark", resolved === "dark");
    if (select) select.value = value;
  }

  try {
    apply(window.localStorage.getItem("email-log-theme") || preference);
  } catch (error) {
    apply(preference);
  }

  if (select)
    select.addEventListener("change", function () {
      apply(select.value);
      try {
        window.localStorage.setItem("email-log-theme", preference);
      } catch (error) {}
    });
  if (system.addEventListener)
    system.addEventListener("change", function () {
      apply(preference);
    });
})();
