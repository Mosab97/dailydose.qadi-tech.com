"use strict";
window.googleTranslateElementInit = function () {
  new google.translate.TranslateElement(
    { pageLanguage: "en" },
    "google_translate_element"
  );

  cleanGoogleElements();
  observeLanguageDropdown();
};

function cleanGoogleElements() {
  const $skip = $("#google_translate_element .skiptranslate");

  $skip.contents().filter(function () {
    return this.nodeType === 3 && $.trim(this.nodeValue) !== "";
  }).remove();

  $skip.find("div").next().remove();
}

function observeLanguageDropdown() {
  const allowed = ["", "ar", "en", "iw"];

  const observer = new MutationObserver(function () {
    filterLanguages(allowed);
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true,
  });
}

function filterLanguages(allowed) {
  const $select = $("select.goog-te-combo");
  if ($select.length === 0) return;

  $select.find("option").each(function () {
    const lang = $(this).val().trim();
    if (lang !== "" && !allowed.includes(lang)) {
      $(this).hide();   // إخفاء الخيار بدلاً من الحذف
    } else {
      $(this).show();
    }
  });
}
