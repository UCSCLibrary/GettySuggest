<?php echo head(array('title' => 'Getty Vocabulary Suggest')); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
jQuery(document).ready(function() {
    jQuery('#element-id').change(function() {
        jQuery.post(
            <?php echo js_escape(url('getty-suggest/index/suggest-endpoint')); ?>, 
            {element_id: jQuery('#element-id').val()}, 
            function(data) {
                jQuery('#suggest-endpoint').val(data);
            }
        );
    });
});
//]]>
</script>
<?php echo flash(); ?>
<form method="post" action="<?php echo url('getty-suggest/suggest/add'); ?>">
<section class="seven columns alpha">
    <div class="field">
        <div id="element-id-label" class="two columns alpha">
            <label for="element-id"><?php echo __('Element'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?php echo __('Select an element to assign it ' 
            . 'a Getty Collection authority/vocabulary. Elements already assigned ' 
            . 'an authority/vocabulary are marked with an asterisk (*).'); ?></p>
            <?php echo $this->formSelect('element_id', null, array('id' => 'element-id'), $this->form_element_options) ?>
        </div>
    </div>
    <div class="field">
        <div id="suggest-endpoint-label" class="two columns alpha">
            <label for="suggest-endpoint"><?php echo __('Authority/Vocab'); ?></label>
        </div>
<div class="inputs five columns omega">
    <p class="explanation"><?php echo __('Enter a Getty collection authority/vocabulary ' 
    . 'to enable the autosuggest feature for the above element. To disable ' 
    . 'the feature just deselect the option. For more information about the ' 
    . 'authorities and vocabularies available at the Getty Collection see ' 
    . '%shttp://getty.edu%s', '<a href="http://www.getty.edu/research/tools/vocabularies/lod/index.html" target="_blank">', '</a>'); ?></p>
            <?php echo $this->formSelect('suggest_endpoint', null, array('id' => 'suggest-endpoint'), $this->form_suggest_options); ?>
        </div>
    </div>
</section>
<section class="three columns omega">
    <div id="edit" class="panel">
        <?php echo $this->formSubmit(
            'add-element-suggest', 
            'Add Suggest', 
            array('class' => 'submit big green button')
        ); ?>
    </div>
</section>
 <?php echo $this->csrf; ?>
</form>
<section class="ten columns alpha">
    <h2><?php echo __('Current Assignments'); ?></h2>
    <?php if ($this->assignments): ?>
    <table>
        <thead>
        <tr>
            <th><?php echo __('Element Set'); ?></th>
            <th><?php echo __('Element'); ?></th>
            <th><?php echo __('Authority/Vocabulary'); ?></th>
            <th style="width:19%;"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($this->assignments as $assignment): ?>
        <tr>
            <td class="element_set_name"><?php echo $assignment['element_set_name']; ?></td>
            <td class="element_name"><?php echo $assignment['element_name']; ?></td>
            <td class="authority_vocabulary"><?php echo $assignment['authority_vocabulary']; ?></td>
            <td><button id="<?php echo $assignment['suggest_id'];?>" class="gv-edit-suggest-button" style="margin:0px 5px 0px 0px;">Edit</button>

<form style="display:inline;" method="post" action="<?php echo url('getty-suggest/suggest/delete/suggest_id/'.$assignment['suggest_id']); ?>">
<?php echo $this->csrf;?>
<button type='submit' style="margin:0px;">
     Delete
</button>
</form>
</td>

        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p><?php echo __('There are no suggest assignments.'); ?></p>
    <?php endif; ?>
</section>
<script>
    jQuery('#suggest-endpoint option[value="cona"]').attr('disabled','disabled');
jQuery(document).ready(function() {
    var gvflag=false;
    jQuery('.gv-edit-suggest-button').click(function(e){
        if(gvflag) {
            if(jQuery(this).attr("id")==gvflag) {
                var element_id = jQuery('#edit-element-id').val();
                var vocab_id = jQuery('#edit-vocab-id').val();
                var form = jQuery("<form method='post' action='<?php echo url('getty-suggest/suggest/edit/suggest_id/');  ?>"+gvflag+"'></form>");
                form.append('<input type="hidden" name="element_id" value="'
+element_id+'" />');
                form.append('<input type="hidden" name="suggest_endpoint" value="'+vocab_id+'" />');
                form.append('<?php echo trim($this->csrf);?>');
                form.appendTo(jQuery('body'));
                form.submit();
            } else {
                alert('Please edit one suggest assignment at a time');
            }
            //prepare and submit form with params from boxes created below
        }else{
            var form_element_options = <?php echo json_encode($this->formSelect('element_id', null, array('id' => 'edit-element-id'), $this->form_element_options)); ?>;
            var suggest_options = <?php echo json_encode($this->formSelect('vocab_id', null, array('id' => 'edit-vocab-id'), $this->form_suggest_options)); ?>;
            jQuery(this).parent().parent().children('.element_set_name').html(form_element_options);
            jQuery(this).parent().parent().children('.element_name').html('');
            jQuery(this).parent().parent().children('.authority_vocabulary').html(suggest_options);
            jQuery(this).html("Save");
            jQuery(this).css('float','left');
            
            jQuery('#edit-suggest-id option[value="cona"]').attr('disabled','disabled');
            jQuery("#edit-element-id").css('max-width','250px');
            gvflag=jQuery(this).attr("id");
        }
        jQuery('#edit-vocab-id option[value="cona"]').attr('disabled','disabled');
    });
});
    
</script>
<?php echo foot(); ?>