<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

            
        
        <div class="contenttop"><br>
                                          <h2>Download GRASSIUS Data</h2> <br>
                                          
                                                
<table class=tfome>
<th>Download link </th><th>Description</th>
<tr ><td><a href="download/Grassis_RegNet.xls">Grassius_RegNet</a></td><td >All TF interactions based on experimental data</td></tr>
<tr ><td><a href="download/maize_all_nt.fasta">Maize_all_nucleotide.fasta</a></td><td >Nucleotide sequence of all maize transcription factors</td></tr>
<tr ><td><a href="download/maize_all_pep.fasta">Maize_all_peptide.fasta</a></td><td >Peptide sequence of all maize transcription factors </td></tr>
<tr ><td><a href="download/rice_all_nt.fasta">Rice_all_nucleotide.fasta</a></td><td >Nucleotide sequence of all rice transcription factors</td></tr>
<tr ><td><a href="download/rice_all_pep.fasta">Rice_all_peptide.fasta</a></td><td >Peptide sequence of all rice transcription factors  <br></td></tr>
<tr ><td><a href="download/sorghum_all_nt.fasta">Sorghum_all_nucleotide.fasta</a></td><td >Nucleotide sequence of all sorghum transcription factors</td></tr>
<tr ><td><a href="download/sorghum_all_pep.fasta">Sorghum_all_peptide.fasta</a></td><td >Peptide sequence of all sorghum transcription factors </td></tr>
<tr ><td><a href="download/sugarcane_all_nt.fasta">Sugarcane_all_nucleotide.fasta</a></td><td >Nucleotide sequence of all sugarcane transcription factors </td></tr>
<tr ><td><a href="download/sugarcane_all_pep.fasta">Sugarcane_all_peptide.fasta</a></td><td >Peptide sequence of all sugarcane transcription factors</td></tr>
</table>





</div>
                    

        
        <div id="content_bottom">
            
        </div>        
        
            
<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_downloads').addClass("head")
    })
</script>

<?= $this->endSection() ?>