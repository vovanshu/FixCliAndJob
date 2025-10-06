<?php

namespace FixCliAndJob;

// use Omeka\Permissions\Acl;
use Omeka\Entity\Job;

trait Common
{

    protected $configName = 'fixcliandjob';

    protected $serviceLocator;

    protected $acl;

    protected $connection;

    protected $settings;

    protected $userSettings;

    protected $config;

    protected $apiManager;

    protected $entityManager;

    protected $logger;

    /**
     * Set the service locator.
     *
     * @param $serviceLocator
     */
    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get the service locator.
     *
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }


    public function getConnection()
    {

        if($this->getServiceLocator()){
            if(!$this->connection){
                $this->connection = $this->getServiceLocator()->get('Omeka\Connection');
            }
            return $this->connection;
        }
        return;

    }

    public function getLogger()
    {

        if($this->getServiceLocator()){
            if(!$this->logger){
                $this->logger = $this->getServiceLocator()->get('Omeka\Logger');
            }
            return $this->logger;
        }
        return;

    }

    public function getApiManager()
    {

        if($this->getServiceLocator()){
            if(!$this->apiManager){
                $this->apiManager = $this->getServiceLocator()->get('Omeka\ApiManager');
            }
            return $this->apiManager;
        }
        return;

    }

    public function getEntityManager()
    {

        if($this->getServiceLocator()){
            if(!$this->entityManager){
                $this->entityManager = $this->getServiceLocator()->get('Omeka\EntityManager');
            }
            return $this->entityManager;
        }
        return;

    }

    public function getAcl()
    {

        if($this->getServiceLocator()){
            if(!$this->acl){
                $this->acl = $this->getServiceLocator()->get('Omeka\Acl');
            }
            return $this->acl;
        }
        return;

    }

    public function getSettings()
    {

        if($this->getServiceLocator()){
            if(!$this->settings){
                $this->settings = $this->getServiceLocator()->get('Omeka\Settings');
            }
            return $this->settings;
        }
        return;

    }

    public function getUserSettings()
    {

        if($this->getServiceLocator()){
            if(!$this->userSettings){
                $this->userSettings = $this->getServiceLocator()->get('Omeka\Settings\User');
            }
            return $this->userSettings;
        }
        return;

    }

    public function getConfigs()
    {

        if($this->getServiceLocator()){
            if(!$this->config){
                $this->config = $this->getServiceLocator()->get('Config');
            }
            return $this->config;
        }
        return;
        
    }

    public function getTranslator()
    {

        if($this->getServiceLocator()){
            if(!$this->translator){
                $this->translator = $this->getServiceLocator()->get('MvcTranslator');
            }
            return $this->translator;
        }
        return;
        
    }

    public function getConf($name = Null, $param = Null, $all = False)
    {

        $config = $this->getConfigs()[$this->configName];
        if(!empty($name)){
            // if(!empty($config['options']) && !empty($config['options'][$name])){
            //     $name = $config['options'][$name];
            // }
            if(!empty($config[$name])){
                if(!empty($param)){
                    if(!empty($config[$name][$param])){
                        return $config[$name][$param];
                    }else{
                        return False;
                    }
                }else{
                    return $config[$name];
                }
            }
        }else{
            if($all){
                return $config;
            }else{
                return False;
            }
        }

    }

    public function getOps($name)
    {

        $config = $this->getConfigs()[$this->configName];
        if(!empty($name)){
            if(!empty($config['options']) && !empty($config['options'][$name])){
                return $config['options'][$name];
            }
        }
        return False;

    }

    public function getSets($name, $callback = [])
    {
        
        $name = (($opt = $this->getOps($name)) ? $opt : $name);
        $r = ($rc = $this->getSettings()->get($name)) ? $rc : ($rc = $this->getConf('settings', $name) ? $rc : Null);
        if(!empty($callback)){
            $r = call_user_func_array($callback, [$r]);
        }
        return $r;
        
    }

    public function setSets($name, $value)
    {
        
        $name = (($opt = $this->getOps($name)) ? $opt : $name);
        $this->getSettings()->set($name, $value);
        
    }

    private function getCurentUserID()
    {

        $user = $this->getAcl()->getAuthenticationService()->getIdentity();
        if($user){
            return $user->getId();
        }
        return Null;

    }

    private function getRoleCurentUser()
    {

        $r = 'public';
        $rc = $this->getAcl()->getAuthenticationService()->getIdentity();
        if($rc){
            $r = $rc->getRoleId();
        }
        return $r;

    }

    private function getRoleUser($userID)
    {

        $r = $this->getUser($userID);
        if(!empty($r['role'])){
            return $r['role'];
        }
        return False;

    }

    private function getUser($userID)
    {

        $user = $this->getApiManager()->read('users', $userID)->getContent();
        if(!empty($user)){
            $r['id'] = $user['o:id'];
            $r['name'] = $user['o:name'];
            $r['email'] = $user['o:email'];
            $r['created'] = $user['o:created'];
            $r['role'] = $user['o:role'];
            return $r;
        }
        return False;

    }

    public function arrayToTextList($string, $separator = ' = ')
    {

        if(!empty($string)){
            if(is_string($string)){
                $rc = json_decode($string, True);
            }else{
                $rc = $string;
            }
            $r = '';
            foreach($rc as $k => $v){
                $r .= "$k$separator$v\r\n";
            }
            return $r;
        }
        return;

    }

    /**
     * Get each line of a string separately as a key-value list.
     *
     * @param string $string
     * @return array
     */
    public function textListToArray($string, $keyValueSeparator = ' = ')
    {

        $result = [];
        foreach ($this->stringToList($string) as $keyValue) {
            [$key, $value] = array_map('trim', explode($keyValueSeparator, $keyValue, 2));
            $result[$key] = $value;
        }
        return $result;

    }

    /**
     * Get each line of a string separately as a list.
     *
     * @param string $string
     * @return array
     */
    public function stringToList($string)
    {
        return array_filter(array_map('trim', explode("\n", $this->fixEndOfLine($string))), 'strlen');
    }

    /**
     * Clean the text area from end of lines.
     *
     * This method fixes Windows and Apple copy/paste from a textarea input.
     *
     * @param string $string
     * @return string
     */
    protected function fixEndOfLine($string)
    {
        return str_replace(["\r\n", "\n\r", "\r"], ["\n", "\n", "\n"], (string) $string);
    }

    private function executeJob($id)
    {

        $job = $this->getEntityManager()->find(Job::class, $id);
        if(!empty($job)){
            $JobDispatcher = $this->getServiceLocator()->get('Omeka\Job\Dispatcher');
            $AuthenticationService = $this->getAcl()->getAuthenticationService();
            $user = $this->getAcl()->getAuthenticationService()->getIdentity();
            $owner = $job->getOwner();
            if($owner) {
                $AuthenticationService->getStorage()->write($owner);
            }
            $job->setPid(getmypid());
            $this->entityManager->flush();
            $strategy = $this->getServiceLocator()->get('Omeka\Job\DispatchStrategy\Synchronous');
            $JobDispatcher->send($job, $strategy);
            $job->setPid(null);
            $this->getEntityManager()->flush();                
            if($owner && $user){
                $AuthenticationService->getStorage()->write($user);
            }
            $result = 'Job with ID '.$id.' executed success.';
        }else{
            $result = 'There is no job with the given ID '.$id;
            $this->getLogger()->err($result);
        }
        return $result;

    }

}
