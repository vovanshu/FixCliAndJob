<?php

namespace FixCliAndJob;

/**
 * Perform a jobs for CRON.
 * 
 */

 // */5 * * * * php -q "/path-to-destination/modules/FixCliAndJob/perform-jobs.php" /dev/null 2>&1

if (!class_exists(\FixCliAndJob\Common::class)) {
    require_once __DIR__ . '/Common.php';
}

use Omeka\Mvc\Application;
use Omeka\Entity\Job;
use FixCliAndJob\Common;

require dirname(dirname(__DIR__)) . '/bootstrap.php';

class PerformJobs
{

    use Common;

    public function __construct($serviceLocator){
        $this->setServiceLocator($serviceLocator);
    }
    
    public function executing()
    {

        $result = '';
        $limit = $this->getConf('CRON_Jobs_limit');
        $time_limit = $this->getConf('time_limit');
        $need_status = Job::STATUS_STARTING;
        $been_status = Job::STATUS_IN_PROGRESS;
        $db = $this->getConnection()->executeQuery("SELECT * FROM `job` WHERE `status` = '{$need_status}' LIMIT $limit;");
        if(!empty($db)){
            $r = $db->fetchAll();
            if(!empty($r)){
                ignore_user_abort(true);
                if(!set_time_limit($time_limit)){
                    $this->getLogger()->err('Set time limit fail!');
                }
                $ids = [];
                foreach($r as $a){
                    $ids[] = $a['id'];
                }
                if(!empty($ids)){
                    $Ids = join('\', \'', $ids);
                    $this->getConnection()->executeQuery("UPDATE `job` SET `status` = '{$been_status}' WHERE `id` IN ('{$Ids}');");
                }
                foreach($r as $a){
                    $result .= $this->executeJob($a['id']);
                    $result .= "\n";
                }
            }
        }
        echo $result;

    }

}

$application = Application::init(require OMEKA_PATH . '/application/config/application.config.php');
$PerformJobs = new PerformJobs($application->getServiceManager());
$PerformJobs->executing();
