/**
 * VAT.SUPPORT - Hero Fix Script
 * Upload to: /wp-content/themes/vat-child/js/hero-fix.js
 * Finds all green gradient heroes and converts them to dark gradient + grid
 */

document.addEventListener('DOMContentLoaded', function() {
  
  // Dark gradient to apply
  var darkGradient = 'linear-gradient(135deg, #0f172a 0%, #042f2e 50%, #0f172a 100%)';
  
  // Find all elements and check for green gradients
  document.querySelectorAll('*').forEach(function(el) {
    var style = el.getAttribute('style') || '';
    
    // Check for green gradient color codes
    if (style.includes('#0f2b') || style.includes('#0a3d') || style.includes('#0d28')) {
      
      // Apply dark gradient
      el.style.background = darkGradient;
      el.style.position = 'relative';
      
      // Add grid pattern overlay if not present
      if (!el.querySelector('.hero-grid-overlay')) {
        var grid = document.createElement('div');
        grid.className = 'hero-grid-overlay';
        el.insertBefore(grid, el.firstChild);
      }
      
      // Ensure content stays above grid
      Array.from(el.children).forEach(function(child) {
        if (!child.classList.contains('hero-grid-overlay')) {
          if (child.style.position !== 'absolute' && child.style.position !== 'fixed') {
            child.style.position = 'relative';
          }
          child.style.zIndex = '2';
        }
      });
    }
  });
});
