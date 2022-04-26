<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;


class AdminController extends BaseController
{
    
    
    /**
     * Show login view
     */
    public function login()
    {
        return view("admin/login");
    }
    
    
    /**
     * recieve post request from login view
     */
    public function attempt_login()
    {
        $password = $this->request->getPost('password');
        if( md5($password) === "7f115193d82363a44b2f7a33b0201e96" ) {
            echo "correct password!";
		    $this->session->set('isAdmin', true);
            return redirect("/");
        } else {
            $this->session->setFlashdata('login_message', 'Incorrect password');
            return redirect()->back();
        }
    }
    
    
    /**
     * 
     */
    public function logout()
    {
        $this->session->set('isAdmin', false);
        $this->session->setFlashdata('message', 'Successfully logged out');
        return redirect()->back();
    }
    
    
    /**
     * show edit family view
     */
    public function edit_family( $family_part1, $family_part2=null )
    {
        // workaround bug in routes
        // accept family names including up to one slash
        if( is_null($family_part2) or (trim($family_part2) == '') ) {
            $familyname = $family_part1;
        } else {
            $familyname = $family_part1."/".$family_part2;
        }
            
        // lookup current description
        $sql= "SELECT description FROM family WHERE familyname = :familyname:";
        $query=$this->db->query($sql,[
            'familyname'   => $familyname
        ]);          
        $famresult = $query->getRowArray();
        
        // show view
        $data["species"] = $familyname;
        $data["familyname"] = $familyname;
        $data["description"] = $famresult["description"];
        return view("admin/edit_family", $data);
    }
    
    /**
     * recieve post from edit family view
     */
    public function post_edit_family( $family_part1, $family_part2=null )
    {
        // workaround bug in routes
        // accept family names including up to one slash
        if( is_null($family_part2) or (trim($family_part2) == '') ) {
            $familyname = $family_part1;
        } else {
            $familyname = $family_part1."/".$family_part2;
        }
        
        
        if( user_is_admin() ) {
        
            $new_description = $this->request->getPost('description');

            $sql= "UPDATE family SET description=:description: WHERE familyname=:familyname:";
            $this->db->query($sql,[
                'description'  => $new_description,
                'familyname'   => $familyname
            ]);                  
        }
        
        //attempt to get most recent species from session vars
        if( $this->session->has('species') ) {
            $species = $this->session->get("species");
        } else {
            $species = "Maize";
        }
        
        // redirect to a family page
        return redirect() ->to("family/".$species."/".$familyname);
    }
}
