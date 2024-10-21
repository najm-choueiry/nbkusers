<?php 

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

class LogsService {
    private $loggerInterface;
	private $entityManager;


    public function __construct(LoggerInterface $loggerInterface = null, EntityManagerInterface $entityManager)
    {
        $this->loggerInterface = $loggerInterface;
        $this->entityManager = $entityManager;

    }

    public function pushLogs($identifier,$request,$response,$url = null,$status = null)
    {
        // dd($identifier,$request,$response);
        try{
            $logs = new Logs();
            
            $logs->setidentifier($identifier);
            $logs->seturl($url);
            $logs->setrequest(json_encode($request));
            $logs->setresponse($response);
            $logs->setresponseStatusCode($status);
            $this->entityManager->persist($logs);
            $this->entityManager->flush();
            // dd('helo');
            return true;
        }catch(Exception $e){
            if ($this->loggerInterface) {
                $this->loggerInterface->info($e->getMessage());
            } else {
                error_log('Logger not initialized!');
            }
            return false;
        }
        
    }
}