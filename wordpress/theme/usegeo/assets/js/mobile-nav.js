// UseGEO Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('.usegeo-menu-toggle');
  var nav = document.querySelector('.usegeo-site-nav');

  if (!toggle || !nav) return;

  toggle.addEventListener('click', function () {
    var expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!expanded));
    nav.classList.toggle('usegeo-site-nav--open');
  });

  // Close menu when a link is clicked (for anchor links)
  nav.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', function () {
      toggle.setAttribute('aria-expanded', 'false');
      nav.classList.remove('usegeo-site-nav--open');
    });
  });
});
