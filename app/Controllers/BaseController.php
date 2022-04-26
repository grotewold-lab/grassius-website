<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ["filesystem","util","queries","view"];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->db =  \Config\Database::connect();
        
        $this->session = \Config\Services::session();
    }
    
    
    
    
    /**
     * get an array of session variables that should be modifiable through the front-end
     */
    private function get_allowed_keys_values()
    {
        return [
            
            # key => allowed values (default value first)            
            "aa_colors" => ['none','ss','dom'],
            
            "Maize_version" => ['v5','v3','v4']
            
        ];
    }
    
    
        
    /**
     * Set the value of a sessions variable if it is allowed
     */
    public function set_session_var( $key, $value )
    {
        $akv = $this->get_allowed_keys_values();
        
        if( array_key_exists($key,$akv) ) {
            if( in_array($value,$akv[$key]) ) {
                $this->session->set($key,$value);
            }
        }
    }
    
    /**
     * Get the value of a session variable if it is allowed
     */
    public function get_session_var( $key )
    {
        $akv = $this->get_allowed_keys_values();
        $s = $this->session;
        
        if( array_key_exists($key,$akv) ) {
            if( (!$s->has($key)) or (!in_array($s->get($key),$akv[$key])) ) {
                $s->set($key, $akv[$key][0]);
            }
            return $s->get($key);
        }
    }
}
