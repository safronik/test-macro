<?php

namespace Models;

use Entities\CallInterval;
use Safronik\DB\DB;
use Safronik\Helpers\TimeHelper;
use Safronik\Repositories\Repository;
use Models\Exceptions\ExceptionApiCallExceeded;

/**
 * Do not store each call time
 */
class ApiCallLimit{
    
    private int    $duration;
    private int    $call_limit;
    private array  $parameters;
    private string $exceed_action;
    private DB     $db;
    
    public function __construct( int $interval_duration, int $call_limit, array $parameters, $exceed_action = 'throw' )
    {
        ! in_array( $exceed_action, [ 'throw', 'sleep' ] )
            && throw new \Exception( "Exceed action $exceed_action is invalid" );
        
        $this->duration      = $interval_duration;
        $this->call_limit    = $call_limit;
        $this->parameters    = $parameters;
        $this->exceed_action = $exceed_action;
        $this->db            = DB::getInstance();
    }
    
    /**
     * Limit the amount of calls in the period of time
     *
     * Available actions:
     *  - Halt the script if the limit exceeded
     *  - throw exception
     *
     * @return void
     * @throws \Exception
     */
    public function controlCallLimit(): void
    {
        $interval = $this->getIntervalByParameters( $this->parameters );
        
        if( $interval ){
            
            if( $interval->isPassed( $this->duration ) ){
                $this->drop( $interval );
                unset( $interval );
                
            }elseif( $interval->isLimitExceeded( $this->call_limit ) ){
                $this->makeExceededAction( $interval, 'throw' );
            }
        }
        
        $this->updateInterval( $interval ?? null );
    }
    
    /**
     * Performs an action. Should be called if the interval exceeded.
     *
     * @param CallInterval $interval
     *
     * @return void
     * @throws \Exception
     */
    private function makeExceededAction( CallInterval $interval ): void
    {
        switch( $this->exceed_action ){
            
            // Sleeps until next peeriod is started. Slows down execution
            case 'sleep':
                time_sleep_until(
                    $interval->getStart() + $this->duration
                );
                break;
            
            // Trows an exception
            case 'throw':
                $next_interval_starts_in = $this->duration - ( time() % $this->duration ) ;
                throw new ExceptionApiCallExceeded( 'Call limit exceeded. You can try again in ' . $next_interval_starts_in . ' seconds', 425 );
        }
    }

    /**
     * Generate interval ID from parameters in the way that parameters oder isn't affect the result
     *
     * @param array $parameters
     *
     * @return string
     */
    private function generateIdFromParameters( array $parameters ): string
    {
        ksort( $parameters );
        
        return md5( implode('', array_merge( array_keys( $parameters ), $parameters  ) ) );
    }
    
    /**
     * Gets interval by given parameters
     *
     * @param $parameters
     *
     * @return CallInterval|array|null
     * @throws \Exception
     */
    private function getIntervalByParameters( $parameters ): null|CallInterval|array
    {
        $id = $this->generateIdFromParameters( $parameters );
            
        return ( new Repository( $this->db, CallInterval::class ) )
            ->read( [ 'id' => $id ] )[0] ?: null;
    }
    
    /**
     * Deletes interval
     *
     * @param CallInterval $interval
     *
     * @return void
     * @throws \Exception
     */
    private function drop( CallInterval $interval ): void
    {
        ( new Repository( $this->db, CallInterval::class ) )
            ->delete( [ 'id' => $interval->getId() ] );
    }
    
    /**
     * Updates or creates interval
     *
     * @param CallInterval|null $interval
     *
     * @return void
     * @throws \Exception
     */
    private function updateInterval( ?CallInterval $interval = null ): void
    {
        $repo = ( new Repository( $this->db, CallInterval::class ) );
        
        if( $interval ){
            $interval->incrementCalls();
        }else{
            /** @var CallInterval $interval */
            $interval = $repo->create(
                [ [
                    'id'       => $this->generateIdFromParameters( $this->parameters ),
                    'start'    => TimeHelper::getIntervalStart( $this->duration ),
                    'calls'    => 1
                ] ],
                true
            )[0];
        }
        
        ( new Repository( $this->db, CallInterval::class ) )
            ->save( $interval );
    }
}