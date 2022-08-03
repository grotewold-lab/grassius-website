<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>
        
        
<br>
<h2 class="wiki-top-header">
    GRASSIUS Contributors
</h2>
<br>
<br>
Current contributors (in alphabetical order)
<ul>
    <li>John Gray (University of Toledo, OH)</li>
    <li>Erich Grotewold (Michigan State University, MI)</li>
    <li>Hadi Nayebi (Michigan State University, MI)</li>
    <li>Oliver Tessmer (Michigan State University, MI)</li>
</ul>

<br>
Former Contributors (in alphabetical order)

<ul>
    <li>Jeffrey Campbell</li>
    <li>Ramana Davuluri</li>
    <li>Eric Easley</li>
    <li>Evans Kataka</li>
    <li>Eric Maina</li>
    <li>Maria Katherine Mejia-Guerra</li>
    <li>Milton Yutaka Nishiyama Jr.</li>
    <li>Alper Yilmaz</li>
    <li>Wilberforce Ouma</li>
    <li>Saran Palaniswamy</li>
    <li>Glaucia Souza</li>
    <li>Hao Sun</li>
</ul>

<br>
<h2 class="wiki-section-header">
    Acknowledgments
</h2>  
<p>
Oliver Tessmer is the webmaster. We are grateful for comments and insights provided by Plant
Genome Project advisory committee, Drs. C. Robin Buell (Michigan State University), Doreen
Ware (Cold Spring Harbor Laboratory), Tom Brutnell (Cornell University) and Patty Springer
(University of California ,Riverside).
</p>
<p>
We wish also to thank Alper Yilmaz, Ramana Davuluri and Saran Palaniswamy for the initial
concept of GRASSIUS, Bernardo Garcia Fuentes for his help in creating and designing the MySQL
database and page design, Milton Yutaka Nishiyama Jr. for his help in populating the
transcription factor data, Dr. Daniel Janies and Eric Easley for their help in construction of
phylogenetic trees. Drs. John Gray and Guo-Liang Wang for help providing TFome collection
data. Dr. Lee Pratt for his help in sorghum data. Sugarcane transcription factor data partially
provided by Dr. Glaucia Souza&#39;s lab. Additional thanks to Andrew Reed and Jeffrey Campbell for
additional functionality and updates to the Grassius website.
</p>
        

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_about').addClass("active")
    })
</script>

<?= $this->endSection() ?>















