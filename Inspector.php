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
 * Install_Ident_Inspector - Inspects a given configuration file and returns
 * a class describing the installation.
 *
 * @category Install_Ident
 * @package  Install_Ident
 * @author   Dave Holloway <dh@dajoho.com>
 * @license  MIT License http://www.opensource.org/licenses/mit-license.html
 * @version  Release: <package_version>
 * @link     http://www.github.com/dajoho/Install_Ident
 */
class Install_Ident_Inspector
{
    private $configFile;
    
    private $idents = array(
        'Joomla15' => array(
                        'identifier' => 'JConfig',
                        'pattern'    => '/var \$host = \'(?P<host>.*?)\'.*var \$user = \'(?P<user>.*?)\'.*var \$db = \'(?P<db>.*?)\'.*var \$password = \'(?P<pass>.*?)\'.*/s'
                   ),
        'Joomla16' => array(
                        'identifier' => 'JConfig',
                        'pattern'    => '/public \$host = \'(?P<host>.*?)\'.*public \$user = \'(?P<user>.*?)\'.*public \$password = \'(?P<pass>.*?)\'.*public \$db = \'(?P<db>.*?)\'.*/s'
                   ),
        'Mambo' => array(
                        'identifier' => 'mosConfig',
                        'pattern'    => '/\$mosConfig_db = \'(?P<db>.*?)\'.*\$mosConfig_host = \'(?P<host>.*?)\'.*\$mosConfig_password = \'(?P<pass>.*?)\'.*\$mosConfig_user = \'(?P<user>.*?)\'.*/s'
                   )
                   
    );
    
    public function __construct($file=null)
    {
        $this->configFile=$file;
    }
    
    public function inspect()
    {
        if ($this->configFile == null) {
            return false;
        }
    
        $configuration = @file_get_contents($this->configFile);
        
        $matches = array();
        
        if ($configuration != '') {

            $found = false;
            foreach ($this->idents as $identName=>$identSettings) {
                if (!$found) {
                    if (strpos($configuration, $identSettings['identifier']) !== false) {
                        preg_match($identSettings['pattern'], $configuration, $matches);
                        if (isset($matches['host']) && isset($matches['db']) && isset($matches['user'])) {
                            $found = true;
                            $matches['type'] = $identName;
                        }
                    }
                }
            }

            if ($found) {
                $installation = new stdClass;
                $installation->path = dirname($this->configFile);
                $installation->type = $matches['type'];
                $installation->host = $matches['host'];
                $installation->db   = $matches['db'];
                $installation->user = $matches['user'];
                $installation->pass = $matches['pass'];
                return $installation;
            }
        }
        return false;
    }

}
?>
