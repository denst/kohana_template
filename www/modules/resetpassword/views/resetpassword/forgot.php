<?php
$form = new Helper_Appform();
    if(isset($errors)){
        $form->post = $post;
        $form->errors = $errors;
    }
?>
<div class="widget container-narrow">
    <div class="widget-header">
        <i class="icon-user"></i>
        <h5>Forgot password?</h5>
    </div> 
        <form action="/resetpassword" method="POST">
            <?=$form->input('email', '',
                        array('class' => 'required', 'placeholder' => 'email'))?>  
            <div style="padding:25px;" class="widget-body clearfix">
					
            <button id="send" type="submit" class="btn pull-right">Send</button>
        </form>
    </div>  
</div>  
    
