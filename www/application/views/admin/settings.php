<div class="row-fluid" id="settings_anchor">
    <div class="widget widget-padding span12">
        <div class="widget-header">
            <i class="icon-cogs"></i><h5>Settings</h5>
            <div class="widget-buttons">
                <a data-original-title="" href="#" data-title="Collapse" data-collapsed="false" class="tip collapse"></a>
            </div>
        </div>
        <form action="<?URL::base()?>settings" method="POST" enctype="multipart/form-data">
            <div class="widget-body">
                <div class="widget-forms clearfix">
                    <div class="form-horizontal">

                        <legend>Main Settings</legend>

                        <div class="control-group">
                            <label class="control-label">Site Name:</label>
                            <div class="controls">
                                <input type="text" name="site_name" class="span4 inputs" placeholder="Site Name"
                                       value="<?=isset($site_name)? $site_name: ''?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Site Logo:</label>
                            <div class="controls">
                                <div class="fileupload fileupload-new" data-provides="fileupload"
                                     style="margin: 0">
                                    <? $settings = Settings::instance()->get_settings();?>
                                    <div id="logo_previev" class="thumbnail" style="max-width: 200px; max-height: 150px; line-height: 10px;">
                                        <img src="<?=URL::base().'img/'.$settings['logo']?>" style="max-height: 150px;">
                                    </div>
                                    <div class="fileupload-new thumbnail" style="width: 200px; height: 150px; display: none"><img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image" /></div>
                                    <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                    <div>
                                        <span class="btn btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span><input id="upload_logo" type="file" name="banner" /></span>
                                        <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
                                    </div>
                                    <? if(isset($errors['image'])):?>
                                         <div class="label_error"><?=$errors['image']?></div>
                                    <? endif?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-horizontal">

                        <legend>Email Settings</legend>

                        <div class="control-group">
                            <label class="control-label">Site Email:</label>
                            <div class="controls">
                                <input type="text" class="span4 inputs" name="site_email" placeholder="Site Email"
                                       value="<?=isset($site_email)? $site_email: ''?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Mandrill API Key:</label>
                            <div class="controls">
                                <input type="text" class="span4 inputs" name="mandrillapikey" placeholder="Mandrill API Key"
                                       value="<?=isset($mandrillapikey)? $mandrillapikey: ''?>">
                            </div>
                        </div>

                    </div>  

                    <div class="form-horizontal">

                        <legend>Payment Settings</legend>

                        <div class="control-group">
                            <label class="control-label">Payment Method:</label>
                            <div class="controls">
                                <div>
                                    <input type="radio" name="payments" style="margin: 0" 
                                        <?=(isset($payments) AND $payments == "real")? 'checked="checked"': ''?>
                                           value="real">
                                    <span>real</span>
                                </div>
                                <div style="margin-top: 10px;">
                                    <input type="radio" name="payments" style="margin: 0"
                                        <?=(isset($payments) AND $payments == "sandbox")? 'checked="checked"': ''?>
                                           value="sandbox">
                                    <span>sandbox</span>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Amazon S3 Bucket:</label>
                            <div class="controls">
                                <input type="text" class="span4 inputs" name="s3bucket" placeholder="Amazon S3 Bucket"
                                       value="<?=isset($s3bucket)? $s3bucket: ''?>">
                            </div>
                        </div>

                    </div>  
                </div>
            </div>
            <div class="widget-footer">
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>