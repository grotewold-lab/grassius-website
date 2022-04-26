<?php

namespace App\Controllers;

class SpeciesportalController extends BaseController
{

    
    public function index( $species)
    {
        // get species name in two forms 
        list($species,$new_species) = parse_species($species);
        
        // get the second part of the species name
        $cies = explode(" ", $new_species)[1];
        
        // lookup organism details
        $org_details = $this->db->table("organism org")
            ->where('org.species', $cies)->get()->getRowArray();
        
        // get a link to an eternal species-specific database
        [$esd_url, $esd_label] = get_external_species_db($species);
        $data["esd_url"] = $esd_url;
        $data["esd_label"] = $esd_label;
        
        $data["ncbi_id"] = get_ncbi_taxonomy_id($species);
        $data["org_details"] = $org_details;
        $data["full_name"] = $org_details['genus'].' '.$org_details['species'];
        $data["species"] = $species;
        $data["title"] = $species." Portal";
        return View("species_portal", $data);
    }
}
