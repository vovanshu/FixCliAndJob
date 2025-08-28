<?php
namespace FixCliAndJob\Stdlib;

use Interop\Container\ContainerInterface;
use Omeka\Entity\Job;
use FixCliAndJob\Common;

class Cli extends \Omeka\Stdlib\Cli
{
 
    use Common;

    public function __construct($serviceLocator, $executeStrategy)
    {

        $this->setServiceLocator($serviceLocator);
        $this->executeStrategy = $executeStrategy;

    }

    /**
     * Execute a command.
     *
     * Expects arguments to be properly escaped.
     *
     * @param string $command An executable command
     * @return string|false The command's standard output or false on error
     */
    public function execute($command)
    {
        return $this->exec($command);
    }

    /**
     * 
     *
     * @param string $command
     * @return string|false
     */
    public function exec($command)
    {

        $result = $command;
        if(stripos($command, 'perform-job.php') !== False){
            $result = $this->sendJob($command);
        }elseif($command == "command -v 'php'"){
            $result = "PHP CLI no allowed!";
        }elseif($command == "command -v 'php' --version" || $command == "PHP CLI no allowed! --version"){
            $result = "PHP Version: " . phpversion() . "\r\nSAPI: " . php_sapi_name();
        }elseif($command == "command -v 'convert'"){
            $result = "ImageMagick no allowed!";
        }elseif($command == "command -v 'convert'/convert --version" || $command == "ImageMagick no allowed!/convert --version"){
            $dest_class = $this->getServiceLocator()->get('Omeka\File\Thumbnailer');
            if($dest_class instanceof \Omeka\File\Thumbnailer\Imagick){
                $result = "Used Imagick " . phpversion("imagick");
            }elseif($dest_class instanceof \Omeka\File\Thumbnailer\Gd){
                $result = "Used Gd " . phpversion("gd");
            }else{
                $result = "Need change local config!\r\nSet service_manage => aliases => Omeka\File\Thumbnailer => Omeka\File\Thumbnailer\Imagick || Omeka\File\Thumbnailer\Gd";
            }
        }else{
            $this->getLogger()->info(sprintf('Unknown command "%s"', $command));
        }
        return $result;

    }

    /**
     *
     *
     * @param string $command
     * @return string|false
     */
    public function procOpen($command)
    {
        return $this->exec($command);
    }

    private function sendJob($command)
    {

        preg_match("/--job-id '(?P<jobid>\d+)' --base-path/", $command, $matches);
        if(!empty($matches['jobid'])){
            $sendJob = $this->getConf('executeJob');
            if($sendJob == 'CRON'){
                return True;
            }elseif($sendJob == 'CURL'){
                return $this->sendCURL($matches['jobid'], $command);
            }else{
                return $this->executeJob($matches['jobid']);
            }

            // 

            // $scheme = 'http';
            // if(!empty($_SERVER['REQUEST_SCHEME'])){
                // $scheme = $_SERVER['REQUEST_SCHEME'];
            // }
            // $url = $scheme . '://' . $_SERVER['SERVER_NAME'] . '/admin/perform-job/' . $matches['jobid'];

            // $this->fast_request($url);
            // $this->fetchWithoutResponseURL( $url );
            // $socket = stream_socket_client($url, $errorno, $errorstr, 0.01);


        }

    }

    private function sendCURL($id, $command)
    {

        $noneed = [92];
            // $_SERVER['HTTP_HOST'] 
        
        $scheme = 'http';
        if(!empty($_SERVER['REQUEST_SCHEME'])){
            $scheme = $_SERVER['REQUEST_SCHEME'];
        }
        $url = $scheme . '://' . $_SERVER['SERVER_NAME'] . '/perform-job/' . $id;
        $this->logger->info(sprintf('Sent Job "%s"; URL: "%s"', $id, $url)); // @translate
        $ch = curl_init();    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,0);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,0.01); 
        // curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        // curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 100); 
        // curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt($ch, CURLOPT_COOKIE, $_SERVER['HTTP_COOKIE'] );
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        // curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/json;", 'Accept:application/json'));


		// curl_setopt($ch, CURLOPT_HEADER, false);
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		// curl_exec($ch);
        // curl_setopt($ch, CURLOPT_ENCODING, 'none');
		// curl_setopt($ch, CURLOPT_HEADERFUNCTION, null);
		// curl_setopt($ch, CURLOPT_WRITEFUNCTION, null);

        curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close( $ch );
        $this->logger->info(sprintf('Send Job "%s"', $id)); // @translate
        if ($errno && !in_array($errno, $noneed)) {
            $this->logger->err(sprintf('Command "%s" failed with status code %s. Curl by URL %s', $command, $errno, $url)); // @translate
        }
        return True;

    }

}
