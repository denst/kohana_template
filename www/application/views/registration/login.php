<div>
    <form action="/login" class="form-horizontal" method="POST">
        
        <?=$form->input('username', '',
                array('class' => 'required', 'placeholder' => 'username'))?>

       <?=$form->password('password', '',
                array('type' => 'password', 'class' => 'required'))?>
        
        <div id="remember">
            <label class="checkbox">
                <input type="checkbox" name="remember" value="true"> Remember me
            </label>
        </div>
        <p>
            <input type="submit" class="pull-right btn btn-large btn-inverse" value="Login" />
        </p>
        <p>
            <i class="icon-user"></i> <a href="<?=URL::base().'resetpassword'?>">Reset Password?</a>
        </p>
        
    </form>
</div>