<?php
/**
 * CHINA SPEED OPTIMIZATION FOR OPENCART 4
 * File: catalog/controller/startup/china_optimize.php
 * 
 * Purpose: Replace blocked external resources with China-accessible CDNs
 * The Great Firewall blocks Google Fonts, jQuery CDN, etc.
 */

namespace Opencart\Catalog\Controller\Startup;

class ChinaOptimize extends \Opencart\System\Engine\Controller {
    
    public function index(): void {
        // Add China-friendly CDN replacements to document
        $this->document->addScript('https://lib.baomitu.com/jquery/3.6.0/jquery.min.js');
        
        // Replace Google Fonts with China mirror
        $this->document->addStyle('https://fonts.loli.net/css2?family=Noto+Sans+SC:wght@400;500;700&display=swap');
    }
}

/**
 * ALTERNATIVE: Add this to your header.twig directly
 * Replace any Google Fonts links with these China mirrors:
 * 
 * Google Fonts -> fonts.loli.net (China mirror)
 * Example: 
 * FROM: https://fonts.googleapis.com/css2?family=Roboto
 * TO:   https://fonts.loli.net/css2?family=Roboto
 * 
 * jQuery CDN -> lib.baomitu.com
 * Example:
 * FROM: https://code.jquery.com/jquery-3.6.0.min.js
 * TO:   https://lib.baomitu.com/jquery/3.6.0/jquery.min.js
 * 
 * Bootstrap CDN -> cdn.bootcdn.net
 * Example:
 * FROM: https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css
 * TO:   https://cdn.bootcdn.net/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css
 */
