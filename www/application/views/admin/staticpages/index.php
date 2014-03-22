<div class="row-fluid">
    <div class="widget widget-padding span12">
        <div class="widget-header">
            <i class="icon-file"></i>
            <h5>Static Pages</h5>
        </div>
        <div class="widget-body">
            <table id="users" class="table table-striped table-bordered dataTable all_ads">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Url</th>
                        <th>Title</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <? $index = 1?>
                    <? foreach($static_pages as $static_page):?>
                        <tr>
                            <td><?=$index++?></td>
                            <td id="url-page"><?=$static_page->url?></td>
                            <td id="title-page"><a href="<?=URL::base().'page/'.$static_page->url?>"><?=$static_page->title?></a></td>
                            <td>
                                <div class="btn-group">
                                  <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                                  More
                                    <span class="caret"></span>
                                  </a>
                                  <ul class="dropdown-menu pull-right">
                                    <form class="formStatipageEdit" id="<?=$static_page->id?>" 
                                        action="<?=URL::base()?>admin/staticpages/edit" method="POST" style="margin: 0">
                                        <input type="hidden" name="page_id" value="<?=$static_page->id?>">
                                        <li><a href="#" class="edit_static_page" id="<?=$static_page->id?>">
                                            <i class="icon-edit"></i> edit</a>
                                        </li>
                                    </form>
                                    <li><a class="delete_static_page_button" id="<?=$static_page->id?>"  data-toggle="modal" href="#deleteStaticPage"><i class="icon-edit"></i> delete</a></li>
                                  </ul>
                                </div>
                            </td>
                        </tr>
                    <? endforeach;?>
                </tbody>
            </table>
            <form action="<?=  URL::base()?>admin/staticpages/add" method="POST">
                <button class="new-page btn btn-primary" type="submit" name="add" value="">New static page</button>
            </form>
        </div> <!-- /widget-body -->
    </div> <!-- /widget -->
</div><script type="text/javascript" src="/js/library/datefilter.js"></script>
<div class="modal hide" id="deleteStaticPage">
  <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">✕</button>
      <h3>Are you sure you want to delete this static page?</h3>
  </div>
  <div class="modal-body" style="text-align:center;">
      <div class="row-fluid">
          <div class="span10 offset1">
              <div id="modalTab">
                  <div class="tab-content">
                      <div class="tab-pane active">
                          <form method="post" action="<?=URL::base()?>admin/staticpages/delete" name="completed-form">
                              <p>
                                  <input type="hidden" id="delete_static_page_id" name="static_page" value="">
                                  <button type="submit" class="btn btn-primary">Ok</button>
                                  <button class="btn btn-primaryclose" data-dismiss="modal">Cancel</button>
                              </p>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>