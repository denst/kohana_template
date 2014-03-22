<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?=(isset($title))?$title:'Ads'?></title>
    <!--begin Css styles-->
    <?php foreach ($styles as $file => $type) echo HTML::style($file, array('media' => $type)), "\n" ?>
    <!--end Css styles-->
    <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
    <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <!-- Inout Adserver - ad code starts -->
</head>
<body>
    <?php ProfilerToolbar::render(true); ?>
    <div class="page">
        <div class="wrap">
            <div class="container-fluid">
                <!-- Main window -->
                <div id="cp_page" style="left: 0 !important; margin: auto !important; width: 77%;" class="main_container">
                    <div id="content_block">
                        <?php
                            if(Helper_Message::count() > 0) {
                                echo Helper_Message::output();
                            }
                            if(isset($content) AND is_object($content)){
                                $content
                                        ->set('settings', (isset($settings))?$settings:'');
                                echo $content; 
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
    <hr>
        <div>
            &copy; 2013
        </div>
    </div>
    <?php foreach ($scripts as $file) echo HTML::script($file), "\n" ?>
</body>
</html>