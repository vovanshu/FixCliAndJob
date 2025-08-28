<?php
namespace FixCliAndJob\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Omeka\Entity\Job;
use FixCliAndJob\Common;

class PerformJobController extends AbstractActionController
{

    use Common;

    public function __construct($serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
    }

    public function executeAction()
    {

        $id = $this->params('id');
        if(!empty($id)){
            $time_limit = $this->getConf('time_limit');
            ignore_user_abort(true);
            if(!set_time_limit($time_limit)){
                $this->getLogger()->err('Set time limit fail!');
            }
            $result = $this->executeJob($id);
        }else{
            $result = 'No job ID given; use --job-id <id>';
            $this->getLogger()->err($result);
        }

        $model = new JsonModel(['result' => $result]);
        return $model->setTerminal(True);

    }
    

    public function executingAction()
    {

        // */5 * * * * wget -q -O /dev/null "https://example.org/perform-jobs" > /dev/null 2>&1
        $result = False;
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
                $result = [];
                $ids = [];
                foreach($r as $a){
                    $ids[] = $a['id'];
                }
                if(!empty($ids)){
                    $Ids = join(',', $ids);
                    $this->getConnection()->executeQuery("UPDATE `job` SET `status` = '{$been_status}' WHERE `id` IN ({$Ids});");
                }
                foreach($r as $a){
                    $result[$a['id']] = $this->executeJob($a['id']);
                }
            }
        }
        $model = new JsonModel(['result' => $result]);
        return $model->setTerminal(True);

    }

    public function testingloopAction()
    {

        $loop = $this->params('loop');
        $timeout = $this->params('timeout');

        $params = [
            'process' => 'TestingLoop',
            'loop' => $loop,
            'timeout' => $timeout
        ];

        $result['Testing start'] = date('Y-m-d H:i:s');
        $this->jobDispatcher()->dispatch(\FixCliAndJob\Job\TestingLoop::class, $params);

        $result['Testing end'] = date('Y-m-d H:i:s');
        return new JsonModel($result);

    }

}
