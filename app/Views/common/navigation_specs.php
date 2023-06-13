<?php

    require "subdomain_urls.php";


    # this file contains specifications for links that will appear
    #  - in the navigation bar at the top of the screen
    #  - in the footer at the bottom of the screen


    # display text for top-level menu headers
    # keys are used to relate with the other specs below
    # keys are also used as html element IDs for the headers

    $all_header_labels = array(
        "nav_about" => "About Grassius",
        "nav_access" => "Access Data",
        "nav_tools" => "Tools"
    );



    # target URLs for when the user clicks on a top-level menu header
    # keys must match $header_labels above

    $all_header_links = array(
        "nav_about" => "/about",
        "nav_access" => "/",
        "nav_tools" => "/"
    );



    # list of sub-menus and links
    # top-level keys must match $header_labels above

    $specs = array(
        "nav_about" => array(
            
            "Learn About Grassius" => array(
                
                # display text => url
                "About" => "/about",
                "People" => "/people",
                "Links" => "/links"
            ),
        ),
        
        "nav_access" => array(
            
            "Species" => array(
                "Maize" => "/species/Maize",
                "Rice" => "/species/Rice",
                "Sorghum" => "/species/Sorghum",
                "Sugarcane" => "/species/Sugarcane",
                "Brachypodium" => "/species/Brachypodium"
            ),
            
            "Databases" => array(
                "CoregDB" => "/browsefamily/Maize/Coreg",
                "TFDB" => "/browsefamily/Maize/TF",
                "Protein-DNA Interactions" => "/pdinetwork",
                "Maize TFome Collection" => "/tfomecollection",
                "Rice TFome Collection" => "/RiceTfome",
            )
        ),
        
        "nav_tools" => array(
            
            "Tools" => array(
                "Translation Tool" => "/translation_tool",
                "BLAST" => $blast_tool_url,
                "Custom Family" => "/customfamily/Maize"
            )
        )
    );

?>

