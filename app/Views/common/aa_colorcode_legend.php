
<?php

# master list from http://mmcif.rcsb.org/dictionaries/mmcif_pdbx_v50.dic/Items/_struct_conf_type.id.html
$all_legend_data = [
    "BEND" => "region with high backbone curvature without specific hydrogen bonding",
    "HELX_LH_27_P" => "left-handed 2-7 helix",
    "HELX_LH_3T_P" => "left-handed 3-10 helix",
    "HELX_LH_AL_P" => "left-handed alpha helix",
    "HELX_LH_A_N" => "left-handed A helix (nucleic acid)",
    "HELX_LH_B_N" => "left-handed B helix (nucleic acid)",
    "HELX_LH_GA_P" => "left-handed gamma helix",
    "HELX_LH_N" => "left-handed helix with type not specified(nucleic acid)",
    "HELX_LH_OM_P" => "left-handed omega helix",
    "HELX_LH_OT_N" => "left-handed helix with type that does notconform to an accepted category (nucleicacid)",
    "HELX_LH_OT_P" => "left-handed helix with type that does notconform to an accepted category",
    "HELX_LH_P" => "left-handed helix with type not specified(protein)",
    "HELX_LH_PI_P" => "left-handed pi helix",
    "HELX_LH_PP_P" => "left-handed polyproline helix",
    "HELX_LH_Z_N" => "left-handed Z helix (nucleic acid)",
    "HELX_N" => "helix with handedness and type not specified(nucleic acid)",
    "HELX_OT_N" => "helix with handedness and type that do notconform to an accepted category (nucleicacid)",
    "HELX_OT_P" => "helix with handedness and type that do notconform to an accepted category",
    "HELX_P" => "helix with handedness and type not specified(protein)",
    "HELX_RH_27_P" => "right-handed 2-7 helix",
    "HELX_RH_3T_P" => "right-handed 3-10 helix",
    "HELX_RH_AL_P" => "right-handed alpha helix",
    "HELX_RH_A_N" => "right-handed A helix (nucleic acid)",
    "HELX_RH_B_N" => "right-handed B helix (nucleic acid)",
    "HELX_RH_GA_P" => "right-handed gamma helix",
    "HELX_RH_N" => "right-handed helix with type not specified(nucleic acid)",
    "HELX_RH_OM_P" => "right-handed omega helix",
    "HELX_RH_OT_N" => "right-handed helix with type that does notconform to an accepted category (nucleicacid)",
    "HELX_RH_OT_P" => "right-handed helix with type that does notconform to an accepted category",
    "HELX_RH_P" => "right-handed helix with type not specified(protein)",
    "HELX_RH_PI_P" => "right-handed pi helix",
    "HELX_RH_PP_P" => "right-handed polyproline helix",
    "HELX_RH_Z_N" => "right-handed Z helix (nucleic acid)",
    "OTHER" => "secondary structure type that does not conform to an accepted category, random coil",
    "STRN" => "beta strand",
    "TURN_OT_P" => "turn with type that does not conform to anaccepted category",
    "TURN_P" => "turn with type not specified",
    "TURN_TY1P_P" => "type I prime turn",
    "TURN_TY1_P" => "type I turn",
    "TURN_TY2P_P" => "type II prime turn",
    "TURN_TY2_P" => "type II turn",
    "TURN_TY3P_P" => "type III prime turn",
    "TURN_TY3_P" => "type III turn",
    "UNDETERMINED" => "no data available"
];
    
# pick a subset of the data above
$keys_to_show = ["BEND","HELX_LH_PP_P","HELX_RH_3T_P","HELX_RH_AL_P","HELX_RH_PI_P","STRN","TURN_TY1_P","UNDETERMINED"];

?>



<div class="aa aa_ss">
    <table class="ss_legend_infobox">
        <tbody>
            <tr>
                <th class="infobox-header" colspan="2">Secondary Structure Color Code</th>
            </tr>
            <?php foreach( $keys_to_show as $key ) { 
                $value = $all_legend_data[$key];
            ?>
                
                
                <tr>
                    <td class="ss_<?php echo $key; ?>" style="font-weight:bold"><?php echo $key; ?></td>
                    <td class="infobox-data"><?php echo $value; ?></td>
                </tr>    
            

            <?php } ?>
        </tbody>
    </table>
</div>