<p>Hello <?=Text::ucfirst($user->username)?>, 
    you sent a request for an account reset password on <?='http://'.$_SERVER['HTTP_HOST']?>
</p>
<p>Please click or copy this link 
    <a href="<?=$link_for_reset_password?>"><?=$link_for_reset_password?></a> 
    to set a new password
</p>
<p>Thank you for using <?='http://'.$_SERVER['HTTP_HOST']?></p>