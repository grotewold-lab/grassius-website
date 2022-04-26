<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>

    <br>
    <h2>About GRASSIUS
    </h2>
    <br>      
    <p align="justify">GRASSIUS provides a public web resource composed by a collection of databases, computational and experimental resources that relate to the control of gene expression in the grasses, and their relationship with agronomic traits. As knowledge on the interactions of transcription factors (TFs) and cis-regulatory elements in the promoters of the genes that they regulate continues to accumulate, the information is acquired by GRASSIUS, either through contributions by the community, or by literature analysis. The overarching objective of GRASSIUS is to provide a "one-stop" resource that will facilitate research and communication within the plant community with regards to genome-wide regulation of gene expression processes.
    </p>
    <p align="justify">GRASSIUS currently contains regulatory information on maize, rice, sorghum, sugarcane and brachypodium. However, as genome sequencing and annotation efforts in other grasses continue to progress, GRASSIUS will expand to include information on them as well. The success of this endeavor is largely dependent on 
    community contributions thus we hope to hear from you soon.
    </p>
    <p align="justify">GRASSIUS integrates information from three large databases. 
      <a href="grasstfdb.php">GrassTFDB
      </a> contains information on TFs, their DNA-binding properties and the genes that they have been shown to bind/regulate. TFs are divided into 50 families based on unique structural characteristics. Information on TFs can be accessed individually by plant (i.e., accessing 
      <a href="browsefamily.php?sp=Maize&typ=transcription_factor">MaizeTFDB
      </a>, 
      <a href="browsefamily.php?sp=Rice&typ=transcription_factor">RiceTFDB
      </a>, 
      <a href="browsefamily.php?sp=Sorghum&typ=transcription_factor">SorghumTFDB
      </a> or 
      <a href="browsefamily.php?sp=Sugarcane&typ=transcription_factor">CaneTFDB
      </a>), or by TF family (e.g., by searching GrassTFDB for a particular TF family, for example 
      <a href="family.php?family=MYB&species=Maize&type=transcription_factor">MYB
      </a>).
      GrassTFDB also contains information on the availability of clones for particular TFs, as part of the 
      <a href="tfomecollection.php">TFome Collection
      </a>, which contains a constantly increasing number of TFs in recombination-ready vectors. 
      <a href="grasspromdb.php">GrassPROMDB
      </a> contains information on promoter sequences across the four grasses, and when available, information on what promoter regions/sequences are important for gene expression. Integrating the information contained in GrassTFDB and GrassPROMDB will be GrassREGNET, a database that will allow the visualization of regulatory motifs and networks across the grasses.
    </p>
    <p align="justify">
      Gene models for existing transcription factors are updated. As of now, GRASSIUS contains
      <li>Maize genome release 5b.60
      </li> 
      <li>Rice genome release 6.1 (
        <i>O. sativa japonica
        </i>)
      </li>
      <li>Sorghum genome release 1.4
      </li>
      <li>Brachypodium genome release 1.2
      </li>
    </p>    

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_about').addClass("active")
    })
</script>

<?= $this->endSection() ?>