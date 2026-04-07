/**
 * VAT.SUPPORT - Hero Fix Script v2
 * Finds ALL green gradient heroes and converts them to dark gradient + grid
 */

document.addEventListener('DOMContentLoaded', function() {
  
  var darkGradient = 'linear-gradient(135deg, #0f172a 0%, #042f2e 50%, #0f172a 100%)';
  
  // Check ALL elements for any green-ish gradient
  document.querySelectorAll('*').forEach(function(el) {
    var style = el.getAttribute('style') || '';
    var styleLower = style.toLowerCase();
    
    // Check for ANY green gradient patterns
    var hasGreenGradient = (
      // Hex codes (various green shades)
      styleLower.includes('#0f2b') ||
      styleLower.includes('#0a3d') ||
      styleLower.includes('#0d28') ||
      styleLower.includes('#0f2') ||
      styleLower.includes('#0a3') ||
      styleLower.includes('#0d2') ||
      styleLower.includes('#1a4') ||
      styleLower.includes('#0b3') ||
      styleLower.includes('#0c3') ||
      styleLower.includes('#0e3') ||
      styleLower.includes('#104') ||
      styleLower.includes('#0f3') ||
      // RGB patterns for green
      styleLower.includes('rgb(15, 43') ||
      styleLower.includes('rgb(10, 61') ||
      styleLower.includes('rgb(13, 40') ||
      styleLower.includes('rgb(15,43') ||
      styleLower.includes('rgb(10,61') ||
      styleLower.includes('rgb(13,40') ||
      // Check if it's a gradient with greenish tones
      (styleLower.includes('gradient') && (
        styleLower.includes('0f2') ||
        styleLower.includes('0a3') ||
        styleLower.includes('0d2') ||
        styleLower.includes('0b3') ||
        styleLower.includes('0e3') ||
        styleLower.includes('1a4') ||
        styleLower.includes('104') ||
        styleLower.includes('2b1f') ||
        styleLower.includes('3d2e') ||
        styleLower.includes('2818')
      ))
    );
    
    if (hasGreenGradient) {
      // Apply dark gradient
      el.style.setProperty('background', darkGradient, 'important');
      el.style.position = 'relative';
      
      // Add grid overlay if not present
      if (!el.querySelector('.hero-grid-overlay')) {
        var grid = document.createElement('div');
        grid.className = 'hero-grid-overlay';
        grid.style.cssText = 'position:absolute;top:0;left:0;right:0;bottom:0;background-image:linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);background-size:60px 60px;pointer-events:none;z-index:1;';
        el.insertBefore(grid, el.firstChild);
      }
      
      // Ensure content stays above grid
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
