<?php
namespace App\Controllers;


class InfoController extends BaseController
{

    
    public function downloads()
    {                
        return view('info/downloads', ["title"=>"Grassius Downloads"]);
    }
    
    public function people()
    {                
        return view('info/people', ["title"=>"Grassius people"]);
    }
    
    public function links()
    {                
        return view('info/links', ["title"=>"Grassius links"]);
    }
    
    public function info()
    {                
        return view('info/info', ["title"=>"Grassius Information"]);
    }
    
    public function about()
    {                
        return view('info/about', ["title"=>"Grassius About"]);
    }
}
