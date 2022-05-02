<?= $this->extend('common/layout') ?>
<?= $this->section('content') ?>


<h2 class="wiki-top-header">
    Translate Gene Model IDs
</h2>
<p>translate Gene IDs between versions of the maize genome</p>

<h2 class="wiki-section-header">
    Input Gene IDs to translate
</h2>
<p>
    Input up to 1,000 gene IDs by pasting them below, or by uploading a text file.
    <br/>
    Any gene IDs that are present in this database will be accepted
</p>


<form action="/translation_tool/translation" method="post", enctype="multipart/form-data">
    <table>
        <tbody>
            <tr>
                <td style="vertical-align:text-top;">
                Enter list
                (<a href="javascript:textarea_example();">Example list</a>)
                <br>
                <textarea style="resize: none;" rows="10" cols="30" name="gm_list" 
                          id="gm_list" placeholder="Enter gene model list here"></textarea>
                </td>

                <td><p style="font-size:20px; font-weight:bold; margin:20px;">OR</p></td>

                <td style="vertical-align:text-top; width:100px">
                    Upload text file (<a href="javascript:file_example();">Example File</a>)
                    <input style="margin-top:70px;" type="file" id="myFile" name="filename">
                </td>
            </tr>
        </tbody>
    </table>
              
    <h2 class="wiki-section-header">
        Select target genome version
    </h2>
    <br/>

    Translate to:<br>
    <select name="trans_to" id="trans_to">
        <option value='all'>All available Maize versions (<?php echo join(",",get_maize_genome_versions())?>)</option>
        <?php foreach (get_maize_genome_versions() as $v) { 
            $desc = describe_maize_genome_version($v);
            echo "<option value='$v'>Maize $v - $desc</option>";
        } ?>
    </select>
    <br><br>
  <input type="submit" value="Submit">

    <?php if( isset($error_message) ){
      echo "<br/><p style='color:red'>$error_message</p>";
    } ?>
    
</form> 

<script>
    var $ = jQuery.noConflict();
    
    function textarea_example(){
        $('#gm_list').val("<?php echo str_replace("\n", "\\n",$input_example); ?>")
    }
    
    function file_example(){
        const a = document.createElement('a');
        const file = new Blob(["<?php echo str_replace("\n", "\\n",$input_example); ?>"], {type: 'text/plain'});

        a.href= URL.createObjectURL(file);
        a.download = 'example.txt';
        a.click();

        URL.revokeObjectURL(a.href);   
    }
    
    $(document).ready(function(){
        $('#nav_about_data').addClass("active");
    })
</script>

<?= $this->endSection() ?>