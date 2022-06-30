

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
            
            if( !isset($domain_colors) ){
                $domain_colors = [];
            }
            
            $default_colors = [
                '#FFA','#FAF','#AFF'
            ];
            $dc_index = 0;

            $dlhi = 0;
            foreach( $domains as $dom ) { 
                $acc = $dom->{'accession'};
                $acc = explode('.',$acc)[0];
                $name = $dom->{'name'};
                $dstart = $dom->{'start'};
                $dend = $dom->{'end'};
                
                $title = $dom->{'title'}.'<br>coordinates: '.$dstart.' - '.$dend;
                $desc = $dom->{'desc'};
                
                $before_pct = 100*$dstart/$seq_len;
                $during_pct = 100*($dend-$dstart)/$seq_len;
                $after_pct = 100-$before_pct-$during_pct;
                
                if( array_key_exists( $acc, $domain_colors ) ){
                    $color = $domain_colors[$acc];
                } else {
                    $color = $default_colors[ $dc_index % count($default_colors) ];
                    $domain_colors[$acc] = $color;
                    $dc_index += 1;
                }
                
                $dlhi_class = "dlhi_".$dlhi;
                if( $dlhi == 0 ){
                    $dlhi_class .= " dom_legend_hover_selected";
                }
            ?>
                
                
                <tr class="dom_legend_hover <?php echo $dlhi_class; ?>" data-title="<?php echo $title; ?>" data-desc="<?php echo $desc; ?>" data-acc="<?php echo $acc; ?>">
                    <td style="background-color:<?php echo $color;?>"><?php echo $acc; ?></td>
                    <td class="infobox-data">
                        <div class="dom_legend_vis">
                            <span class="vis_before_dom" style="background-color:<?php echo $color;?>; width:<?php echo $before_pct;?>%"></span>
                            <span class="vis_dom" style="background-color:<?php echo $color;?>; width:<?php echo $during_pct;?>%"></span>
                            <span class="vis_after_dom" style="background-color:<?php echo $color;?>; width:<?php echo $after_pct;?>%"></span>
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
            $(".sequence.aa.aa_dom.dom_background").removeClass('blend').show();
        } else {
            $(".sequence.aa.aa_dom").hide();
            $(".sequence.aa.aa_dom.dom_background").removeClass('blend').show();
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