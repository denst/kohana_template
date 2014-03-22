<script type="text/javascript" src="/ckeditor/ckeditor.js"></script>
<div id="stati_page_form_error" class="alert-error alert fade in" style="margin-top: 15px; display: none">
    <button class="close" data-dismiss="alert" type="button">×</button>
    Required fields must be filled
</div>
<div class="row-fluid" id="settings_anchor">
    <div class="widget widget-padding span12">
        <div class="widget-header">
            <i class="icon-cogs"></i><h5>Static Page</h5>
            <div class="widget-buttons">
                <a data-original-title="" href="#" data-title="Collapse" data-collapsed="false" class="tip collapse"></a>
            </div>
        </div>
        <div class="widget-body">
            <div class="widget-forms clearfix">
                <form id="stati_page_form" action="<?=URL::base()?>admin/staticpages/execution" 
                      class="form-horizontal" method="POST">
                    <? if(isset($static_page)):?>
                        <input type="hidden" name="id" value="<?=$static_page->id?>">
                    <? endif?>
                    <legend>
                        <input type="text" name="title" class="span4 inputs required" placeholder="Title"
                                style="width: 400px;" value="<?=isset($static_page)?$static_page->title:''?>">
                    </legend>
                    <div>
                        <textarea style="width: 600px;" name="content" id="editor1"><?=isset($static_page)?$static_page->content:''?></textarea>
                        <script type="text/javascript">
                            CKEDITOR.replace( 'editor1' );
                        </script>
                    </div>
                    <br/>
                    <button class="btn btn-primary" type="submit" 
                        name="<?=(isset($static_page))?'edit':'add'?>">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>