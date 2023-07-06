<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

<?php require "common/subdomain_urls.php";?>

<table class="infobox" style="margin-top:20px;">
   <tbody>
      <tr>
          <th class="infobox-above" colspan="2"><?php echo $full_name; ?></th>
      </tr>
      <tr>
          <th class="infobox-image" colspan="2">
            <?php show_species_infobox_image($species); ?>
            <div class="infobox-caption">
                <?php echo $full_name; ?> plant.
              </div>
          </th>
      </tr>
      <tr>
          <th class="infobox-header" colspan="2">Information</th>
      </tr>
      <tr>
        <td class="infobox-label">Abbreviation:</th>
        <td class="infobox-data"><?php echo $org_details['abbreviation']; ?></th>
      </tr>
      <tr>
        <td class="infobox-label">Genus:</td>
        <td class="infobox-data"><?php echo $org_details['genus']; ?></td>
      </tr>
      <tr>
        <td class="infobox-label">Species:</td>
        <td class="infobox-data"><?php echo $org_details['species']; ?></td>
      </tr>
      <tr>
        <td class="infobox-label">Common Name:</td>
        <td class="infobox-data"><?php echo $org_details['common_name']; ?></td>
      </tr>
      <tr>
        <td class="infobox-label">NCBI Taxonomy ID:</td>
        <td class="infobox-data"><a class='external' target = '_blank' href="https://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=<?php echo $ncbi_id; ?>"> <?php echo $ncbi_id; ?></td>
      </tr>
      <tr>
        <td class="infobox-label">Other:</td>
        <td class="infobox-data"><a class='external' target = '_blank' href="<?php echo $esd_url; ?>"> <?php echo $esd_label; ?></td>
      </tr>
   </tbody>
</table>


<h2 class="wiki-top-header"><?php echo $full_name; ?></h2>
<br>

<?php
    if( $species == "Maize" ){
        require_once "common/maize_version_controls.php";
    }
    if( $species_version == "_" ){
        $label_prefix = "Download";
    } else {
        $label_prefix = "Download $species_version";
    }
    echo "
    
        <a id='download_gene_list' 
            href='/download_species_gene_list/$species/$species_version'
            >$label_prefix gene list (csv)</a>

        <br>

        <a id='download_seqs' 
            href='/download_sequences_csv/$species/$species_version'
            >$label_prefix sequences (csv)</a>

        <br>

        <a id='download_fasta_seqs' 
            href='/download_sequences_fasta/$species/$species_version'
            >$label_prefix sequences (fasta)</a>
    ";
?>



<p><?php echo $org_details['comment']; ?></p>


<a href="/browsefamily/<?php echo $species; ?>/TF" >
    <h2 class="wiki-section-header" style="position:relative; height:50px;">
        <img
          src="/images/diagram-3.svg"
          style="height:50px;"
        /> 
        <?php echo $species; ?> TFDB
        <small>[browse transcription factors]</small>
    </h2>
</a>
<p>Transcription factors, defined here specifically as proteins containing domains that suggest sequence-specific DNA-binding activities, are classified based on the presence of 50+ conserved domains. Links to resources that provide information on mutants available, map positions or putative functions for these transcription factors are provided. Transcription factors are being named using a standard nomenclature that will simplify references to them as well as comparative analyses between the grasses.</p>


<a href="/browsefamily/<?php echo $species; ?>/Coreg" >
    <h2 class="wiki-section-header" style="position:relative; height:50px;">
        <img
          src="/images/diagram-2-fill.svg"
          style="height:50px;"
        /> 
        <?php echo $species; ?> CoregDB
        <small>[browse coregulators]</small>
    </h2>
</a>
<p>These proteins are broadly defined as transcriptional regulators that either act by interacting with transcription factors or as chromatin modifiers restricting or releasing DNA accessibility. Thus, GrassCoRegDB includes proteins with functions such as covalent histone modification (acetylation, methylation, phosphorylation, ubiquitination and sumoylation), as members of the ATP dependent chromatin remodeling complexes, and as histone chaperones, among others. The specific role of many of these proteins in transcription is still unknown. In some cases the community has already set-up some rules for systematic naming of those proteins, in these a protein name is reported in Grassius. For proteins lacking a systematic name, loci identifiers or common names are used instead. As identification of proteins with roles in transcriptional regulation is a growing research field, GrassCoRegDB will be frequently updated to keep pace with the literature. For this set of proteins the same curation efforts as for other Grassius collections are applied and links to other Grassius resources such as the TFome collection are available.</p>


<?php if( $species == 'Maize' ){ ?>

    <h2 class="wiki-section-header" style="position:relative; height:50px;">
        <img
          src="/images/tools.svg"
          style="height:50px;"
        /> 
        <?php echo $species; ?> Tools
    </h2>

    <p></p>

  <div class="row" >
    <div class="tools-col col-sm-9" >
      <div class="tools-wrapper" >
        <div class="row list-group" >
          <a
            href="/translation_tool"
            class="gramene-tool col-md-6 list-group-item"
          >
            <div class="media" >
              <div class="media-middle media-left" >
                  <img src="/images/translate.svg">
              </div>
              <div class="media-middle gramene-tool-text media-body">
                <h4 class="media-heading" >
                  Translation Tool
                </h4>
                <p class="gramene-tool-desc" >
                  Translate Gene Model IDs
                </p>
              </div>
            </div>
          </a>
          <a
            href="<?php echo $blast_tool_url; ?>"
            class="gramene-tool col-md-6 list-group-item"
          >
            <div class="media" >
              <div class="media-middle media-left" >
                  <img src="/images/search.svg">
              </div>
              <div class="media-middle gramene-tool-text media-body">
                <h4 class="media-heading" >
                  BLAST
                </h4>
                <p class="gramene-tool-desc" >
                  Homology Search Tool
                </p>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>

<?php } ?>
 
<script>
    var $ =jQuery.noConflict();

    $(document).ready(function(){
        $("#nav_access").addClass("active");
        
        
        <?php if( $species=='Maize' ){ ?>
        
            // update download links to reflect selected maize version
            function update_download_links( new_version_id ) {

                $("#download_gene_list").html("Download " + new_version_id + " gene list (csv)")
                $("#download_gene_list").attr("href", "/download_species_gene_list/<?php echo $species; ?>/" + new_version_id )
                $("#download_seqs").html("Download " + new_version_id + " sequences (csv)")
                $("#download_seqs").attr("href", "/download_sequences_csv/<?php echo $species; ?>/" + new_version_id ) 
                $("#download_fasta_seqs").html("Download " + new_version_id + " sequences (fasta)")
                $("#download_fasta_seqs").attr("href", "/download_sequences_fasta/<?php echo $species; ?>/" + new_version_id)
            }
        
            // add listener to detect changed species version
            version_change_listeners.push(update_download_links);
    
        <?php } ?>
    });
</script>


<?= $this->endSection() ?>