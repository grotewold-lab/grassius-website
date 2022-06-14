

<div class="aa aa_dom">
    <table class="ss_legend_infobox">
        <tbody>
            <tr>
                <th class="infobox-header" colspan="2">Domains Present</th>
            </tr>
            <?php 
            
            if( !isset($domains) ){
                $domains = [];
            }

            $dlhi = 0;
            foreach( $domains as $dom ) { 
                $acc = $dom->{'accession'};
                $name = $dom->{'name'};
                $dstart = $dom->{'start'};
                $dend = $dom->{'end'};
                
                $before_pct = 100*$dstart/$seq_len;
                $during_pct = 100*($dend-$dstart)/$seq_len;
                $after_pct = 100-$before_pct-$during_pct;
                
                $dcolor_class = 'do_'.($dlhi % 6);
                
                $dlhi_class = "dlhi_".$dlhi;
                if( $dlhi == 0 ){
                    $dlhi_class .= " dom_legend_hover_selected";
                }
            ?>
                
                
                <tr class="dom_legend_hover <?php echo $dlhi_class; ?>" data-title="<?php echo $dom->{'title'}; ?>" data-desc="<?php echo $dom->{'desc'}; ?>" data-acc="<?php echo $acc; ?>">
                    <td class="<?php echo $dcolor_class; ?>"><?php echo $acc; ?></td>
                    <td class="infobox-data">
                        <div class="dom_legend_vis">
                            <span class="vis_before_dom <?php echo $dcolor_class; ?>" style="width:<?php echo $before_pct;?>%"></span>
                            <span class="vis_dom <?php echo $dcolor_class; ?>" style="width:<?php echo $during_pct;?>%"></span>
                            <span class="vis_after_dom <?php echo $dcolor_class; ?>" style="width:<?php echo $after_pct;?>%"></span>
                        </div>
                    </td>
                </tr>    
            

            <?php 
                $dlhi += 1;
            } 
            ?>
            
            <tr class="dom_legend_hover dlhi_all">
                <td colspan="2">
                    Hover here to show all domains
                </td>
            </tr>
        </tbody>
    </table>
</div>


<script>
    var $ = jQuery.noConflict();
        
    // update display
    function update_dlh(dlhi){
        
        if( dlhi == "all" ){
            $(".sequence.aa.aa_dom").addClass('blend').show();
            $(".sequence.aa.aa_dom.dom_foreground").removeClass('blend').show();
        } else {
            $(".sequence.aa.aa_dom").hide();
            $(".sequence.aa.aa_dom.dom_"+dlhi).removeClass('blend').show();
        }
    }
    
    $(document).ready(function(){  
        
        // convenience function
        function get_dlhi_class(elem){
            return elem.attr('class').split(" ").filter(function (c) {
              return c.startsWith("dlhi_");
            })[0];
        }
        
        // respond to hovering on legend
        $(".dom_legend_hover").mouseover(function(){
            $(".dom_legend_hover").removeClass("dom_legend_hover_selected");
            $(this).addClass("dom_legend_hover_selected");
            var dlhi = get_dlhi_class( $(this) ).substring(5);
            update_dlh(dlhi);
        });
        
        update_dlh("0");
    })
</script>