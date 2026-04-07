/**
 * VAT.SUPPORT - Hero Fix Script v3 (Safe)
 * Only targets hero sections with padding 80px or 64px (typical hero padding)
 */

document.addEventListener('DOMContentLoaded', function() {
  
  var darkGradient = 'linear-gradient(135deg, #0f172a 0%, #042f2e 50%, #0f172a 100%)';
  
  document.querySelectorAll('*').forEach(function(el) {
    var style = el.getAttribute('style') || '';
    var styleLower = style.toLowerCase();
    
    // Only target elements that look like heroes:
    // 1. Has gradient with green colors
    // 2. Has hero-like padding (80px or 64px)
    var isHero = (
      styleLower.includes('gradient') &&
      (styleLower.includes('padding:80px') || styleLower.includes('padding:64px') || styleLower.includes('padding: 80px') || styleLower.includes('padding: 64px'))
    );
    
    // Also check for .vat-hero class
    if (el.classList.contains('vat-hero')) {
      isHero = true;
    }
    
    var hasGreenGradient = (
      styleLower.includes('#0f2b') ||
      styleLower.includes('#0a3d') ||
      styleLower.includes('#0d28')
    );
    
    if (isHero && hasGreenGradient) {
      el.style.setProperty('background', darkGradient, 'important');
      el.style.position = 'relative';
      
      if (!el.querySelector('.hero-grid-overlay')) {
        var grid = document.createElement('div');
        grid.className = 'hero-grid-overlay';
        grid.style.cssText = 'position:absolute;top:0;left:0;right:0;bottom:0;background-image:linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);background-size:60px 60px;pointer-events:none;z-index:1;';
        el.insertBefore(grid, el.firstChild);
      }
      
      Array.from(el.children).forEach(function(child) {
        if (!child.classList.contains('hero-grid-overlay')) {
          var childPos = child.style.position;
          if (childPos !== 'absolute' && childPos !== 'fixed') {
            child.style.position = 'relative';
          }
          child.style.zIndex = '2';
        }
      });
    }
  });
});
