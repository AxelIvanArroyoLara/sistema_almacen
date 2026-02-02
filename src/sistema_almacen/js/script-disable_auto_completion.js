document.addEventListener("DOMContentLoaded", () => {
  // Apaga autocomplete en todos los forms
  document.querySelectorAll("form").forEach(f => f.setAttribute("autocomplete", "off"));

  // Apaga autocomplete en todos los campos típicos
  document.querySelectorAll("input, textarea, select").forEach(el => {
    el.setAttribute("autocomplete", "off");
  });
});
