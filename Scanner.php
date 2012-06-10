<?php
/**
 * Install_Ident
 *
 * PHP version 5
 *
 * @category Install_Ident
 * @package  Install_Ident
 * @author   Dave Holloway <dh@dajoho.com>
 * @license  MIT License http://www.opensource.org/licenses/mit-license.html
 * @version  GIT: <git_id>
 * @link     http://www.github.com/dajoho/Install_Ident
 */

/**
 * Install_Ident_Scanner - Inspects a given folder and returns an array
 * of installation classes.
 *
 * @category Install_Ident
 * @package  Install_Ident
 * @author   Dave Holloway <dh@dajoho.com>
 * @license  MIT License http://www.opensource.org/licenses/mit-license.html
 * @version  Release: <package_version>
 * @link     http://www.github.com/dajoho/Install_Ident
 */
class Install_Ident_Scanner
{
    private $results = array();
    private $dir;
    private $scanFiles = array(
        'configuration.php', 'wp-config.php', 'master.inc.php', 'config.inc.php'
    );
    private $ignoredFolders = array(
        '/administrator/components/',
        '/stats/includes/',
    );
    
    public function __construct($dir=null)
    {
        if ($dir != null) {
            $this->dir = rtrim($dir, '/');
        }
    }
    
    public function scan() {
        $results = array();
        
        foreach (glob($this->dir.'/*') as $file) {
        
            $ignore = false;
            foreach ($this->ignoredFolders as $ignoredFolder) {
                if (strpos($file,$ignoredFolder) !== false) {
                    $ignore = true;
                }
            }
            if ($ignore) {
                break;
            }
            
            if (is_dir($file)) {
                $me = __CLASS__;
                $subscanner = new $me($file);
                $subresults = $subscanner->scan();
                $results = array_merge($results, $subresults);
            } else {
                if (in_array(basename($file), $this->scanFiles)) {
                    
                    $inspector = new Install_Ident_Inspector($file);
                    $result = $inspector->inspect();
                    if (!is_object($result)) {
                        echo '[UNIDENTIFIED] - ';
                        echo $file . "\n";
                    } else {
                        echo '[IDENTIFIED] - ' . $result->type . ': ' . $result->path. "\n";
                        $results[] = $result;
                    }
                    
                }
            }
        }
        return $results;
    }
}
?>