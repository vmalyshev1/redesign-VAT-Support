/**
 * VAT Support - Custom JavaScript
 * Precision Finance Design
 */

document.addEventListener('DOMContentLoaded', function() {
    var navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
    initMobileSubmenuToggle();
    var offcanvasEl = document.getElementById('offcanvasNavbar');
    if (offcanvasEl) {
        offcanvasEl.addEventListener('show.bs.offcanvas', function() {
            initMobileSubmenuToggle();
        });
    }
});

function initMobileSubmenuToggle() {
    var offcanvasMenu = document.querySelector('.offcanvas-menu-list');
    if (!offcanvasMenu) return;
    var menuItemsWithChildren = offcanvasMenu.querySelectorAll('.menu-item-has-children');
    menuItemsWithChildren.forEach(function(menuItem) {
        if (menuItem.dataset.submenuInit) return;
        menuItem.dataset.submenuInit = '1';
        var link = menuItem.querySelector('a');
        var submenu = menuItem.querySelector('ul.sub-menu') || menuItem.querySelector('ul.dropdown-menu');
        if (!link || !submenu) return;
        var toggleBtn = document.createElement('button');
        toggleBtn.className = 'submenu-toggle';
        toggleBtn.setAttribute('aria-label', 'Toggle submenu');
        toggleBtn.setAttribute('type', 'button');
        toggleBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>';
        link.parentNode.insertBefore(toggleBtn, link.nextSibling);
        submenu.style.display = 'none';
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var isOpen = menuItem.classList.contains('submenu-open');
            offcanvasMenu.querySelectorAll('.menu-item-has-children.submenu-open').forEach(function(item) {
                if (item !== menuItem) {
                    item.classList.remove('submenu-open');
                    var s = item.querySelector('ul.sub-menu') || item.querySelector('ul.dropdown-menu');
                    var b = item.querySelector('.submenu-toggle svg');
                    if (s) s.style.display = 'none';
                    if (b) b.style.transform = 'rotate(0deg)';
                }
            });
            var svgEl = toggleBtn.querySelector('svg');
            if (isOpen) {
                menuItem.classList.remove('submenu-open');
                submenu.style.display = 'none';
                if (svgEl) svgEl.style.transform = 'rotate(0deg)';
            } else {
                menuItem.classList.add('submenu-open');
                submenu.style.display = 'block';
                if (svgEl) svgEl.style.transform = 'rotate(180deg)';
            }
        });
    });
}

// VAT Rates Filter
document.addEventListener('DOMContentLoaded', function() {
  function setupFilters(search, region, type, filing, rate, reset, count, noRes, table) {
    if (!table || !search) return;
    function apply() {
      var q = (search.value || '').toLowerCase().trim();
      var rg = region.value;
      var tp = type.value;
      var fl = filing.value;
      var rt = rate.value;
      var rows = table.querySelectorAll('tr');
      var shown = 0;
      for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var ok = true;
        if (rg !== 'all' && row.getAttribute('data-region') !== rg) ok = false;
        if (ok && tp !== 'all' && row.getAttribute('data-type') !== tp) ok = false;
        if (ok && fl !== 'all') {
          var rowFl = (row.getAttribute('data-filing') || '').toLowerCase();
          if (rowFl.indexOf(fl) === -1) ok = false;
        }
        if (ok && rt !== 'all' && row.getAttribute('data-rate') !== rt) ok = false;
        if (ok && q) {
          var kw = (row.getAttribute('data-kw') || '').toLowerCase();
          var txt = row.textContent.toLowerCase();
          if (kw.indexOf(q) === -1 && txt.indexOf(q) === -1) ok = false;
        }
        row.style.display = ok ? '' : 'none';
        if (ok) shown++;
      }
      count.textContent = shown;
      noRes.style.display = shown === 0 ? 'block' : 'none';
    }
    function resetAll() {
      search.value = '';
      region.value = 'all';
      type.value = 'all';
      filing.value = 'all';
      rate.value = 'all';
      apply();
    }
    search.addEventListener('input', apply);
    region.addEventListener('change', apply);
    type.addEventListener('change', apply);
    filing.addEventListener('change', apply);
    rate.addEventListener('change', apply);
    reset.addEventListener('click', resetAll);
    apply();
  }
  var s = document.getElementById('vatSearch');
  if (s) setupFilters(s, document.getElementById('vatRegion'), document.getElementById('vatType'), document.getElementById('vatFiling'), document.getElementById('vatRate'), document.getElementById('vatReset'), document.getElementById('vatCount'), document.getElementById('noResults'), document.getElementById('vatTable'));
  var sRu = document.getElementById('vatSearchRu');
  if (sRu) setupFilters(sRu, document.getElementById('vatRegionRu'), document.getElementById('vatTypeRu'), document.getElementById('vatFilingRu'), document.getElementById('vatRateRu'), document.getElementById('vatResetRu'), document.getElementById('vatCountRu'), document.getElementById('noResultsRu'), document.getElementById('vatTableRu'));
  var sZh = document.getElementById('vatSearchZh');
  if (sZh) setupFilters(sZh, document.getElementById('vatRegionZh'), document.getElementById('vatTypeZh'), document.getElementById('vatFilingZh'), document.getElementById('vatRateZh'), document.getElementById('vatResetZh'), document.getElementById('vatCountZh'), document.getElementById('noResultsZh'), document.getElementById('vatTableZh'));
});
