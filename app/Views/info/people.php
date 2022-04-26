<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>
        
        <div class="contenttop">
            <p class="description">
                    <br>
                  <h2>People behind GRASSIUS</h2>
                <ul >
                <li> Erich Grotewold (Michigan State University,MI)</li>
                <ul>
                    
                    <li>Eric Maina (Michigan State University,MI)</li>
                    <li>Fabio Gomez Cano  (Michigan State University,MI)</li>
                    <li>Steve Lundback (Michigan State University,MI)</li>
                </ul>
                    <li>John Gray (University of Toledo, OH)</li>
                </ul>
                
                
                <hr>
                
                <h2>Former Contributors</h2>
                <ul >
                    <li>Alper Yilmaz</li>
                    <li>Wilberforce Ouma</li>    
                    <li>Maria Katherine</li>
                    <li>Jeffrey Campbell </li>
                    <li>Evans Kataka </li>
                        <li> Daniel Janies </li>
                    <li>Eric Easley</li>
                    <li> Guo-Liang Wang </li>
                    <li>Xinli Sun </li>
                    <li>Glaucia Souza </li>
                    <li>Milton Yutaka Nishiyama Jr. </li>
                    <li> Ramana Davuluri</li>
                    <li>Hao Sun </li>
                    <li>Saran Palaniswamy </li>
                </ul>
                <p></p>
                
                <hr>
                
                      <h2>Acknowledgments</h2>
                      <br>
                <p align="justify">Eric Maina is the webmaster. We are grateful for comments and insights provided by Plant Genome Project advisory committee, <a href="http://www.plantbiology.msu.edu/faculty/faculty-research/robin-buell/">Drs. C. Robin Buell</a> (Michigan State University), <a href="http://www.cshl.edu/Faculty/ware-doreen-associate-professor.html">Doreen Ware</a> (Cold Spring Harbor Laboratory), <a href="http://vivo.cornell.edu/display/individual5030">Tom Brutnell</a> (Cornell University) and <a href="http://ucanr.edu/find_people/academic_directory/?facultyid=2146">Patty Springer</a> (University of California ,Riverside).
                <br/><br/>
                We wish also to thank Alper Yilmaz, Ramana Davuluri and Saran Palaniswamy for the initial concept of GRASSIUS, Bernardo Garcia Fuentes for his help in creating and designing the MySQL database and page design, Milton Yutaka Nishiyama Jr. for his help in populating the transcription factor data, Dr. Daniel Janies and Eric Easley for their help in construction of phylogenetic trees. Drs. John Gray and Guo-Liang Wang for help providing TFome collection data. Dr. Lee Pratt for his help in sorghum data. Sugarcane transcription factor data partially provided by Dr. Glaucia Souza's lab.  Additional thanks to Andrew Reed and Jeffrey Campbell for additional functionality and updates to the Grassius website. 
                 </p>
        </p>
        </div>      

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function(){
        $('#nav_about').addClass("active")
    })
</script>

<?= $this->endSection() ?>















