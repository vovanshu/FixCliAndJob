<?php declare(strict_types=1);

namespace FixCliAndJob\Job;

use Omeka\Job\AbstractJob;
use Omeka\Entity\Job;

class TestingLoop extends AbstractJob
{

    public function perform(): void
    {

        $logger = $this->serviceLocator->get('Omeka\Logger');
        $loop = $this->getArg('loop');
        if(!$loop){
            $loop = 1;
        }
        $timeout = (integer) $this->getArg('timeout');
        if(!$timeout){
            $timeout = 5;
        }
        // The reference id is the job id for now.
        $referenceIdProcessor = new \Laminas\Log\Processor\ReferenceId();
        $referenceIdProcessor->setReferenceId('fix-Cli&Job/testing-loop/job_' . $this->job->getId());

        $logger->addProcessor($referenceIdProcessor);

        for($i = 1; $i <= $loop; $i++){
            sleep($timeout);
            $timestamp = date('Y-m-d H:i:s');
            $logger->info('Current timestamp - ' . $timestamp . '; Loop - ' . $i . '/' . $loop . '; Timeout - ' . $timeout . ' sec');
        }

        if ($this->job->getStatus() === Job::STATUS_ERROR) {
            return;
        }

    }


}
